<?php

function elastic_mail($subject, $message, $to, $bodyText = '')
{
	$url = 'https://api.elasticemail.com/v2/email/send';

	try {
		//$to = array('sagarpatil2112@gmail.com', 'test.interlinks@gmail.com');
		$to = implode(";", $to);
		$post = array(
			'from' => 'semiconindia@mmactiv.com',//'enquiry@startupmahakumbh.org', // 'vivek.patil@mmactiv.com',
			'fromName' => "SEMICON INDIA",
			'apikey' => 'B28BC46A67EAFBAF60DDFE3257D34E756B550950312375B641A3C111D1811928822355B83637DA21623EBE9535648F65',
			'subject' => $subject,
			'to' => $to,
			'bodyHtml' => $message,
			'bodyText' => $bodyText
		); //,//'<h1>Html Body</h1>',
		//'bodyText' => 'Text Body');

		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_SSL_VERIFYPEER => false
		));

		$result = curl_exec($ch);
		curl_close($ch);

		$data = json_decode($result, true);
		if (isset($data['success']) && $data['success']) {
			print_r($data);
			return true;
		} else {
			// echo  . '#<br/>';
		}
		echo $result . '#<br/>';
		return false;
	} catch (Exception $ex) {
		echo $ex->getMessage();
	}

	//exit;
}


// Function to send email with CC and BCC
function elastic_mail_cc($subject, $message, $to, $cc = array(), $bcc = array(), $bodyText = '')
{
	$url = 'https://api.elasticemail.com/v2/email/send';

	try {
		$toStr  = is_array($to)  ? implode(",", $to)  : $to;
		$ccStr  = is_array($cc)  && !empty($cc)  ? implode(",", $cc)  : '';
		$bccStr = is_array($bcc) && !empty($bcc) ? implode(",", $bcc) : '';

		$post = array(
			'from' => 'semiconindia@mmactiv.com',
			'fromName' => "SEMICON INDIA",
			'apikey' => 'B28BC46A67EAFBAF60DDFE3257D34E756B550950312375B641A3C111D1811928822355B83637DA21623EBE9535648F65',
			'subject' => $subject,
			'to' => $toStr,
			'bodyHtml' => $message,
			'bodyText' => $bodyText
		);

		if ($ccStr !== '') {
			$post['cc'] = $ccStr;
		}
		if ($bccStr !== '') {
			$post['bcc'] = $bccStr;
		}

		// print_r($post);
		// die;

		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_SSL_VERIFYPEER => false
		));

		$result = curl_exec($ch);
		curl_close($ch);

		$data = json_decode($result, true);
		if (isset($data['success']) && $data['success']) {
			print_r($data);
			return true;
		}
		echo $result . '#<br/>';
		return false;
	} catch (Exception $ex) {
		echo $ex->getMessage();
	}
}



// test mail to manish.sharma@interlinks.in

// $subject = "Test Email";
// $bodyText = "Test Email";
// $message = "<h1>Test Email</h1>";
// $to = array('manish.sharma@interlinks.in');

// elastic_mail($subject, $message, $to, $bodyText);

$subject = "Wi-Fi & Lead Retrieval Tool Requirements – SEMICON INDIA";


$message1 = '
<table style="max-width:600px;width:100%;border:1px solid #e0e0e0;border-radius:8px;font-family:Arial,sans-serif;background:#fafbfc;">
	<tr>
		<td style="padding:24px;">
			<div style="text-align:center;">
				<h2 style="color:#1a237e;margin-top:0;">Wi-Fi & Lead Retrieval Tool Requirements – SEMICON INDIA</h2>
				<p style="font-size:16px;color:#222;">Dear Exhibitor,</p>
				<p style="font-size:15px;color:#222;line-height:1.6;">
					We are in the process of collecting requirements from exhibitors, delegates, and partners regarding Wi-Fi connectivity and the Lead Retrieval Tool for the upcoming exhibition.
				</p>
				<p style="font-size:15px;color:#222;line-height:1.6;">
					Kindly take a few moments to complete the Google Form linked below to share your specific needs:
				</p>
				<p>
					<a href="https://docs.google.com/forms/d/e/1FAIpQLSdvkdErU0ZFR9xxHpi4-n2AcQOTCtBIQiBiDTTgsHzeO75-dg/viewform" style="background:#1a73e8;color:#fff;padding:10px 18px;text-decoration:none;border-radius:4px;display:inline-block;font-size:15px;">Fill Google Form</a>
				</p>
				<p style="font-size:14px;color:#222;line-height:1.6;">
					Or copy and paste this link into your browser:<br>
					<a href="https://docs.google.com/forms/d/e/1FAIpQLSdvkdErU0ZFR9xxHpi4-n2AcQOTCtBIQiBiDTTgsHzeO75-dg/viewform" style="color:#1a73e8;word-break:break-all;">https://docs.google.com/forms/d/e/1FAIpQLSdvkdErU0ZFR9xxHpi4-n2AcQOTCtBIQiBiDTTgsHzeO75-dg/viewform</a>
				</p>
				<p style="font-size:15px;color:#222;line-height:1.6;">
					Your prompt response will help us ensure that all necessary arrangements are made in a timely.
				</p>
				<p style="font-size:15px;color:#222;line-height:1.6;">
					Thank you for your cooperation.
				</p>
				<p style="font-size:15px;color:#222;">
					Best regards,<br>
					<strong>SEMICON INDIA</strong>
				</p>
			</div>
		</td>
	</tr>
</table>
';

$message2 = '
<table style="max-width:600px;width:100%;border:1px solid #e0e0e0;border-radius:8px;font-family:Arial,sans-serif;background:#fafbfc;margin:auto;">
	<tr>
		<td style="padding:24px;">
			<div style="text-align:center;">
				<h2 style="color:#1a237e;margin-top:0;text-align:center;">Wi-Fi & Lead Retrieval Tool Now Available – SEMICON India 2025</h2>
				<p style="font-size:16px;color:#222;text-align:center;">Dear Exhibitor,</p>
				<p style="font-size:15px;color:#222;line-height:1.6;text-align:center;">
					Wi-Fi services and the Lead Retrieval tool are now available for the event dates (September 2nd to 4th, 2025).
					You can apply for these services through your exhibitor portal under <strong>extra requirements</strong>.
					Please log in using your registered email ID to apply.
				</p>
				<p style="font-size:15px;color:#222;line-height:1.6;text-align:center;">
					Please ignore this message if you have already applied and paid for the service.
				</p>
				<p style="font-size:15px;color:#222;text-align:center;">
					Best regards,<br>
					<strong>SEMICON India</strong>
				</p>
			</div>
		</td>
	</tr>
</table>
';

$message = '
<table style="max-width:600px;width:100%;border:1px solid #e0e0e0;border-radius:8px;font-family:Arial,sans-serif;background:#fafbfc;">
	<tr>
		<td style="padding:24px;">
			<div style="text-align:center;">
				<h2 style="color:#1a237e;margin-top:0;">Payment Pending for Extra Requirements – SEMICON India 2025</h2>
				<p style="font-size:16px;color:#222;">Dear Exhibitor,</p>
				<p style="font-size:15px;color:#222;line-height:1.6;">
					Greetings of the Day!
				</p>
				<p style="font-size:15px;color:#222;line-height:1.6;">
					We have noticed that you have placed an order for extra requirements but have not yet made the payment. If already paid, kindly upload the payment slip on the portal.
				</p>
				<p style="font-size:15px;color:#222;line-height:1.6;">
					Please note that these orders will only be processed once payment is received, and surcharges will apply as per the following slabs:
				</p>
				<ul style="font-size:15px;color:#222;text-align:left;display:inline-block;margin:0 auto 16px auto;padding-left:20px;">
					<li>50% Surcharge: Payments made after <strong>15 August 2025</strong> (subject to availability)</li>
					<li>75% Surcharge: Onsite payments (subject to resource availability)</li>
				</ul>
				<p style="font-size:15px;color:#222;line-height:1.6;">
					If you have made the payment using offline mode, your payment status will be updated in a day or two.
				</p>
				<p style="font-size:15px;color:#222;line-height:1.6;">
					Please remove your order from your portal if you don\'t want extra requirements.
				</p>
				<p style="font-size:15px;color:#222;">
					Warm regards,<br>
					<strong>Team SEMICON India 2025</strong>
				</p>
			</div>
		</td>
	</tr>
</table>
';

$lead = '
<div style="width:100%;display:flex;justify-content:center;">
<table style="max-width:600px;width:100%;border:1px solid #e0e0e0;border-radius:8px;font-family:Arial,sans-serif;background:#fafbfc;margin:auto;">
	<tr>
		<td style="padding:24px;">
			<div>
				<h2 style="color:#1a237e;margin-top:0;text-align:left;">Lead Retrieval System Orientation - SEMICON India 2025</h2>
				<p style="font-size:16px;color:#222;text-align:left;">Dear Exhibitor,</p>
				<p style="font-size:15px;color:#222;line-height:1.6;text-align:left;">
					We are excited to introduce the Lead Retrieval System that will be used during the event to help you capture, manage, and follow up with your leads more efficiently.
				</p>
				<p style="font-size:15px;color:#222;line-height:1.6;text-align:left;">
					In this session, we will cover:
				</p>
				<ul style="font-size:15px;color:#222;text-align:left;display:inline-block;margin:0 auto 16px auto;padding-left:20px;">
					<li>How the lead retrieval system works</li>
					<li>How you can access and use it during the event</li>
				</ul>
				<p style="font-size:15px;color:#222;line-height:1.6;text-align:left;">
					We invite you to join us for a short orientation via Zoom:
				</p>
				<p style="font-size:15px;color:#222;line-height:1.6;text-align:left;">
					📅 <strong>Date &amp; Time:</strong> 30th August, 2025 - 2:00 PM IST<br>
					🔗 <strong>Zoom Link:</strong> <a href="https://us06web.zoom.us/j/81327676519?pwd=mbvJVwfeAg40q2fOiP4exsi7I83KbQ.1" style="color:#1a73e8;word-break:break-all;">https://us06web.zoom.us/j/81327676519?pwd=mbvJVwfeAg40q2fOiP4exsi7I83KbQ.1</a>
				</p>
				<p style="font-size:15px;color:#222;line-height:1.6;text-align:left;">
					We strongly encourage all exhibitor representatives to attend this session to ensure smooth use of the system during the event.
				</p>
				<p style="font-size:15px;color:#222;text-align:left;">
					Looking forward to your participation.
				</p>
				<p style="font-size:15px;color:#222;text-align:left;">
					Best regards,<br>
					<strong>SEMICON India Team</strong>
				</p>
			</div>
		</td>
	</tr>
</table>
</div>
';

//$to = array('harvinder.singh@mmactiv.com'); // Replace with actual recipient(s)

//elastic_mail($subject, $message, $to, 'Wi-Fi & Lead Retrieval Tool Requirements – SEMICON INDIA');



$message4 = '<table style="max-width:600px;width:100%;border:1px solid #e0e0e0;border-radius:8px;font-family:Arial,sans-serif;background:#fafbfc;">
	<tr>
		<td style="padding:24px;">
			<div style="text-align:left;">
				<h2 style="color:#1a237e;margin-top:0;text-align:left;">Exhibition Move-Out Instructions – 4th September 2025</h2>
				<p style="font-size:16px;color:#222;text-align:left;">Dear Exhibitors,</p>
				<p style="font-size:15px;color:#222;line-height:1.6;text-align:left;">
					Please take note of the following important move-out guidelines for the smooth and safe closure of the exhibition:
				</p>
				<ol style="font-size:15px;color:#222;text-align:left;display:inline-block;margin:0 auto 16px auto;padding-left:20px;">
					<li><strong>Exhibition Timings:</strong> The exhibition will remain open to visitors from 09:00 hrs to 17:00 hrs.</li>
					<li><strong>Exit Pass:</strong> Exit passes will be issued from 15:00 hrs onwards at the counter near the food court.</li>
					<li><strong>Booth Dismantling:</strong> Dismantling of booths is strictly permitted only after 17:00 hrs.</li>
					<li><strong>Truck Entry:</strong> Trucks will be allowed entry into the premises only after 21:00 hrs through Gate No. 11.</li>
					<li><strong>Truck Exit:</strong> Trucks must exit from Gate No. 9.</li>
					<li><strong>DD Return:</strong> The Demand Draft (DD) will be returned only after successful handover of the exhibition space by the fabricator.</li>
				</ol>
				<p style="font-size:15px;color:#222;line-height:1.6;text-align:left;">
					For Logistics and Exit Pass, please contact: <strong>Amit Kumar</strong><br>
					For Refund of Security Deposit (DD), please contact: <strong>Nitin Chauhan</strong>
				</p>
				<p style="font-size:15px;color:#222;line-height:1.6;text-align:left;">
					We request your full cooperation to ensure a safe and orderly move-out process.
				</p>
				<p style="font-size:15px;color:#222;text-align:left;">
					Thank you for your support.<br>
					<strong>SEMICON India</strong>
				</p>
			</div>
		</td>
	</tr>
</table>';


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
</html>
 
'; 

