@php
    $siteUrl = rtrim(config('app.url'), '/');

    $organizationId = $siteUrl . '/#organization';
    $websiteId = $siteUrl . '/#website';
    $webpageId = $siteUrl . '/#webpage';
    $logoId = $siteUrl . '/#logo';

    $brandName = data_get(setting('company'), 'name') ?: config('app.name');
    $brandEmail = data_get(setting('company'), 'email') ?: ('support@' . parse_url($siteUrl, PHP_URL_HOST));
    $brandPhone = data_get(setting('company'), 'phone') ?: '';
    $brandAddress = data_get(setting('company'), 'address') ?: '';
    $fbUrl = data_get(setting('social'), 'facebook') ?: null;
    $logoUrl = file_exists(public_path('performance/images/hk-logo-640.webp'))
        ? asset('performance/images/hk-logo-640.webp')
        : (data_get(setting('logo'), 'desktop') ? asset(data_get(setting('logo'), 'desktop')) : null);

    $organization = [
        '@type' => 'OnlineStore',
        '@id' => $organizationId,
        'name' => $brandName,
        'url' => $siteUrl . '/',
        'areaServed' => [
            '@type' => 'Country',
            'name' => 'Bangladesh',
        ],
        'acceptedPaymentMethod' => 'Cash on Delivery',
    ];

    if ($logoUrl) {
        $organization['logo'] = [
            '@type' => 'ImageObject',
            '@id' => $logoId,
            'url' => $logoUrl,
            'contentUrl' => $logoUrl,
            'caption' => $brandName,
        ];
        $organization['image'] = [
            '@id' => $logoId,
        ];
    }

    if ($brandEmail) {
        $organization['email'] = $brandEmail;
    }

    if ($brandPhone) {
        $organization['telephone'] = $brandPhone;
        $organization['contactPoint'] = [
            '@type' => 'ContactPoint',
            'telephone' => $brandPhone,
            'email' => $brandEmail ?: ('support@' . parse_url($siteUrl, PHP_URL_HOST)),
            'contactType' => 'customer service',
            'areaServed' => 'BD',
            'availableLanguage' => [
                'Bangla',
                'English',
            ],
        ];
    }

    if ($brandAddress) {
        $organization['address'] = [
            '@type' => 'PostalAddress',
            'streetAddress' => $brandAddress,
            'addressCountry' => 'BD',
        ];
    }

    if ($fbUrl) {
        $organization['sameAs'] = [$fbUrl];
    }

    $homeSchema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            $organization,
            [
                '@type' => 'WebSite',
                '@id' => $websiteId,
                'url' => $siteUrl . '/',
                'name' => $brandName,
                'publisher' => [
                    '@id' => $organizationId,
                ],
                'inLanguage' => [
                    'en',
                    'bn',
                ],
            ],
            [
                '@type' => 'WebPage',
                '@id' => $webpageId,
                'url' => $siteUrl . '/',
                'name' => 'Trusted Online Shopping Store in Bangladesh | ' . $brandName,
                'isPartOf' => [
                    '@id' => $websiteId,
                ],
                'about' => [
                    '@id' => $organizationId,
                ],
                'publisher' => [
                    '@id' => $organizationId,
                ],
                'inLanguage' => [
                    'en',
                    'bn',
                ],
            ],
        ],
    ];
@endphp

<script type="application/ld+json">{!! json_encode(
    $homeSchema,
    JSON_UNESCAPED_SLASHES
    | JSON_UNESCAPED_UNICODE
    | JSON_HEX_TAG
    | JSON_HEX_AMP
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
) !!}</script>
