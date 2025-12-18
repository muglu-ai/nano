<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailService
{
    /**
     * Send the ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' information email.
     *
     * @param  string|array  $to          Single email or array of emails
     * @param  string        $subject     Email subject
     * @param  string|null   $attachment  Full path to file to attach (optional)
     * @param  array         $cc          CC recipients (optional)
     * @param  array         $bcc         BCC recipients (optional)
     * @return bool                      True on success, false on failure
     */
    public static function sendSemiconNotice($to, string $subject = 'Important Information – ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'), ?string $attachment = null, array $cc = [], array $bcc = []): bool
    {
        // Your provided body (HTML). You can edit freely.
        $emailBody = <<<'HTML'
<p>Dear Participant,</p>

<p>Thank you for your interest in <strong>' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . '</strong>, scheduled for <strong>2nd–4th September 2025</strong>. Entry passes (e-Badges) are now being issued.</p>

<p>Please note the following important information:</p>
<ul>
  <li>Entry for the <strong>Inaugural Session on 2nd September</strong> will be restricted.</li>
  <li>Only <strong>Inaugural Badge</strong> holders with QR codes may collect their physical badges and enter the venue between <strong>6:30 AM and 8:00 AM</strong> on 2nd September.</li>
  <li><strong>Non-Inaugural and Exhibitor Badge</strong> holders may enter after <strong>12:00 noon</strong> on 2nd September.</li>
  <li>Participants who have not yet received e-Badges will get them from <strong>3rd September</strong> onwards.</li>
  <li>The Inaugural Session will also be streamed live at:
    <a href="https://www.youtube.com/@IndiaSemiconductorMission/streams" target="_blank" rel="noopener">https://www.youtube.com/@IndiaSemiconductorMission/streams</a>
  </li>
  <li>Shuttle service details are attached.</li>
</ul>

<p>We regret any inconvenience caused. These arrangements are as per ISM guidelines, and we appreciate your cooperation.</p>

<p>We look forward to welcoming you to ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . '.</p>

<p>
  <strong>Click Here to View Shuttle Service Plan:</strong><br>
  <a href="https://portal.semiconindia.org/storage/pdf/Semicon-2025-Shuttle_Plan.pdf" target="_blank" rel="noopener">
    https://portal.semiconindia.org/storage/pdf/Semicon-2025-Shuttle_Plan.pdf
  </a>
</p>

<p>Best regards,<br>
' . config('constants.EVENT_NAME') . ' Team</p>
HTML;

        // Create a simple plaintext alternative for better deliverability
        $textAlt = strip_tags(
            preg_replace('/<\s*br\s*\/?>/i', "\n", $emailBody)
        );

        try {
            Mail::send([], [], function ($message) use ($to, $subject, $emailBody, $textAlt, $attachment, $cc, $bcc) {
                // To
                if (is_array($to)) {
                    foreach ($to as $addr) {
                        if ($addr) {
                            $message->to($addr);
                        }
                    }
                } elseif ($to) {
                    $message->to($to);
                }

                // Subject + bodies
                $message->subject($subject);
                $message->setBody($emailBody, 'text/html');
                $message->addPart($textAlt, 'text/plain');

                // Optional CC/BCC
                if (!empty($cc)) {
                    $message->cc($cc);
                }
                if (!empty($bcc)) {
                    $message->bcc($bcc);
                }

                // Optional attachment (e.g., shuttle plan PDF)
                if ($attachment && file_exists($attachment)) {
                    $message->attach($attachment);
                }
            });

            // If Mail::failures() is empty, it generally indicates success.
            if (method_exists(Mail::getFacadeRoot(), 'failures')) {
                if (!empty(Mail::failures())) {
                    Log::warning('EmailService: Mail failures', ['failures' => Mail::failures()]);
                    return false;
                }
            }

            return true;
        } catch (Throwable $e) {
            Log::error('EmailService: sendSemiconNotice failed', [
                'to'        => $to,
                'subject'   => $subject,
                #'attachment'=> $attachment,
                'error'     => $e->getMessage(),
            ]);
            return false;
        }
    }
}
