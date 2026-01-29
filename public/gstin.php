<?php

// Allow CORS for cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Read the JSON input
$request_body = file_get_contents("php://input");
$data = json_decode($request_body, true);

// Check if GSTIN is provided
if (!isset($data['gstin'])) {
    echo json_encode(["error" => "GSTIN is required"]);
    exit;
}

$gstin = $data['gstin'];

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://my.gstzen.in/api/gstin-validator/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode(["gstin" => $gstin]),
    CURLOPT_HTTPHEADER => array(
        'Token: 5479841c-b3ff-42ba-90bf-cb9866f52321',
        'Content-Type: application/json'
    ),
));

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

// Return the response from the API
http_response_code($httpCode);
echo $response;
