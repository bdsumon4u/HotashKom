@php
    $siteUrl = rtrim(config('app.url'), '/');
    $currentPath = trim(request()->path(), '/');
    $pageUrl = $siteUrl . '/' . $currentPath;

    $brandName = data_get(setting('company'), 'name') ?: config('app.name');
    $brandEmail = data_get(setting('company'), 'email') ?: ('support@' . parse_url($siteUrl, PHP_URL_HOST));
    $brandPhone = data_get(setting('company'), 'phone') ?: '';
    $brandAddress = data_get(setting('company'), 'address') ?: '';
    $fbUrl = data_get(setting('social'), 'facebook') ?: null;
    $logoUrl = file_exists(public_path('performance/images/hk-logo-640.webp'))
        ? asset('performance/images/hk-logo-640.webp')
        : (data_get(setting('logo'), 'desktop') ? asset(data_get(setting('logo'), 'desktop')) : null);

    $pageDefinitions = [
        'about-us' => [
            'type' => 'AboutPage',
            'name' => 'About Us',
            'description' => 'Learn about ' . $brandName . ', its customer-focused online shopping service and commitment to customers across Bangladesh.',
        ],
        'contact-us' => [
            'type' => 'ContactPage',
            'name' => 'Contact ' . $brandName,
            'description' => 'Contact ' . $brandName . ' for product, order, payment, delivery, return, refund and customer service assistance.',
        ],
        'terms-and-conditions' => [
            'type' => 'WebPage',
            'name' => 'Terms and Conditions',
            'description' => 'Read the terms and conditions for using ' . $brandName . ' and ordering products through the website.',
        ],
        'privacy-policy' => [
            'type' => 'WebPage',
            'name' => 'Privacy Policy',
            'description' => 'Read how ' . $brandName . ' collects, uses, stores and protects customer and website visitor information.',
        ],
        'disclaimer' => [
            'type' => 'WebPage',
            'name' => 'Disclaimer',
            'description' => 'Read important information and limitations regarding ' . $brandName . ' products, orders, website content and services.',
        ],
        'return-and-refund-policy' => [
            'type' => 'WebPage',
            'name' => 'Return and Refund Policy',
            'description' => 'Read the ' . $brandName . ' return, replacement and refund conditions, procedures and applicable timeframes.',
        ],
        'shipping-and-delivery-policy' => [
            'type' => 'WebPage',
            'name' => 'Shipping and Delivery Policy',
            'description' => 'Read ' . $brandName . ' order processing, delivery time, delivery charge and courier service information.',
        ],
    ];

    $definition = $pageDefinitions[$currentPath] ?? [
        'type' => 'WebPage',
        'name' => $page->title ?? 'Page',
        'description' => $page->meta_description ?? ('Information from ' . $brandName),
    ];

    $organizationId = $siteUrl . '/#organization';
    $websiteId = $siteUrl . '/#website';
    $logoId = $siteUrl . '/#logo';

    $webpageId = $pageUrl . '#webpage';
    $breadcrumbId = $pageUrl . '#breadcrumb';

    $organizationSchema = [
        '@type' => 'OnlineStore',
        '@id' => $organizationId,
        'name' => $brandName,
        'url' => $siteUrl . '/',
        'areaServed' => [
            '@type' => 'Country',
            'name' => 'Bangladesh',
        ],
    ];

    if ($logoUrl) {
        $organizationSchema['logo'] = [
            '@type' => 'ImageObject',
            '@id' => $logoId,
            'url' => $logoUrl,
            'contentUrl' => $logoUrl,
            'caption' => $brandName,
        ];
    }

    if ($brandEmail) {
        $organizationSchema['email'] = $brandEmail;
    }

    if ($brandPhone) {
        $organizationSchema['telephone'] = $brandPhone;
        $organizationSchema['contactPoint'] = [
            '@type' => 'ContactPoint',
            'telephone' => $brandPhone,
            'email' => $brandEmail,
            'contactType' => 'customer service',
            'areaServed' => 'BD',
            'availableLanguage' => [
                'Bangla',
                'English',
            ],
            'hoursAvailable' => [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => [
                    'https://schema.org/Saturday',
                    'https://schema.org/Sunday',
                    'https://schema.org/Monday',
                    'https://schema.org/Tuesday',
                    'https://schema.org/Wednesday',
                    'https://schema.org/Thursday',
                ],
                'opens' => '09:00',
                'closes' => '20:00',
            ],
        ];
    }

    if ($brandAddress) {
        $organizationSchema['address'] = [
            '@type' => 'PostalAddress',
            'streetAddress' => $brandAddress,
            'addressCountry' => 'BD',
        ];
    }

    if ($fbUrl) {
        $organizationSchema['sameAs'] = [$fbUrl];
    }

    $websiteSchema = [
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
    ];

    $breadcrumbSchema = [
        '@type' => 'BreadcrumbList',
        '@id' => $breadcrumbId,
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => $siteUrl . '/',
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $definition['name'],
                'item' => $pageUrl,
            ],
        ],
    ];

    $pageSchema = [
        '@type' => $definition['type'],
        '@id' => $webpageId,
        'url' => $pageUrl,
        'name' => $definition['name'],
        'description' => $definition['description'],
        'isPartOf' => [
            '@id' => $websiteId,
        ],
        'breadcrumb' => [
            '@id' => $breadcrumbId,
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
    ];

    if ($definition['type'] === 'ContactPage') {
        $pageSchema['mainEntity'] = [
            '@id' => $organizationId,
        ];
    }

    if (
        isset($page)
        && is_object($page)
        && isset($page->updated_at)
        && $page->updated_at
    ) {
        $pageSchema['dateModified'] = $page
            ->updated_at
            ->toIso8601String();
    }

    $staticPageSchema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            $organizationSchema,
            $websiteSchema,
            $pageSchema,
            $breadcrumbSchema,
        ],
    ];
@endphp

<script type="application/ld+json">{!! json_encode(
    $staticPageSchema,
    JSON_UNESCAPED_SLASHES
    | JSON_UNESCAPED_UNICODE
    | JSON_HEX_TAG
    | JSON_HEX_AMP
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
) !!}</script>