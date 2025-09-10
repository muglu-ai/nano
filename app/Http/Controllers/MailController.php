<?php

namespace App\Http\Controllers;

use App\Mail\SubmissionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\GeneralMail;
use App\Mail\InvoiceMail;
use App\Mail\ReminderMail;
use App\Mail\ThankYouMail;
use App\Mail\SponsorInvoiceMail;
use App\Mail\PortalReminder;

class MailController extends Controller
{
    //function mailTest to test the mail return view as mailtest
    public function mailTest()
    {
        return view('emails.mailtest');
    }

    /**
     * Send email
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmail(Request $request)
    {

        Log::info('Email request', $request->all());

        $emailType = $request->input('email_type'); // Type of email to send
        $to = (array) $request->input('to'); // Recipient email
        $data = $request->all(); // Additional data for email
        $application_id = $request->input('application_id');
        //$to = 'manish.interlink@gmail.com';


        //dd($emailType, $to, $data, $application_id); // Debugging line

        switch ($emailType) {
            case 'invoice':
                $recipients = is_array($to) ? $to : [$to];
                $recipients[] = 'test.interlinks@gmail.com'; // Add default email
                foreach ($recipients as $recipient) {
                    // Mail::to($recipient)->send(new InvoiceMail($application_id));
                }
                break;
            case 'sponsor_invoice':
                $recipients[] = 'manish.interlink@gmail.com';

                $recipients[] = 'manish.sharma@interlinks.in'; // Add default email
                foreach ($recipients as $recipient) {

                    //Mail::to($recipient)->queue(new SponsorInvoiceMail($application_id));
                    $mailInstance = new SponsorInvoiceMail($application_id);
                    $data = $mailInstance->build()->viewData ?? [];
                    //  dd($data); // Debugging line
                    //can we display the sent email view here
                    return view('emails.sponsor_invoice', $data);
                }
                //dd($recipients);
                break;


            //            case 'sponsorship_invoice':

            //            case 'general':
            //                Mail::to($to)->send(new GeneralMail($data));
            //                break;
            //
            //            case 'submission':
            //                Mail::to($to)->send(new SubmissionMail($data));
            //                break;



            case 'reminder':
                $recipients = is_array($to) ? $to : [$to];
                $recipients[] = 'test.interlinks@gmail.com'; // Add default email
                foreach ($recipients as $recipient) {
                    Mail::to($recipient)->send(new ReminderMail($application_id));
                }
                break;

            //            case 'thank_you':
            //                Mail::to($to)->send(new ThankYouMail($data));
            //                break;

            default:
                return response()->json(['message' => 'Invalid email type'], 400);
        }

        return response()->json(['message' => 'Email has been queued and will be sent shortly.'], 200);
    }

    public function inactiveUsersReminder()
    {
        $recipients = [
            ['name' => 'Exposome Private Limited', 'email' => 'ankit.vadlamani@exposome.in'],
            ['name' => 'Honeywell International Inc', 'email' => 'yeonkyoung.ahn@honeywell.com'],
            ['name' => 'DHL Supply Chain India', 'email' => 'vinit.suri@dhl.com'],
            ['name' => 'SEMI SEA', 'email' => 'glenntan@semi.org'],
            ['name' => 'kaynes', 'email' => 's.budhadev@kaynessemicon.com'],
            ['name' => 'Veeco Asia Pte Ltd', 'email' => 'achay@veeco.com'],
            ['name' => 'ORISSA COMPUTER APPLICATION CENTRE', 'email' => 'ifitp.eit@odisha.gov.in'],
            ['name' => 'RAANA SEMICONDUCTORS PVT LTD', 'email' => 'rajasekar@raana.in'],
            ['name' => 'U.P. Electronics Corporation Limited (Department of IT & Electronics, Government of Uttar Pradesh)', 'email' => 'uplclko@gmail.com'],
            ['name' => 'NCFlexE IIT KANPUR', 'email' => 'sudheerk@iitk.ac.in'],
            ['name' => 'Assam Industrial Development Corporation Limited', 'email' => 'sanjeeb.aidc@gmail.com'],
            ['name' => 'Tata Electronics', 'email' => 'abhishek.kumar3@tataelectronics.co.in'],
            ['name' => 'HTL Co. India Pvt. Ltd.', 'email' => 'sanjayacharya1@yahoo.com'],
            ['name' => 'India Semiconductor Mission', 'email' => 'piyush.priy@ism.gov.in'],
            ['name' => 'India Electronics & Semiconductor Association', 'email' => 'vidya@semi.org'],
            ['name' => 'Tessolve Semiconductor Pvt. Ltd.', 'email' => 'shruti.siddeshwar@tessolve.com'],
            ['name' => 'Bharat Semitech Pvt LTd', 'email' => 'kumar@techovedas.com'],
            ['name' => 'Maharashtra Industrial Development Corporation', 'email' => 'gmpr@midcindia.org'],
            ['name' => 'Lindstrom Services (I) Pvt. Ltd.', 'email' => 'hemant.chanchlani@lindstromgroup.com'],
            ];

                foreach ($recipients as $r) {
                    $email = trim($r['email'] ?? '');
                    if ($email === '') continue;

                    // Mail::to($email)
                    //     ->cc('semiconindia@mmactiv.com')   // CC on every message
                    //     ->queue(new PortalReminder(
                    //         name: $r['name'] ?? 'Exhibitor',
                    //         loginEmail: $email
                    //     ));
                }

        return response()->json(['message' => 'Inactive user reminder emails have been queued and will be sent shortly.'], 200);
    }




    public function reminderVenue(){

        ini_set('max_execution_time', 0); //300 seconds = 5 minutes

        $message5 = '
<table style="max-width:600px;width:100%;border:1px solid #e0e0e0;border-radius:8px;font-family:Arial,sans-serif;background:#fafbfc;">
	<tr>
		<td style="padding:24px;">
			<div style="text-align:left;">
				<h2 style="color:#1a237e;margin-top:0;text-align:left;">Final Day – SEMICON India 2025 Exhibition & Conference</h2>
				<p style="font-size:16px;color:#222;text-align:left;">Dear All,</p>
				<p style="font-size:15px;color:#222;line-height:1.6;text-align:left;">
					Welcome to the final day of the spectacular SEMICON India 2025 exhibition and conference sessions.<br>
					Today’s (4th Sept 2025) conference sessions will start at 9 AM as per the published schedule, and the exhibition will also be open from 9 AM onwards.
				</p>
				<p style="font-size:15px;color:#222;line-height:1.6;text-align:left;">
					<strong>ENTRY:</strong> Please note that entry to the venue is available from <strong>Gate 6</strong> and <strong>Gate 8</strong> (any of these gates). Vehicles can be parked in the basement.
				</p>
				<p style="font-size:15px;color:#222;line-height:1.6;text-align:left;">
					Look forward to seeing you all.
				</p>
				<p style="font-size:15px;color:#222;text-align:left;">
					Regards,<br>
					<strong>SEMICON India Team</strong>
				</p>
			</div>
		</td>
	</tr>
</table>
';
        $subject = '4TH SEPT - ENTRY FROM GATE 6 OR 10. CONF AND EXHIBITION STARTS AT 9 AM | SEMICON India 2025';

        // Fetch recipients as before
        $recipients = \App\Models\Attendee::where('registration_type', 'In-Person')
            // ->whereNotNull('reminder')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->whereRaw("JSON_EXTRACT(reminder, '$.reminder4') IS NULL")
            // ->whereNotIn('source', ['ISM-2', 'ISM-3', 'Thomas Cook'])
            ->get(['id', 'email'])
            ->toArray();

            // dd($recipients);

        // Chunk recipients into batches of 450
        $chunks = array_chunk($recipients, 450);

        foreach ($chunks as $chunk) {
            $bcc = [];
            $ids = [];
            foreach ($chunk as $r) {
            $email = trim($r['email'] ?? '');
            if ($email === '') continue;
            $bcc[] = $email;
            $ids[] = $r['id'];
            }
            if (count($bcc) > 0) {
            // Mail::send([], [], function ($message) use ($bcc, $subject, $message5) {
            //     $message->to('semiconindia@mmactiv.com') // main recipient (can be any, as all real recipients are BCC)
            //             ->bcc($bcc)
            //             ->subject($subject)
            //             ->html($message5);
            // });
            // Update reminder column for these attendees
            \App\Models\Attendee::whereIn('id', $ids)->update([
                'reminder' => \DB::raw("JSON_SET(COALESCE(reminder, '{}'), '$.reminder4', NOW())")
            ]);
            }
        }

        
       

    }

            //send to all exhibitors as well from stall_manning table where email is not null and email != '' and JSON_EXTRACT(reminder, '$.reminder4') is null

            public function reminderExhibitors(){

                ini_set('max_execution_time', 0);

               $message5 = '
<table style="max-width:600px;width:100%;border:1px solid #e0e0e0;border-radius:8px;font-family:Arial,sans-serif;background:#fafbfc;">
	<tr>
		<td style="padding:24px;">
			<div style="text-align:left;">
				<h2 style="color:#1a237e;margin-top:0;text-align:left;">Final Day – SEMICON India 2025 Exhibition & Conference</h2>
				<p style="font-size:16px;color:#222;text-align:left;">Dear All,</p>
				<p style="font-size:15px;color:#222;line-height:1.6;text-align:left;">
					Welcome to the final day of the spectacular SEMICON India 2025 exhibition and conference sessions.<br>
					Today’s (4th Sept 2025) conference sessions will start at 9 AM as per the published schedule, and the exhibition will also be open from 9 AM onwards.
				</p>
				<p style="font-size:15px;color:#222;line-height:1.6;text-align:left;">
					<strong>ENTRY:</strong> Please note that entry to the venue is available from <strong>Gate 6</strong> and <strong>Gate 8</strong> (any of these gates). Vehicles can be parked in the basement.
				</p>
				<p style="font-size:15px;color:#222;line-height:1.6;text-align:left;">
					Look forward to seeing you all.
				</p>
				<p style="font-size:15px;color:#222;text-align:left;">
					Regards,<br>
					<strong>SEMICON India Team</strong>
				</p>
			</div>
		</td>
	</tr>
</table>
';
            

            $subject = '4TH SEPT - ENTRY FROM GATE 6 OR 8. CONF AND EXHIBITION STARTS AT 9 AM | SEMICON India 2025';

            // Fetch recipients as before
            $recipients = \App\Models\StallManning::where('first_name', '!=', '')
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->whereRaw("JSON_EXTRACT(reminder, '$.reminder4') IS NULL")
                ->get(['id', 'email'])
                ->toArray();

            // Chunk recipients into batches of 450
            $chunks = array_chunk($recipients, 450);
            foreach ($chunks as $chunk) {
                $bcc = [];
                $ids = [];
                foreach ($chunk as $r) {
                $email = trim($r['email'] ?? '');
                if ($email === '') continue;
                $bcc[] = $email;
                $ids[] = $r['id'];
                }
                if (count($bcc) > 0) {
                Mail::send([], [], function ($message) use ($bcc, $subject, $message5) {
                    $message->to('semiconindia@mmactiv.com') // main recipient (can be any, as all real recipients are BCC)
                            ->bcc($bcc)
                            ->subject($subject)
                            ->html($message5);
                });
                // Update reminder column for these attendees
                \App\Models\StallManning::whereIn('id', $ids)->update([
                    'reminder' => \DB::raw("JSON_SET(COALESCE(reminder, '{}'), '$.reminder4', NOW())")
                ]);
                }
            }
        
    }


    //send thank you mail to all companies who exhibited at semicon india 2025
    public function thankYouMail(){
        ini_set('max_execution_time', 0); //300 seconds = 5 minutes

        $message6 = '<html>
<head>
    <title>Thank You - SEMICON India 2025</title>
 
</head>
<body>
    <table style="width: 100%; max-width: 610px; margin: auto; border-collapse: collapse; background-color: #fff; border:#1f69a8 2px solid; padding:0px 20px;">
        <tr>
            <td style="padding: 20px;  color:#2B2929; text-align: justify; line-height: 1.8; font-size:14px; font-family: Verdana, Geneva, sans-serif;  ">
                
                <img src="https://portal.semiconindia.org/SEMI-thank-you.jpg" alt="SEMICON India 2025 Thank You" style="width: 100%;  height: auto; margin-bottom: 20px;">
                <p style="margin-bottom: 15px;">
					<strong>Dear Exhibitors and Participants</strong>,<br>
					On behalf of the organizing team, we extend our heartfelt gratitude to each one of you for making <strong>SEMICON India 2025</strong> an <strong>unprecedented success</strong>. With <strong>35,000 registrations, over 30,000 footfalls, 25,000+ live/online viewers, 350 exhibitors, and 48 international delegations</strong>, along with hundreds of MoUs, press releases, and thousands of B2B meetings, SEMICON India 2025 has truly set a <strong>new benchmark in India’s semiconductor journey</strong>. The impressive lineup of global leaders and speakers has further strengthened our confidence in driving this mission ahead. The event witnessed <strong>unprecedented participation across the entire value chain</strong>—from industry leaders, government officials, and academia to researchers and students—making it a landmark gathering for the ecosystem.</p>
                <p style="margin-bottom: 15px;">We were deeply <strong>honored</strong> by the presence of <strong>Hon’ble Prime Minister Shri&nbsp;Narendra Modi, Union Minister Shri Ashwini Vaishnaw, MoS Shri&nbsp;Jitin Prasada,</strong>  along with the <strong>Chief Ministers of Delhi and Odisha</strong>. The inaugural session was further enriched by the participation of <strong>nine global CXOs</strong>, who shared their insights on the progress and opportunities in the semiconductor industry. Their active engagement reflected the strong leadership commitment to advancing India’s semiconductor agenda. A special highlight of the event was the <strong>Prime Minister’s visit to the exhibition booths and his roundtable interaction with global CXOs</strong>—a moment that will play a pivotal role in shaping the future of the world’s technology landscape.</p>
                <p style="margin-bottom: 15px;">We sincerely apologize for any inconvenience caused during the initial days due to VVIP movement and the reduced exhibition time. We appreciate your understanding and patience and would improve next year. By the third day, our team had the pleasure of visiting most booths, and it was encouraging to hear how the <strong>quality of B2B leads and discussions remained strong throughout the event</strong> including unprecedented visitors.</p>
                <p style="margin-bottom: 15px;">Now is the time to pause, reflect, and recharge— as we regroup to continue shaping<strong> India as a trusted global semiconductor powerhouse</strong>.</p>
                <ul style="margin-bottom: 15px; line-height: 1.8;">
                    <li>📌 Please block your calendars: <strong>SEMICON India 2026</strong> will be held in <strong>New Delhi, 16–18 September 2026</strong>.</li>
                    <li>📸 Don’t forget to explore our <strong>AI-enabled photo gallery</strong>, where you can search and retrieve your pictures instantly using face <strong>recognition</strong>. (Use FACE SEARCH option)</li>
                </ul>
                <p style="margin-bottom: 15px;"><a href="https://events.fotoowl.ai/gallery/155046?vip-link=1&share_key=6853" style="color: #0056b3; text-decoration: none;" target="_blank">https://events.fotoowl.ai/gallery/155046?vip-link=1&share_key=6853</a></p>
                <p style="margin-bottom: 15px;">Thank you once again for your overwhelming support and contribution. Together, we are building a vibrant ecosystem and scripting India’s semiconductor story for the world.</p>
                <p style="margin-bottom:5px;">Wishing you continued success.</p>
                <table style="width: 100%; color:#2B2929; text-align: justify; line-height: 1.8; font-size:14px; font-family: Verdana, Geneva, sans-serif; ">
                    <tr >
                        <td align="center" valign="top" style=" padding: 10px;">
                            <p style="margin-bottom: 5px;"><strong>AJIT MANOCHA</strong><br>CEO and President,<br> SEMI</p>
                            <img src="https://portal.semiconindia.org/logo-semi.png" alt="SEMI Emailer Logo" style="width: 100px;  margin-right: 10px;">
                        </td>
                        <td align="center" valign="top" style=" padding: 10px;">
                          <p style="margin-bottom: 5px;"><strong>ASHOK CHANDAK</strong><br>CEO and President,<br> SEMI India and IESA</p>
                            <img src="https://portal.semiconindia.org/images/logos/SEMI_IESA_logo.png" alt="SEMI IESA Logo" style="width: 150px;  margin-right: 10px;">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

        $subject = "Thank You for Making SEMICON India 2025 a Grand Success!";

        // Select application contact email from applications which is approved 
        // take company_email from applications table where status is approved and company_email is not null and company_email != ''
        // take contact person email which is relation with applications table eventContact->email 
        // $recipients = \App\Models\Application::where('status', 'approved')
        //     ->whereNotNull('company_email')
        //     ->where('company_email', '!=', '')
        //     ->orWhereHas('eventContact', function($query) {
        //         $query->whereNotNull('email')
        //               ->where('email', '!=', '');
        //     })
        //     ->with(['eventContact' => function($query) {
        //         $query->select('id', 'application_id', 'email');
        //     }])
        //     ->get()
        //     ->flatMap(function($application) {
        //         $emails = [];
        //         if ($application->company_email) {
        //             $emails[] = $application->company_email;
        //         }

        //         //dd($application->eventContact);
        //         if ($application->eventContact && $application->eventContact->email) {
        //             $emails[] = $application->eventContact->email;
        //         }
        //         return $emails;
        //     })
        //     ->unique()
        //     ->toArray();
        // dd($recipients);

        
            //make array of following emails manish.sharma@interlinks.in, 

        $recipients[] = 'manish.sharma@interlinks.in';
        $recipients[] = 'vivek@interlinks.in';
        $recipients[] = 'vibha.bhatia@mmactiv.com';
        $recipients[] = 'vmall@semi.org';
        $recipients[] = 'achandak@semi.org';
        $recipients[] = 'gowthami@semi.org';


        // Chunk recipients into batches of 450
        $chunks = array_chunk($recipients, 450);
        foreach ($chunks as $chunk) {
            $bcc = [];
            foreach ($chunk as $email) {
            $email = trim($email ?? '');
            if ($email === '') continue;
            $bcc[] = $email;
            }

            // print_r($bcc);

            // dd($bcc);
            if (count($bcc) > 0) {
            // Mail::send([], [], function ($message) use ($bcc, $subject, $message6) {
            //    $message->to('semiconindia@semi.org')
            //             ->bcc($bcc)
            //             ->subject($subject)
            //             ->html($message6);
            // });
            
        }
    }
        return response()->json(['message' => 'Thank you emails have been queued and will be sent shortly.'], 200);
    }

}