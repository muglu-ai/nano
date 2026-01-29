<?php

namespace App\Mail;

use App\Models\Ticket\DelegateNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DelegateNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notification;
    public $recipientName;

    /**
     * Create a new message instance.
     */
    public function __construct(DelegateNotification $notification, string $recipientName = '')
    {
        $this->notification = $notification;
        $this->recipientName = $recipientName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $eventName = config('constants.EVENT_NAME', 'Event');
        $eventYear = config('constants.EVENT_YEAR', date('Y'));

        return $this->subject("{$this->notification->title} - {$eventName} {$eventYear}")
            ->view('emails.delegate-notification');
    }
}
