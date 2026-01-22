<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\Ticket\TicketPromoCode;
use App\Models\Ticket\TicketRegistrationCategory;
use App\Models\Ticket\TicketCategory;
use App\Models\Ticket\EventDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminPromoCodeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!in_array($user->role, ['admin', 'super-admin'])) {
                abort(403, 'Unauthorized access');
            }
            return $next($request);
        });
    }

    /**
     * List all promocodes for an event
     */
    public function index(Request $request, $eventId)
    {
        $event = Events::findOrFail($eventId);
        
        $query = TicketPromoCode::where('event_id', $eventId)
            ->with(['createdBy', 'redemptions'])
            ->orderBy('created_at', 'desc');

        // Filter by organization if provided
        if ($request->has('organization') && $request->organization) {
            $query->where('organization_name', $request->organization);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $promoCodes = $query->get();
        
        // Get unique organizations for filter
        $organizations = TicketPromoCode::where('event_id', $eventId)
            ->whereNotNull('organization_name')
            ->distinct()
            ->pluck('organization_name')
            ->sort()
            ->values();

        return view('tickets.admin.promo-codes.index', compact('event', 'promoCodes', 'organizations'));
    }

    /**
     * Show create form
     */
    public function create($eventId)
    {
        $event = Events::findOrFail($eventId);
        
        // Get data for form dropdowns
        $registrationCategories = TicketRegistrationCategory::where('event_id', $eventId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $ticketCategories = TicketCategory::where('event_id', $eventId)
            ->orderBy('name')
            ->get();
        
        $eventDays = EventDay::where('event_id', $eventId)
            ->orderBy('date')
            ->get();

        return view('tickets.admin.promo-codes.create', compact(
            'event',
            'registrationCategories',
            'ticketCategories',
            'eventDays'
        ));
    }

    /**
     * Store new promocode
     */
    public function store(Request $request, $eventId)
    {
        $event = Events::findOrFail($eventId);

            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:100|unique:ticket_promo_codes,code',
                'organization_name' => 'nullable|string|max:255',
                'type' => 'required|in:percentage,fixed',
                'value' => 'required|numeric|min:0',
                'valid_from' => 'nullable|date',
                'valid_to' => 'nullable|date|after_or_equal:valid_from',
                'max_uses' => 'nullable|integer|min:1',
                'max_uses_per_contact' => 'nullable|integer|min:1',
                'min_order_amount' => 'nullable|numeric|min:0',
                'applicable_registration_category_ids' => 'nullable|array',
                'applicable_registration_category_ids.*' => 'exists:ticket_registration_categories,id',
                'applicable_ticket_category_ids' => 'nullable|array',
                'applicable_ticket_category_ids.*' => 'exists:ticket_categories,id',
                'applicable_event_day_ids' => 'nullable|array',
                'applicable_event_day_ids.*' => 'exists:event_days,id',
                'max_delegates' => 'nullable|integer|min:1',
                'min_delegates' => 'nullable|integer|min:1',
                'description' => 'nullable|string|max:1000',
            ]);

        // Validate percentage doesn't exceed 100
        if ($request->type === 'percentage' && $request->value > 100) {
            $validator->errors()->add('value', 'Percentage discount cannot exceed 100%.');
            return back()->withErrors($validator)->withInput();
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $data = [
                'event_id' => $eventId,
                'code' => strtoupper(trim($request->code)),
                'organization_name' => $request->organization_name ? trim($request->organization_name) : null,
                'type' => $request->type,
                'value' => $request->value,
                'valid_from' => $request->valid_from ? date('Y-m-d H:i:s', strtotime($request->valid_from)) : null,
                'valid_to' => $request->valid_to ? date('Y-m-d H:i:s', strtotime($request->valid_to)) : null,
                'max_uses' => $request->has('unlimited_uses') ? null : ($request->max_uses ?? null),
                'max_uses_per_contact' => $request->max_uses_per_contact ?? null,
                'min_order_amount' => $request->min_order_amount ?? null,
                'applicable_registration_category_ids_json' => $request->applicable_registration_category_ids ?? null,
                'applicable_ticket_category_ids_json' => $request->applicable_ticket_category_ids ?? null,
                'applicable_event_day_ids_json' => $request->applicable_event_day_ids ?? null,
                'max_delegates' => $request->has('unlimited_delegates') ? null : ($request->max_delegates ?? null),
                'min_delegates' => $request->min_delegates ?? 1,
                'apply_to_base_amount_only' => true, // Always true as per requirements
                'description' => $request->description,
                'is_active' => (bool) $request->input('is_active', false),
                'created_by' => auth()->id(),
            ];

            TicketPromoCode::create($data);

            DB::commit();

            return redirect()->route('admin.tickets.events.promo-codes', $eventId)
                ->with('success', 'Promocode created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating promocode: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show edit form
     */
    public function edit($eventId, $promoCodeId)
    {
        $event = Events::findOrFail($eventId);
        $promoCode = TicketPromoCode::where('event_id', $eventId)
            ->findOrFail($promoCodeId);
        
        // Get data for form dropdowns
        $registrationCategories = TicketRegistrationCategory::where('event_id', $eventId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $ticketCategories = TicketCategory::where('event_id', $eventId)
            ->orderBy('name')
            ->get();
        
        $eventDays = EventDay::where('event_id', $eventId)
            ->orderBy('date')
            ->get();

        return view('tickets.admin.promo-codes.edit', compact(
            'event',
            'promoCode',
            'registrationCategories',
            'ticketCategories',
            'eventDays'
        ));
    }

    /**
     * Update promocode
     */
    public function update(Request $request, $eventId, $promoCodeId)
    {
        $event = Events::findOrFail($eventId);
        $promoCode = TicketPromoCode::where('event_id', $eventId)
            ->findOrFail($promoCodeId);

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:100|unique:ticket_promo_codes,code,' . $promoCodeId,
            'organization_name' => 'nullable|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_contact' => 'nullable|integer|min:1',
            'min_order_amount' => 'nullable|numeric|min:0',
            'applicable_registration_category_ids' => 'nullable|array',
            'applicable_registration_category_ids.*' => 'exists:ticket_registration_categories,id',
            'applicable_ticket_category_ids' => 'nullable|array',
            'applicable_ticket_category_ids.*' => 'exists:ticket_categories,id',
            'applicable_event_day_ids' => 'nullable|array',
            'applicable_event_day_ids.*' => 'exists:event_days,id',
            'max_delegates' => 'nullable|integer|min:1',
            'min_delegates' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:1000',
        ]);

        // Validate percentage doesn't exceed 100
        if ($request->type === 'percentage' && $request->value > 100) {
            $validator->errors()->add('value', 'Percentage discount cannot exceed 100%.');
            return back()->withErrors($validator)->withInput();
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $data = [
                'code' => strtoupper(trim($request->code)),
                'organization_name' => $request->organization_name ? trim($request->organization_name) : null,
                'type' => $request->type,
                'value' => $request->value,
                'valid_from' => $request->valid_from ? date('Y-m-d H:i:s', strtotime($request->valid_from)) : null,
                'valid_to' => $request->valid_to ? date('Y-m-d H:i:s', strtotime($request->valid_to)) : null,
                'max_uses' => $request->has('unlimited_uses') ? null : ($request->max_uses ?? null),
                'max_uses_per_contact' => $request->max_uses_per_contact ?? null,
                'min_order_amount' => $request->min_order_amount ?? null,
                'applicable_registration_category_ids_json' => $request->applicable_registration_category_ids ?? null,
                'applicable_ticket_category_ids_json' => $request->applicable_ticket_category_ids ?? null,
                'applicable_event_day_ids_json' => $request->applicable_event_day_ids ?? null,
                'max_delegates' => $request->has('unlimited_delegates') ? null : ($request->max_delegates ?? null),
                'min_delegates' => $request->min_delegates ?? 1,
                'description' => $request->description,
                'is_active' => (bool) $request->input('is_active', false),
            ];

            $promoCode->update($data);

            DB::commit();

            return redirect()->route('admin.tickets.events.promo-codes', $eventId)
                ->with('success', 'Promocode updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating promocode: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete promocode
     */
    public function destroy($eventId, $promoCodeId)
    {
        $promoCode = TicketPromoCode::where('event_id', $eventId)
            ->findOrFail($promoCodeId);

        // Check if promocode has been used
        $usedCount = $promoCode->getUsedCount();
        if ($usedCount > 0) {
            return back()->with('error', 'Cannot delete promocode that has been used. Deactivate it instead.');
        }

        try {
            $promoCode->delete();
            return redirect()->route('admin.tickets.events.promo-codes', $eventId)
                ->with('success', 'Promocode deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting promocode: ' . $e->getMessage());
        }
    }

    /**
     * Toggle promocode status
     */
    public function toggleStatus($eventId, $promoCodeId)
    {
        $promoCode = TicketPromoCode::where('event_id', $eventId)
            ->findOrFail($promoCodeId);

        $promoCode->update([
            'is_active' => !$promoCode->is_active
        ]);

        $status = $promoCode->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Promocode {$status} successfully!");
    }

    /**
     * Show promocode analytics
     */
    public function analytics($eventId, $promoCodeId)
    {
        $event = Events::findOrFail($eventId);
        $promoCode = TicketPromoCode::where('event_id', $eventId)
            ->with(['redemptions.order', 'redemptions.contact'])
            ->findOrFail($promoCodeId);

        $redemptions = $promoCode->redemptions()
            ->with(['order.registration', 'contact'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_redemptions' => $promoCode->getUsedCount(),
            'remaining_uses' => $promoCode->getRemainingUses(),
            'total_discount_given' => $redemptions->sum('discount_amount'),
            'total_orders' => $redemptions->count(),
        ];

        // Determine currency (default to INR)
        $currencySymbol = '₹';

        return view('tickets.admin.promo-codes.analytics', compact('event', 'promoCode', 'redemptions', 'stats', 'currencySymbol'));
    }

    /**
     * Show organization report
     */
    public function organizationReport($eventId, $organizationName)
    {
        $event = Events::findOrFail($eventId);
        
        $promoCodes = TicketPromoCode::where('event_id', $eventId)
            ->where('organization_name', $organizationName)
            ->with(['redemptions', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tickets.admin.promo-codes.organization-report', compact('event', 'promoCodes', 'organizationName'));
    }
}
