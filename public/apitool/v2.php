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
	$map = array(
		// Confident mappings from the above list
		'FMC GO Pass' => array('category_id' => 3546, 'name' => 'FMC GO'),
		'FMC Premium Delegate Pass' => array('category_id' => 3545, 'name' => 'FMC Premium'),
		'Premium Delegate Pass' => array('category_id' => 3543, 'name' => 'PREMIUM'),
		'Standard Delegate Pass' => array('category_id' => 3544, 'name' => 'STANDARD'),
	);

	// VIP Delegate Pass can vary by day(s)
	if (strcasecmp($pass_category, 'VIP Delegate Pass') === 0) {
		// If $days is empty or null, treat as "All Days"
		if (empty($days)) {
			$days = "All Days";
		}
		if ($days !== null) {
			// Database stores days as: "All Days", "Day 1", "Day 2", "Day 3", e.g. "Day 1, Day 2", etc.
			// Normalize those into keys for $vipDaysMap

			// Helper: map day text to number
			$day_map = array(
				'day 1' => '1',
				'day 2' => '2',
				'day 3' => '3'
			);

			if (is_array($days)) {
				// if DB ever stores as array: rare, but supported
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

			sort($norm_days);
			$key = implode('&', $norm_days);

			$vipDaysMap = array(
				'1' => array('category_id' => 3537, 'name' => 'VIP PASS Day 1'),
				'2' => array('category_id' => 3538, 'name' => 'VIP PASS Day 2'),
				'3' => array('category_id' => 3539, 'name' => 'VIP PASS Day 3'),
				'1&2' => array('category_id' => 3540, 'name' => 'VIP PASS Day 1 & 2'),
				'1&3' => array('category_id' => 3541, 'name' => 'VIP PASS Day 1 & 3'),
				'2&3' => array('category_id' => 3542, 'name' => 'VIP PASS Day 2 & 3'),
				'1&2&3' => array('category_id' => 3536, 'name' => 'VIP PASS'),
			);

			if (isset($vipDaysMap[$key])) {
				return $vipDaysMap[$key];
			}
		}

		// Default VIP when days not specified
		return array('category_id' => 3536, 'name' => 'VIP PASS');
	}

	// Heuristic: If generic "FMC Delegate Pass" is used, fallback to FMC GO unless specified otherwise
	if (strcasecmp($pass_category, 'FMC Delegate Pass') === 0) {
		return array('category_id' => 3546, 'name' => 'FMC GO');
	}

	// Unknown mapping (e.g., MPMF Delegate Pass not listed above) → return null to let caller handle
	if (isset($map[$pass_category])) {
		return $map[$pass_category];
	}

	return null;
}


function get_data_from_database($tin_no) {
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

    // print_r($res);
    // exit;

    for ($i = 1; $i <= $res['sub_delegates']; $i++) {
		$dele_title = $res['title' . $i];
		$dele_fname = $res['fname' . $i];
		$dele_lname = $res['lname' . $i];
		$dele_email = 'manish.sharma@interlinks.in';
		
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
		$data['name'] = $dele_fname . ' ' . $dele_lname;
		$data['email'] = $dele_email;
		$data['country_code'] = $country_code;
		// $data['mobile'] = $phone;
		$data['mobile'] = 9801217815;
		$data['company'] = $res['org'];
		$data['designation'] = $job_title;
		$sector = $res['org_reg_type'];
		

		$data['country'] = $res['country'];
		$data['city'] = $res['city'];

		$matchPassesCategory = matchPassesCategory($cate, $eventDays);
		$data['category_id'] = $matchPassesCategory['category_id'];
		$data['qsn_933'] = $matchPassesCategory['name'];
		$data['qsn_934'] = '';
		$data['qsn_935'] = '';
		$data['qsn_936'] = '';



		//send to chkdin api
		// $response = sendchkdinapi($data);
		// print_r($response);
		// exit;


		
    }
	echo(json_encode($data));
	// print_r($data);
	exit;

}

$link2 = mysqli_connect($host, $user, $pass, $db);


print_r(get_data_from_database('TIN-BTS2025-409942543'));
    



