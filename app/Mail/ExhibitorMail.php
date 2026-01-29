<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExhibitorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.visitor_confirmation')
            ->with('data', $this->data)
            ->subject('Exhibitor Badge Form Submission -'.config('constants.EVENT_NAME').' '.config('constants.EVENT_YEAR'));
    }
}
