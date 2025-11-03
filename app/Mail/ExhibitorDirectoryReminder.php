<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExhibitorDirectoryReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $loginEmail;
    public string $loginPassword;
    public string $loginUrl;
    public string $forgotUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $loginEmail,
        string $loginPassword,
        string $loginUrl = 'https://www.bengalurutechsummit.com/portal/public/login',
        string $forgotUrl = 'https://www.bengalurutechsummit.com/portal/public/forgot-password'
    ) {
        $this->loginEmail = $loginEmail;
        $this->loginPassword = $loginPassword;
        $this->loginUrl = $loginUrl;
        $this->forgotUrl = $forgotUrl;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Bengaluru Tech Summit — Action Required')
            ->view('emails.exhibitor.exhibitor-directory-reminder')
            ->with([
                'loginEmail' => $this->loginEmail,
                'loginPassword' => $this->loginPassword,
                'loginUrl' => $this->loginUrl,
                'forgotUrl' => $this->forgotUrl,
            ]);
    }
}

