<?php

ini_set('display_errors', 1);
/*
End Point :
https://studio.chkdin.com/api/v1/push_guest

METHOD: POST
PARAMS:
    api_key ( Use This : scan626246ff10216s477754768osk ) ( required )
    event_id  (  USE : 118150 for  BTS 2025  )  ( required )
    name ( required )
    category_id ( required ) - ( CATEGORY_ID find below )
    email ( required )
    country_code ( required )
    mobile ( required )
    company
    qsn_933 (. For printable category like: SPEAKER , DELEGATE )
    qsn_934 (. For Day Access like: Day 1 , Day 2 )
    qsn_935 (. Extra Variable 1  )
    qsn_936 (. Extra Variable 2 )

$servername = 'localhost'; // Your MySql Server Name or IP address here
	$dbusername = 'btsblnl265_asd1d_bengaluruite'; // Login user id here
	$dbpassword = 'Disl#vhfj#Af#DhW65'; // Login password here
	$dbname = 'btsblnl265_asd1d_bengaluruite';

*/ 

const API_ENDPOINT = 'https://studio.chkdin.com/api/v1/push_guest';
const API_KEY = 'scan626246ff10216s477754768osk';
const EVENT_ID = 118150;

CONST TEST_MODE = true;

define('DB_HOST', 'localhost');
define('DB_NAME', 'btsblnl265_asd1d_bengaluruite');
define('DB_USERNAME', 'btsblnl265_asd1d_bengaluruite');
define('DB_PASSWORD', 'Disl#vhfj#Af#DhW65');
// define('DB_PORT', 3306);
define('DB_NAME2', 'btsblnl265_asd1d_portal');

// create a dbconnection function
function dbconnection() {
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

$conn = dbconnection();


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
PREMIUM Pass Day 1 – 3575
PREMIUM Pass Day 2 – 3576
PREMIUM Pass Day 3 – 3577
PREMIUM Pass Day 1 & 2 – 3578
PREMIUM Pass Day 1 & 3 – 3579
PREMIUM Pass Day 2 & 3 – 3580
STANDARD Pass Day 1 – 3581
STANDARD Pass Day 2 – 3582
STANDARD Pass Day 3 – 3583
STANDARD Pass Day 1 & 2 – 3584
STANDARD Pass Day 1 & 3 – 3585
STANDARD Pass Day 2 & 3 – 3586

*/ 

function insert_api_log($name, $email, $booking_id, $ticket_id, $ticket_type, $status, $message, $request, $response) {
    $sql = "INSERT INTO it_2025_chkdin_api_log (name, email, booking_id, ticket_id, ticket_type, status, message, request, response, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = dbconnection()->prepare($sql);
    $stmt->execute([$name, $email, $booking_id, $ticket_id, $ticket_type, $status, $message, $request, $response]);
}



function send_guest_data($name, $category_id, $email, $country_code, $mobile, $company, $qsn_933, $qsn_934, $qsn_935, $qsn_936) {
    $data = [
        'api_key' => API_KEY,
        'event_id' => EVENT_ID,
        'name' => $name,
        'category_id' => $category_id,
        'email' => $email,
        'country_code' => $country_code,
        'mobile' => $mobile,
        'company' => $company,
        'qsn_933' => $qsn_933,
        'qsn_934' => $qsn_934,
        'qsn_935' => $qsn_935,
        'qsn_936' => $qsn_936,
    ];

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options to send as application/x-www-form-urlencoded with proper headers and SSL
    curl_setopt($ch, CURLOPT_URL, API_ENDPOINT);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'User-Agent: PHP-Guest-API-Client/1.0'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);

    // Close cURL
    curl_close($ch);

    // Handle errors
    if ($error) {
        return [
            'success' => false,
            'error' => 'cURL Error: ' . $error,
            'http_code' => $httpCode
        ];
    }



    // Parse response
    $responseData = json_decode($response, true);

    insert_api_log($name, $email, $responseData['guest_id'], $responseData['qr_code'], $responseData['qr_image'], $responseData['status'], $responseData['message'], $data, $response);
    

    return [
        'success' => $httpCode >= 200 && $httpCode < 300,
        'http_code' => $httpCode,
        'response' => $responseData ?: $response,
        'raw_response' => $response
    ];
}

return [
    'send_guest_data' => 'send_guest_data',
];

