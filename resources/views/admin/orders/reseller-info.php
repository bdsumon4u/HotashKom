<?php
    $user = $order->user;

    if (!isOninda() || !(setting('show_option')->resellers_invoice ?? false)) {
        // Not Oninda app OR resellers_invoice is false - use current website's settings
        $companyName = $company->name ?? '';
        $logoUrl = isset($logo->mobile) ? asset($logo->mobile) : null;
        $phoneNumber = $company->phone ?? '';
        $address = $company->address ?? '';

        // Sender info for non-reseller invoices
        $senderName = ($user && $user->shop_name) ? $user->shop_name : ($company->name ?? '');
        $senderPhone = ($user && $user->phone_number) ? $user->phone_number : ($company->phone ?? '');
        $senderAddress = ($user && $user->address) ? $user->address : ($company->address ?? '');
    } else {
        // Oninda app with resellers_invoice enabled
        $resellerInfo = $user ? ($resellerData[$user->id] ?? null) : null;
        $isResellerConnected = $resellerInfo && $resellerInfo['connected'];

        if ($isResellerConnected) {
            $resellerCompany = $resellerInfo['company'];
            $resellerLogo = $resellerInfo['logo'];

            $companyName = ($user && $user->shop_name) ? $user->shop_name : ($resellerCompany->name ?? ($company->name ?? ''));

            // Logo with proper URL construction
            if (isset($resellerLogo->mobile)) {
                $domain = $user->domain ?? '';
                if ($domain && !str_starts_with($domain, 'http')) {
                    $domain = (parse_url(config('app.url'), PHP_URL_SCHEME) ?: 'https') . '://' . $domain;
                }
                $logoUrl = $domain . $resellerLogo->mobile;
            } else {
                $logoUrl = ($user && $user->logo) ? asset('storage/' . $user->logo) : (isset($logo->mobile) ? asset($logo->mobile) : null);
            }

            $phoneNumber = ($resellerCompany->phone ?? null) ?: (($user && $user->phone_number) ? $user->phone_number : ($company->phone ?? ''));
            $address = ($resellerCompany->address ?? null) ?: (($user && $user->address) ? $user->address : ($company->address ?? ''));
        } else {
            $companyName = ($user && $user->shop_name) ? $user->shop_name : ($company->name ?? '');
            $logoUrl = ($user && $user->logo) ? asset('storage/' . $user->logo) : (isset($logo->mobile) && !($user && $user->shop_name) ? asset($logo->mobile) : null);
            $phoneNumber = ($user && $user->phone_number) ? $user->phone_number : ($company->phone ?? '');
            $address = ($user && $user->address) ? $user->address : ($company->address ?? '');
        }

        // For resellers_invoice true, sender info is same as header info
        $senderName = $companyName;
        $senderPhone = $phoneNumber;
        $senderAddress = $address;
    }
?>
