<?php 

return [
    'mode'    => env('PAYPAL_MODE', 'sandbox'),
    'sandbox' => [
        'client_id'  => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_SECRET'),
    ],
    'live' => [
        'client_id'  => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_SECRET'),
    ],
    'payment_action' => 'Sale', // Add this line
    'currency'       => env('PAYPAL_CURRENCY', 'USD'),
    'locale'         => 'en_IN',
    'notify_url'    => env('APP_URL').'/paypal/notify',
    'validate_ssl'   => true,
];
