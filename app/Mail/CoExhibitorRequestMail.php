<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use App\Models\CoExhibitor;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CoExhibitorRequestMail extends Mailable
{
    use Queueable, SerializesModels;
    public $coExhibitor;


    /**
     * Create a new message instance.
     */
    public function __construct(CoExhibitor $coExhibitor)
    {
        $this->coExhibitor = $coExhibitor;
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Co-Exhibitors Application Submitteds',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.co_exhibitor_requests'
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
