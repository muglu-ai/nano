<?php 
exit;
ini_set('display_errors', 1);
require 'emailFunction.php';
$message = $message5;
$subject = '4TH SEPT - ENTRY FROM GATE 6 OR 10. CONF AND EXHIBITION STARTS AT 9 AM | SEMICON India 2025';

$sql = "SELECT EMAIL FROM attendees WHERE approvedCate = 'Dignitaries' OR approvedCate = 'Delegate' AND email IS NOT NULL AND email != '' AND JSON_EXTRACT(reminder, '$.reminder4') IS NULL";

require_once 'dbcon.php';

$conn = $link;

$result = $conn->query($sql);

$to = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $email = $row['EMAIL'];
        $to[] = $email;
        // echo $email;
        // exit;
        $to = array($email); // Replace with actual recipient(s)
        elastic_mail($subject, $message, $to, $subject);

        //update in the db that email has been sent in reminder column in attendees table which is json type column
        $updateQuery = "UPDATE attendees SET reminder = JSON_SET(COALESCE(reminder, '{}'), '$.reminder4', NOW()) WHERE email = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        // $stmt->close();
       // echo $message;

        //exit;
        sleep(1); // Optional: pause between sends to avoid server overload
    }
}