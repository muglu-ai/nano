<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\CoExhibitor;

class CoExhibitorApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $coExhibitor;
    public $password;
    public $exhibiting_under;

    public function __construct(CoExhibitor $coExhibitor, $password, $exhibiting_under)
    {
        $this->coExhibitor = $coExhibitor;
        $this->password = $password;
        $this->exhibiting_under = $exhibiting_under;
    }

    public function build()
    {
        return $this->subject('Co-Exhibitor Account Approved')
            ->view('emails.co_exhibitor_approval')
            ->with([
                'coExhibitor' => $this->coExhibitor,
                'password' => $this->password,
                'exhibiting_under' => $this->exhibiting_under,
            ]);
    }
}
