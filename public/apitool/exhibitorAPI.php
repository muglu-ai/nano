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
 * Map ticketType based on applicationType
 * @param string $ticketType - The ticket type from complimentary_delegates table
 * @param string $applicationType - The application type from applications table
 * @return string - The mapped ticket type with proper prefix
 */
function mapTicketTypeByApplicationType($ticketType, $applicationType) {
	if (empty($ticketType)) {
		return null;
	}
	
	// Normalize application type
	$applicationType = strtolower(trim($applicationType));
	$ticketTypeLower = strtolower(trim($ticketType));
	$ticketType = trim($ticketType);
	
	// Handle GIA PARTNER and VIP GIA PARTNER - these are special categories that don't need prefixing
	if (stripos($ticketTypeLower, 'vip gia partner') !== false || $ticketTypeLower === 'vip gia partner') {
		return 'VIP GIA PARTNER';
	}
	if (stripos($ticketTypeLower, 'gia partner') !== false || $ticketTypeLower === 'gia partner') {
		// If it contains "VIP", return VIP GIA PARTNER, otherwise GIA PARTNER
		if (stripos($ticketTypeLower, 'vip') !== false) {
			return 'VIP GIA PARTNER';
		}
		return 'GIA PARTNER';
	}
	
	// Map based on application type
	if ($applicationType === 'exhibitor') {
		// For exhibitor, prefix with "Exhibitor " if not already prefixed
		if (stripos($ticketType, 'Exhibitor') === false && stripos($ticketType, 'Sponsor') === false) {
			// Handle special cases - check in order of specificity
			if (stripos($ticketTypeLower, 'fmc go') !== false) {
				return 'Exhibitor FMC GO';
			}
			if (stripos($ticketTypeLower, 'fmc premium') !== false || (stripos($ticketTypeLower, 'fmc') !== false && stripos($ticketTypeLower, 'premium') !== false)) {
				return 'Exhibitor FMC Premium';
			}
			if (stripos($ticketTypeLower, 'fmc') !== false) {
				return 'Exhibitor FMC Premium'; // Default FMC to Premium
			}
			if (stripos($ticketTypeLower, 'vip') !== false || stripos($ticketTypeLower, 'vip pass') !== false) {
				return 'Exhibitor VIP Pass';
			}
			if (stripos($ticketTypeLower, 'premium') !== false || stripos($ticketTypeLower, 'premium pass') !== false) {
				return 'Exhibitor Premium Pass';
			}
			if (stripos($ticketTypeLower, 'standard') !== false || stripos($ticketTypeLower, 'standard pass') !== false) {
				return 'Exhibitor Standard Pass';
			}
			if (stripos($ticketTypeLower, 'service') !== false || stripos($ticketTypeLower, 'service pass') !== false) {
				return 'Service Pass';
			}
			if (stripos($ticketTypeLower, 'business visitor') !== false) {
				return 'BUSINESS VISITOR';
			}
			// Default to Exhibitor
			return 'Exhibitor';
		}
		return $ticketType;
	} elseif ($applicationType === 'sponsor' || $applicationType === 'exhibitor+sponsor' || stripos($applicationType, 'sponsor') !== false) {
		// For sponsor or exhibitor+sponsor, prefix with "Sponsor " if not already prefixed
		if (stripos($ticketType, 'Sponsor') === false && stripos($ticketType, 'Exhibitor') === false) {
			// Handle special cases - check in order of specificity
			if (stripos($ticketTypeLower, 'fmc go') !== false) {
				return 'Sponsor FMC GO';
			}
			if (stripos($ticketTypeLower, 'fmc premium') !== false || (stripos($ticketTypeLower, 'fmc') !== false && stripos($ticketTypeLower, 'premium') !== false)) {
				return 'Sponsor FMC Premium';
			}
			if (stripos($ticketTypeLower, 'fmc') !== false) {
				return 'Sponsor FMC Premium'; // Default FMC to Premium
			}
			if (stripos($ticketTypeLower, 'vip') !== false || stripos($ticketTypeLower, 'vip pass') !== false) {
				return 'Sponsor VIP Pass';
			}
			if (stripos($ticketTypeLower, 'premium') !== false || stripos($ticketTypeLower, 'premium pass') !== false) {
				return 'Sponsor Premium';
			}
			if (stripos($ticketTypeLower, 'standard') !== false || stripos($ticketTypeLower, 'standard pass') !== false) {
				return 'Sponsor Standard';
			}
			// Default to Sponsor Premium
			return 'Sponsor Premium';
		}
		return $ticketType;
	}
	
	// If no mapping found, return as is
	return $ticketType;
}

/**
 * Map application_type to category_id_map for exhibitor tickets
 */
$category_id_map = array(
	'Sponsor VIP Pass'         => array('category_id' => 3548, 'name' => 'VIP PASS'),
	'Sponsor Premium'          => array('category_id' => 3549, 'name' => 'PREMIUM PASS'),
	'Sponsor Standard'         => array('category_id' => 3550, 'name' => 'STANDARD PASS'),
	'Sponsor FMC Premium'      => array('category_id' => 3551, 'name' => 'FMC Premium'),
	'Sponsor FMC GO'            => array('category_id' => 3552, 'name' => 'FMC GO'),
	'Exhibitor VIP Pass'                 => array('category_id' => 3553, 'name' => 'VIP PASS'),
	'Exhibitor Premium Pass'             => array('category_id' => 3554, 'name' => 'PREMIUM PASS'),
	'Exhibitor Standard Pass'            => array('category_id' => 3555, 'name' => 'STANDARD PASS'),
	'Exhibitor FMC Premium'    => array('category_id' => 3556, 'name' => 'FMC Premium'),
	'Exhibitor FMC GO'         => array('category_id' => 3557, 'name' => 'FMC GO'),
	'Exhibitor'                => array('category_id' => 3558, 'name' => 'EXHIBITOR'),
	'VIP GIA PARTNER'          => array('category_id' => 3532, 'name' => 'VIP GIA PARTNER'),
	'GIA PARTNER'              => array('category_id' => 3533, 'name' => 'GIA PARTNER'),
	'BUSINESS VISITOR'         => array('category_id' => 3564, 'name' => 'BUSINESS VISITOR'),
	'Premium'                  => array('category_id' => 3554, 'name' => 'PREMIUM PASS'),
	'Standard'                 => array('category_id' => 3555, 'name' => 'STANDARD PASS'),
	'FMC GO'                   => array('category_id' => 3557, 'name' => 'FMC GO'),
	'Service Pass'             => array('category_id' => 3558, 'name' => 'Service Pass'),
	'Business Visitor'    => array('category_id' => 3564, 'name' => 'BUSINESS VISITOR'),
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
		$ticketType = isset($row['ticketType']) ? $row['ticketType'] : null;
		$applicationType = null;
		
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
				$stmt2->bind_result($applicationType);
				$stmt2->fetch();
				$stmt2->close();
			}
		}
		
		// Map ticketType based on applicationType
		if (!empty($ticketType) && !empty($applicationType)) {
			$ticketType = mapTicketTypeByApplicationType($ticketType, $applicationType);
		} elseif (empty($ticketType) && isset($row['ticketType']) && !empty($row['ticketType'])) {
			// Fallback: use ticketType from complimentary_delegates table if available
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

        // echo json_encode($data);
        // exit;
		
		// Send to API
		try {
			$response_raw = sendchkdinapi($data);
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
