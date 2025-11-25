<?php 

// write php script to send thank you email to the speakers using the emailFunction.php and a html file speaker-thank.html file and send a test email to the manish.sharma@interlinks.in

require 'emailFunction.php';

$message = file_get_contents('BTS-Speakers.html');
$subject = 'Thank You for Your Valuable Participation at BTS 2025';

// Load recipients from JSON
$jsonPath = __DIR__ . '/email_recipients.json';
$recipients = json_decode(file_get_contents($jsonPath), true);

$count = 0;
$limit = 500;
foreach ($recipients as &$recipient) {
    if ($count >= $limit) break;
    if (isset($recipient['status']) && $recipient['status'] === 'sent') continue;
    $email = $recipient['email'];
    $to = [$email];
    echo "Sending email to: " . implode(", ", $to) . "<br/>";
    // Send the email and only mark as sent if successful
    $sent = elastic_mail_cc($subject, $message, $to, $cc = array(), $bcc = array(), $subject);
    if ($sent !== false) {
        $recipient['status'] = 'sent';
        $count++;
    } else {
        echo "Failed to send to: " . $email . "<br/>";
    }
    sleep(1);
}
unset($recipient); // To avoid reference carry-over

// Save updated status back to JSON
file_put_contents($jsonPath, json_encode($recipients, JSON_PRETTY_PRINT));

