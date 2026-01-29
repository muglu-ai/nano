<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExhibitorPortalReminder extends Mailable implements ShouldQueue
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
        ?string $loginUrl = null,
        ?string $forgotUrl = null,
        ?string $supportEmail = null
    ) {
        $this->name = $name;
        $this->loginEmail = $loginEmail;
        $this->loginUrl = $loginUrl ?? config('constants.APP_URL') . '/login';
        $this->forgotUrl = $forgotUrl ?? config('constants.APP_URL') . '/forgot-password';
        $this->supportEmail = $supportEmail ?? ORGANIZER_EMAIL;
    }

    public function build()
    {
        return $this->subject('Action Required: Log in to the ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' Exhibitor Portal')
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
