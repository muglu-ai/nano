<?php 

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
/*
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
	*/


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