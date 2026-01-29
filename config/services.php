<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'help_tool' => [
    'endpoint' => env('HELP_TOOL_ENDPOINT'),
    // 'username' => env('HELP_TOOL_USERNAME'),
    // 'password' => env('HELP_TOOL_PASSWORD'),
    'X-API-KEY' => env('HELP_TOOL_API_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'recaptcha' => [
        'site_key'    => env('RECAPTCHA_SITE_KEY'),
        // For legacy v2 usage (kept for backwards compatibility, not used in Enterprise flow)
        'secret_key'  => env('RECAPTCHA_SECRET_KEY'),
        // reCAPTCHA Enterprise / v3 server-side verification
        'project_id'  => env('RECAPTCHA_PROJECT_ID'),
        'api_key'     => env('RECAPTCHA_API_KEY'),
    ],

];
