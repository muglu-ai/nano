<?php
namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminApplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function build()
    {

        //dd($this->application->eventContact->salutation);
        if ($this->application->has_sponsorship == 1) {
        return $this->subject('New Sponsorship Application Submitted')
            ->view('mail.admin_application_submitted')
            ->with([
                'application' => $this->application
            ]);
        } else {
            return $this->subject('New Application Submitted')
                ->view('mail.admin_application_submitted')
                ->with([
                    'application' => $this->application
                ]);
        }

    }
}

