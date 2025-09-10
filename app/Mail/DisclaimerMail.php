<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DisclaimerMail extends Mailable
{
    use Queueable, SerializesModels;
    public $subjectLine;

    /**
     * Create a new message instance.
     */
    public function __construct($subject)
    {
        $this->subject($subject);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Important Update for Exhibitors – SEMICON India 2025")
            ->view('emails.disclaimer'); // Using view template
    }
}
