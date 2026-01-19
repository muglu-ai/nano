<?php

namespace App\Http\Controllers\Delegate;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket\TicketDelegate;
use App\Models\Ticket\DelegateNotification;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketRegistration;

class DelegateDashboardController extends Controller
{
    /**
     * Show delegate dashboard
     */
    public function dashboard()
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        // Get all delegates for this contact
        $delegates = TicketDelegate::whereHas('registration', function ($query) use ($contact) {
            $query->where('contact_id', $contact->id);
        })->with(['ticket', 'registration.event'])->get();

        // Get all tickets
        $tickets = Ticket::whereHas('delegate.registration', function ($query) use ($contact) {
            $query->where('contact_id', $contact->id);
        })->with(['delegate', 'ticketType.category', 'event'])->get();

        // Get all registrations
        $registrations = TicketRegistration::where('contact_id', $contact->id)
            ->with(['event', 'delegates', 'order'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get unread notifications count
        $unreadNotificationsCount = DelegateNotification::where(function ($query) use ($contact, $delegates) {
            $query->where('contact_id', $contact->id)
                ->orWhereIn('delegate_id', $delegates->pluck('id'));
        })->unread()->count();

        // Get recent notifications
        $recentNotifications = DelegateNotification::where(function ($query) use ($contact, $delegates) {
            $query->where('contact_id', $contact->id)
                ->orWhereIn('delegate_id', $delegates->pluck('id'));
        })->orderBy('created_at', 'desc')->limit(5)->get();

        return view('delegate.dashboard.index', compact(
            'delegates',
            'tickets',
            'registrations',
            'unreadNotificationsCount',
            'recentNotifications'
        ));
    }
}
