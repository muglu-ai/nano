<?php

namespace App\Mail;

use App\Models\Poster;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PosterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $poster;
    public $invoice;
    public $paymentDetails;
    public $isPaymentThankYou;
    
    // Section visibility flags
    public $showPaymentConfirmation;
    public $showPaymentBreakdown;
    public $showOrganizerFooter;

    /**
     * Create a new message instance.
     * 
     * @param Poster $poster
     * @param string $emailType 'payment_thank_you'
     * @param Invoice|null $invoice
     * @param array $paymentDetails
     */
    public function __construct(
        Poster $poster, 
        string $emailType = 'payment_thank_you',
        Invoice $invoice = null, 
        array $paymentDetails = []
    ) {
        $this->poster = $poster;
        $this->invoice = $invoice;
        $this->paymentDetails = $paymentDetails;
        
        // Set email type flags
        $this->isPaymentThankYou = ($emailType === 'payment_thank_you');
        
        // Set section visibility based on email type
        $this->showPaymentConfirmation = $this->isPaymentThankYou;
        $this->showPaymentBreakdown = $this->isPaymentThankYou;
        $this->showOrganizerFooter = $this->isPaymentThankYou;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = '';
        
        if ($this->isPaymentThankYou) {
            $subject = 'Thank You for Your Payment - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' Poster Registration';
        }
        
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.poster',
            with: [
                'poster' => $this->poster,
                'invoice' => $this->invoice,
                'paymentDetails' => $this->paymentDetails,
                'isPaymentThankYou' => $this->isPaymentThankYou,
                'showPaymentConfirmation' => $this->showPaymentConfirmation,
                'showPaymentBreakdown' => $this->showPaymentBreakdown,
                'showOrganizerFooter' => $this->showOrganizerFooter,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
