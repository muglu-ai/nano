<?php
exit; 
ini_set('display_errors', 1);
require 'emailFunction.php';


$emailList = [
    "vivek@playshifu.com",
    "prithvi@qunulabs.in",
    "sanjay@spyne.ai",
    "darryl.d.dias@gmail.com",
    "prem@marutdrones.com",
    "pramod@unboxrobotics.com",
    "akshay@kazam.in",
    "nikhil@cynlr.com",
    "nagendra@jed-i.in",
    "pratik@neuronenergy.in",
    "vadhiraja@vmvh.in",
    "peeyush@vecmocon.com",
    "kartikey.hariyani@chargezone.co",
    "sandeep@sksadvisor.com",
    "ramaseshan.satagopan@generalaeronautics.com",
    "adithyavs@tvastagroup.in",
    "tapan@mihup.com",
    "jyothis@netrasemi.com",
    "akshit712@gmail.com",
    "jayant@nayantech.com",
    "anindya.das@neysanetworks.com",
    "manish.doshi@voltup.in",
    "arpit@dharaksha.com",
    "mayank@scalenut.com",
    "jagga@perceptyne.com",
    "notul.atul@hotmail.com",
    "akash.gupta@cleanelectric.in",
    "sanjay@exponent.energy",
    "raj@kogo.ai",
    "harshal_thakur@amphoursolar.com",
    "vinodh@jidoka-tech.com",
    "pradeep@thanos.in",
    "narayanlalgurjar98@gmail.com",
    "armaanmehta12@gmail.com",
    "ganeshg@gnani.ai",
    "KAUSTUBHD271@GMAIL.COM",
    "neha@zerocircle.in",
    "mailpranjal99@gmail.com",
    "charu@3rdflix.com",
    "pratik.somani@energycompany.in",
    "anirvan@haystackanalytics.in",
    "rituraj@gro.care",
    "ajayraj.ramineni@gmail.com",
    "manoj@orbo.ai",
    "dreamflyinnovations@gmail.com",
    "tonmoy@binocs.co",
    "vishwanath.jha@gamutanalytics.com",
    "sameer@indigridtechnology.com",
    "saurabh@flickstree.com",
    "gopinath@satsure.co",
    "manesh@flomobility.com",
    "sriram@sociographsolutions.in",
    "offgridenergylabs@gmail.com",
    "divyanshu@redinent.com",
    "arun@racenergy.in",
    "shanmukhs@iisc.ac.in",
    "akshay@predictt.ai",
    "shrey@altcarbon.in",
    "mohal@mattermotor.com",
    "laina.emmanuel@brainsightai.com",
    "Shrikant@skyeair.tech",
    "prasanta@newtrace.io",
    "pinakd2013@email.iimcal.ac.in",
    "hshah@replusengitech.com",
    "raghus.iitm@gmail.com",
    "vimal@ionage.in",
    "aks.che@gmail.com",
    "ramesh.srinivasan@zepcotek.com",
    "janakiram@hbrobotics.in",
    "john.kuruvilla@kalpnik.com",
    "sraghavan@iisc.ac.in",
    "ritwika@unscript.ai",
    "aditya@frinks.in",
    "accounts@zoop.one",
    "Om@igdrones.com",
    "kushagra@vidyo.ai",
    "varun.goenka@echargeup.com",
    "akhil@pulseenergy.io",
    "nakul@devnagri.com",
    "ramanunnim1995@gmail.com",
    "ANKIT@SKILLR.AI",
    "arthchowdhary18@gmail.com"
];


// $emailList = [
//     'chandrachood.as@mmactiv.com'
// ];


$subject = "Invitation for Startups to Participate in the Startup India Pavilion at Bengaluru Tech Summit 2025";
// $message = "Your email message here.";

$message = "Dear Startup Founder,<br><br>
We are pleased to invite you to showcase your innovation at the Startup India Pavilion during the Bengaluru Tech Summit 2025. This is a prime opportunity to gain visibility, connect with investors, network with industry leaders and engage with the vibrant tech ecosystem.<br><br>
A limited number of complimentary stalls are available and selection will be at the discretion of the organisers. Shortlisted startups will be allotted stalls on a first-come, first-served basis from among the eligible applicants.<br><br>
If you wish to participate, kindly complete your registration at the earliest using the link below:<br><br>
Register here: <a href=\"https://forms.gle/QvuXdPcevETaX4cL6\">https://forms.gle/QvuXdPcevETaX4cL6</a><br><br>
We look forward to your participation and to celebrating pioneering innovations at the Summit.<br><br>
Warm regards,<br>
Chandrachood<br>
Bengaluru Tech Summit Secretariat";



// $headers = "From: sender@example.com\r\nReply-To: sender@example.com\r\n";

foreach ($emailList as $email) {
    $email = array($email);
    elastic_mail($subject, $message, $email, 'Invitation: Bengaluru Tech Summit 2025');

    // exit;
}