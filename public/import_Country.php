<?php

$servername = "localhost"; // Change if needed
$username = "semicon";
$password = "Qwerty@123";
$database = "semicon";

// Connect to MySQL database
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Path to the CSV file
$csvFile = "countries.csv";

// Open CSV file
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Skip the header row
    fgetcsv($handle);

    // Prepare the insert query
    $stmt = $conn->prepare("INSERT INTO countries (name, code, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $name = trim($data[0]);
        $phonecode = trim($data[1]);

        // Bind values and execute
        $stmt->bind_param("ss", $name, $phonecode);
        $stmt->execute();
    }

    fclose($handle);
    echo "Data imported successfully!";
} else {
    echo "Error opening file.";
}

$stmt->close();
$conn->close();

?>
