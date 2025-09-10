<?php 
require_once 'emailFunction.php';

require_once 'dbcon.php';

// $link = $conn; // Use the existing connection from dbcon.php
$conn = $link;
// $sql = "select"

// echo $message;

// select id, company_name from applications table then from event_contacts.email where application_id = application.id

$sql = "SELECT a.id, a.company_name, ec.email 
    FROM applications a 
    JOIN event_contacts ec ON a.id = ec.application_id 
    WHERE ec.email IS NOT NULL 
      AND ec.email != '' 
      AND a.submission_status = 'approved'
      AND a.stallNumber IS NOT NULL
      AND a.allocated_sqm > 0
      ";

$result = $conn->query($sql);

$emails = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $company_name = $row['company_name'];
        echo $email = $row['email'];
        // $email = 'manish.sharma@interlinks.in';
        $to = array($email); // Replace with actual recipient(s)
        // $to = array($email); // Replace with actual recipient(s)
        //elastic_mail($subject, $message, $to, 'Wi Fi and Lead Retrieval Services at SEMICON India 2025');
        $emails[$company_name] = $email;
        // exit;

    }
}



