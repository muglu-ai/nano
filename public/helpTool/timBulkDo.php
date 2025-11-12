<?php 

//we have to do send bulk emails to the exhibitors to all applications where submission_status is approved
require 'dbcon.php';
require 'emailFunction.php';

$conn = $link;
require_once '../Final-Emailer/email.html';

//get all applications where submission_status is approved
$sql = "SELECT * FROM applications WHERE submission_status = 'approved'";
$result = $conn->query($sql);

//get the emailBody from ../Final-Emailer/email.html

//write a query to get all applications where submission_status is approved elastic_mail
while ($row = $result->fetch_assoc()) {
    $email = $row['company_email'];
    $to = array($email);
    $subject = 'Need extra hands at your Bengaluru Tech Summit stall?';
    $message = file_get_contents('../Final-Emailer/email.html');

    echo $message;
    exit;

    //elastic_mail($subject, $message, $to);
}