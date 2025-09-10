<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\EventContact;

class SubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;

        //query the data from the application table using application id
        $application = Application::where('application_id', $data['application_id'])
            ->select('id','application_id', 'submission_date')
            ->first();

        //get the contact details from the eventContact table using application id
        $eventContact = EventContact::where('application_id', $application->id)
            ->select('first_name', 'last_name')
            ->first();
        //dd($eventContact);

        //get the first name and last name from the eventContact table
        $this->data['firstName'] = $eventContact->first_name;
        $this->data['lastName'] = $eventContact->last_name;

        $this->data['applicationID'] = $application->application_id;
        $this->data['submissionDate'] = date('d-m-Y', strtotime($application->submission_date));


    }

    public function build()
    {
        return $this->subject('Thank for Submission for Application at SEMICON India 2025')
            ->view('emails.submission')
            ->with('data', $this->data);
    }
}
