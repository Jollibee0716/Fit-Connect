
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Store configuration for external services such as
    | email providers, notification services, and payment gateways.
    |
    */

    // Postmark
    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    // Resend
    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    // Amazon SES
    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Slack Notifications
    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Brevo Email Service
    'brevo' => [
        'key' => env('BREVO_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Xendit Payment Gateway
    |--------------------------------------------------------------------------
    |
    | Used for FitConnect product orders and membership payments.
    | Keep all secret keys in the .env file.
    |
    */

    'xendit' => [
        'secret_key' => env('XENDIT_SECRET_KEY'),

        'api_url' => env(
            'XENDIT_API_URL',
            'https://api.xendit.co'
        ),

        'webhook_token' => env('XENDIT_WEBHOOK_TOKEN'),
    ],

];
