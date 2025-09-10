<?php



ini_set('display_errors', 1);

error_reporting(E_ALL); // Enable all errors



$link = mysqli_connect('localhost', 'portalsemiconind_db', '9J#P+7Uv2OYE', 'portalsemiconind_db', 3306);



if (!$link) {

    die("Not connected: " . mysqli_connect_error()); // Corrected error handling

}
