<?php

namespace App\Mail;

use App\Models\ProductCategory;
use App\Models\Sector;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use App\Models\BillingDetail;
use App\Models\Invoice;
use App\Models\CoExhibitor;
use App\Models\MeetingRoomBooking;
use App\Models\MeetingRoomType;
use App\Models\MeetingRoomSlot;
use App\Models\EventContact;


class MeetingRoomInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    //construct function
    public function __construct($meeting_id)
    {
        // Fetch the meeting room data
        $meeting = MeetingRoomBooking::where('booking_id', $meeting_id)
            ->first();
        if (!$meeting) {
            throw new \Exception('Meeting not found for the given ID.');
        }

        $application_id = $meeting->application_id;
        $app = Application::where('id', $application_id)
            ->with(['billingDetail'])
            ->first();



        // if not found, throw an exception
        if (!$app) {
            throw new \Exception('Application not found for the given ID.');
        }

        $billing = $app->billingDetail;


        $meetingRoomType = MeetingRoomType::where('id', $meeting->room_type_id)
            ->first();

        $meetingRoomSlot = MeetingRoomSlot::where('id', $meeting->slot_id)
            ->first();

        $eventContact = EventContact::where('application_id', $app->id)
            ->first();



        try {
            $this->data['booking_id'] = $meeting->booking_id;
            $this->data['confirmation_date'] = $meeting->confirmation_date;
            $this->data['exhibitor_name'] = $app->company_name;
            $this->data['confirmation_status'] = $meeting->confirmation_status ?? '';
            // Room Details
            $this->data['room_type'] = $meetingRoomType->room_type;
            $this->data['room_location'] = $meetingRoomType->location;
            $this->data['booking_date'] = $meeting->booking_date;
            $this->data['time_slot'] = $meetingRoomSlot->slot_name . ' (' . $meetingRoomSlot->start_time . '-' . $meetingRoomSlot->end_time . ')';
            // Calculate duration in minutes between start_time and end_time
            $start = \Carbon\Carbon::parse($meetingRoomSlot->start_time);
            $end = \Carbon\Carbon::parse($meetingRoomSlot->end_time);
            $durationMinutes = $start->diffInMinutes($end);
            $durationHours = round($durationMinutes / 60, 2);
            $this->data['duration'] = $durationHours . ' Hours';
            $this->data['capacity'] = $meetingRoomType->capacity;
            // Room Features
            $this->data['room_features'] = $meetingRoomType->equipment ?? '';
            $this->data['fnb'] = $meetingRoomType->fnb ?? '';
            //add above to features 
            $this->data['room_features'] = trim($this->data['room_features'] . ' ' . $this->data['fnb']);
            // Payment Information
            $this->data['payment_status'] = $meeting->payment_status ?? 'Pending';
            $this->data['transaction_id'] = $meeting->transaction_id ?? '';
            $this->data['payment_date'] = $meeting->payment_date ?? '';
            // Company Billing Information
            $this->data['company_name'] = $billing->billing_company ?? '';
            $this->data['billing_address'] = $billing->address ?? '';
            $this->data['billing_address_line2'] = $billing->billing_address_line2 ?? '';
            $this->data['city'] = $billing->city_id ?? '';
            $this->data['state'] = $billing->state->name ?? '';
            $this->data['country'] = $billing->country->name ?? '';
            $this->data['postal_code'] = $billing->postal_code ?? '';
            $this->data['phone'] = $billing->phone ?? '';
            $this->data['email'] = $billing->email ?? '';
            $this->data['gst_number'] = $app->gst_no ?? '';

            $this->data['salutation'] = $eventContact->salutation ?? '';
            $this->data['first_name'] = $eventContact->first_name ?? '';
            $this->data['last_name'] = $eventContact->last_name ?? '';
            $eventContact->contact_person = trim($eventContact->first_name . ' ' . $eventContact->last_name);
            $this->data['contact_person'] = $eventContact->contact_person ?? '';
            $this->data['contact_email'] = $eventContact->email ?? '';
            $this->data['contact_phone'] = $eventContact->contact_number ?? '';
            // Organizer Team Information
            $this->data['final_price'] = $meeting->final_price ?? 0;
            // Calculate subtotal, GST, and total amount
            $subtotal = $meeting->final_price ?? 0;
            $gst = 0;
            $total_amount = $subtotal;

            if (!empty($app->gst_no)) {
                // Assuming GST is 18%
                $gst = round($subtotal * 0.18, 2);
                $total_amount = $subtotal + $gst;
            }

            $this->data['subtotal'] = $subtotal;
            $this->data['gst'] = $gst;
            $this->data['total_amount'] = $total_amount;
            $this->data['organizer_team'] = env('APP_NAME', 'Organizer Team');

            //dd($this->data);
        } catch (\Throwable $e) {
            throw new \Exception('Error preparing meeting room invoice data: ' . $e->getMessage());
        }
        // Set the view for the email
        $this->view('emails.meeting-booking', [
            'data' => $this->data,
        ]);
    }
}
