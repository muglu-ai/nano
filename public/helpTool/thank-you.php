<?php 
exit;
require 'emailFunction.php';

$emails = [ 'vibha.bhatia@mmactiv.com' ];

$message = $message6;
$subject = "Thank You for Making " . config('constants.EVENT_NAME') . " " . config('constants.EVENT_YEAR') . " a Grand Success!";
echo $message;
foreach ($emails as $email) {
    $email = array($email);
    echo "Sending email to: " . implode(", ", $email) . "<br/>";
    
    elastic_mail($subject, $message, $email, 'Thank You for Making ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' a Grand Success!');
}