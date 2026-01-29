<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentRejectionNotice extends Mailable
{
    use Queueable, SerializesModels;

    public $htmlContent;
    public $subject = 'Extra Requirements Payment Update - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR');

    public function __construct($htmlContent, $subject)
    {
         $this->htmlContent = $htmlContent;
        $this->customSubject = $subject ?: 'Extra Requirements Payment Update - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR');
        
    }

    public function build()
    {
        return $this->subject($this->customSubject)
            ->html($this->htmlContent);
    }
}
