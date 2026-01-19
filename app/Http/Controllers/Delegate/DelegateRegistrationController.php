<?php

namespace App\Http\Controllers\Delegate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket\TicketRegistration;
use App\Models\Ticket\TicketDelegate;
use App\Models\Ticket\TicketOrder;
use App\Models\Ticket\TicketPayment;

class DelegateRegistrationController extends Controller
{
    /**
     * List all registrations
     */
    public function index()
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        // Get all registrations where this contact is the primary contact
        $registrations = TicketRegistration::where('contact_id', $contact->id)
            ->with(['event', 'delegates.ticket.ticketType.category', 'order', 'registrationCategory'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Also get registrations where delegate email matches
        $delegateRegistrations = TicketRegistration::whereHas('delegates', function ($query) use ($contact) {
            $query->where('email', $contact->email);
        })->with(['event', 'delegates.ticket.ticketType.category', 'order', 'registrationCategory'])
            ->where('contact_id', '!=', $contact->id) // Exclude already included
            ->get();

        return view('delegate.registrations.index', compact('registrations', 'delegateRegistrations'));
    }

    /**
     * Show registration details
     */
    public function show($id)
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        // Get registration where contact is primary OR delegate email matches
        $registration = TicketRegistration::where(function ($query) use ($contact, $id) {
            $query->where('id', $id)
                ->where(function ($q) use ($contact) {
                    $q->where('contact_id', $contact->id)
                        ->orWhereHas('delegates', function ($dq) use ($contact) {
                            $dq->where('email', $contact->email);
                        });
                });
        })->with([
            'event',
            'contact',
            'delegates.ticket.ticketType.category',
            'delegates.assignment.ticketType',
            'order.items.ticketType',
            'registrationCategory'
        ])->firstOrFail();

        // Get payment information
        $payment = null;
        if ($registration->order) {
            $payment = TicketPayment::whereJsonContains('order_ids_json', $registration->order->id)
                ->where('status', 'completed')
                ->orderBy('paid_at', 'desc')
                ->first();
        }

        // Get upgrade requests for this registration
        $upgradeRequests = \App\Models\Ticket\TicketUpgradeRequest::where('registration_id', $registration->id)
            ->with(['upgradeOrder'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('delegate.registrations.show', compact('registration', 'payment', 'upgradeRequests'));
    }
}
