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

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'rajaongkir' => [
        'key' => env('RAJAONGKIR_API_KEY', ''),
        'base_url' => env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),
        'origin_city_id' => env('RAJAONGKIR_ORIGIN_CITY_ID', 501),
        'default_courier' => env('RAJAONGKIR_DEFAULT_COURIER', 'jne'),
        'fallback_cost' => env('RAJAONGKIR_FALLBACK_COST', 20000),
        'default_item_weight' => env('RAJAONGKIR_DEFAULT_ITEM_WEIGHT', 1000),
        'admin_fee' => env('CHECKOUT_ADMIN_FEE', 5000),
    ],
];
