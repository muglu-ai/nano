<?php

const MAIL_FROM_NAME = 'Bengaluru Tech Summit 2026';
const APP_NAME = 'Bengaluru Tech Summit 2026';
const APP_NAME_SHORT = 'BTS 2026';
const EVENT_NAME = 'Bengaluru Tech Summit';
const EVENT_YEAR = '2026';
const SHORT_NAME = 'BTS';

// TIN number prefix: event short name (SHORT_NAME), year, EXH, then 6-digit random number will be appended in code.
const TIN_NO_PREFIX = SHORT_NAME . '-' . EVENT_YEAR . '-EXH-';

const PIN_NO_PREFIX = 'PRN-' . SHORT_NAME . '-' . EVENT_YEAR . '-EXHP-';
const EVENT_WEBSITE = 'https://www.bengalurutechsummit.com';
//event dates
const EVENT_DATE_START = '19-11-2026';
const EVENT_DATE_END = '21-11-2026';
const EVENT_VENUE = 'Bengaluru International Exhibition Centre (BIEC), Bengaluru, India';

const APP_URL = 'https://bengalurutechsummit.com/bts-2026/public';
const SHELL_SCHEME_RATE = 14000; // per sqm
const SHELL_SCHEME_RATE_USD = 175; // per sqm
const RAW_SPACE_RATE = 13000; // per sqm
const RAW_SPACE_RATE_USD = 160; // per sqm
const IND_PROCESSING_CHARGE = 3; // 5% processing fee for National payments
const INT_PROCESSING_CHARGE = 9; // 9% processing fee for International payments
const GST_RATE = 18; // GST rate for India
const IGST_RATE = 18; // IGST rate for India
const CGST_RATE = 9; // CGST rate for India
const SGST_RATE = 9; // SGST rate for India
const GROUP_DISCOUNT_RATE = 10; // 10% group discount for 4+ delegates
const GROUP_DISCOUNT_MIN_DELEGATES = 4; // Minimum delegates required for group discount
const SOC_LINKEDIN = 'https://in.linkedin.com/company/bengaluru-tech-summit';
const SOC_TWITTER = 'https://twitter.com/blrtechsummit';
const SOC_FACEBOOK = 'https://www.facebook.com/BengaluruTechSummit';
const SOC_INSTAGRAM = 'https://www.instagram.com/blrtechsummit/';
const SOC_YOUTUBE = 'https://www.youtube.com/@bengalurutechsummit/streams';
const ORGANIZER_NAME = 'MM Activ Sci-Tech Communications PVT LTD';
define('ORGANIZER_ADDRESS', !empty($_ENV['ORGANIZER_ADDRESS'])
    ? $_ENV['ORGANIZER_ADDRESS']
    : 'No.11/3, NITON, Block C, 2nd Floor, Palace Road, <br>Bengaluru - 560001, Karnataka, India');
const FAVICON_APPLE = 'https://www.bengalurutechsummit.com/apple-touch-icon.png';
const FAVICON = 'https://www.bengalurutechsummit.com/favicon-32x32.png';
const FAVICON_16 = 'https://www.bengalurutechsummit.com/favicon-16x16.png';
const ORGANIZER_PHONE = '+91-8069328400';
const ORGANIZER_EMAIL = 'enquiry@bengalurutechsummit.com';
const ORGANIZER_WEBSITE = 'https://mmactiv.in/';
const ORGANIZER_LOGO = 'https://www.mmactiv.in/images/mma.jpg';
const EVENT_LOGO = 'https://bengalurutechsummit.com/web/it_forms/images/logo2026.png';
const EVENT_FAVICON = 'https://www.bengalurutechsummit.com/favicon-16x16.png';

const EXTRA_REQUIREMENTS_ACTIVE = false; //true or false

const SEND_CREDENTIALS_ON_REGISTRATION = false; // Set to true to send credentials email immediately on registration, false to disable
const SEND_CREDENTIALS_AFTER_PAYMENT = false; // Set to true to send credentials email after payment is successful, false to disable

const LATE_REGISTRATION_DEADLINE = '2026-11-19'; // last

//custom registration link for exhibitors
const EXHIBITOR_REGISTRATION_LINK = 'https://www.bengalurutechsummit.com/web/it_forms/enquiry.php';

const GUIDE_LINK = 'https://bengalurutechsummit.com/pdf/BTS-Exhibitor-Portal-Guide.pdf';

const GST_API_URL = 'https://my.gstzen.in/api/gstin-validator/';
const GST_API_KEY = '5479841c-b3ff-42ba-90bf-cb9866f52321';

const TICKET_REGISTRATION_LINK = 'https://bengalurutechsummit.com/conference.php#delegate-tariff';

//define("DEFAULT_REGISTRATION_LINK", route('register.form'));
const DEFAULT_REGISTRATION_LINK = null;
const PAYPAL_MODE = "live";
const PAYPAL_CLIENT_ID = "Acv2dOnqohq3Ty2yKnWFaHsjWEEo9jF6KlzXDoNfgFcG7ZZ8z1wBc2CRy2JRfz28HYPKE-zC3rw6Jag3";
const PAYPAL_SECRET = "EErD4CPPTPWzpUFSU4mNlZrLgF_ZlradgkfFLrk9B875BooSGI7_ElX1uiMvYC7MPXKPsDk3Q8_GtgqE";
const PAYPAL_SANDBOX_CLIENT_ID = "AdBqjyTeEI9u0lPQpVnXsJsc5YYVzKYNGcWz3DWVSY8N8j9Yugu8x6_XYr0h9ITzmP-G_kZ1TSVyZzEp";
const PAYPAL_SANDBOX_SECRET = "EKsqobP6xNcMNyPNQIS-XBCS0KMak5Wym_AehrFtHSnLvrNWPRXUJtCeTrEnunrSUCti3lKqV3zN-ERf";
const EMAILER_HEADER_LOGO = APP_URL . "/mails/emailer-header.png";
return [
    'EMAILER_HEADER_LOGO' => EMAILER_HEADER_LOGO,
    'TICKET_REGISTRATION_LINK' => TICKET_REGISTRATION_LINK,
    // Feature toggles
    'RECAPTCHA_ENABLED' => true, // Set true to enable Google reCAPTCHA on startup zone form
    'EVENT_NAME' => EVENT_NAME,
    'event_name' => EVENT_NAME,
    'EVENT_YEAR' => EVENT_YEAR,
    'SHORT_NAME' => SHORT_NAME,
    'EVENT_DATE_START' => EVENT_DATE_START,
    'EVENT_DATE_END' => EVENT_DATE_END,
    'EVENT_VENUE' => EVENT_VENUE,
    'EVENT_WEBSITE' => EVENT_WEBSITE,
    'APP_URL' => APP_URL,
    'APP_NAME' => APP_NAME,
    'APP_NAME_SHORT' => SHORT_NAME,
    'APPLICATION_ID_PREFIX' => 'TIN-BTS-2026-EXH-',
    'SPONSORSHIP_ID_PREFIX' => 'TIN-BTS-2026-SPONSOR-',
    'TIN_NO_PREFIX' => TIN_NO_PREFIX,
    'PIN_NO_PREFIX' => PIN_NO_PREFIX,
    'TICKET_ORDER_PREFIX' => 'TIN-' . SHORT_NAME . '-' . EVENT_YEAR . '-TKT-',
    'COMPLIMENTARY_REG_ID_PREFIX' => 'TIN-' . SHORT_NAME . EVENT_YEAR . '-EXHC',
    'CONFIRMATION_ID_PREFIX_EXH' => 'PIN-' . SHORT_NAME . EVENT_YEAR . '-EXHC',
    'DELEGATE_ID_PREFIX' => 'TIN-' . SHORT_NAME . EVENT_YEAR,
    'GUIDE_LINK' => GUIDE_LINK,

    'LATE_REGISTRATION_DEADLINE' => LATE_REGISTRATION_DEADLINE,
    'EXHIBITOR_REGISTRATION_LINK' => EXHIBITOR_REGISTRATION_LINK,
    'DEFAULT_REGISTRATION_LINK' => DEFAULT_REGISTRATION_LINK ?: EXHIBITOR_REGISTRATION_LINK,
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
    'EVENT_FAVICON' => EVENT_FAVICON,
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
    'PAYPAL_MODE' => PAYPAL_MODE, // Can be either 'sandbox' or 'live' - Set to 'sandbox' for testing
    'PAYPAL_CURRENCY' => "USD",
    
    // PayPal Sandbox Credentials (for testing) - REST API (Client ID & Secret)
    // NOTE: These are DIFFERENT from NVP/SOAP credentials (Username/Password/Signature)
    // To get REST API credentials:
    // 1. Go to https://developer.paypal.com/
    // 2. Log in and go to Dashboard -> My Apps & Credentials
    // 3. Under "Sandbox" tab, click "Create App" or use existing app
    // 4. Copy the "Client ID" and "Secret" (these are REST API credentials)
    // 5. NVP/SOAP credentials (Username/Password/Signature) will NOT work with this system
    'PAYPAL_SANDBOX_CLIENT_ID' => env('PAYPAL_SANDBOX_CLIENT_ID', 'AdBqjyTeEI9u0lPQpVnXsJsc5YYVzKYNGcWz3DWVSY8N8j9Yugu8x6_XYr0h9ITzmP-G_kZ1TSVyZzEp'),
    'PAYPAL_SANDBOX_SECRET' => env('PAYPAL_SANDBOX_SECRET', 'EKsqobP6xNcMNyPNQIS-XBCS0KMak5Wym_AehrFtHSnLvrNWPRXUJtCeTrEnunrSUCti3lKqV3zN-ERf'),
    
    // Temporary: If you don't have sandbox credentials yet, you can use live credentials here for testing
    // BUT: Live credentials will NOT work in sandbox environment - you must get proper sandbox credentials
    // 'PAYPAL_SANDBOX_CLIENT_ID' => env('PAYPAL_SANDBOX_CLIENT_ID', 'YOUR_SANDBOX_CLIENT_ID_HERE'),
    // 'PAYPAL_SANDBOX_SECRET' => env('PAYPAL_SANDBOX_SECRET', 'YOUR_SANDBOX_SECRET_HERE'),
    
    // PayPal Live Credentials (for production)
    // If env variable is empty, use the default value
    'PAYPAL_LIVE_CLIENT_ID' => PAYPAL_CLIENT_ID,
    'PAYPAL_LIVE_SECRET' => PAYPAL_SECRET,
    // Legacy support (will use mode-specific credentials)
    'PAYPAL_CLIENT_ID' => PAYPAL_CLIENT_ID, // Deprecated - use PAYPAL_SANDBOX_CLIENT_ID or PAYPAL_LIVE_CLIENT_ID
    'PAYPAL_SECRET' => PAYPAL_SECRET, // Deprecated - use PAYPAL_SANDBOX_SECRET or PAYPAL_LIVE_SECRET
    'INT_PROCESSING_CHARGE' => INT_PROCESSING_CHARGE, // 9% processing fee for International payments
    'EXTRA_REQUIREMENTS_ACTIVE' => EXTRA_REQUIREMENTS_ACTIVE,
    // CCAVENUE Configuration
    'CCAVENUE_ACCESS_CODE' => "AVJS71ME17AS68SJSA",
    'CCAVENUE_WORKING_KEY' => "7AF39D44C8DC0DE71EDD69C288C96694",
    'CCAVENUE_MERCHANT_ID' => "7700",
    'CCAVENUE_REDIRECT_URL' => APP_URL . "/payment/ccavenue-success",
    'ccavenue' => [
        'environment' => env('CCAVENUE_ENV', 'production'), // 'test' or 'production'
        'test' => [
            'merchant_id' => env('CCAVENUE_TEST_MERCHANT_ID', ''),
            'access_code' => env('CCAVENUE_TEST_ACCESS_CODE', ''),
            'working_key' => env('CCAVENUE_TEST_WORKING_KEY', ''),
            'api_url' => 'https://apitest.ccavenue.com/apis/servlet/DoWebTrans',
        ],
        'production' => [
            'merchant_id' => env('CCAVENUE_MERCHANT_ID', '7700'),
            'access_code' => env('CCAVENUE_ACCESS_CODE', 'AVAX60MC26BE01XAEB'),
            'working_key' => env('CCAVENUE_WORKING_KEY', 'DBBE266B02508AF7118D4A2598763D69'),
            'api_url' => 'https://api.ccavenue.com/apis/servlet/DoWebTrans',
        ],
        'webhook_url' => env('APP_URL', APP_URL) . '/ccavenue/webhook',
    ],
    'IND_PROCESSING_CHARGE' => IND_PROCESSING_CHARGE, // 3% processing fee for National payments
    'GSTIN' => '29AABCM2615H1ZM',
    'GST_STATE' => 'Karnataka',
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
    'hosted_url' => APP_URL,
    'HOSTED_URL' => APP_URL,
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
    // Registration-specific admin email recipients (emails sent individually to each)
    'registration_emails' => [
        // Startup Zone registration emails
        'startup' => [
            'test.interlinks@gmail.com',
            'chandrachood.as@mmactiv.com',

            // Add more emails as needed
        ],
        // Exhibitor registration emails  
        'exhibitor' => [
            'test.interlinks@gmail.com',
            'ambika.kiran@mmactiv.com',
            'gurunath.angadi@mmactiv.com',
            // Add more emails as needed
        ],
        // Delegate/Ticket registration emails
        'delegate' => [
            'test.interlinks@gmail.com',
            'accounts@mmactiv.com',
            'bhavya.n@mmactiv.com',
            'vani.faustina@mmactiv.com',

            // Add more emails as needed
        ],
        // Poster registration emails
        'poster' => [
            'test.interlinks@gmail.com',
            'prabha.j@mmactiv.com',

            // Add more emails as needed
        ],
    ],
    
    'db_connection2' => [
        'DB_HOST' => env('DB_HOST1', '95.216.2.164'),
        'DB_PORT' => env('DB_PORT1', '3306'),
        'DB_DATABASE' => env('DB_DATABASE1', 'btsblnl265_asd1d_bengaluruite'),
        'DB_USERNAME' => env('DB_USERNAME1', 'btsblnl265_asd1d_bengaluruite'),
        'DB_PASSWORD' => env('DB_PASSWORD1', 'Disl#vhfj#Af#DhW65'),
    ],
    'GST_API_URL' => GST_API_URL,
    'GST_API_KEY' => GST_API_KEY,
];
