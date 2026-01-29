<?php

namespace App\Mail;

use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnquiryAdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $enquiry;

    /**
     * Create a new message instance.
     */
    public function __construct(Enquiry $enquiry)
    {
        $this->enquiry = $enquiry;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $eventName = $this->enquiry->event ? $this->enquiry->event->event_name : config('constants.EVENT_NAME', 'Event');
        $eventYear = $this->enquiry->event_year ?? config('constants.EVENT_YEAR', date('Y'));
        
        return new Envelope(
            subject: "New Enquiry Received - {$eventName} {$eventYear}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.enquiry.admin-notification',
            with: [
                'enquiry' => $this->enquiry,
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
