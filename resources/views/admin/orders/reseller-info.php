<?php

$user = $order->user;
$isWalkIn = $user && $user->email === 'walkin@hotash.tech';
$isResellerInvoice = isOninda() && (setting('show_option')->resellers_invoice ?? false);

// Helper function to get fallback value
$getFallback = (fn ($value, $fallback) => $value ?: $fallback);

// Helper function to get user or company value
$getUserOrCompany = (fn ($userField, $companyField) => ($user && $user->$userField) ? $user->$userField : ($company->$companyField ?? ''));

// For walk-in users or non-reseller invoices, use current platform settings
if ($isWalkIn || ! $isResellerInvoice) {
    $companyName = $company->name ?? '';
    $logoUrl = isset($logo->mobile) ? asset($logo->mobile) : null;
    $phoneNumber = $company->phone ?? '';
    $address = $company->address ?? '';

    // Sender info for non-reseller invoices
    $senderName = $getUserOrCompany('shop_name', 'name');
    $senderPhone = $getUserOrCompany('phone_number', 'phone');
    $senderAddress = $getUserOrCompany('address', 'address');
} else {
    // Oninda app with resellers_invoice enabled
    $resellerInfo = $user ? ($resellerData[$user->id] ?? null) : null;
    $isResellerConnected = $resellerInfo && $resellerInfo['connected'];

    if ($isResellerConnected) {
        $resellerCompany = $resellerInfo['company'];
        $resellerLogo = $resellerInfo['logo'];

        $companyName = $getUserOrCompany('shop_name', 'name');
        if (! $user || ! $user->shop_name) {
            $companyName = $resellerCompany->name ?? $companyName;
        }

        // Logo with proper URL construction
        if (isset($resellerLogo->mobile)) {
            $domain = $user->domain ?? '';
            if ($domain && ! str_starts_with((string) $domain, 'http')) {
                $domain = (parse_url((string) config('app.url'), PHP_URL_SCHEME) ?: 'https').'://'.$domain;
            }
            $logoUrl = $domain.$resellerLogo->mobile;
        } else {
            $logoUrl = $user?->logo_url ?: ($getUserOrCompany('logo', 'mobile') ?: (isset($logo->mobile) ? asset($logo->mobile) : null));
        }

        $phoneNumber = $getFallback($resellerCompany->phone ?? null, $getUserOrCompany('phone_number', 'phone'));
        $address = $getFallback($resellerCompany->address ?? null, $getUserOrCompany('address', 'address'));
    } else {
        $companyName = $getUserOrCompany('shop_name', 'name');
        $logoUrl = $user?->logo_url ?: ($getUserOrCompany('logo', 'mobile') ?: (isset($logo->mobile) && ! ($user && $user->shop_name) ? asset($logo->mobile) : null));
        $phoneNumber = $getUserOrCompany('phone_number', 'phone');
        $address = $getUserOrCompany('address', 'address');
    }

    // For resellers_invoice true, sender info is same as header info
    $senderName = $companyName;
    $senderPhone = $phoneNumber;
    $senderAddress = $address;
}
