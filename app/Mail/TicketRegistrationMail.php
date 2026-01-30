<?php

namespace App\Mail;

use App\Models\Ticket\TicketOrder;
use App\Models\Events;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class TicketRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $event;
    public $isPaymentSuccessful;

    /**
     * Create a new message instance.
     */
    public function __construct(TicketOrder $order, Events $event, $isPaymentSuccessful = false)
    {
        $this->order = $order;
        $this->event = $event;
        $this->isPaymentSuccessful = $isPaymentSuccessful;
    }

    /**
     * Get the message envelope.
     * Note: Individual emails are sent to each delegate separately from the controller.
     * No CC or reply-to is used to ensure clean email delivery.
     */
    public function envelope(): Envelope
    {
        $eventName = $this->event->event_name ?? config('constants.EVENT_NAME', 'Event');
        $eventYear = $this->event->event_year ?? config('constants.EVENT_YEAR', date('Y'));
        
        // Check if payment is successful (either from parameter or order status)
        $isPaid = $this->isPaymentSuccessful || ($this->order->status === 'paid');
        
        $subject = $isPaid 
            ? "Thank You for Registration at {$eventName} {$eventYear}"
            : "Thank you for Submitting Information on {$eventName} {$eventYear}. Please complete the payment using following pay now link / button";
        
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

