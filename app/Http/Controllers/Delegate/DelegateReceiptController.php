<?php

namespace App\Http\Controllers\Delegate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket\TicketReceipt;
use App\Models\Ticket\TicketOrder;
use App\Models\Ticket\TicketUpgradeRequest;
use App\Models\Ticket\TicketRegistration;

class DelegateReceiptController extends Controller
{
    /**
     * List all receipts
     */
    public function index()
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        // Get all registrations for this contact
        $registrationIds = TicketRegistration::where('contact_id', $contact->id)->pluck('id');

        // Get all orders for these registrations
        $orderIds = TicketOrder::whereIn('registration_id', $registrationIds)->pluck('id');

        // Get receipts
        $receipts = TicketReceipt::whereIn('order_id', $orderIds)
            ->with(['order.registration.event', 'registration'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get upgrade receipts
        $upgradeRequests = TicketUpgradeRequest::where('contact_id', $contact->id)
            ->where('status', 'paid')
            ->with(['upgradeOrder.receipt', 'registration.event'])
            ->get();

        return view('delegate.receipts.index', compact('receipts', 'upgradeRequests'));
    }

    /**
     * Show receipt details
     */
    public function show($id)
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        $receipt = TicketReceipt::whereHas('order.registration', function ($query) use ($contact) {
            $query->where('contact_id', $contact->id);
        })->with(['order.registration.event', 'order.items.ticketType', 'registration'])->findOrFail($id);

        return view('delegate.receipts.show', compact('receipt'));
    }

    /**
     * Download receipt PDF
     */
    public function download($id)
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        $receipt = TicketReceipt::whereHas('order.registration', function ($query) use ($contact) {
            $query->where('contact_id', $contact->id);
        })->with(['order.registration.event', 'order.items.ticketType', 'registration'])->findOrFail($id);

        // Generate PDF (implement PDF generation logic)
        // For now, return view
        return view('delegate.receipts.pdf', compact('receipt'));
    }
}
