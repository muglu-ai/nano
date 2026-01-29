<?php 

$host = "localhost";
$user = "btsblnl265_asd1d_bengaluruite";
$pass = "Disl#vhfj#Af#DhW65";
$db = "btsblnl265_asd1d_bengaluruite";



//if connected successfully then print the message
// if ($link2) {
//     echo "Connected successfully";
// } else {
//     echo "Connection failed";
// }

global $link2;
function sendchkdinapi($data)
{
	global $link2;
	$method = 'POST';

	$data['api_key'] = 'scan626246ff10216s477754768osk';
	$data['event_id'] = "118150";
	if (empty($url)) {
		$url = 'https://studio.chkdin.com/api/v1/push_guest';
	}
	//echo json_encode($data);exit;
	$curl = curl_init();

	switch ($method) {
		case "POST":
			curl_setopt($curl, CURLOPT_POST, 1);
			if ($data) {
				$fields_string = '';
				foreach ($data as $key => $value) {
					$fields_string .= $key . '=' . urlencode($value) . '&';
				}
				rtrim($fields_string, '&');
				curl_setopt($curl, CURLOPT_POST, count($data));
				curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
				//curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
			}
			break;
		case "PUT":
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
			if ($data)
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			break;
		default:
			if ($data)
				$url = sprintf("%s?%s", $url, http_build_query($data));
	}
	//echo '##';
	//print_r($data);
	// OPTIONS:
	curl_setopt($curl, CURLOPT_URL, $url);
	//curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

	// EXECUTE:
	$result = curl_exec($curl);

	if (!$result) {
		echo "Connection Failure";
	}

	curl_close($curl);

	$response = json_decode($result, true);


	$store_success = ($response['message'] === "Success") ? "success" : "false";
	$email_exist = "";
	if ($response['guest_id'] === 0) {
		$email_exist = "email already exist";
	}

	$datajson = json_encode($data);

	$response_json = json_encode($response);




	//insert into it_2025_badge_api_log table
	$sq_qr = "INSERT INTO it_2025_badge_api_log (name, email, mobile, category_id, status, response, tin_no, data, email_exist) VALUES 
	 ('" . $data['name'] . "', '" . $data['email'] . "', '" . $data['mobile'] . "', '" . $data['category_id'] . "', 
	 '" . $store_success . "', '" . $response_json . "', '" . $data['qsn_366'] . "', '" . $datajson . "', '" . $email_exist . "')";
	mysqli_query($link2, $sq_qr);

	return $result;

}

/*
VVIP - 3516
VIP - 3517
Ministers VIP - 3518
VISION GROUP ITE - 3519
VISION GROUP BIOTECH - 3520
VISION GROUP STARTUPS - 3521
VISION GROUP SPACE - 3522
VISION GROUP NANOTECH - 3523
CONFERENCE COMMITTEE - 3524
SPEAKER - 3525
Organiser Green - 3526
Organiser Blue - 3527
GoK Sr. Officer - 3528
GoK Staff - 3529
PROTOCOL - 3530
EVENT PARTNER - 3531
VIP GIA PARTNER - 3532
GIA PARTNER - 3533
ASSOCIATION PARTNER - 3534
ASSOCIATION SUPPORT - 3535
VIP PASS - 3536
VIP PASS Day 1 - 3537
VIP PASS Day 2 - 3538
VIP PASS Day 3 - 3539
VIP PASS Day 1 & 2 - 3540
VIP PASS Day 1 & 3 - 3541
VIP PASS Day 2 & 3 - 3542
PREMIUM - 3543
STANDARD - 3544
FMC Premium - 3545
FMC GO - 3546
POSTER DELEGATE - 3547
Sponsor VIP Pass - 3548
Sponsor Premium - 3549
Sponsor Standard - 3550
Sponsor FMC Premium - 3551
Sponsor FMC GO - 3552
Exhibitor VIP Pass PAID - 3553
Exhibitor Premium - 3554
Exhibitor Standard - 3555
Exhibitor FMC Premium - 3556
Exhibitor FMC GO - 3557
Exhibitor - 3558
Media - 3559
Invitee - 3560
SESSION ATTENDEE - 3561
AWARD NOMINEE - 3562
QUIZ - 3563
BUSINESS VISITOR - 3564
VISITOR - 3565
STUDENT - 3566
*/ 


/**
 * 
 * 
 * FMC Delegate Pass

* FMC GO Pass

* FMC Premium Delegate Pass

* MPMF Delegate Pass

* Premium Delegate Pass

* Standard Delegate Pass

 * VIP Delegate Pass

 */
function matchPassesCategory($pass_category, $days = null ){
	// Baseline categories (all-days)
	$map = array(
		'FMC GO Pass' => array('category_id' => 3546, 'name' => 'FMC GO'),
		'FMC Premium Delegate Pass' => array('category_id' => 3545, 'name' => 'FMC Premium'),
		'Premium Delegate Pass' => array('category_id' => 3543, 'name' => 'Premium Pass'),
		'Standard Delegate Pass' => array('category_id' => 3544, 'name' => 'Standard Pass'),
		'SPEAKER' => array('category_id' => 3525, 'name' => 'SPEAKER'),
		'VIP GIA PARTNER' => array('category_id' => 3532, 'name' => 'VIP GIA PARTNER'),
		'GIA PARTNER' => array('category_id' => 3533, 'name' => 'GIA PARTNER'),
	);

	// Day mapping for easy conversion to keys
	$day_map = array(
		'day 1' => '1',
		'day 2' => '2',
		'day 3' => '3'
	);

	// For mapping day numbers to event dates
	$event_day_dates = array(
		'1' => '18 Nov',
		'2' => '19 Nov',
		'3' => '20 Nov'
	);

	// VIP Delegate Pass day wise mapping
	$vipDaysMap = array(
		'1' => array('category_id' => 3537, 'name' => 'VIP PASS Day 1'),
		'2' => array('category_id' => 3538, 'name' => 'VIP PASS Day 2'),
		'3' => array('category_id' => 3539, 'name' => 'VIP PASS Day 3'),
		'1&2' => array('category_id' => 3540, 'name' => 'VIP PASS Day 1 & 2'),
		'1&3' => array('category_id' => 3541, 'name' => 'VIP PASS Day 1 & 3'),
		'2&3' => array('category_id' => 3542, 'name' => 'VIP PASS Day 2 & 3'),
		'1&2&3' => array('category_id' => 3536, 'name' => 'VIP PASS'),
	);

	// Premium Delegate Pass day wise mapping
	$premiumDaysMap = array(
		'1' => array('category_id' => 3575, 'name' => 'PREMIUM Pass Day 1'),
		'2' => array('category_id' => 3576, 'name' => 'PREMIUM Pass Day 2'),
		'3' => array('category_id' => 3577, 'name' => 'PREMIUM Pass Day 3'),
		'1&2' => array('category_id' => 3578, 'name' => 'PREMIUM Pass Day 1 & 2'),
		'1&3' => array('category_id' => 3579, 'name' => 'PREMIUM Pass Day 1 & 3'),
		'2&3' => array('category_id' => 3580, 'name' => 'PREMIUM Pass Day 2 & 3'),
		'1&2&3' => array('category_id' => 3543, 'name' => 'PREMIUM Pass'), // All-days = original
	);

	// Standard Delegate Pass day wise mapping
	$standardDaysMap = array(
		'1' => array('category_id' => 3581, 'name' => 'STANDARD Pass Day 1'),
		'2' => array('category_id' => 3582, 'name' => 'STANDARD Pass Day 2'),
		'3' => array('category_id' => 3583, 'name' => 'STANDARD Pass Day 3'),
		'1&2' => array('category_id' => 3584, 'name' => 'STANDARD Pass Day 1 & 2'),
		'1&3' => array('category_id' => 3585, 'name' => 'STANDARD Pass Day 1 & 3'),
		'2&3' => array('category_id' => 3586, 'name' => 'STANDARD Pass Day 2 & 3'),
		'1&2&3' => array('category_id' => 3544, 'name' => 'STANDARD Pass'), // All-days = original
	);

	// Helper to normalize days for day-pass tickets
	$normalize_days_key = function($days) use ($day_map) {
		if (empty($days)) return '1&2&3'; // treat as "All Days"
		if (is_array($days)) {
			$norm_days = array();
			foreach ($days as $day) {
				$d = trim(strtolower($day));
				if (isset($day_map[$d])) {
					$norm_days[] = $day_map[$d];
				}
			}
		} else {
			$days_trim = trim($days);
			$days_lc = strtolower($days_trim);
			if ($days_lc === "all days") {
				$norm_days = array('1', '2', '3');
			} else {
				$pieces = preg_split('/,/', $days_lc);
				$norm_days = array();
				foreach ($pieces as $d) {
					$d = trim($d);
					if (isset($day_map[$d])) {
						$norm_days[] = $day_map[$d];
					}
				}
			}
		}
		sort($norm_days); // sorting ensures consistent key
		return implode('&', $norm_days);
	};

	// Helper to get the event dates in required format for the given day key (e.g. '1', '1&2', '1&2&3')
	$get_event_dates = function($key) use ($event_day_dates) {
		$segments = explode('&', $key);
		$datesArr = array();
		foreach ($segments as $seg) {
			$seg = trim($seg);
			if (isset($event_day_dates[$seg])) {
				$datesArr[] = $event_day_dates[$seg];
			}
		}
		if (count($datesArr) == 1) {
			return $datesArr[0];
		} elseif (count($datesArr) == 2) {
			// For 2 days, format as "18 & 19 Nov" instead of "18 Nov & 19 Nov"
			$days = array();
			$month = '';
			foreach ($datesArr as $date) {
				$parts = explode(' ', $date);
				if (count($parts) == 2) {
					$days[] = $parts[0]; // Day number
					$month = $parts[1]; // Month (should be same for all)
				}
			}
			return implode(' & ', $days) . ' ' . $month;
		} elseif (count($datesArr) == 3) {
			// For all 3 days, format as "18, 19, 20 Nov" instead of "18 Nov, 19 Nov & 20 Nov"
			$days = array();
			$month = '';
			foreach ($datesArr as $date) {
				$parts = explode(' ', $date);
				if (count($parts) == 2) {
					$days[] = $parts[0]; // Day number
					$month = $parts[1]; // Month (should be same for all)
				}
			}
			return implode(', ', $days) . ' ' . $month;
		} else {
			return '';
		}
	};

	// Check for VIP Delegate Pass day-wise
	if (strcasecmp($pass_category, 'VIP Delegate Pass') === 0) {
		$key = $normalize_days_key($days);
		if (isset($vipDaysMap[$key])) {
			$pass = $vipDaysMap[$key];
			$dates = $get_event_dates($key);
			$pass['event_dates'] = $dates;
			return $pass;
		}
		// fallback: All days
		return array('category_id' => 3536, 'name' => 'VIP PASS', 'event_dates' => $get_event_dates('1&2&3'));
	}
	// Premium Delegate Pass day-wise
	if (strcasecmp($pass_category, 'Premium Delegate Pass') === 0) {
		$key = $normalize_days_key($days);
		if (isset($premiumDaysMap[$key])) {
			$pass = $premiumDaysMap[$key];
			$dates = $get_event_dates($key);
			$pass['event_dates'] = $dates;
			return $pass;
		}
		return array('category_id' => 3543, 'name' => 'Premium Pass', 'event_dates' => $get_event_dates('1&2&3'));
	}
	// Standard Delegate Pass day-wise
	if (strcasecmp($pass_category, 'Standard Delegate Pass') === 0) {
		$key = $normalize_days_key($days);
		if (isset($standardDaysMap[$key])) {
			$pass = $standardDaysMap[$key];
			$dates = $get_event_dates($key);
			$pass['event_dates'] = $dates;
			return $pass;
		}
		return array('category_id' => 3544, 'name' => 'Standard Pass', 'event_dates' => $get_event_dates('1&2&3'));
	}

	// SPEAKER special handling (always all three days: 18, 19, 20)
	if (strcasecmp($pass_category, 'SPEAKER') === 0) {
		// hardcoding days as all three, format "18, 19, 20 Nov"
		return array(
			'category_id' => 3525,
			'name' => 'SPEAKER',
			'event_dates' => '18, 19, 20 Nov'
		);
	}

	// Heuristic: If generic "FMC Delegate Pass" is used, fallback to FMC GO unless specified otherwise
	if (strcasecmp($pass_category, 'FMC Delegate Pass') === 0) {
		return array('category_id' => 3546, 'name' => 'FMC GO', 'event_dates' => '20 Nov');
	}

	// Special handling for FMC passes with custom date formats
	if (strcasecmp($pass_category, 'FMC Premium Delegate Pass') === 0) {
		return array('category_id' => 3545, 'name' => 'FMC Premium ', 'event_dates' => '20th Nov');
	}
	
	if (strcasecmp($pass_category, 'FMC GO Pass') === 0) {
		return array('category_id' => 3546, 'name' => 'FMC GO', 'event_dates' => '20 Nov');
	}

	// Handle VIP GIA PARTNER and GIA PARTNER
	if (strcasecmp($pass_category, 'VIP GIA PARTNER') === 0) {
		return array(
			'category_id' => 3532,
			'name' => 'VIP GIA PARTNER',
			'event_dates' => $get_event_dates('1&2&3')
		);
	}
	if (strcasecmp($pass_category, 'GIA PARTNER') === 0) {
		return array(
			'category_id' => 3533,
			'name' => 'GIA PARTNER',
			'event_dates' => $get_event_dates('1&2&3')
		);
	}


	// Unknown mapping (e.g., MPMF Delegate Pass not listed above) → return null to let caller handle
	if (isset($map[$pass_category])) {
		// These are always all-days for these passes
		return $map[$pass_category] + array('event_dates' => $get_event_dates('1&2&3'));
	}

	return null;
}

function clean_html_entities($string) {
    // Decode HTML entities and ensure UTF-8
    $string = html_entity_decode($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    // Remove non-printable characters
    $string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
    return $string;
}
/**
 * Recursively decode HTML entities until no more changes occur
 * Handles double-encoded entities like &amp;amp; -> &amp; -> &
 * Also fixes encoding issues for special characters like "Mrs. Vilma PagirÄ—"
 */
function clean_html_entities2($text) {
	if (empty($text)) {
		return $text;
	}
	
	// Ensure the text is treated as a string (not null)
	$text = (string)$text;
	
	// First, ensure proper UTF-8 encoding - detect and convert if needed
	// This is critical for preserving characters like "Ä—" (Lithuanian e with dot)
	if (!mb_check_encoding($text, 'UTF-8')) {
		// Try to detect the encoding
		$detected = mb_detect_encoding($text, array('UTF-8', 'ISO-8859-1', 'Windows-1252', 'ISO-8859-15'), true);
		if ($detected && $detected !== 'UTF-8') {
			$text = mb_convert_encoding($text, 'UTF-8', $detected);
		} else {
			// Last resort: assume it's UTF-8 and clean it
			$text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
		}
	}
	
	// Decode HTML entities recursively
	$decoded = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
	
	// If decoding changed the string, decode again (handles double-encoding)
	while ($decoded !== $text) {
		$text = $decoded;
		$decoded = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
	}
	
	// Remove any remaining non-printable control characters (but preserve valid UTF-8)
	$decoded = preg_replace('/[\x00-\x08\x0B-\x0C\x0E-\x1F\x7F]/u', '', $decoded);
	
	return $decoded;
}

/*
function get_data_from_database2($tin_no) {
	$host = "95.216.2.164";
	$user = "btsblnl265_asd1d_bengaluruite";
	$pass = "Disl#vhfj#Af#DhW65";
	$db = "btsblnl265_asd1d_bengaluruite";
	$link = mysqli_connect($host, $user, $pass, $db);
	if (!$link) {
		die("Connection failed: " . mysqli_connect_error());
	}
	mysqli_set_charset($link, 'utf8mb4');


    $result = mysqli_query($link, "SELECT * FROM it_2025_reg_tbl WHERE tin_no='$tin_no'");
    if (!$result || mysqli_num_rows($result) === 0) {
        return "No Data Found";
    }
    $data = mysqli_fetch_array($result);

    $res = $data;

	// echo json_encode($data);

	// echo $res['cata1'];
	// echo "<br>";

    // print_r($res);
    // exit;

    for ($i = 1; $i <= $res['sub_delegates']; $i++) {
		$dele_title = $res['title' . $i];
		$dele_fname = $res['fname' . $i];
		$dele_lname = $res['lname' . $i];
		$dele_email = 'manish.sharma+1@interlinks.in';
		
		$job_title = $res['job_title' . $i];
		$dele_cellno = str_replace('+', '', $res['cellno' . $i]);
		$dele_cellno_arr = explode("-", $dele_cellno);

		$cate = $res['cata' . $i];
		$eventDays = $res['sessionDay'];


		if (isset($dele_cellno_arr[0])) {
			$country_code = $dele_cellno_arr[0];
			if (strlen($country_code) >= 6) {
				$phone = $country_code;
				$country_code = '91';
			}
		}
		if (isset($dele_cellno_arr[1])) {
			$phone = $dele_cellno_arr[1];
		}
		//Call save Operator API
		$data = array();
		$data['api_key'] = 'scan626246ff10216s477754768osk';
		$data['event_id'] = 117859;
		$data['name'] = clean_html_entities2(trim($dele_title . ' ' . $dele_fname . ' ' . $dele_lname));
		$data['email'] = $dele_email;
		$data['country_code'] = $country_code;
		// $data['mobile'] = $phone;
		$data['mobile'] = 9801217815;
		$data['company'] = clean_html_entities2($res['org']);
		$data['designation'] = clean_html_entities2($job_title);
		$sector = clean_html_entities2($res['org_reg_type']);
		//if sector is Investor then only show Investor in the dropdown
		// Map more sector names to standardized sector output
		$sector = trim($sector);
		if ($sector == 'Investors') {
			$sector = 'Investor';
		} elseif($sector == 'Institutional Investor'){
			$sector = 'Institutional Investor';
		}
		elseif ($sector == 'Startup' || $sector == 'Start-Up' || $sector == 'Startups') {
			$sector = 'Startup';
		} else {
			$sector = '';
		}
		

		$data['country'] = clean_html_entities2($res['country']);
		$data['city'] = clean_html_entities2($res['city']);

		$matchPassesCategory = matchPassesCategory($cate, $eventDays);
		$data['category_id'] = $matchPassesCategory['category_id'];
		$data['qsn_933'] = $matchPassesCategory['name'];
		$data['qsn_934'] = $matchPassesCategory['event_dates'];
		$data['qsn_935'] = $sector;
		$data['qsn_936'] = '';


		echo json_encode($data);

		//send to chkdin api
		//$response = sendchkdinapi($data);
		//print_r($response);
	//	 exit;


		
    }
	echo(json_encode($data));
	// print_r($data);
	//exit;

}

*/
$link2 = mysqli_connect($host, $user, $pass, $db);
if ($link2) {
	mysqli_set_charset($link2, 'utf8mb4');
}






