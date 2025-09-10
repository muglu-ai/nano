<?php

define('MAIL_FROM_NAME', 'Bengaluru Tech Summit 2025');
define('APP_NAME', 'Bengaluru Tech Summit 2025');
define('APP_NAME_SHORT', 'BTS 2025');
define('EVENT_NAME', 'Bengaluru Tech Summit');
define('EVENT_YEAR', '2025');
define('SHORT_NAME', 'BTS');
define('EVENT_WEBSITE', 'https://www.bengalurutechsummit.com');
//event dates
define('EVENT_DATE_START', '19-11-2025');
define('EVENT_DATE_END', '21-11-2025');
define('EVENT_VENUE', 'Bengaluru International Exhibition Centre (BIEC), Bengaluru, India');

define('APP_URL', 'http://127.0.0.1:8000');
define('SHELL_SCHEME_RATE', 13000); // per sqm
define('RAW_SPACE_RATE', 12000); // per sqm
define('IND_PROCESSING_CHARGE', 3); // 5% processing fee for National payments
define('INT_PROCESSING_CHARGE', 9); // 9% processing fee for International payments
define('GST_RATE', 18); // GST rate for India
define('SOC_LINKEDIN', 'https://in.linkedin.com/company/bengaluru-tech-summit');
define('SOC_TWITTER', 'https://twitter.com/blrtechsummit');
define('SOC_FACEBOOK', 'https://www.facebook.com/BengaluruTechSummit');
define('SOC_INSTAGRAM', 'https://www.instagram.com/blrtechsummit/');
define('SOC_YOUTUBE', 'https://www.youtube.com/@bengalurutechsummit/streams');
define('ORGANIZER_NAME', 'MM Activ Sci-Tech Communications');
define('ORGANIZER_ADDRESS', 'No.11/3, NITON, Block C, 2nd Floor, Palace Road, Bengaluru - 560001, Karnataka, India');
define('FAVICON_APPLE', 'https://www.bengalurutechsummit.com/apple-touch-icon.png');
define('FAVICON', 'https://www.bengalurutechsummit.com/favicon-32x32.png');
define('FAVICON_16', 'https://www.bengalurutechsummit.com/favicon-16x16.png');
define('ORGANIZER_PHONE', '+91-8069328400');
define('ORGANIZER_EMAIL', 'enquiry@bengalurutechsummit.com');
define('ORGANIZER_WEBSITE', 'https://mmactiv.in/');
define('ORGANIZER_LOGO', 'https://www.mmactiv.in/images/mma.jpg');
define('EVENT_LOGO', 'https://bengalurutechsummit.com/web/it_forms/images/logo2.png');




return [
    'EVENT_NAME' => EVENT_NAME,
    'event_name' => EVENT_NAME,
    'EVENT_YEAR' => EVENT_YEAR,
    'SHORT_NAME' => SHORT_NAME,
    'EVENT_DATE_START' => EVENT_DATE_START,
    'EVENT_DATE_END' => EVENT_DATE_END,
    'EVENT_VENUE' => EVENT_VENUE,
    'EVENT_WEBSITE' => EVENT_WEBSITE,
    'APP_URL' => EVENT_WEBSITE . '/app',
    'APP_NAME' => APP_NAME,
    'APP_NAME_SHORT' => SHORT_NAME,
    'APPLICATION_ID_PREFIX' => 'TIN-BTS-2025-EXH-',
    'SPONSORSHIP_ID_PREFIX' => 'TIN-BTS-2025-SPONSOR-',
    'FAVICON' => FAVICON,
    'FAVICON_APPLE' => FAVICON_APPLE,
    'FAVICON_16' => FAVICON_16,
    'SOCIAL_LINKS' => [
        'linkedin' => SOC_LINKEDIN,
        'twitter' => SOC_TWITTER,
        'facebook' => SOC_FACEBOOK,
        'instagram' => SOC_INSTAGRAM,
        'youtube' => SOC_YOUTUBE,
    ],
    'TERMS_URL' => 'https://www.bengalurutechsummit.com/privacy-policy.php',
    //Database connection
    'DB_HOST' => 'localhost',
    'DB_PORT' => '3306',
    'DB_DATABASE' => 'semicon',
    'DB_USERNAME' => 'semicon',
    'DB_PASSWORD' => 'Qwerty@123',
    //Email configuration
    'MAIL_MAILER' => 'smtp',
    'MAIL_HOST' => 'smtp.example.com',
    'MAIL_PORT' => 587,
    'MAIL_USERNAME' => 'test@example.com',
    'MAIL_PASSWORD' => 'password',
    'MAIL_ENCRYPTION' => 'tls',
    'MAIL_FROM_ADDRESS' => 'test@test.com',
    'MAIL_FROM_NAME' => MAIL_FROM_NAME,
    'MAIL_REPLY_TO_ADDRESS' => 'test@test.com',
    'MAIL_REPLY_TO_NAME' => MAIL_FROM_NAME,


    // Payment Gateway
    'GST_RATE' => GST_RATE, // GST rate for India
    //Paypal Configuration
    'PAYPAL_CLIENT_ID' => "Af98MdWNTOZO-rKE9MdjRJE50vr3Rp9DOYfr3TwidA9kzexdt2NGYAfXP9DfjK_5PTmTzxsxtoufZCyT",
    'PAYPAL_SECRET' => "EPdptPZ_JJ5vFhlO4Cf4dJH9m6RIS7exO7xbGgy68pjGE42y2Cv2txd6Sh8g3l775b28SVX6gb7arBoQ",
    'PAYPAL_CURRENCY' => "USD",
    'PAYPAL_MODE' => "LIVE", // Can be either 'sandbox' or 'live'
    'INT_PROCESSING_CHARGE' => INT_PROCESSING_CHARGE, // 9% processing fee for International payments



    // CCAVENUE Configuration
    'CCAVENUE_ACCESS_CODE' => "AVAX60MC26BE01XAEB",
    'CCAVENUE_WORKING_KEY' => "DBBE266B02508AF7118D4A2598763D69",
    'CCAVENUE_MERCHANT_ID' => "7700",
    'CCAVENUE_REDIRECT_URL' => APP_URL . "/payment/ccavenue-success",
    'IND_PROCESSING_CHARGE' => IND_PROCESSING_CHARGE, // 3% processing fee for National payments





    'GSTIN' => '29AABCM2615H1ZM',
    'PAN'   => 'AABCS1234A',
    'CIN'  => 'U12345DL2025PTC123456',
    'organizer' => [
        'name' => ORGANIZER_NAME,
        'address' => ORGANIZER_ADDRESS,
        'phone' => ORGANIZER_PHONE,
        'email' => ORGANIZER_EMAIL,
        'website' => ORGANIZER_WEBSITE,
    ],
    'organizer_logo' => ORGANIZER_LOGO,
    'event_logo' => EVENT_LOGO,
    'SHELL_SCHEME_RATE' => SHELL_SCHEME_RATE, // per sqm
    'RAW_SPACE_RATE' => RAW_SPACE_RATE,    // per sqm

    'max_attendees' => env('MAX_ATTENDEES', 1),
    'hosted_url' => env('HOSTED_URL', 'https://www.bengalurutechsummit.com/app'),
    'HOSTED_URL' => env('hosted_url', 'https://www.bengalurutechsummit.com/app'),
    'sectors' => [
        'Startup',
        'Information Technology',
        'Electronics',
        'Semiconductor',
        'Telecommunication',
        'Cybersecurity',
        'Artificial Intelligence',
        'Cloud Services',
        'E-Commerce',
        'Automation',
        'AVGC',
        'Space Tech',
        'MobilityTech',
        'Infrastructure',
        'Biotech',
        'Agritech',
        'Medtech',
        'Fintech',
        'Healthtech',
        'Edutech',
        'Biotechnology',
        'Academia & University (not for Student Only Faculty and HOD)',
        'Others',
    ],
    'organization_types' => [
        'Startup',
        'MSME',
        'Traditional Businesses',
        'Incubator',
        'Accelerator',
        'Institutional Investor',
        'Academic Institution',
        'Corporate / Industry',
        'R&D Labs',
        'Investors',
        'Government',
        'Industry Associations',
        'Consulting',
        'Trade Mission',
        'Service Enabler / Consulting',
        'Trade Mission / Embassay',
        'Students',
        'Others'
    ],
    'SUB_SECTORS' => [
        'IT',
        'IoT',
        'AI & ML',
        'AR & VR',
        'BlockChain',
        'Digital Learning',
        'Electronics',
        'FinTech',
        'Robo & Drone',
        'Gaming',
        'Mobility',
        'IT Services',
        'BioTech',
        'AgriTech',
        'MedTech',
        'Healthtech',
        'SmartTech',
        'Cyber security & Human Resource',
        'EV',
        'Semiconductor',
        'Other',
    ],
    'product_categories' => [
        'Design/R&D',
        'Logistics and Operations planning',
        'Management/business head',
        'Manufacturing/processing/quality control',
        'Marketing/advertising/PR',
        'Purchasing/procuring',
        'Production planning',
        'Software/Systems development and integration'
    ],

    'job_functions' => [
        'Owner/Founder',
        'Purchasing Manager',
        'Sales/Marketing',
        'Technical Manager',
        'Operations',
        'Consultant',
        'Other'
    ],



    'exhibition_cost' => [
        // Shell Space options
        '9s'  => '9sqm (9sqm x Rs. ' . SHELL_SCHEME_RATE . ') Shell Space = ' . number_format(9 * SHELL_SCHEME_RATE) . ' + 18% GST',
        '12s' => '12sqm (12sqm x Rs. ' . SHELL_SCHEME_RATE . ') Shell Space = ' . number_format(12 * SHELL_SCHEME_RATE) . ' + 18% GST',
        '15s' => '15sqm (15sqm x Rs. ' . SHELL_SCHEME_RATE . ') Shell Space = ' . number_format(15 * SHELL_SCHEME_RATE) . ' + 18% GST',
        '18s' => '18sqm (18sqm x Rs. ' . SHELL_SCHEME_RATE . ') Shell Space = ' . number_format(18 * SHELL_SCHEME_RATE) . ' + 18% GST',
        '27s' => '27sqm (27sqm x Rs. ' . SHELL_SCHEME_RATE . ') Shell Space = ' . number_format(27 * SHELL_SCHEME_RATE) . ' + 18% GST',
        // Raw Space options (dynamic)
        '36'  => '36sqm (36sqm x Rs. ' . RAW_SPACE_RATE . ') Raw Space = ' . number_format(36 * RAW_SPACE_RATE) . ' + 18% GST',
        '48'  => '48sqm (48sqm x Rs. ' . RAW_SPACE_RATE . ') Raw Space = ' . number_format(48 * RAW_SPACE_RATE) . ' + 18% GST',
        '54'  => '54sqm (54sqm x Rs. ' . RAW_SPACE_RATE . ') Raw Space = ' . number_format(54 * RAW_SPACE_RATE) . ' + 18% GST',
        '72'  => '72sqm (72sqm x Rs. ' . RAW_SPACE_RATE . ') Raw Space = ' . number_format(72 * RAW_SPACE_RATE) . ' + 18% GST',
        '108' => '108sqm (108sqm x Rs. ' . RAW_SPACE_RATE . ') Raw Space = ' . number_format(108 * RAW_SPACE_RATE) . ' + 18% GST',
        '135' => '135sqm (135sqm x Rs. ' . RAW_SPACE_RATE . ') Raw Space = ' . number_format(135 * RAW_SPACE_RATE) . ' + 18% GST',
    ],

    'SHELL_SCHEME_RATE_USD' => SHELL_SCHEME_RATE * 0.012, // Assuming 1 INR = 0.012 USD
    'RAW_SPACE_RATE_USD' => RAW_SPACE_RATE * 0.012, // Assuming 1 INR = 0.012 USD


    // Admin Emails to receive notifications
    'admin_emails' => [
        'to' => ['enquiry@bengalurutechsummit.com'],  // Primary recipient
        'bcc' => [    // BCC recipients
            'test.interlinks@gmail.com',
            'manish.sharma@interlinks.in'
        ],
        'visitor_emails' => [
            'test.interlinks@gmail.com',
        ],

        'payment_emails' => array_merge(
            [
                'accounts@mmactiv.com',
                'test.interlinks@gmail.com',
            ],
            (array) (config('admin_emails.to') ?? [])
        ),

    ],

    //extra requirements details
    'extra_requirements_contact' => [
        'name' => "Vivek",
        'email' => "vivek.saraf@mindmeshix.com",
    ],
    //visitor registration unique id

    'visitor_registration_unique_id' => [
        'prefix' => 'BTS25VI_',
        'length' => 6,
    ],





];
