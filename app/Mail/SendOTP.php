<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOTP extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public $subject;

    public function __construct($otp, $subject)
    {
        $this->subject = 'Your OTP for Verification for ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR');

        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject($this->subject)
            ->view('mail.otp');
    }
}
