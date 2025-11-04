<?php 
/**
 * Cronjob script to process pending registrations and send to CHKDIN API
 * 
 * This script fetches records from it_2025_reg_tbl where:
 * - apiStatus = 0
 * - pay_status IN ('Paid', 'Free', 'Complimentary')
 * 
 * For each record, it processes all delegates and sends them to the API.
 * After processing, it updates:
 * - apiStatus = 1
 * - response = JSON response from API
 * 
 * Run this via cron: php /path/to/public/apitool/chkdinAPI.php
 * Or set up cronjob (runs every 5 minutes):
 *   0,5,10,15,20,25,30,35,40,45,50,55 * * * * php /path/to/public/apitool/chkdinAPI.php >> /path/to/logs/chkdinapi.log 2>&1
 */

// Include the main functions file
require_once __DIR__ . '/v2.php';

// Ensure link2 is initialized for sendchkdinapi function
global $link2;
$link2 = mysqli_connect($host, $user, $pass, $db);

if (!$link2) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($link2, 'utf8mb4');

// function clean_html_entities($string) {
//     // Decode HTML entities and ensure UTF-8
//     $string = html_entity_decode($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
//     // Remove non-printable characters
//     $string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
//     return $string;
// }


/**
 * Cronjob function to process pending registrations and send to API
 */
function process_pending_registrations() {
	global $link2;
	
	$host = "localhost";
	$user = "btsblnl265_asd1d_bengaluruite";
	$pass = "Disl#vhfj#Af#DhW65";
	$db = "btsblnl265_asd1d_bengaluruite";
	$link = mysqli_connect($host, $user, $pass, $db);
	
	if (!$link) {
		echo "[" . date('Y-m-d H:i:s') . "] Connection failed: " . mysqli_connect_error() . "\n";
		return;
	}
	mysqli_set_charset($link, 'utf8mb4');
	
	// Fetch records with apiStatus = 0 and pay_status IN ('Paid', 'Free', 'Complimentary')
	$query = "SELECT * FROM it_2025_reg_tbl 
			  WHERE apiStatus = 0 
			  AND (pay_status = 'Paid' OR pay_status = 'Free' OR pay_status = 'Complimentary')
			--   AND cata = 'Complimentary Delegate'
			  ORDER BY srno ASC
			  LIMIT 20"; // Process 100 records at a time to avoid timeout
	
	$result = mysqli_query($link, $query);
	
	if (!$result) {
		echo "[" . date('Y-m-d H:i:s') . "] Error fetching records: " . mysqli_error($link) . "\n";
		mysqli_close($link);
		return;
	}
	
	$total_records = mysqli_num_rows($result);
	echo "[" . date('Y-m-d H:i:s') . "] Found {$total_records} records to process\n";
	
	if ($total_records == 0) {
		echo "[" . date('Y-m-d H:i:s') . "] No records to process\n";
		mysqli_close($link);
		return;
	}
	
	$processed = 0;
	$errors = 0;
	
	while ($res = mysqli_fetch_array($result)) {
		$srno = $res['srno'];
		$tin_no = $res['tin_no'];
		
		echo "[" . date('Y-m-d H:i:s') . "] Processing TIN: {$tin_no} (SRNO: {$srno})\n";
		
		$all_responses = array();
		$all_success = true;
		
		// Process each delegate
		$sub_delegates = isset($res['sub_delegates']) ? intval($res['sub_delegates']) : 0;

		//store the previous response in a variable
		$previous_response = $res['response'];
		
		if ($sub_delegates == 0) {
			// log the error in the json file for the record
			$error_json = json_encode(array(
				'tin_no' => $tin_no,
				'srno' => $srno,
				'error' => 'No delegates found',
				'timestamp' => date('Y-m-d H:i:s')
			));
			file_put_contents('sendingDataError.json', $error_json . "\n", FILE_APPEND);
			echo "[" . date('Y-m-d H:i:s') . "] Warning: No delegates found for TIN {$tin_no}, skipping...\n";
			// Still update status to avoid reprocessing
			$update_query = "UPDATE it_2025_reg_tbl 
							SET apiStatus = 0, 
								response = '[]'
							WHERE srno = {$srno}";
			mysqli_query($link, $update_query);
			$processed++;
			continue;
		}
		
		for ($i = 1; $i <= $sub_delegates; $i++) {
			$dele_title = isset($res['title' . $i]) ? $res['title' . $i] : '';
			$dele_fname = isset($res['fname' . $i]) ? $res['fname' . $i] : '';
			$dele_lname = isset($res['lname' . $i]) ? $res['lname' . $i] : '';
			
			// Skip if no name
			if (empty($dele_fname) && empty($dele_lname)) {
				// log the error in the json file for the record
				$error_json = json_encode(array(
					'tin_no' => $tin_no,
					'srno' => $srno,
					'error' => 'No name found',
					'timestamp' => date('Y-m-d H:i:s')
				));
				file_put_contents('sendingDataError.json', $error_json . "\n", FILE_APPEND);
				echo "[" . date('Y-m-d H:i:s') . "] Warning: Delegate {$i} has no name, skipping...\n";
				continue;
			}
			
			// Check for email field (email1, email2, etc. or fallback to main email)
			$dele_email = '';
			// Use email as stored in email1, email2, ... without generating or altering
			if (isset($res['email' . $i]) && !empty($res['email' . $i])) {
				$dele_email = trim($res['email' . $i]);
			} else {
				$dele_email = '';
			}

			if (empty($dele_email)) {
				// log the error in the json file for the record
				$error_json = json_encode(array(
					'tin_no' => $tin_no,
					'srno' => $srno,
					'error' => 'No email found',
					'timestamp' => date('Y-m-d H:i:s')
				));
				file_put_contents('sendingDataError.json', $error_json . "\n", FILE_APPEND);
				echo "[" . date('Y-m-d H:i:s') . "] Warning: Delegate {$i} has no email, skipping...\n";
				continue;
			}
			
			$job_title = isset($res['job_title' . $i]) ? $res['job_title' . $i] : '';
			$dele_cellno = isset($res['cellno' . $i]) ? str_replace('+', '', $res['cellno' . $i]) : '';
			$dele_cellno_arr = explode("-", $dele_cellno);
			
			$cate = isset($res['cata' . $i]) ? $res['cata' . $i] : '';

			// if cata is GIA VIP then pass category is VIP GIA PARTNER
			if ($cate == 'GIA VIP') {
				$cate = 'VIP GIA PARTNER';
			}
			// if cata is GIA then pass category is GIA PARTNER
			if ($cate == 'GIA Delegate') {
				$cate = 'GIA PARTNER';
			}
			
			$eventDays = isset($res['sessionDay']) ? $res['sessionDay'] : null;
			
			$country_code = '91'; // default
			$phone = '';
			
			if (isset($dele_cellno_arr[0]) && !empty($dele_cellno_arr[0])) {
				$country_code = trim($dele_cellno_arr[0]);
				if (strlen($country_code) >= 6) {
					$phone = $country_code;
					$country_code = '91';
				}
			}
			if (isset($dele_cellno_arr[1]) && !empty($dele_cellno_arr[1])) {
				$phone = trim($dele_cellno_arr[1]);
			}
			if (empty($phone)) {
				$phone = isset($res['mobile']) ? $res['mobile'] : (isset($res['cellno']) ? str_replace('+', '', $res['cellno']) : '');
			}
			
			// Prepare data for API
			$data = array();
			
			// Fix encoding issues: ensure proper UTF-8 handling for special characters
			// First, ensure the strings are properly encoded UTF-8
			if (!mb_check_encoding($dele_title, 'UTF-8')) {
				$dele_title = mb_convert_encoding($dele_title, 'UTF-8', mb_detect_encoding($dele_title));
			}
			if (!mb_check_encoding($dele_fname, 'UTF-8')) {
				$dele_fname = mb_convert_encoding($dele_fname, 'UTF-8', mb_detect_encoding($dele_fname));
			}
			if (!mb_check_encoding($dele_lname, 'UTF-8')) {
				$dele_lname = mb_convert_encoding($dele_lname, 'UTF-8', mb_detect_encoding($dele_lname));
			}
			
			// Use clean_html_entities2 which recursively decodes HTML entities
			// This handles cases like "Mrs. Vilma PagirÄ—" properly
			$full_name = trim(  $dele_fname . ' ' . $dele_lname);

			//display the title only if it is Dr. Or Prof.  else remove it
			if ($dele_title == 'Dr.' || $dele_title == 'Prof.') {
				$data['name'] = clean_html_entities2($dele_title . ' ' . $full_name);
			} else {
				$data['name'] = clean_html_entities2($full_name);
			}

			$data['email'] = trim($dele_email);
			$data['country_code'] = $country_code;
			$data['mobile'] = $phone;
			$data['company'] = clean_html_entities2(isset($res['org']) ? $res['org'] : '');
			$data['designation'] = clean_html_entities2($job_title);
			
			// Process sector
			$sector = clean_html_entities2(isset($res['org_reg_type']) ? $res['org_reg_type'] : '');
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
			
			$data['country'] = clean_html_entities2(isset($res['country']) ? $res['country'] : '');
			$data['city'] = clean_html_entities2(isset($res['city']) ? $res['city'] : '');
			
			// Get category information
			if (!empty($cate)) {
				$matchPassesCategory = matchPassesCategory($cate, $eventDays);
				if ($matchPassesCategory !== null) {
					$data['category_id'] = $matchPassesCategory['category_id'];
					$data['qsn_933'] = $matchPassesCategory['name'];
					$data['qsn_934'] = $matchPassesCategory['event_dates'];
				} else {
					echo "[" . date('Y-m-d H:i:s') . "] Warning: Invalid category '{$cate}' for delegate {$i} (TIN: {$tin_no}), skipping...\n";
					continue;
				}
			} else {
				echo "[" . date('Y-m-d H:i:s') . "] Warning: No category for delegate {$i} (TIN: {$tin_no}), skipping...\n";
				continue;
			}
			
			$data['qsn_935'] = $sector;
			$data['qsn_936'] = '';
			$data['qsn_366'] = $tin_no; // For API log

			// Debug: Uncomment to see the data being sent
			// echo json_encode($data);
			// exit;
			
			// Send to API
			try {
				$response_raw = sendchkdinapi($data);
				$response = json_decode($response_raw, true);
				
				// Store response
				$delegate_response = array(
					'delegate' => $i,
					'name' => $data['name'],
					'email' => $data['email'],
					'response' => $response,
					'raw_response' => $response_raw,
					'timestamp' => date('Y-m-d H:i:s')
				);
				
				$all_responses[] = $delegate_response;
				
				if ($response && isset($response['message']) && $response['message'] === 'Success') {
					echo "[" . date('Y-m-d H:i:s') . "] Delegate {$i} ({$data['name']}): Success - Guest ID: " . (isset($response['guest_id']) ? $response['guest_id'] : 'N/A') . "\n";
				} else {
					echo "[" . date('Y-m-d H:i:s') . "] Delegate {$i} ({$data['name']}): Failed - " . (isset($response['message']) ? $response['message'] : 'Unknown error') . "\n";
					$all_success = false;
				}
			} catch (Exception $e) {
				echo "[" . date('Y-m-d H:i:s') . "] Exception for delegate {$i} ({$data['name']}): " . $e->getMessage() . "\n";
				$all_responses[] = array(
					'delegate' => $i,
					'name' => $data['name'],
					'email' => $data['email'],
					'error' => $e->getMessage(),
					'timestamp' => date('Y-m-d H:i:s')
				);
				$all_success = false;
			}
		
		
		
		}

		
		
		// Update the record by concatenating the response
		$response_json = json_encode($all_responses);

		$response_json = $previous_response . $response_json;

		//$response_json_escaped = mysqli_real_escape_string($link, $response_json);
		$response_json_escaped = '';

		
		
		// Fix: Properly update the response column with new JSON (do not CONCAT to an empty string)
		$update_query = "UPDATE it_2025_reg_tbl SET apiStatus = 1, response = '{$response_json_escaped}' WHERE srno = {$srno}";
		
		if (mysqli_query($link, $update_query)) {
			echo "[" . date('Y-m-d H:i:s') . "] Record updated successfully (apiStatus = 1) for TIN: {$tin_no}\n";
			$processed++;
		} else {
			echo "[" . date('Y-m-d H:i:s') . "] Error updating record: " . mysqli_error($link) . "\n";
			$errors++;
		}
	}
	
	mysqli_close($link);
	
	echo "\n[" . date('Y-m-d H:i:s') . "] === Processing Complete ===\n";
	echo "[" . date('Y-m-d H:i:s') . "] Total records processed: {$processed}\n";
	echo "[" . date('Y-m-d H:i:s') . "] Errors: {$errors}\n";
}

// Run the cronjob
process_pending_registrations();
