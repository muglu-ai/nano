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

$sql = "SELECT * FROM it_visitor_pass WHERE event_year = 2025 AND apiSent = 0 limit 1";

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
    $fone_arr = explode('-', $fone);
    $country_code = $fone_arr[0];
    $phone = $fone_arr[1];

    // fallback to the mobile if fone is not present
    if (empty($fone)) {
        $mobile = $row['mobile'];
        $fone_arr = explode('-', $mobile);
        $country_code = $fone_arr[0];
        $phone = $fone_arr[1];
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
    exit;

    // send the data to the api
    $response = sendchkdinapi($data);
    // echo "Response: ";
    // print_r($response);
    // echo "<br><br>";

    // update the apiSent to 1
    $sql = "UPDATE it_visitor_pass SET apiSent = 1 WHERE id = " . $row['srno'];
    mysqli_query($link2, $sql);


}