<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\CoExhibitor;
use Illuminate\Contracts\Queue\ShouldQueue; 


class CoExhibitorRequest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $coExhibitor;

    public function __construct(array $coExhibitor)
    {
        $this->coExhibitor = $coExhibitor;
    }

    public function build()
    {
        return $this->subject('New Co-Exhibitors Application Submitted')
            ->view('emails.co_exhibitor_requests')
            ->with(['coExhibitor' => $this->coExhibitor]);
    }
}
