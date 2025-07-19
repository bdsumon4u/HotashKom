@php
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
        $shopName = $user && $user->shop_name ? $user->shop_name : null;
        $resellerInfo = $user ? ($resellerData[$user->id] ?? null) : null;
        $isResellerConnected = $resellerInfo && $resellerInfo['connected'];

        if ($isResellerConnected) {
            $resellerCompany = $resellerInfo['company'];
            $resellerLogo = $resellerInfo['logo'];

            $companyName = $shopName ?? ($resellerCompany->name ?? ($company->name ?? ''));

            // Logo with proper URL construction
            $logoUrl = null;
            if (isset($resellerLogo->mobile)) {
                $resellerDomain = $user->domain ?? '';
                if ($resellerDomain && !str_starts_with($resellerDomain, 'http')) {
                    $appUrl = config('app.url');
                    $scheme = parse_url($appUrl, PHP_URL_SCHEME) ?: 'https';
                    $resellerDomain = $scheme . '://' . $resellerDomain;
                }
                $logoUrl = $resellerDomain . $resellerLogo->mobile;
            } else {
                $hasLogo = $user && $user->logo;
                $logoUrl = $hasLogo ? asset('storage/' . $user->logo) : (isset($logo->mobile) ? asset($logo->mobile) : null);
            }

            $phoneNumber = ($resellerCompany->phone ?? null) ?: (($user && $user->phone_number) ? $user->phone_number : ($company->phone ?? ''));
            $address = ($resellerCompany->address ?? null) ?: (($user && $user->address) ? $user->address : ($company->address ?? ''));
        } else {
            $companyName = $shopName ?? ($company->name ?? '');
            $hasLogo = $user && $user->logo;
            $logoUrl = $hasLogo ? asset('storage/' . $user->logo) : (isset($logo->mobile) && !$shopName ? asset($logo->mobile) : null);
            $phoneNumber = ($user && $user->phone_number) ? $user->phone_number : ($company->phone ?? '');
            $address = ($user && $user->address) ? $user->address : ($company->address ?? '');
        }

        // For resellers_invoice true, sender info is same as header info
        $senderName = $companyName;
        $senderPhone = $phoneNumber;
        $senderAddress = $address;
    }
@endphp
