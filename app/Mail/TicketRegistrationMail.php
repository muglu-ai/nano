<?php

namespace App\Mail;

use App\Models\Ticket\TicketOrder;
use App\Models\Events;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $event;

    /**
     * Create a new message instance.
     */
    public function __construct(TicketOrder $order, Events $event)
    {
        $this->order = $order;
        $this->event = $event;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $eventName = $this->event->event_name ?? config('constants.EVENT_NAME', 'Event');
        $eventYear = $this->event->event_year ?? config('constants.EVENT_YEAR', date('Y'));
        
        return new Envelope(
            subject: "Payment Confirmation - {$eventName} {$eventYear} - Order #{$this->order->order_no}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.registration',
            with: [
                'order' => $this->order,
                'event' => $this->event,
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

