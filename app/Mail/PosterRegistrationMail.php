<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\PosterRegistration;
use App\Models\PosterAuthor;
use App\Models\Invoice;
use App\Models\Country;
use App\Models\State;

class PosterRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;
    public $invoice;
    public $authors;
    public $paymentUrl;
    public $emailType;
    public $isThankYouEmail;

    /**
     * Create a new message instance.
     * 
     * @param PosterRegistration $registration
     * @param Invoice $invoice
     * @param string $emailType 'provisional_receipt' | 'payment_thank_you'
     */
    public function __construct(PosterRegistration $registration, Invoice $invoice, string $emailType = 'provisional_receipt')
    {
        $this->registration = $registration;
        $this->invoice = $invoice;
        $this->emailType = $emailType;
        $this->isThankYouEmail = ($emailType === 'payment_thank_you');
        
        // Get authors with country/state names
        $this->authors = PosterAuthor::where('poster_registration_id', $registration->id)
            ->orderBy('author_index')
            ->get()
            ->map(function ($author) {
                // Enrich with country and state names
                if ($author->country_id) {
                    $country = Country::find($author->country_id);
                    $author->country_name = $country ? $country->name : '';
                }
                if ($author->state_id) {
                    $state = State::find($author->state_id);
                    $author->state_name = $state ? $state->name : '';
                }
                if ($author->affiliation_country_id) {
                    $affCountry = Country::find($author->affiliation_country_id);
                    $author->affiliation_country_name = $affCountry ? $affCountry->name : '';
                }
                
                // Mark lead author
                $author->is_lead = $author->is_lead_author;
                
                return $author;
            });
        
        // Payment URL
        $this->paymentUrl = route('poster.register.showPayment', $registration->tin_no);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $isPaid = $this->invoice->payment_status === 'paid';
        
        if ($this->isThankYouEmail) {
            // Thank you email after payment
            $subject = config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' - Thank You for Your Payment - Poster Registration';
        } else {
            // Provisional receipt email before payment
            $subject = config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' - Poster Registration Provisional Receipt & Payment Link';
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
            view: 'emails.poster-registration',
            with: [
                'registration' => $this->registration,
                'invoice' => $this->invoice,
                'authors' => $this->authors,
                'paymentUrl' => $this->paymentUrl,
                'isThankYouEmail' => $this->isThankYouEmail,
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
