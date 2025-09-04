<?php

return [
    'sandbox' => env('BKASH_SANDBOX', true),

    'credentials' => [
        'app_key' => env('BKASH_APP_KEY', ''),
        'app_secret' => env('BKASH_APP_SECRET', ''),
        'username' => env('BKASH_USERNAME', ''),
        'password' => env('BKASH_PASSWORD', ''),
    ],

    'sandbox_base_url' => env('SANDBOX_BASE_URL', 'https://tokenized.sandbox.bka.sh'),
    'live_base_url' => env('LIVE_BASE_URL', 'https://tokenized.pay.bka.sh'),

    'version' => 'v1.2.0-beta',

    'cache' => [
        'token_lifetime' => 3600,
    ],

    'default_currency' => 'BDT',
    'default_intent' => 'sale',

    'redirect_urls' => [
        'success' => null, // We'll handle redirects in our callback
        'failed' => null, // We'll handle redirects in our callback
    ],

    'routes' => [
        'enabled' => true, // Enable package routes for callback handling
        'prefix' => 'bkash',
        'middleware' => ['web'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Event Configuration
    |--------------------------------------------------------------------------
    | Configure whether to fire events on successful payments
    */
    'events' => [
        'payment_success' => env('BKASH_FIRE_PAYMENT_SUCCESS_EVENT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    */
    'database' => [
        'table_prefix' => env('BKASH_TABLE_PREFIX', 'bkash_'),
    ],
];
