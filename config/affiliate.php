<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dual-Leg Matching Commission Tiers
    |--------------------------------------------------------------------------
    |
    | When one leg reaches exactly 'x' successful referrals and the counter leg
    | has >= 'x' successful referrals, commission is calculated as:
    | z% of (2 * x * config('app.verification_fee'))
    |
    */
    'tiers' => [
        1 => [
            'percentage' => 25,
        ],
        2 => [
            'percentage' => 10,
        ],
        8 => [
            'percentage' => 5,
        ],
        24 => [
            'percentage' => 3,
        ],
        48 => [
            'percentage' => 2,
        ],
        100 => [
            'percentage' => 2,
        ],
        500 => [
            'percentage' => 3,
        ],
    ],

];
