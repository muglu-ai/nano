<?php



ini_set('display_errors', 1);

error_reporting(E_ALL); // Enable all errors

$host = "95.216.2.164"; // Adjust if needed
$username = "btsblnl265_asd1d_bengaluruite"; // Adjust if needed
$password = "Disl#vhfj#Af#DhW65"; // Adjust if needed
$database = "btsblnl265_asd1d_portal"; // Adjust if needed



$link = mysqli_connect($host, $username, $password, $database);



if (!$link) {

    die("Not connected: " . mysqli_connect_error()); // Corrected error handling

}
