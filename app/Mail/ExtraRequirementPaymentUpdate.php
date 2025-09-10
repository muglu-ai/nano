<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExtraRequirementPaymentUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $htmlContent;
    public $subject = 'Extra Requirements Payment Update - SEMICON India 2025';

    public function __construct($htmlContent, $subject)
    {
         $this->htmlContent = $htmlContent;
        $this->customSubject = $subject ?: 'Extra Requirements Payment Update - SEMICON India 2025';

    }

    public function build()
    {
        return $this->subject($this->customSubject)
            ->html($this->htmlContent);
    }
}
