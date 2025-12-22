<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use App\Models\Invoice;
use App\Models\EventContact;
use App\Models\Sector;

class ExhibitorRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $invoice;
    public $contact;
    public $paymentUrl;
    public $sectorName;

    /**
     * Create a new message instance.
     */
    public function __construct(Application $application, Invoice $invoice, $contact = null)
    {
        $this->application = $application;
        $this->invoice = $invoice;
        $this->contact = $contact;
        $this->paymentUrl = route('startup-zone.payment', $application->application_id);
        
        // Get sector name if sector_id exists
        $this->sectorName = null;
        if ($application->sector_id) {
            $sector = Sector::find($application->sector_id);
            $this->sectorName = $sector ? $sector->name : null;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Set subject based on payment status
        $isPaid = $this->invoice->payment_status === 'paid';
        
        if ($isPaid) {
            $subject = config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' - Startup Exhibitor Registration Confirmation & Payment';
        } else {
            $subject = config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' - Startup Exhibitor Registration Initiated & Payment Link';
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
            view: 'emails.exhibitor-registration',
            with: [
                'application' => $this->application,
                'invoice' => $this->invoice,
                'contact' => $this->contact,
                'paymentUrl' => $this->paymentUrl,
                'sectorName' => $this->sectorName,
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
