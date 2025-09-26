<?php



ini_set('display_errors', 1);

error_reporting(E_ALL); // Enable all errors



$link = mysqli_connect('localhost', '', '', '', 3306);



if (!$link) {

    die("Not connected: " . mysqli_connect_error()); // Corrected error handling

}
