<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\BillingDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($application_id)
    {
        $application = Application::where('application_id', $application_id)->first();
        $id = $application->id;
        // Fetch billing details
        $billing = BillingDetail::where('application_id', $id)->first();
        $this->data = [
            'name' => $billing->contact_name,
            'event_name' => config('constants.EVENT_NAME'),
            'event_year' => config('constants.EVENT_YEAR'),
        ];
        Log::info('Reminder email data', $this->data);
    }

    public function build()
    {
        return $this->subject('Complete your application at ' . config('app.name'))
            ->view('emails.reminder')
            ->with('data', $this->data);
    }
}
