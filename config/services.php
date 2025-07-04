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

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'pay_gateway' => [
        'monnify' => [
            'api_key' => env("MONNIFY_API_KEY"),
            'api_secret' => env("MONNIFY_API_SECRET"),
            'api_url' => env("MONNIFY_API_URL"),
            'contract_code' => env("MONNIFY_CONTRACT_CODE")
        ]
    ],
    'utils' => [
        'pagination_count' => env("PAGINATION_COUNT", 100)
    ],
    'vtu' => [
        'gsubz' => [
            'app_code' => env("GSUBZ_APP_CODE"),
            'api_url' => env("GSUBZ_API_URL"),
            'api_key' => env("GSUBZ_API_KEY"),
            'widget_key' => env("GSUBZ_WIDGET_KEY")
        ]
    ]
];
