<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\Invoice;
use App\Models\EventContact;
use App\Models\BillingDetail;
use App\Models\Sector;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StartupZonePaymentThankYouMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $invoice;
    public $contact;
    public $billingDetail;
    public $sectorName;
    public $exhibitorData;
    public $paymentDetails;

    /**
     * Create a new message instance.
     */
    public function __construct(Application $application, Invoice $invoice, EventContact $contact = null, $paymentDetails = [])
    {
        $this->application = $application;
        $this->invoice = $invoice;
        $this->contact = $contact;
        $this->billingDetail = BillingDetail::where('application_id', $application->id)->first();
        $this->paymentDetails = $paymentDetails;
        
        // Get sector name if sector_id exists
        $this->sectorName = null;
        if ($application->sector_id) {
            $sector = Sector::find($application->sector_id);
            $this->sectorName = $sector ? $sector->name : null;
        }
        
        // Prepare exhibitor data from application
        $this->exhibitorData = [
            'name' => $application->company_name,
            'email' => $application->company_email,
            'address' => $application->address,
            'city' => $application->city_id,
            'state' => $application->state ? $application->state->name : null,
            'country' => $application->country ? $application->country->name : null,
            'postal_code' => $application->postal_code,
            'telephone' => $application->landline,
            'website' => $application->website,
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank You for Your Payment - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' Startup Exhibition',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.startup-zone-payment-thank-you',
            with: [
                'application' => $this->application,
                'invoice' => $this->invoice,
                'contact' => $this->contact,
                'billingDetail' => $this->billingDetail,
                'sectorName' => $this->sectorName,
                'exhibitorData' => $this->exhibitorData,
                'paymentDetails' => $this->paymentDetails,
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

