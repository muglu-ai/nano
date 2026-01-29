<?php

namespace App\Http\Controllers\Delegate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketType;
use App\Models\Ticket\TicketRegistration;
use App\Models\Ticket\TicketDelegate;
use App\Models\Ticket\TicketDelegateAssignment;
use App\Models\Ticket\TicketUpgradeRequest;
use App\Models\Ticket\TicketUpgrade;
use App\Services\TicketUpgradeService;
use Illuminate\Support\Facades\Log;

class DelegateUpgradeController extends Controller
{
    protected $upgradeService;

    public function __construct(TicketUpgradeService $upgradeService)
    {
        $this->upgradeService = $upgradeService;
    }

    /**
     * Show upgrade index page
     */
    public function index()
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        // Get all tickets for this contact
        $tickets = Ticket::whereHas('delegate.registration', function ($query) use ($contact) {
            $query->where('contact_id', $contact->id);
        })->with(['delegate', 'ticketType.category', 'event'])->get();

        // Get all registrations
        $registrations = TicketRegistration::where('contact_id', $contact->id)
            ->with(['delegates.ticket.ticketType.category', 'event'])
            ->get();

        // Get pending upgrade requests
        $pendingUpgrades = TicketUpgradeRequest::where('contact_id', $contact->id)
            ->where('status', 'pending')
            ->active()
            ->with(['registration', 'upgradeOrder'])
            ->get();

        return view('delegate.upgrades.index', compact('tickets', 'registrations', 'pendingUpgrades'));
    }

    /**
     * Show individual upgrade form
     */
    public function showIndividualUpgradeForm($ticketId)
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        $ticket = Ticket::whereHas('delegate.registration', function ($query) use ($contact) {
            $query->where('contact_id', $contact->id);
        })->with(['ticketType.category', 'delegate', 'event'])->findOrFail($ticketId);

        // Check for existing pending upgrade
        $existingUpgrade = TicketUpgradeRequest::where('contact_id', $contact->id)
            ->where('status', 'pending')
            ->whereJsonContains('upgrade_data_json->tickets', [['ticket_id' => $ticketId]])
            ->active()
            ->first();

        // Get available higher category ticket types
        $availableTicketTypes = $this->getAvailableUpgradeOptions($ticket->ticketType, $ticket->event_id);

        return view('delegate.upgrades.individual-form', compact('ticket', 'availableTicketTypes', 'existingUpgrade'));
    }

    /**
     * Show group upgrade form
     */
    public function showGroupUpgradeForm($registrationId)
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        $registration = TicketRegistration::where('contact_id', $contact->id)
            ->with(['delegates.ticket.ticketType.category', 'event'])
            ->findOrFail($registrationId);

        // Get all tickets in this registration
        $tickets = Ticket::whereHas('delegate', function ($query) use ($registrationId) {
            $query->where('registration_id', $registrationId);
        })->with(['ticketType.category', 'delegate'])->get();

        // Get available upgrade options (based on highest ticket type in registration)
        $highestTicketType = $tickets->map(function ($ticket) {
            return $ticket->ticketType;
        })->sortByDesc(function ($type) {
            return $type->getCurrentPrice('national');
        })->first();

        $availableTicketTypes = $this->getAvailableUpgradeOptions($highestTicketType, $registration->event_id);

        return view('delegate.upgrades.group-form', compact('registration', 'tickets', 'availableTicketTypes'));
    }

    /**
     * Preview upgrade with calculated amounts
     */
    public function previewUpgrade(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'new_ticket_type_id' => 'required|exists:ticket_types,id',
        ]);

        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        $ticket = Ticket::whereHas('delegate.registration', function ($query) use ($contact) {
            $query->where('contact_id', $contact->id);
        })->with(['ticketType', 'delegate.registration'])->findOrFail($request->ticket_id);

        $newTicketType = TicketType::findOrFail($request->new_ticket_type_id);

        // Get nationality from registration
        $nationality = $ticket->delegate->registration->nationality ?? 'Indian';

        // Calculate price difference
        $calculation = $this->upgradeService->calculatePriceDifference(
            $ticket->ticketType,
            $newTicketType,
            1,
            $nationality
        );

        return response()->json([
            'success' => true,
            'calculation' => $calculation,
            'old_ticket_type' => [
                'id' => $ticket->ticketType->id,
                'name' => $ticket->ticketType->name,
                'category' => $ticket->ticketType->category->name ?? null,
            ],
            'new_ticket_type' => [
                'id' => $newTicketType->id,
                'name' => $newTicketType->name,
                'category' => $newTicketType->category->name ?? null,
            ],
        ]);
    }

    /**
     * Process individual upgrade
     */
    public function processIndividualUpgrade(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'new_ticket_type_id' => 'required|exists:ticket_types,id',
        ]);

        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        $ticket = Ticket::whereHas('delegate.registration', function ($query) use ($contact) {
            $query->where('contact_id', $contact->id);
        })->with(['ticketType', 'delegate.registration', 'delegate.assignment'])->findOrFail($request->ticket_id);

        // Check for existing pending upgrade
        $existingUpgrade = TicketUpgradeRequest::where('contact_id', $contact->id)
            ->where('status', 'pending')
            ->whereJsonContains('upgrade_data_json->tickets', [['ticket_id' => $ticket->id]])
            ->active()
            ->first();

        if ($existingUpgrade) {
            return redirect()->route('delegate.upgrades.form', $ticket->id)
                ->with('error', 'You already have a pending upgrade request for this ticket.');
        }

        $newTicketType = TicketType::with('category')->findOrFail($request->new_ticket_type_id);

        // Get nationality
        $nationality = $ticket->delegate->registration->nationality ?? 'Indian';
        $nationalityForPrice = ($nationality === 'International' || $nationality === 'international') 
            ? 'international' 
            : 'national';

        // Get already paid amount from assignment (price_snapshot) - this is what was actually paid
        $alreadyPaidPrice = $ticket->delegate->assignment->price_snapshot ?? $ticket->ticketType->getCurrentPrice($nationalityForPrice);
        
        // Get new price from table
        $newPrice = $newTicketType->getCurrentPrice($nationalityForPrice);

        // Verify it's a higher category (price-based)
        if ($newPrice <= $alreadyPaidPrice) {
            return back()->withErrors([
                'new_ticket_type_id' => 'You can only upgrade to a higher category ticket.',
            ]);
        }

        // Calculate price difference (subtract already paid from new total)
        $calculation = $this->upgradeService->calculatePriceDifference(
            $ticket->ticketType,
            $newTicketType,
            1,
            $nationality,
            $alreadyPaidPrice // Pass already paid amount
        );

        // Prepare upgrade data
        $upgradeData = [
            'type' => 'individual',
            'tickets' => [
                [
                    'ticket_id' => $ticket->id,
                    'delegate_id' => $ticket->delegate_id,
                    'old_ticket_type_id' => $ticket->ticket_type_id,
                    'old_ticket_type_name' => $ticket->ticketType->name,
                    'old_price' => $alreadyPaidPrice,
                    'old_total' => $calculation['old_total'] ?? 0,
                    'new_ticket_type_id' => $newTicketType->id,
                    'new_ticket_type_name' => $newTicketType->name,
                    'new_price' => $newPrice,
                    'new_total' => $calculation['new_total'] ?? 0,
                    'price_difference' => $calculation['price_difference'],
                    'remaining_amount' => $calculation['remaining_amount'],
                    'quantity' => 1,
                ],
            ],
            'totals' => [
                'old_total' => $calculation['old_total'] ?? 0,
                'new_total' => $calculation['new_total'] ?? 0,
                'subtotal' => $calculation['price_difference'],
                'gst_amount' => $calculation['gst_amount'],
                'processing_charge_amount' => $calculation['processing_charge_amount'],
                'total' => $calculation['total_amount'], // Remaining amount to pay
            ],
        ];

        // Create upgrade request
        $upgradeRequest = $this->upgradeService->createUpgradeRequest('individual', [
            'contact_id' => $contact->id,
            'registration_id' => $ticket->delegate->registration_id,
            'upgrade_data' => $upgradeData,
            'price_difference' => $calculation['price_difference'], // Unit price difference
            'gst_amount' => $calculation['gst_amount'],
            'processing_charge_amount' => $calculation['processing_charge_amount'],
            'total_amount' => $calculation['total_amount'], // Remaining amount to pay
        ]);

        return redirect()->route('delegate.upgrades.receipt', $upgradeRequest->id)
            ->with('success', 'Upgrade request created. Please proceed to payment.');
    }

    /**
     * Process group upgrade
     */
    public function processGroupUpgrade(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|exists:ticket_registrations,id',
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:tickets,id',
            'new_ticket_type_ids' => 'required|array',
            'new_ticket_type_ids.*' => 'exists:ticket_types,id',
        ]);

        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        $registration = TicketRegistration::where('contact_id', $contact->id)
            ->findOrFail($request->registration_id);

        $ticketIds = $request->ticket_ids;
        $newTicketTypeIds = $request->new_ticket_type_ids;

        if (count($ticketIds) !== count($newTicketTypeIds)) {
            return back()->withErrors(['ticket_ids' => 'Number of tickets and new ticket types must match.']);
        }

        $tickets = Ticket::whereIn('id', $ticketIds)
            ->whereHas('delegate', function ($query) use ($registration) {
                $query->where('registration_id', $registration->id);
            })
            ->with(['ticketType', 'delegate.assignment'])
            ->get();

        if ($tickets->count() !== count($ticketIds)) {
            return back()->withErrors(['ticket_ids' => 'Some tickets are invalid.']);
        }

        $nationality = $registration->nationality ?? 'Indian';
        $totalPriceDifference = 0;
        $upgradeTickets = [];

        $nationalityForPrice = ($nationality === 'International' || $nationality === 'international') 
            ? 'international' 
            : 'national';

        foreach ($tickets as $index => $ticket) {
            $newTicketType = TicketType::find($newTicketTypeIds[$index]);
            if (!$newTicketType) {
                continue;
            }

            // Get already paid amount from assignment (price_snapshot)
            $alreadyPaidPrice = $ticket->delegate->assignment->price_snapshot ?? $ticket->ticketType->getCurrentPrice($nationalityForPrice);
            $newPrice = $newTicketType->getCurrentPrice($nationalityForPrice);

            if ($newPrice <= $alreadyPaidPrice) {
                return back()->withErrors([
                    'new_ticket_type_ids.' . $index => 'You can only upgrade to a higher category ticket.',
                ]);
            }

            $calculation = $this->upgradeService->calculatePriceDifference(
                $ticket->ticketType,
                $newTicketType,
                1,
                $nationality,
                $alreadyPaidPrice // Pass already paid amount
            );

            $totalPriceDifference += $calculation['remaining_amount']; // Use remaining amount

            $upgradeTickets[] = [
                'ticket_id' => $ticket->id,
                'delegate_id' => $ticket->delegate_id,
                'old_ticket_type_id' => $ticket->ticket_type_id,
                'old_ticket_type_name' => $ticket->ticketType->name,
                'old_price' => $alreadyPaidPrice,
                'old_total' => $calculation['old_total'] ?? 0,
                'new_ticket_type_id' => $newTicketType->id,
                'new_ticket_type_name' => $newTicketType->name,
                'new_price' => $newPrice,
                'new_total' => $calculation['new_total'] ?? 0,
                'price_difference' => $calculation['price_difference'],
                'remaining_amount' => $calculation['remaining_amount'],
                'quantity' => 1,
            ];
        }

        // Calculate totals from all tickets
        $totalOldAmount = collect($upgradeTickets)->sum('old_total');
        $totalNewAmount = collect($upgradeTickets)->sum('new_total');
        $totalRemaining = $totalNewAmount - $totalOldAmount;

        // Calculate GST and charges breakdown from all calculations
        $totalGstDiff = collect($allCalculations)->sum('gst_amount');
        $totalProcessingDiff = collect($allCalculations)->sum('processing_charge_amount');

        $upgradeData = [
            'type' => 'group',
            'tickets' => $upgradeTickets,
            'totals' => [
                'old_total' => $totalOldAmount,
                'new_total' => $totalNewAmount,
                'subtotal' => collect($upgradeTickets)->sum('price_difference'),
                'gst_amount' => $totalGstDiff,
                'processing_charge_amount' => $totalProcessingDiff,
                'total' => $totalRemaining, // Remaining amount to pay
            ],
        ];

        // Create upgrade request
        $upgradeRequest = $this->upgradeService->createUpgradeRequest('group', [
            'contact_id' => $contact->id,
            'registration_id' => $registration->id,
            'upgrade_data' => $upgradeData,
            'price_difference' => collect($upgradeTickets)->sum('price_difference'), // Unit price difference
            'gst_amount' => $totalGstDiff,
            'processing_charge_amount' => $totalProcessingDiff,
            'total_amount' => $totalRemaining, // Remaining amount to pay
        ]);

        return redirect()->route('delegate.upgrades.receipt', $upgradeRequest->id)
            ->with('success', 'Group upgrade request created. Please proceed to payment.');
    }

    /**
     * Confirm upgrade and create order
     */
    public function confirmUpgrade($requestId)
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        $upgradeRequest = TicketUpgradeRequest::where('contact_id', $contact->id)
            ->findOrFail($requestId);

        if (!$upgradeRequest->canBeProcessed()) {
            return redirect()->route('delegate.upgrades.index')
                ->with('error', 'Upgrade request is not available for processing.');
        }

        // Create order
        $order = $this->upgradeService->createUpgradeOrder($requestId);

        return redirect()->route('delegate.upgrades.payment.initiate', $requestId);
    }

    /**
     * Show upgrade receipt
     */
    public function showReceipt($requestId)
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        $upgradeRequest = TicketUpgradeRequest::where('contact_id', $contact->id)
            ->with(['registration.event', 'upgradeOrder'])
            ->findOrFail($requestId);

        $receipt = $upgradeRequest->upgradeOrder?->receipt;

        return view('delegate.upgrades.receipt', compact('upgradeRequest', 'receipt'));
    }

    /**
     * Show upgrade history
     */
    public function history()
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        $upgrades = TicketUpgradeRequest::where('contact_id', $contact->id)
            ->where('status', 'paid')
            ->with(['registration.event', 'upgradeOrder', 'upgrades'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('delegate.upgrades.history', compact('upgrades'));
    }

    /**
     * Get available upgrade options (higher categories only)
     */
    private function getAvailableUpgradeOptions(TicketType $currentTicketType, $eventId)
    {
        $currentPrice = $currentTicketType->getCurrentPrice('national');

        return TicketType::where('event_id', $eventId)
            ->where('is_active', true)
            ->where('id', '!=', $currentTicketType->id)
            ->with(['category', 'subcategory'])
            ->get()
            ->filter(function ($ticketType) use ($currentPrice) {
                return $ticketType->getCurrentPrice('national') > $currentPrice;
            })
            ->sortBy(function ($ticketType) {
                return $ticketType->getCurrentPrice('national');
            })
            ->values();
    }
}
