<?php

/*
End Point :
https://studio.chkdin.com/api/v1/push_guest

METHOD: POST
PARAMS:
    api_key ( Use This : scan626246ff10216s477754768osk ) ( required )
    event_id  (  USE : 118150 for  BTS 2025  )  ( required )
    name ( required )
    category_id ( required ) - ( CATEGORY_ID find below )
    email ( required )
    country_code ( required )
    mobile ( required )
    company
    qsn_933 (. For printable category like: SPEAKER , DELEGATE )
    qsn_934 (. For Day Access like: Day 1 , Day 2 )
    qsn_935 (. Extra Variable 1  )
    qsn_936 (. Extra Variable 2 )



*/ 

const API_ENDPOINT = 'https://studio.chkdin.com/api/v1/push_guest';
const API_KEY = 'scan626246ff10216s477754768osk';
const EVENT_ID = 118150;

CONST TEST_MODE = true;

/*
VVIP - 3516
VIP - 3517
Ministers VIP - 3518
VISION GROUP ITE - 3519
VISION GROUP BIOTECH - 3520
VISION GROUP STARTUPS - 3521
VISION GROUP SPACE - 3522
VISION GROUP NANOTECH - 3523
CONFERENCE COMMITTEE - 3524
SPEAKER - 3525
Organiser Green - 3526
Organiser Blue - 3527
GoK Sr. Officer - 3528
GoK Staff - 3529
PROTOCOL - 3530
EVENT PARTNER - 3531
VIP GIA PARTNER - 3532
GIA PARTNER - 3533
ASSOCIATION PARTNER - 3534
ASSOCIATION SUPPORT - 3535
VIP PASS - 3536
VIP PASS Day 1 - 3537
VIP PASS Day 2 - 3538
VIP PASS Day 3 - 3539
VIP PASS Day 1 & 2 - 3540
VIP PASS Day 1 & 3 - 3541
VIP PASS Day 2 & 3 - 3542
PREMIUM - 3543
STANDARD - 3544
FMC Premium - 3545
FMC GO - 3546
POSTER DELEGATE - 3547
Sponsor VIP Pass - 3548
Sponsor Premium - 3549
Sponsor Standard - 3550
Sponsor FMC Premium - 3551
Sponsor FMC GO - 3552
Exhibitor VIP Pass PAID - 3553
Exhibitor Premium - 3554
Exhibitor Standard - 3555
Exhibitor FMC Premium - 3556
Exhibitor FMC GO - 3557
Exhibitor - 3558
Media - 3559
Invitee - 3560
SESSION ATTENDEE - 3561
AWARD NOMINEE - 3562
QUIZ - 3563
BUSINESS VISITOR - 3564
VISITOR - 3565
STUDENT - 3566
PREMIUM Pass Day 1 – 3575
PREMIUM Pass Day 2 – 3576
PREMIUM Pass Day 3 – 3577
PREMIUM Pass Day 1 & 2 – 3578
PREMIUM Pass Day 1 & 3 – 3579
PREMIUM Pass Day 2 & 3 – 3580
STANDARD Pass Day 1 – 3581
STANDARD Pass Day 2 – 3582
STANDARD Pass Day 3 – 3583
STANDARD Pass Day 1 & 2 – 3584
STANDARD Pass Day 1 & 3 – 3585
STANDARD Pass Day 2 & 3 – 3586


*/ 

function send_guest_data($name, $category_id, $email, $country_code, $mobile, $company, $qsn_933, $qsn_934, $qsn_935, $qsn_936) {
    $data = [
        'api_key' => API_KEY,
        'event_id' => EVENT_ID,
        'name' => $name,
        'category_id' => $category_id,
        'email' => $email,
    ];

    $ch = curl_init(API_ENDPOINT);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);

    // print_r($response);
    curl_close($ch);
    return $response;
}

return [
    'send_guest_data' => 'send_guest_data',
];

