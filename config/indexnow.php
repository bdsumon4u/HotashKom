<?php

return [
    'enabled' => env('INDEXNOW_ENABLED', true),

    'key' => env('INDEXNOW_KEY'),

    'endpoint' => env(
        'INDEXNOW_ENDPOINT',
        'https://api.indexnow.org/indexnow'
    ),
];
