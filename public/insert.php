<?php
// Database connection
$host = "localhost";
$user = "semicon";
$password = "Qwerty@123";
$dbname = "semicon";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read JSON file
$jsonFile = 'country_state.json';
$jsonData = file_get_contents($jsonFile);
$countries = json_decode($jsonData, true);

if (!$countries) {
    die("Error decoding JSON file.");
}

// Prepare SQL statements
$countryInsert = $conn->prepare("INSERT INTO countries_new (name, code, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
$stateInsert = $conn->prepare("INSERT INTO states_new (country_id, name, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");

// Loop through each country
foreach ($countries as $country) {
    $countryInsert->bind_param("ss", $country['name'], $country['phonecode']);
    $countryInsert->execute();
    $country_id = $conn->insert_id; // Get inserted country ID

    // Insert states for this country
    if (!empty($country['states'])) {
        foreach ($country['states'] as $state) {
            $stateInsert->bind_param("is", $country_id, $state['name']);
            $stateInsert->execute();
        }
    }
}

// Close connections
$countryInsert->close();
$stateInsert->close();
$conn->close();

echo "Data inserted successfully.";
?>
