<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeetingRoom;
use App\Models\MeetingRoomBooking;
use Carbon\Carbon;
use App\Models\MeetingRoomType;
use App\Models\MeetingRoomSlot;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;
use Illuminate\Support\Str;


class MeetingRoomAdminController extends Controller
{

    //construct to check if user is authenticated and role is admin
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                return redirect()->route('login')->with('error', 'You must be an admin to access this page.');
            }
            return $next($request);
        });
    }
    /**
     * Display the list of booking asked.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 1. Get all meeting rooms
        $bookings = MeetingRoomBooking::getAllBookings();




        // 3. Return view with data
        return view('meeting-rooms.admin.meetings', compact('bookings',));
    }
    /**
     * Mark a booking as paid.
     * payment_status should be 'paid' 
     * confirmation_status should be 'confirmed'
     * all data sshould be from post request
     */
    public function markAsPaid(Request $request)
    {
        //get the booking id from request and validate it
        $request->validate([
            'booking_id' => 'required|exists:meeting_room_bookings,booking_id',
        ]);
        //find the booking by id
        $booking = MeetingRoomBooking::where('booking_id', $request->booking_id)->firstOrFail();

        // if confirmation_status is confirmed then return error
        if ($booking->confirmation_status === 'confirmed') {
            return redirect()->back()->with('error', 'This booking is already confirmed.');
        }
        // Update the booking status
        $booking->payment_status = 'paid';
        $booking->confirmation_status = 'confirmed';
        $booking->save();
        // Redirect back with success message
        return redirect()->back()->with('success', 'Booking marked as paid and confirmed successfully.');
    }
}
