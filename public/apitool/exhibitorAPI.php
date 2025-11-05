<?php 
/**
 * Cronjob script to process pending exhibitor delegates and send to CHKDIN API
 * 
 * This script fetches records from complimentary_delegates where:
 * - first_name IS NOT NULL AND TRIM(first_name) != ''
 * - api_sent = 0 OR api_sent IS NULL
 * 
 * For each record, it processes the delegate and sends them to the API.
 * After processing, it updates:
 * - api_response = JSON response from API
 * - api_data = JSON data sent to API
 * - api_sent = 1
 * 
 * Run this via cron: php /path/to/public/apitool/exhibitorAPI.php
 */

require_once 'exhibitorSetup.php';

// Ensure link2 is initialized for sendchkdinapi function
global $link2;
if (!$link2) {
	$link2 = mysqli_connect($host, $user, $pass, $db);
	if (!$link2) {
		die("Connection failed: " . mysqli_connect_error());
	}
	mysqli_set_charset($link2, 'utf8mb4');
}

$db2 = 'btsblnl265_asd1d_portal';

/**
 * Map application_type to category_id_map for exhibitor tickets
 */
$category_id_map = array(
	'Sponsor VIP Pass'         => array('category_id' => 3548, 'name' => 'VIP PASS'),
	'Sponsor Premium'          => array('category_id' => 3549, 'name' => 'PREMIUM PASS'),
	'Sponsor Standard'         => array('category_id' => 3550, 'name' => 'STANDARD PASS'),
	'Sponsor FMC Premium'      => array('category_id' => 3551, 'name' => 'FMC Premium'),
	'Sponsor FMC GO'            => array('category_id' => 3552, 'name' => 'FMC GO'),
	'VIP Pass'                 => array('category_id' => 3553, 'name' => 'VIP PASS'),
	'Premium Pass'             => array('category_id' => 3554, 'name' => 'PREMIUM PASS'),
	'Standard Pass'            => array('category_id' => 3555, 'name' => 'STANDARD PASS'),
	'Exhibitor FMC Premium'    => array('category_id' => 3556, 'name' => 'FMC Premium'),
	'Exhibitor FMC GO'         => array('category_id' => 3557, 'name' => 'FMC GO'),
	'Exhibitor'                => array('category_id' => 3558, 'name' => 'EXHIBITOR'),
	'BUSINESS VISITOR'         => array('category_id' => 3564, 'name' => 'BUSINESS VISITOR PASS'),
	'Premium'                  => array('category_id' => 3554, 'name' => 'PREMIUM PASS'),
	'Standard'                 => array('category_id' => 3555, 'name' => 'STANDARD PASS'),
	'FMC GO'                   => array('category_id' => 3557, 'name' => 'FMC GO'),
	'Service Pass'             => array('category_id' => 3558, 'name' => 'Service Pass'),
	'Business Visitor Pass'    => array('category_id' => 3564, 'name' => 'BUSINESS VISITOR PASS'),
);

/**
 * Cronjob function to process pending exhibitor delegates and send to API
 */
function process_pending_exhibitor_delegates() {
	global $link2;
	
	$host = "95.216.2.164";
	$user = "btsblnl265_asd1d_bengaluruite";
	$pass = "Disl#vhfj#Af#DhW65";
	$db2 = "btsblnl265_asd1d_portal";
	
	$dbConnection = new mysqli($host, $user, $pass, $db2);
	
	if ($dbConnection->connect_error) {
		echo "[" . date('Y-m-d H:i:s') . "] Connection failed: " . $dbConnection->connect_error . "\n";
		return;
	}
	$dbConnection->set_charset('utf8mb4');
	
	// Fetch records from complimentary_delegates where first_name is present and api_sent = 0 or NULL
	$sql = "SELECT * FROM complimentary_delegates 
			WHERE first_name IS NOT NULL 
			AND TRIM(first_name) != ''
			AND (api_sent = 0 OR api_sent IS NULL)
			ORDER BY id ASC
			LIMIT 20"; // Process 20 records at a time to avoid timeout
	
	$result = $dbConnection->query($sql);
	
	if (!$result) {
		echo "[" . date('Y-m-d H:i:s') . "] Error fetching records: " . $dbConnection->error . "\n";
		$dbConnection->close();
		return;
	}
	
	$total_records = $result->num_rows;
	echo "[" . date('Y-m-d H:i:s') . "] Found {$total_records} records to process\n";
	
	if ($total_records == 0) {
		echo "[" . date('Y-m-d H:i:s') . "] No records to process\n";
		$dbConnection->close();
		return;
	}
	
	$processed = 0;
	$errors = 0;
	
	// Get category_id_map
	global $category_id_map;
	
	while ($row = $result->fetch_assoc()) {
		$delegate_id = $row['id'];
		$exhibition_participant_id = isset($row['exhibition_participant_id']) ? $row['exhibition_participant_id'] : null;
		
		echo "[" . date('Y-m-d H:i:s') . "] Processing Delegate ID: {$delegate_id}\n";
		
		// Get application_id from ExhibitionParticipant
		$application_id = null;
		$ticketType = null;
		
		if ($exhibition_participant_id) {
			$appIdSql = "SELECT application_id FROM exhibition_participants WHERE id = ?";
			$stmt = $dbConnection->prepare($appIdSql);
			$stmt->bind_param("i", $exhibition_participant_id);
			$stmt->execute();
			$stmt->bind_result($application_id);
			$stmt->fetch();
			$stmt->close();
			
			// Get application_type from application table using application_id
			if ($application_id) {
				$catSql = "SELECT application_type FROM applications WHERE id = ?";
				$stmt2 = $dbConnection->prepare($catSql);
				$stmt2->bind_param("i", $application_id);
				$stmt2->execute();
				$stmt2->bind_result($ticketType);
				$stmt2->fetch();
				$stmt2->close();
			}
		}
		
		// Fallback to ticketType from complimentary_delegates table if available
		if (empty($ticketType) && isset($row['ticketType']) && !empty($row['ticketType'])) {
			$ticketType = $row['ticketType'];
		}
		
		// Skip if no ticket type found
		if (empty($ticketType)) {
			echo "[" . date('Y-m-d H:i:s') . "] Warning: No ticket type found for delegate ID {$delegate_id}, skipping...\n";
			$errors++;
			continue;
		}
		
		// Get category mapping
		if (!isset($category_id_map[$ticketType])) {
			echo "[" . date('Y-m-d H:i:s') . "] Warning: Unknown ticket type '{$ticketType}' for delegate ID {$delegate_id}, skipping...\n";
			$errors++;
			continue;
		}
		
		$category_info = $category_id_map[$ticketType];
		$category_id = $category_info['category_id'];
		$name = $category_info['name'];
		
		// Process mobile number
		$mobile = isset($row['mobile']) ? $row['mobile'] : '';
		$country_code = '91'; // default
		$phone = '';
		
		if (!empty($mobile)) {
			// Handle format: +91-7905080871
			$mobile_arr = explode('-', $mobile);
			if (isset($mobile_arr[0]) && !empty($mobile_arr[0])) {
				$country_code = str_replace('+', '', trim($mobile_arr[0]));
				if (strlen($country_code) >= 6) {
					$phone = $country_code;
					$country_code = '91';
				}
			}
			if (isset($mobile_arr[1]) && !empty($mobile_arr[1])) {
				$phone = trim($mobile_arr[1]);
			}
		}
		
		// Get event days - default to all days except for FMC passes
		$event_days = '18, 19, 20 Nov';
		if ($ticketType == 'FMC GO' || $ticketType == 'FMC Premium' || $ticketType == 'Sponsor FMC GO' || $ticketType == 'Sponsor FMC Premium' || $ticketType == 'Exhibitor FMC GO' || $ticketType == 'Exhibitor FMC Premium') {
			$event_days = '20 Nov';
		}
		
		// Get first and last name
		$first_name = isset($row['first_name']) ? trim($row['first_name']) : '';
		$last_name = isset($row['last_name']) ? trim($row['last_name']) : '';
		
		// Skip if no name
		if (empty($first_name)) {
			echo "[" . date('Y-m-d H:i:s') . "] Warning: No first name for delegate ID {$delegate_id}, skipping...\n";
			$errors++;
			continue;
		}
		
		// Get email
		$email = isset($row['email']) ? trim($row['email']) : '';
		if (empty($email)) {
			echo "[" . date('Y-m-d H:i:s') . "] Warning: No email for delegate ID {$delegate_id}, skipping...\n";
			$errors++;
			continue;
		}
		
		// Prepare data for API
		$full_name = trim($first_name . ' ' . $last_name);
		
		// Fix encoding issues
		if (!mb_check_encoding($full_name, 'UTF-8')) {
			$full_name = mb_convert_encoding($full_name, 'UTF-8', mb_detect_encoding($full_name));
		}
		
		$data = array();
		$data['name'] = clean_html_entities2($full_name);
		$data['email'] = $email;
		$data['country_code'] = $country_code;
		$data['mobile'] = $phone;
		$data['company'] = clean_html_entities2(isset($row['organisation_name']) ? $row['organisation_name'] : '');
		$data['designation'] = clean_html_entities2(isset($row['job_title']) ? $row['job_title'] : '');
		$data['category_id'] = $category_id;
		$data['qsn_933'] = $name;
		$data['qsn_934'] = $event_days;
		$data['qsn_935'] = ''; // Sector (not applicable for exhibitors typically)
		$data['qsn_936'] = '';
		$data['qsn_366'] = isset($row['unique_id']) ? $row['unique_id'] : ''; // For API log

        echo json_encode($data);
        exit;
		
		// Send to API
		try {
			//$response_raw = sendchkdinapi($data);
			$response = json_decode($response_raw, true);
			
			// Prepare data for storage
			$api_data_json = json_encode($data);
			$api_response_json = json_encode($response);
			
			// Escape JSON for SQL (using the same connection as the UPDATE query)
			$api_data_escaped = $dbConnection->real_escape_string($api_data_json);
			$api_response_escaped = $dbConnection->real_escape_string($api_response_json);
			
			// Update the record with api_response, api_data, and api_sent
			$update_query = "UPDATE complimentary_delegates 
							SET api_response = '{$api_response_escaped}', 
								api_data = '{$api_data_escaped}', 
								api_sent = 1 
							WHERE id = {$delegate_id}";
			
			if ($dbConnection->query($update_query)) {
				if ($response && isset($response['message']) && $response['message'] === 'Success') {
					echo "[" . date('Y-m-d H:i:s') . "] Delegate ({$data['name']}): Success - Guest ID: " . (isset($response['guest_id']) ? $response['guest_id'] : 'N/A') . "\n";
				} else {
					echo "[" . date('Y-m-d H:i:s') . "] Delegate ({$data['name']}): Failed - " . (isset($response['message']) ? $response['message'] : 'Unknown error') . "\n";
				}
				$processed++;
			} else {
				echo "[" . date('Y-m-d H:i:s') . "] Error updating record: " . $dbConnection->error . "\n";
				$errors++;
			}
		} catch (Exception $e) {
			echo "[" . date('Y-m-d H:i:s') . "] Exception for delegate ({$data['name']}): " . $e->getMessage() . "\n";
			
			// Log error in JSON file
			$error_json = json_encode(array(
				'delegate_id' => $delegate_id,
				'name' => $data['name'],
				'email' => $data['email'],
				'error' => $e->getMessage(),
				'timestamp' => date('Y-m-d H:i:s')
			));
			file_put_contents('sendingDataError.json', $error_json . "\n", FILE_APPEND);
			
			$errors++;
		}
	}
	
	$dbConnection->close();
	
	echo "\n[" . date('Y-m-d H:i:s') . "] === Processing Complete ===\n";
	echo "[" . date('Y-m-d H:i:s') . "] Total records processed: {$processed}\n";
	echo "[" . date('Y-m-d H:i:s') . "] Errors: {$errors}\n";
}

// Run the cronjob
process_pending_exhibitor_delegates();
