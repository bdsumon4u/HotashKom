<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Investment Module Status
    |--------------------------------------------------------------------------
    |
    | When disabled (false), no investor routes are registered, no middleware
    | processes requests, and no scheduled jobs execute, ensuring zero
    | performance overhead on the application.
    |
    */
    'enabled' => (bool) env('INVESTMENT_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Investment Duration & Multiplier Settings
    |--------------------------------------------------------------------------
    |
    | Default investment term in months (36 months = 3 years) and return multiplier
    | (2.0 = 2x the invested amount).
    |
    */
    'duration_months' => (int) env('INVESTMENT_DURATION_MONTHS', 36),
    'multiplier' => (float) env('INVESTMENT_MULTIPLIER', 2.0),

    /*
    |--------------------------------------------------------------------------
    | Initial Investment Deduction & Referrer Bonus
    |--------------------------------------------------------------------------
    |
    | When an investor invests an amount X:
    | - initial_deduction_rate: Total percentage deducted immediately (e.g. 0% or 10%).
    | - initial_referrer_bonus_rate: Percentage that goes to the referrer's bonus
    |   account (e.g. 0% or 5%).
    | - The remaining portion (initial_deduction_rate - initial_referrer_bonus_rate)
    |   goes to the company account. If there is no referrer, the full deduction
    |   goes to the company account.
    |
    */
    'initial_deduction_rate' => (float) env('INVESTMENT_INITIAL_DEDUCTION_RATE', 0.0),
    'initial_referrer_bonus_rate' => (float) env('INVESTMENT_INITIAL_REFERRER_BONUS_RATE', 0.0),

    /*
    |--------------------------------------------------------------------------
    | Monthly Installment Deduction & Referrer Bonus
    |--------------------------------------------------------------------------
    |
    | On each monthly return installment (gross installment = 2X / 36):
    | - monthly_deduction_rate: Total percentage deducted from installment (e.g. 10%).
    | - monthly_referrer_bonus_rate: Percentage that goes to the referrer's bonus
    |   account (e.g. 5%).
    | - The remaining portion goes to the company account.
    | - The net amount (gross - deduction) is credited to the investor's balance.
    |
    */
    'monthly_deduction_rate' => (float) env('INVESTMENT_MONTHLY_DEDUCTION_RATE', 10.0),
    'monthly_referrer_bonus_rate' => (float) env('INVESTMENT_MONTHLY_REFERRER_BONUS_RATE', 5.0),
];
