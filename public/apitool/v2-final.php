<?php 

$host = "95.216.2.164";
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




	//insert into nano_2025_badge_api_log table
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
		'Standard Delegate Pass' => array('category_id' => 3544, 'name' => 'STANDARD'),
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
		'1&2&3' => array('category_id' => 3543, 'name' => 'Premium Pass'), // All-days = original
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
		return array('category_id' => 3544, 'name' => 'STANDARD', 'event_dates' => $get_event_dates('1&2&3'));
	}

	// Heuristic: If generic "FMC Delegate Pass" is used, fallback to FMC GO unless specified otherwise
	if (strcasecmp($pass_category, 'FMC Delegate Pass') === 0) {
		return array('category_id' => 3546, 'name' => 'FMC GO', 'event_dates' => '20 Nov');
	}

	// Special handling for FMC passes with custom date formats
	if (strcasecmp($pass_category, 'FMC Premium Delegate Pass') === 0) {
		return array('category_id' => 3545, 'name' => 'FMC Premium', 'event_dates' => '20th Nov');
	}
	
	if (strcasecmp($pass_category, 'FMC GO Pass') === 0) {
		return array('category_id' => 3546, 'name' => 'FMC GO', 'event_dates' => '20 Nov');
	}

	// Unknown mapping (e.g., MPMF Delegate Pass not listed above) → return null to let caller handle
	if (isset($map[$pass_category])) {
		// These are always all-days for these passes
		return $map[$pass_category] + array('event_dates' => $get_event_dates('1&2&3'));
	}

	return null;
}

/**
 * Recursively decode HTML entities until no more changes occur
 * Handles double-encoded entities like &amp;amp; -> &amp; -> &
 */
function clean_html_entities($text) {
	if (empty($text)) {
		return $text;
	}
	
	$decoded = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
	
	// If decoding changed the string, decode again (handles double-encoding)
	while ($decoded !== $text) {
		$text = $decoded;
		$decoded = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
	}
	
	return $decoded;
}

function get_data_from_database2($tin_no) {
	$host = "95.216.2.164";
	$user = "btsblnl265_asd1d_bengaluruite";
	$pass = "Disl#vhfj#Af#DhW65";
	$db = "btsblnl265_asd1d_bengaluruite";
	$link = mysqli_connect($host, $user, $pass, $db);
	if (!$link) {
		die("Connection failed: " . mysqli_connect_error());
	}


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
		$data['name'] = clean_html_entities($dele_fname . ' ' . $dele_lname);
		$data['email'] = $dele_email;
		$data['country_code'] = $country_code;
		// $data['mobile'] = $phone;
		$data['mobile'] = 9801217815;
		$data['company'] = clean_html_entities($res['org']);
		$data['designation'] = clean_html_entities($job_title);
		$sector = clean_html_entities($res['org_reg_type']);
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
		

		$data['country'] = clean_html_entities($res['country']);
		$data['city'] = clean_html_entities($res['city']);

		$matchPassesCategory = matchPassesCategory($cate, $eventDays);
		$data['category_id'] = $matchPassesCategory['category_id'];
		$data['qsn_933'] = $matchPassesCategory['name'];
		$data['qsn_934'] = $matchPassesCategory['event_dates'];
		$data['qsn_935'] = $sector;
		$data['qsn_936'] = '';


		echo json_encode($data);

		//send to chkdin api
		$response = sendchkdinapi($data);
		print_r($response);
	//	 exit;


		
    }
	echo(json_encode($data));
	// print_r($data);
	//exit;

}

$link2 = mysqli_connect($host, $user, $pass, $db);


// //print_r(get_data_from_database('TIN-BTS2025-376488731'));

// echo "<br>";

// //print_r(get_data_from_database('TIN-BTS2025-369547875'));
// echo "<br>";

// //print_r(get_data_from_database('TIN-BTS2025-547232136'));

// echo "<br>";

// //print_r(get_data_from_database('TIN-BTS2025-1819037'));
// echo "<br>";

// //vip all days
// //print_r(get_data_from_database('TIN-BTS2025-2266371'));
// echo "<br>";

// // TIN-BTS2025-369669514 all days 2 
// //print_r(get_data_from_database('TIN-BTS2025-369669514'));
// echo "<br>";

// // TIN-BTS2025-379215861
// //print_r(get_data_from_database('TIN-BTS2025-379215861'));
// echo "<br>";

// // TIN-BTS2025-434358911 session day 2
// //print_r(get_data_from_database('TIN-BTS2025-434358911'));
// echo "<br>";

// //TIN-BTS2025-347626129 investor
// print_r(get_data_from_database('TIN-BTS2025-102659931'));

// Define all categories to test
$categories_to_test = [
	// VIP Delegate Pass variations
	
	
	// FMC Passes
	// ['pass_category' => 'FMC Premium Delegate Pass', 'days' => null],
	// ['pass_category' => 'FMC GO Pass', 'days' => null],
	// ['pass_category' => 'FMC Delegate Pass', 'days' => null],
	
	// Direct category mappings (categories not handled by matchPassesCategory)
	// ['direct' => true, 'category_id' => 3516, 'name' => 'VVIP', 'event_dates' => '18, 19, 20 Nov'],
	// ['direct' => true, 'category_id' => 3517, 'name' => 'VIP', 'event_dates' => '18, 19, 20 Nov'],
	// ['direct' => true, 'category_id' => 3518, 'name' => 'Ministers VIP', 'event_dates' => '18, 19, 20 Nov'],
	
	// ['direct' => true, 'category_id' => 3531, 'name' => 'EVENT PARTNER', 'event_dates' => '18, 19, 20 Nov'],
	// ['direct' => true, 'category_id' => 3532, 'name' => 'VIP GIA PARTNER', 'event_dates' => '18, 19, 20 Nov'],
	// ['direct' => true, 'category_id' => 3533, 'name' => 'GIA PARTNER', 'event_dates' => '18, 19, 20 Nov'],
	// ['direct' => true, 'category_id' => 3534, 'name' => 'ASSOCIATION PARTNER', 'event_dates' => '18, 19, 20 Nov'],
	// ['direct' => true, 'category_id' => 3535, 'name' => 'ASSOCIATION SUPPORT', 'event_dates' => '18, 19, 20 Nov'],
	// ['direct' => true, 'category_id' => 3547, 'name' => 'POSTER DELEGATE', 'event_dates' => '18, 19, 20 Nov'],
	['direct' => true, 'category_id' => 3548, 'name' => 'VIP Pass', 'event_dates' => '18, 19, 20 Nov'],
	['direct' => true, 'category_id' => 3549, 'name' => 'Premium Pass', 'event_dates' => '18, 19, 20 Nov'],
	['direct' => true, 'category_id' => 3550, 'name' => 'Standard Pass', 'event_dates' => '18, 19, 20 Nov'],
	['direct' => true, 'category_id' => 3551, 'name' => 'FMC Premium', 'event_dates' => '20th Nov'],
	['direct' => true, 'category_id' => 3552, 'name' => 'FMC GO', 'event_dates' => '20 Nov'],
	['direct' => true, 'category_id' => 3553, 'name' => 'VIP Pass', 'event_dates' => '18, 19, 20 Nov'],
	['direct' => true, 'category_id' => 3554, 'name' => 'Premium Pass', 'event_dates' => '18, 19, 20 Nov'],
	['direct' => true, 'category_id' => 3555, 'name' => 'Standard Pass', 'event_dates' => '18, 19, 20 Nov'],
	['direct' => true, 'category_id' => 3556, 'name' => 'FMC Premium', 'event_dates' => '20th Nov'],
	['direct' => true, 'category_id' => 3557, 'name' => 'FMC GO', 'event_dates' => '20 Nov'],
	['direct' => true, 'category_id' => 3558, 'name' => 'Exhibitor', 'event_dates' => '18, 19, 20 Nov'],
	// ['direct' => true, 'category_id' => 3559, 'name' => 'Media', 'event_dates' => '18, 19, 20 Nov'],
	// ['direct' => true, 'category_id' => 3560, 'name' => 'Invitee', 'event_dates' => '18, 19, 20 Nov'],
	// ['direct' => true, 'category_id' => 3561, 'name' => 'SESSION ATTENDEE', 'event_dates' => '18, 19, 20 Nov'],
	// ['direct' => true, 'category_id' => 3562, 'name' => 'AWARD NOMINEE', 'event_dates' => '18, 19, 20 Nov'],
	// ['direct' => true, 'category_id' => 3563, 'name' => 'QUIZ', 'event_dates' => '18, 19, 20 Nov'],
	// ['direct' => true, 'category_id' => 3564, 'name' => 'BUSINESS VISITOR', 'event_dates' => '18, 19, 20 Nov'],
	// ['direct' => true, 'category_id' => 3565, 'name' => 'VISITOR', 'event_dates' => '18, 19, 20 Nov'],
	// ['direct' => true, 'category_id' => 3566, 'name' => 'STUDENT', 'event_dates' => '18, 19, 20 Nov'],
];

// Available sectors (including empty string)
$sectors = ['Investor', 'Institutional Investor', 'Startup', ''];

// Base dummy data
$base_data = [
	'name' => 'Manish Sharma',
	'country_code' => '91',
	'mobile' => '9876543210',
	'company' => 'Test Company',
	'designation' => 'Test Designation',
	'country' => 'Test Country',
	'city' => 'Test City',
];

// Send data to API for each category
foreach ($categories_to_test as $index => $category_info) {
	// Get category details - handle both direct mappings and matchPassesCategory
	if (isset($category_info['direct']) && $category_info['direct'] === true) {
		// Direct category mapping
		$matchPassesCategory = [
			'category_id' => $category_info['category_id'],
			'name' => $category_info['name'],
			'event_dates' => $category_info['event_dates']
		];
		$category_label = $category_info['name'];
	} else {
		// Use matchPassesCategory function
		$matchPassesCategory = matchPassesCategory($category_info['pass_category'], $category_info['days'] ?? null);
		
		if ($matchPassesCategory === null) {
			echo "Skipping invalid category: " . $category_info['pass_category'] . "<br>";
			continue;
		}
		
		$category_label = $category_info['pass_category'];
		if (isset($category_info['days']) && $category_info['days']) {
			$category_label .= " (" . $category_info['days'] . ")";
		}
	}
	
	// Prepare data for this category
	$data = $base_data;
	
	// Make email unique for each category to avoid duplicate issues
	$email_base = 'manish.sharma';
	$email_domain = '@interlinks.in';
	$email = $email_base . '+' . $index . $email_domain;
	$data['email'] = $email;
	
	// Add category information
	$data['category_id'] = $matchPassesCategory['category_id'];
	$data['qsn_933'] = $matchPassesCategory['name'];
	$data['qsn_934'] = $matchPassesCategory['event_dates'];
	
	// Randomly select a sector
	$random_sector = $sectors[array_rand($sectors)];
	$data['qsn_935'] = $random_sector;
	$data['qsn_936'] = '';
	// $data['qsn_366'] = 'TEST-TIN-' . $index; // For API log
	
	// Display what we're sending
	echo "<h3>Category " . ($index + 1) . ": " . $category_label . "</h3>";
	echo "Category ID: " . $data['category_id'] . "<br>";
	echo "Category Name: " . $data['qsn_933'] . "<br>";
	echo "Event Dates: " . $data['qsn_934'] . "<br>";
	echo "Sector: " . ($random_sector ?: '(empty)') . "<br>";
	echo "Email: " . $data['email'] . "<br>";
	
	// Send to API
	$response = sendchkdinapi($data);
	echo "Response: ";
	print_r($response);
	echo "<br><br>";
}


// VIP GIA PARTNER - 3532
// GIA PARTNER - 3533
//send these two 
$data = [
	'category_id' => 3532,
	'name' => 'VIP GIA PARTNER',
	'event_dates' => '18, 19, 20 Nov',
	'email' => 'manish.sharma@interlinks.in',
	'country_code' => '91',
	'mobile' => '9876543210',
	'company' => 'Test Company',
	'designation' => 'Test Designation',
	'country' => 'Test Country',
	'city' => 'Test City',
	'qsn_933' => 'VIP GIA PARTNER',
	'qsn_934' => '18, 19, 20 Nov',
	'qsn_935' => 'Investor',
	'qsn_936' => '',
	'qsn_366' => '',
];
//$response = sendchkdinapi($data);
echo "Response: ";
print_r($response);
echo "<br><br>";

$data = [
	'category_id' => 3533,
	'name' => 'GIA PARTNER',
	'event_dates' => '18, 19, 20 Nov',
	'email' => 'manish.sharma@interlinks.in',
	'mobile' => '9876543210',
	'country_code' => '91',
	'company' => 'Test Company',
	'designation' => 'Test Designation',
	'country' => 'Test Country',
	'city' => 'Test City',
	'qsn_933' => 'GIA PARTNER',
	'qsn_934' => '18, 19, 20 Nov',
	'qsn_935' => 'Investor',
	'qsn_936' => '',
	'qsn_366' => '',
];
//$response = sendchkdinapi($data);
echo "Response: ";
print_r($response);
echo "<br><br>";


//send event partner - 3531
$data = [
	'category_id' => 3531,
	'name' => 'EVENT PARTNER',
	'event_dates' => '18, 19, 20 Nov',
	'email' => 'manish.sharma@interlinks.in',
	'mobile' => '9876543210',
	'country_code' => '91',
	'company' => 'Test Company',
	'designation' => 'Test Designation',
	'country' => 'Test Country',
	'city' => 'Test City',
	'qsn_933' => 'EVENT PARTNER',
	'qsn_934' => '18, 19, 20 Nov',
	'qsn_935' => '',
	'qsn_936' => '',
	'qsn_366' => '',
];
//$response = sendchkdinapi($data);
echo "Response: ";
print_r($response);
echo "<br><br>";

//send poster delegate - 3547
$data = [
	'category_id' => 3547,
	'name' => 'POSTER DELEGATE',
	'event_dates' => '18, 19, 20 Nov',
	'email' => 'manish.sharma@interlinks.in',
	'mobile' => '9876543210',
	'country_code' => '91',
	'company' => 'Test Company',
	'designation' => 'Test Designation',
	'country' => 'Test Country',
	'city' => 'Test City',
	'qsn_933' => 'POSTER DELEGATE',
	'qsn_934' => '18, 19, 20 Nov',
	'qsn_935' => '',
	'qsn_936' => '',
	'qsn_366' => '',
];
// $response = sendchkdinapi($data);
echo "Response: ";
print_r($response);
echo "<br><br>";

/**
 * Cronjob function to process pending registrations and send to API
 * Fetches records with apiStatus = 0 and pay_status IN ('Paid', 'Free', 'Complimentary')
 * Processes each delegate and sends to API
 * Updates response and sets apiStatus = 1
 */
// function process_pending_registrations2() {
// 	global $link2;
	
// 	$host = "95.216.2.164";
// 	$user = "btsblnl265_asd1d_bengaluruite";
// 	$pass = "Disl#vhfj#Af#DhW65";
// 	$db = "btsblnl265_asd1d_bengaluruite";
// 	$link = mysqli_connect($host, $user, $pass, $db);
	
// 	if (!$link) {
// 		die("Connection failed: " . mysqli_connect_error());
// 	}
	
// 	// Fetch records with apiStatus = 0 and pay_status IN ('Paid', 'Free', 'Complimentary')
// 	$query = "SELECT * FROM it_2025_reg_tbl 
// 			  WHERE apiStatus = 0 
// 			  AND (pay_status = 'Paid' OR pay_status = 'Free' OR pay_status = 'Complimentary')
// 			  ORDER BY srno ASC";
	
// 	$result = mysqli_query($link, $query);
	
// 	if (!$result) {
// 		echo "Error fetching records: " . mysqli_error($link) . "\n";
// 		mysqli_close($link);
// 		return;
// 	}
	
// 	$total_records = mysqli_num_rows($result);
// 	echo "Found {$total_records} records to process\n";
	
// 	$processed = 0;
// 	$errors = 0;
	
// 	while ($res = mysqli_fetch_array($result)) {
// 		$srno = $res['srno'];
// 		$tin_no = $res['tin_no'];
		
// 		echo "\n--- Processing TIN: {$tin_no} (SRNO: {$srno}) ---\n";
		
// 		$all_responses = array();
// 		$all_success = true;
		
// 		// Process each delegate
// 		for ($i = 1; $i <= $res['sub_delegates']; $i++) {
// 			$dele_title = isset($res['title' . $i]) ? $res['title' . $i] : '';
// 			$dele_fname = isset($res['fname' . $i]) ? $res['fname' . $i] : '';
// 			$dele_lname = isset($res['lname' . $i]) ? $res['lname' . $i] : '';
			
// 			// Check for email field (email1, email2, etc. or fallback to main email)
// 			$dele_email = '';
// 			if (isset($res['email' . $i]) && !empty($res['email' . $i])) {
// 				$dele_email = $res['email' . $i];
// 			} elseif (isset($res['email']) && !empty($res['email'])) {
// 				// Use main email with suffix for multiple delegates
// 				$base_email = $res['email'];
// 				if ($res['sub_delegates'] > 1) {
// 					// Extract base email and domain
// 					if (strpos($base_email, '@') !== false) {
// 						list($email_local, $email_domain) = explode('@', $base_email, 2);
// 						$dele_email = $email_local . '+' . $i . '@' . $email_domain;
// 					} else {
// 						$dele_email = $base_email;
// 					}
// 				} else {
// 					$dele_email = $base_email;
// 				}
// 			} else {
// 				// Generate a default email if none exists
// 				$dele_email = 'guest' . $srno . '_' . $i . '@example.com';
// 			}
			
// 			$job_title = isset($res['job_title' . $i]) ? $res['job_title' . $i] : '';
// 			$dele_cellno = isset($res['cellno' . $i]) ? str_replace('+', '', $res['cellno' . $i]) : '';
// 			$dele_cellno_arr = explode("-", $dele_cellno);
			
// 			$cate = isset($res['cata' . $i]) ? $res['cata' . $i] : '';
// 			$eventDays = isset($res['sessionDay']) ? $res['sessionDay'] : null;
			
// 			$country_code = '91'; // default
// 			$phone = '';
			
// 			if (isset($dele_cellno_arr[0])) {
// 				$country_code = $dele_cellno_arr[0];
// 				if (strlen($country_code) >= 6) {
// 					$phone = $country_code;
// 					$country_code = '91';
// 				}
// 			}
// 			if (isset($dele_cellno_arr[1])) {
// 				$phone = $dele_cellno_arr[1];
// 			}
// 			if (empty($phone)) {
// 				$phone = isset($res['mobile']) ? $res['mobile'] : '9801217815'; // fallback
// 			}
			
// 			// Prepare data for API
// 			$data = array();
// 			$data['name'] = clean_html_entities(trim($dele_fname . ' ' . $dele_lname));
// 			$data['email'] = trim($dele_email);
// 			$data['country_code'] = $country_code;
// 			$data['mobile'] = $phone;
// 			$data['company'] = clean_html_entities(isset($res['org']) ? $res['org'] : '');
// 			$data['designation'] = clean_html_entities($job_title);
			
// 			// Process sector
// 			$sector = clean_html_entities(isset($res['org_reg_type']) ? $res['org_reg_type'] : '');
// 			$sector = trim($sector);
// 			if ($sector == 'Investors') {
// 				$sector = 'Investor';
// 			} elseif($sector == 'Institutional Investor'){
// 				$sector = 'Institutional Investor';
// 			}
// 			elseif ($sector == 'Startup' || $sector == 'Start-Up' || $sector == 'Startups') {
// 				$sector = 'Startup';
// 			} else {
// 				$sector = '';
// 			}
			
// 			$data['country'] = clean_html_entities(isset($res['country']) ? $res['country'] : '');
// 			$data['city'] = clean_html_entities(isset($res['city']) ? $res['city'] : '');
			
// 			// Get category information
// 			if (!empty($cate)) {
// 				$matchPassesCategory = matchPassesCategory($cate, $eventDays);
// 				if ($matchPassesCategory !== null) {
// 					$data['category_id'] = $matchPassesCategory['category_id'];
// 					$data['qsn_933'] = $matchPassesCategory['name'];
// 					$data['qsn_934'] = $matchPassesCategory['event_dates'];
// 				} else {
// 					echo "Warning: Invalid category '{$cate}' for delegate {$i}, skipping...\n";
// 					continue;
// 				}
// 			} else {
// 				echo "Warning: No category for delegate {$i}, skipping...\n";
// 				continue;
// 			}
			
// 			$data['qsn_935'] = $sector;
// 			$data['qsn_936'] = '';
// 			$data['qsn_366'] = $tin_no; // For API log
			
// 			// Send to API
// 			$response_raw = sendchkdinapi($data);
// 			$response = json_decode($response_raw, true);
			
// 			// Store response
// 			$delegate_response = array(
// 				'delegate' => $i,
// 				'name' => $data['name'],
// 				'email' => $data['email'],
// 				'response' => $response,
// 				'raw_response' => $response_raw
// 			);
			
// 			$all_responses[] = $delegate_response;
			
// 			if ($response && isset($response['message']) && $response['message'] === 'Success') {
// 				echo "Delegate {$i} ({$data['name']}): Success\n";
// 			} else {
// 				echo "Delegate {$i} ({$data['name']}): Failed or Error\n";
// 				$all_success = false;
// 			}
// 		}
		
// 		// Update the record
// 		$response_json = json_encode($all_responses);
// 		$response_json_escaped = mysqli_real_escape_string($link, $response_json);
		
// 		$update_query = "UPDATE it_2025_reg_tbl 
// 						SET apiStatus = 1, 
// 							response = '{$response_json_escaped}'
// 						WHERE srno = {$srno}";
		
// 		if (mysqli_query($link, $update_query)) {
// 			echo "Record updated successfully (apiStatus = 1)\n";
// 			$processed++;
// 		} else {
// 			echo "Error updating record: " . mysqli_error($link) . "\n";
// 			$errors++;
// 		}
// 	}
	
// 	mysqli_close($link);
	
// 	echo "\n=== Processing Complete ===\n";
// 	echo "Total records processed: {$processed}\n";
// 	echo "Errors: {$errors}\n";
// }

// Uncomment the line below to run the cronjob
// process_pending_registrations();



