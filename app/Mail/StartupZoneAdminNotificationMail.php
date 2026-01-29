<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\EventContact;
use App\Models\BillingDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StartupZoneAdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $contact;
    public $billingDetail;
    public $exhibitorData;

    /**
     * Create a new message instance.
     */
    public function __construct(Application $application, EventContact $contact = null, BillingDetail $billingDetail = null)
    {
        $this->application = $application;
        $this->contact = $contact;
        $this->billingDetail = $billingDetail;
        
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
            subject: config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' - New Startup Zone Application Submitted',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.startup-zone-admin-notification',
            with: [
                'application' => $this->application,
                'contact' => $this->contact,
                'billingDetail' => $this->billingDetail,
                'exhibitorData' => $this->exhibitorData,
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

