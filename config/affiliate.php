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
        20_000 => [
            'label' => 'Silver',
            'benefit' => '10,00,000 Tk Cash',
        ],
        40_000 => [
            'label' => 'Gold',
            'benefit' => '46,00,000 Tk Car',
        ],
        80_000 => [
            'label' => 'Diamon',
            'benefit' => '80,00,000 Tk Flat',
        ],
    ],

];
