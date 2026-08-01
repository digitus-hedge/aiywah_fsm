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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

   'whatsapp' => [
    'token'           => env('WHATSAPP_ACCESS_TOKEN'),
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    'version'         => env('WHATSAPP_API_VERSION', 'v23.0'),

    // Role codes that receive internal SR alerts (see roles.code)
    'internal_role_codes' => ['SA', 'HP'],

    // Optional extras not tied to a user account — "Name:Phone" pairs,
    // comma-separated. e.g. WHATSAPP_INTERNAL_NUMBERS="Ops Desk:971501112233"
    'internal_recipients' => array_values(array_filter(array_map(
        function ($pair) {
            [$name, $phone] = array_pad(explode(':', trim($pair), 2), 2, null);
            return $phone
                ? ['name' => trim($name), 'phone' => preg_replace('/\D/', '', $phone)]
                : null;
        },
        explode(',', (string) env('WHATSAPP_INTERNAL_NUMBERS', ''))
    ))),
],

];
