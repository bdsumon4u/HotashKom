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

    'services' => [
        'one' => 'strokya/images/sprite.svg#check-12x9',
        'two' => 'strokya/images/sprite.svg#fi-24-hours-48',
        'three' => 'strokya/images/sprite.svg#fi-payment-security-48',
        'four' => 'strokya/images/sprite.svg#fi-tag-48',
    ],

    'shipping' => [
        'Inside Dhaka' => 60,
        'Outside Dhaka' => 100,
    ],

    'stdfst' => [
        'key' => env('STEADFAST_KEY'),
        'secret' => env('STEADFAST_SECRET'),
    ],

    'logo' => [
        'desktop' => [
            'width' => 260,
            'height' => 54,
        ],
        'mobile' => [
            'width' => 150,
            'height' => 40,
        ],
        'favicon' => [
            'width' => 56,
            'height' => 56,
        ],
    ],

    'products_count' => [
        'related' => 20,
    ],

    'slides' => [
        'mobile' => [360, 180],
        'desktop' => [1125, 395],
    ],

    'bdwebs' => [
        'api_key' => env('BDWEBS_API_KEY'),
        'senderid' => env('BDWEBS_SENDERID'),
    ],

    'courier_report' => [
        'cheap' => env('CHEAP_COURIER_REPORT'),
        'url' => env('COURIER_REPORT_URL'),
        'key' => env('COURIER_REPORT_KEY'),
        'expires' => env('COURIER_EXPIRES'),
    ],

    'imagekit' => [
        'username' => env('IMAGEKIT_USERNAME'),
    ],

    'cloudinary' => [
        'username' => env('CLOUDINARY_USERNAME'),
    ],

    'gumlet' => [
        'username' => env('GUMLET_USERNAME'),
    ],

    'facebook' => [
        'access_token' => env('FACEBOOK_ACCESS_TOKEN'),
        'test_event_code' => env('FACEBOOK_TEST_EVENT_CODE'),
    ],
];
