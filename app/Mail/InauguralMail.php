<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InauguralMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $emailBody;

    /**
     * Create a new message instance.
     */
    public function __construct($emailBody)
    {
        $this->emailBody = $emailBody;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Important Information on Entry Passes & Access for ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))
                    ->view('emails.passes-reminder')
                    ->with(['emailBody' => $this->emailBody]);
    }
}
