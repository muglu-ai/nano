<?php

namespace App\Http\Controllers\Delegate;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket\TicketDelegate;

class DelegateBadgeController extends Controller
{
    /**
     * Show badge (Coming Soon placeholder)
     */
    public function show($delegateId)
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        // Verify delegate belongs to this contact
        $delegate = TicketDelegate::whereHas('registration', function ($query) use ($contact) {
            $query->where('contact_id', $contact->id);
        })->findOrFail($delegateId);

        return view('delegate.badges.show', compact('delegate'));
    }
}
