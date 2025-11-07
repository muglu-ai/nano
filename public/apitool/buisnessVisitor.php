<?php 
require_once __DIR__ . '/v2.php';

// Ensure link2 is initialized for sendchkdinapi function
global $link2;
$link2 = mysqli_connect($host, $user, $pass, $db);

if (!$link2) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($link2, 'utf8mb4');


// select all the business visitor from it_visitor_pass where event_year = 2024 and apiSent = 0

$sql = "SELECT * FROM it_visitor_pass WHERE event_year = 2025 AND apiSent = 0";

//curate the data for the api to be send to 

// columns names are as below

// fname , lname, email, job_title job_title, org, city, state, fone 

// from fone we have explode and get the country code and phone number 91-6364627440 


$result = mysqli_query($link2, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $fname = $row['fname'];
    $lname = $row['lname'];
    $email = $row['email'];
    $job_title = $row['job_title'];
    $org = $row['org'];
    $city = $row['city'];
    $state = $row['state'];
    $fone = $row['fone'];

    // get the country code and phone number from the fone
    $country_code = '91'; // default country code
    $phone = '';
    
    if (!empty($fone)) {
        $fone_arr = explode('-', $fone);
        if (isset($fone_arr[0]) && isset($fone_arr[1])) {
            $country_code = $fone_arr[0];
            $phone = $fone_arr[1];
        } else {
            // If no dash found, use the whole fone as phone
            $phone = $fone;
        }
    } elseif (!empty($row['mobile'])) {
        // fallback to the mobile if fone is not present
        $mobile = $row['mobile'];
        $fone_arr = explode('-', $mobile);
        if (isset($fone_arr[0]) && isset($fone_arr[1])) {
            $country_code = $fone_arr[0];
            $phone = $fone_arr[1];
        } else {
            // If no dash found, use the whole mobile as phone
            $phone = $mobile;
        }
    }

    // prepare the data for the api to be send to
    $data = array();
    $name = $fname . ' ' . $lname;
    $data['name'] = clean_html_entities2($name);
    $data['email'] = $email;
    $data['designation'] = clean_html_entities2($job_title);
    $data['company'] = clean_html_entities2($org);
    $data['country_code'] = $country_code;
    $data['mobile'] = $phone;
    $data['category_id'] = 3564;
    $data['qsn_933'] = 'BUSINESS VISITOR';
    $data['qsn_934'] = '18, 19 & 20 Nov';
    $data['qsn_935'] = '';
    $data['qsn_936'] = '';
    $data['qsn_366'] = 'BUSINESS VISITOR-' . $row['srno'];

   echo json_encode($data);
    //exit;

    // send the data to the api
    $response_raw = sendchkdinapi($data);
    // Decode the JSON response
    $response = json_decode($response_raw, true);
    
    // Check if decoding was successful
    if ($response === null || !is_array($response)) {
        echo "Error: Failed to decode API response. Raw response: " . $response_raw;
        exit;
    }
    
    // echo "Response: ";
    // print_r($response);
    // echo "<br><br>";

    // update apiSent to 1 only when the response is success
    if (isset($response['message']) && $response['message'] === "Success") {
        $sql = "UPDATE it_visitor_pass SET apiSent = 1 WHERE srno = " . $row['srno'];
        mysqli_query($link2, $sql);
    }

     
    $sql = "INSERT INTO it_2025_badge_api_log (name, email, mobile, category_id, status, response, tin_no, data, email_exist) VALUES 
    ('" . $data['name'] . "', '" . $data['email'] . "', '" . $data['mobile'] . "', '" . $data['category_id'] . "', 
    '" . ($response['message'] ?? 'Unknown') . "', '" . json_encode($response) . "', '" . $data['qsn_366'] . "', '" . json_encode($data) . "', '" . ($response['guest_id'] ?? 0) . "')";
    mysqli_query($link2, $sql);

    // exit;


}