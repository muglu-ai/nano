<?php

namespace App\Http\Controllers\Delegate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket\DelegateNotification;
use App\Models\Ticket\TicketDelegate;

class DelegateNotificationController extends Controller
{
    /**
     * List all notifications
     */
    public function index(Request $request)
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        // Get all delegates for this contact
        $delegateIds = TicketDelegate::whereHas('registration', function ($query) use ($contact) {
            $query->where('contact_id', $contact->id);
        })->pluck('id');

        $query = DelegateNotification::where(function ($q) use ($contact, $delegateIds) {
            $q->where('contact_id', $contact->id)
                ->orWhereIn('delegate_id', $delegateIds);
        });

        // Filter by read status
        if ($request->has('filter')) {
            if ($request->filter === 'unread') {
                $query->unread();
            } elseif ($request->filter === 'read') {
                $query->read();
            }
        }

        $notifications = $query->with(['delegate', 'contact', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('delegate.notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        $delegateIds = TicketDelegate::whereHas('registration', function ($query) use ($contact) {
            $query->where('contact_id', $contact->id);
        })->pluck('id');

        $notification = DelegateNotification::where(function ($q) use ($contact, $delegateIds) {
            $q->where('contact_id', $contact->id)
                ->orWhereIn('delegate_id', $delegateIds);
        })->findOrFail($id);

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        $delegateIds = TicketDelegate::whereHas('registration', function ($query) use ($contact) {
            $query->where('contact_id', $contact->id);
        })->pluck('id');

        DelegateNotification::where(function ($q) use ($contact, $delegateIds) {
            $q->where('contact_id', $contact->id)
                ->orWhereIn('delegate_id', $delegateIds);
        })->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread count (API endpoint)
     */
    public function unreadCount()
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        $delegateIds = TicketDelegate::whereHas('registration', function ($query) use ($contact) {
            $query->where('contact_id', $contact->id);
        })->pluck('id');

        $count = DelegateNotification::where(function ($q) use ($contact, $delegateIds) {
            $q->where('contact_id', $contact->id)
                ->orWhereIn('delegate_id', $delegateIds);
        })->unread()->count();

        return response()->json(['count' => $count]);
    }
}
