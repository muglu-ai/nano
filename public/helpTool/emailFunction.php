<?php

function elastic_mail($subject, $message, $to, $bodyText = '')
{
	$url = 'https://api.elasticemail.com/v2/email/send';

	try {
		$to = implode(";", $to);
		$post = array(
			'from' => 'secretariat@bengalurutechsummit.com',//'enquiry@startupmahakumbh.org', // 'vivek.patil@mmactiv.com',
			'fromName' => "Bengaluru Tech Summit",
			'apikey' => 'B28BC46A67EAFBAF60DDFE3257D34E756B550950312375B641A3C111D1811928822355B83637DA21623EBE9535648F65',
			'subject' => $subject,
			'to' => $to,
			'bodyHtml' => $message,
			'bodyText' => $message
		); 
		//,//'<h1>Html Body</h1>',
		//'bodyText' => 'Text Body');
		// echo $message;
		// echo "br";
		// echo $bodyText;



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
			//print_r($data);
			return true;
		} else {
			// echo  . '#<br/>';
		}
		//echo $result . '#<br/>';
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
		$toStr  = is_array($to)  ? implode(";", $to)  : $to;
		$ccStr  = is_array($cc)  && !empty($cc)  ? implode(";", $cc)  : '';
		$bccStr = is_array($bcc) && !empty($bcc) ? implode(";", $bcc) : '';

		$post = array(
			'from' => 'secretariat@bengalurutechsummit.com',
			'fromName' => "Bengaluru Tech Summit",
			'apikey' => 'B28BC46A67EAFBAF60DDFE3257D34E756B550950312375B641A3C111D1811928822355B83637DA21623EBE9535648F65',
			'subject' => $subject,
			'to' => $toStr,
			'bodyHtml' => $message,
			'bodyText' => $bodyText !== '' ? $bodyText : strip_tags($message)
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
			//print_r($data);
			return true;
		}
		//echo $result . '#<br/>';
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
