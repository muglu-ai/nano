<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PortalReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $name;
    public string $loginEmail;
    public string $loginUrl;
    public string $forgotUrl;
    public string $supportEmail;

    public function __construct(
        string $name,
        string $loginEmail,
        string $loginUrl = 'https://portal.semiconindia.org/login',
        string $forgotUrl = 'https://portal.semiconindia.org/forgot-password',
        string $supportEmail = 'semiconindia@mmactiv.com'
    ) {
        $this->name = $name;
        $this->loginEmail = $loginEmail;
        $this->loginUrl = $loginUrl;
        $this->forgotUrl = $forgotUrl;
        $this->supportEmail = $supportEmail;
    }

    public function build()
    {
        return $this->subject('Action Required: Log in to the SEMICON India 2025 Exhibitor Portal')
            ->view('emails.exhibitor_portal_reminder')
            ->with([
                'name' => $this->name,
                'loginEmail' => $this->loginEmail,
                'loginUrl' => $this->loginUrl,
                'forgotUrl' => $this->forgotUrl,
                'supportEmail' => $this->supportEmail,
            ]);
    }
}
