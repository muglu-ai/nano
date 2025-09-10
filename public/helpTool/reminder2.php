<?php

require 'emailFunction.php';


require_once 'dbcon.php';


$message =  $message5;
$conn = $link;

$subject = '4TH SEPT - ENTRY FROM GATE 6 OR 10. CONF AND EXHIBITION STARTS AT 9 AM | SEMICON India 2025';

$query = "SELECT email from stall_manning where first_name != '' AND email IS NOT NULL AND email != '' AND JSON_EXTRACT(reminder, '$.reminder2') IS NULL";
$result = $conn->query($query);

$to = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $email = $row['email'];
        $to[] = $email;
    }
}

$count = count($to);
//echo "Total emails to send: " . $count . "\n";

foreach ($to as $email) {
    echo "Sending to: " . $email . "\n";
    $recipient = array($email);

    //update in the db that email has been sent in reminder column in stall_manning table which is json type column
    $updateQuery = "UPDATE stall_manning SET reminder = JSON_SET(COALESCE(reminder, '{}'), '$.reminder2', NOW()) WHERE email = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    // Uncomment the line below to actually send the email
    
    elastic_mail($subject, $message, $recipient, $subject);
    sleep(1); // Optional: pause between sends to avoid server overload
    //print_r($recipient);
    //echo $message;
   // exit;


}