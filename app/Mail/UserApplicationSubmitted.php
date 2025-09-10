<?php
namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserApplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function build()
    {
        //if this->application has sponsorship type, then use the sponsor view
        if ($this->application->has_sponsorship == 1) {
            return $this->subject('Thank you for filling out the sponsorship application form.')
                ->view('mail.user_application_submitted')
                ->with([
                    'application' => $this->application
                ]);
        } else {
            $this->subject('Thank you for filling out the onboarding application form.')
                ->view('mail.user_application_submitted')
                ->with([
                    'application' => $this->application
                ]);
        }
    }
}
