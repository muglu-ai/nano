<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeetingRoom;
use App\Models\MeetingRoomBooking;
use Carbon\Carbon;
use App\Models\MeetingRoomType;
use App\Models\MeetingRoomSlot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Application;
use App\Mail\MeetingRoomInvoice;
use Illuminate\Support\Facades\Mail;

class MeetingRoomBookingController extends Controller
{
    /**
     * Display the main meeting room booking page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 1. Fetch all meeting room types ordered by type
        $rooms = MeetingRoomType::orderBy('room_type')->get();

        // 2. Define the event dates
        $eventDates = [
            '2025-09-02',
            '2025-09-03',
            '2025-09-04',
        ];

        // 3. Return view with data
        return view('meeting-rooms.index', compact('rooms', 'eventDates'));
    }

    //generate a bookingid check if dones't exist use format as SI25-MR-random
    private function generateBookingId()
    {
        $prefix = 'SI25-MR-';
        $randomString = strtoupper(Str::random(6)); // Generate a random 6-character string
        $bookingId = $prefix . $randomString;
        // Check if the booking ID already exists
        while (MeetingRoomBooking::where('booking_id', $bookingId)->exists()) {
            $randomString = strtoupper(Str::random(6)); // Generate a new random string
            $bookingId = $prefix . $randomString; // Create a new booking ID
        }
        return $bookingId;
    }
    /**
     * Check availability for selected rooms on a specific date
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAvailability(Request $request)
    {
        try {
            $request->validate([
                'room_ids' => 'required|string',
                'date' => 'required|date|date_format:Y-m-d',
            ]);

            $roomIds = explode(',', $request->room_ids);
            $date = Carbon::parse($request->date);

            // Get rooms information
            $rooms = MeetingRoom::whereIn('id', $roomIds)->get();

            // Morning session: 8:30 - 12:30
            $morningStart = Carbon::parse($date)->setTime(8, 30);
            $morningEnd = Carbon::parse($date)->setTime(12, 30);

            // Afternoon session: 13:30 - 17:30
            $afternoonStart = Carbon::parse($date)->setTime(13, 30);
            $afternoonEnd = Carbon::parse($date)->setTime(17, 30);

            // Get existing bookings for both sessions
            $morningBookings = MeetingRoomBooking::whereIn('room_type_id', $roomIds)
                ->where('booking_date', $date->format('Y-m-d'))
                ->where(function ($query) use ($morningStart, $morningEnd) {
                    $query->where(function ($q) use ($morningStart, $morningEnd) {
                        $q->where('start_time', '>=', $morningStart)
                            ->where('start_time', '<', $morningEnd);
                    })->orWhere(function ($q) use ($morningStart, $morningEnd) {
                        $q->where('end_time', '>', $morningStart)
                            ->where('end_time', '<=', $morningEnd);
                    });
                })
                ->count();

            $afternoonBookings = MeetingRoomBooking::whereIn('room_type_id', $roomIds)
                ->where('booking_date', $date->format('Y-m-d'))
                ->where(function ($query) use ($afternoonStart, $afternoonEnd) {
                    $query->where(function ($q) use ($afternoonStart, $afternoonEnd) {
                        $q->where('start_time', '>=', $afternoonStart)
                            ->where('start_time', '<', $afternoonEnd);
                    })->orWhere(function ($q) use ($afternoonStart, $afternoonEnd) {
                        $q->where('end_time', '>', $afternoonStart)
                            ->where('end_time', '<=', $afternoonEnd);
                    });
                })
                ->count();

            // Calculate total capacity for selected rooms
            $totalRoomCapacity = $rooms->sum('qty');

            // Check if date is blocked (e.g., weekends or holidays)
            $isWeekend = $date->isWeekend();
            $isHoliday = $this->isHoliday($date); // You'll need to implement this method

            $response = [
                'morning' => [
                    'is_blocked' => $isWeekend || $isHoliday,
                    'is_available' => !$isWeekend && !$isHoliday && $morningBookings < $totalRoomCapacity,
                    'available_count' => max(0, $totalRoomCapacity - $morningBookings),
                    'reason' => $isWeekend ? 'Weekend' : ($isHoliday ? 'Holiday' : null)
                ],
                'afternoon' => [
                    'is_blocked' => $isWeekend || $isHoliday,
                    'is_available' => !$isWeekend && !$isHoliday && $afternoonBookings < $totalRoomCapacity,
                    'available_count' => max(0, $totalRoomCapacity - $afternoonBookings),
                    'reason' => $isWeekend ? 'Weekend' : ($isHoliday ? 'Holiday' : null)
                ]
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while checking availability.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function check(Request $request)
    {
        $roomIds = explode(',', $request->query('room_ids'));
        $date = $request->query('date');

        if (empty($roomIds) || !$date) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $response = [
            'morning' => $this->checkSlotAvailability($roomIds, $date, 'Morning Slot'),
            'afternoon' => $this->checkSlotAvailability($roomIds, $date, 'Afternoon Slot'),
        ];

        return response()->json($response);
    }

    private function checkSlotAvailability(array $roomIds, $date, $slotName)
    {
        $slot = MeetingRoomSlot::where('slot_name', $slotName)->first();

        if (!$slot) {
            return [

                'is_available' => false,
                'available_count' => 0,
                'is_blocked' => true,
                'reason' => 'Slot not found'
            ];
        }

        // Count booked rooms
        $bookedCount = MeetingRoomBooking::whereIn('room_type_id', $roomIds)
            ->where('slot_id', $slot->id)
            ->where('booking_date', $date)
            ->count();

        // Get total available qty
        $totalAvailable = MeetingRoomType::whereIn('id', $roomIds)->sum('qty');

        if ($totalAvailable === 0) {
            return [
                'is_available' => false,
                'available_count' => 0,
                'is_blocked' => true,
                'reason' => 'No rooms available'
            ];
        }

        if ($bookedCount >= $totalAvailable) {
            return [
                'is_available' => false,
                'available_count' => 0,
                'is_blocked' => false
            ];
        }

        return [

            'is_available' => true,
            'available_count' => $totalAvailable - $bookedCount,
            'is_blocked' => false
        ];
    }

    /**
     * Check if a given date is a holiday
     *
     * @param Carbon $date
     * @return bool
     */
    private function isHoliday(Carbon $date)
    {
        // You can implement your holiday checking logic here
        // For example, you could maintain a table of holidays in the database
        // or use a holiday API

        $holidays = [
            '2025-08-15', // Independence Day
            '2025-10-02', // Gandhi Jayanti
            // Add more holidays as needed
        ];

        return in_array($date->format('Y-m-d'), $holidays);
    }

    // get the id based on the room type and slot type 
    private function getRoomTypeId($roomType, $slotType)
    {
        //  dd($slotType, $roomType);
        //get the 
        $roomType = MeetingRoomSlot::where('room_type_id', $roomType)->first();
        if (!$roomType) {
            return null;
        }


        if ($slotType === 'morning') {
            $slotType = 'Morning Slot';
        } elseif ($slotType === 'afternoon') {
            $slotType = 'Afternoon Slot';
        } else {
            return null; // Invalid slot type
        }



        $slot = MeetingRoomSlot::where('slot_name', $slotType)->first();
        if (!$slot) {
            return null;
        }

        return [
            'room_type_id' => $roomType->id,
            'slot_id' => $slot->id
        ];
    }

    //booking a meeting room
    public function book(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'room_type_id' => 'required|exists:meeting_room_types,id',
            'booking_date' => 'required|date',
            'slot' => 'required|in:morning,afternoon',
        ]);

        //get the room type and slot id based on the room type and slot type
        $roomTypeData = $this->getRoomTypeId($request->room_type_id, $request->slot);
        if (!$roomTypeData) {

            return response()->json(['success' => false, 'message' => 'Invalid room type or slot type.'], 400);
        }

        $application = \App\Models\Application::where('user_id', auth()->id())->first();
        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application not found for the user.'], 404);
        }

        //get the membership_verified status from the application
        $membershipVerified = $application->membership_verified;
        //if verified then set the member_price else non_member_price from the room type
        $roomType = MeetingRoomType::find($request->room_type_id);
        if (!$roomType) {
            return response()->json(['success' => false, 'message' => 'Room type not found.'], 404);
        }
        $finalPrice = $membershipVerified ? $roomType->member_price : $roomType->non_member_price;


        $booking = new MeetingRoomBooking();
        $booking->application_id = $application->id; // Assuming application ID is needed
        $booking->booking_id = $this->generateBookingId(); // Generate unique booking ID

        $booking->room_type_id = $request->room_type_id;
        $booking->booking_date = Carbon::parse($request->booking_date)->format('Y-m-d');
        $booking->final_price = $finalPrice;
        $booking->user_id = auth()->id(); // Assuming user is authenticated
        $booking->slot_id = $roomTypeData['slot_id'];
        $booking->payment_status = 'pending'; // Default payment status
        $booking->confirmation_status = 'pending'; // Default confirmation status







        if ($booking->save()) {
            //redirect to meeting_rooms.mybook 
            //return success message
            //send an new MeetingRoomInvoice($meeting_id) 
            //to the user email with the booking details
            // Collect recipient emails
            $toEmails = [
                Auth::user()->email,
                $application->event_contact_person_email,
            ];

            // Remove duplicates and filter out empty/null emails
            $toEmails = array_filter(array_unique($toEmails));

            Mail::to($toEmails)
                // ->cc('semiconindia@semi.org')
                ->bcc('test.interlinks@gmail.com')
                ->send(new MeetingRoomInvoice($booking->booking_id));
            return redirect()->route('meeting_rooms.mybook');

            return response()->json(['success' => true, 'message' => 'Room booked successfully!']);
        } else {
            //redirect to meeting_rooms.index with error message
            return redirect()->route('meeting_rooms.index')->withErrors(['message' => 'Failed to book the room.']);
            return response()->json(['success' => false, 'message' => 'Failed to book the room.'], 500);
        }
    }

    //my bookings
    public function myBookings()
    {
        $user = Auth::user();

        $bookings = MeetingRoomBooking::with(['roomType', 'slot'])
            ->where('user_id', $user->id)
            ->get();



        $upcomingCount = $bookings->filter(fn($b) => \Carbon\Carbon::parse($b->date)->isFuture())->count();
        $totalHours = $bookings->sum('duration');

        $rooms = MeetingRoomType::all();

        return view('meeting-rooms.my_bookings', compact('bookings', 'upcomingCount', 'totalHours', 'rooms'));
    }
}
