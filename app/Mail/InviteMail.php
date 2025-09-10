<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $companyName;
    public $delegateType;
    public $token;

    /**
     * Create a new message instance.
     */
    public function __construct($companyName, $delegateType, $token)
    {
        $this->companyName = $companyName;
        $this->delegateType = $delegateType;
        $this->token = $token;
    }
    /**
     * Get the message envelope.
     */   
    public function envelope(): Envelope
    {
        $subject = match($this->delegateType) {
            'delegate' => 'Registration Form to participate in SEMICON India 2025 (2nd-4th Sept 2025)',
            default => 'Invitation to Participate in SEMICON India 2025'
        };
        
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
            view: 'emails.invitee',
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
