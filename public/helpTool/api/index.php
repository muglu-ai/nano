<?php
header("Content-Type: application/json");

// ----------- CONFIG -----------
$validUser = "admin";
$validPass = "123456";   // Hardcoded username & password
// ------------------------------

// Read incoming JSON
$input = file_get_contents("php://input");

//store the data in json format

//if file does not exist, create it
if (!file_exists("data.json")) {
    file_put_contents("data.json", "");
}

file_put_contents("data.json", $input);


$data  = json_decode($input, true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON input"
    ]);
    exit;
}

// Check authentication
if (!isset($data['username'], $data['password'])) {
    echo json_encode([
        "success" => false,
        "message" => "Username and password required"
    ]);
    exit;
}

if ($data['username'] !== $validUser || $data['password'] !== $validPass) {
    echo json_encode([
        "success" => false,
        "message" => "Authentication failed"
    ]);
    exit;
}

// If authenticated, return back received data
$response = [
    "success" => true,
    "message" => "Authentication successful",
    "received" => $data['payload'] ?? null
];

echo json_encode($response, JSON_PRETTY_PRINT);
