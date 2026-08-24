@php
    $siteUrl = rtrim(config('app.url'), '/');
    $productUrl = route('products.show', $product);

    $organizationId = $siteUrl . '/#organization';
    $websiteId = $siteUrl . '/#website';
    $logoId = $siteUrl . '/#logo';

    $webpageId = $productUrl . '#webpage';
    $productId = $productUrl . '#product';
    $breadcrumbId = $productUrl . '#breadcrumb';

    $primaryCategory = $product->categories->first();

    $rawDescription = filled($product->short_description)
        ? $product->short_description
        : $product->description;

    $productDescription = trim(
        preg_replace(
            '/\s+/u',
            ' ',
            html_entity_decode(
                strip_tags((string) $rawDescription),
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            )
        )
    );

    if ($productDescription === '') {
        $productDescription = $product->name;
    }

    $productImages = collect($product->images ?? [])
        ->push($product->base_image)
        ->filter(
            fn ($image): bool =>
                $image !== null
                && filled($image->src ?? null)
        )
        ->map(
            fn ($image): string => asset($image->src)
        )
        ->unique()
        ->values();

    $productPrice = (float) $product->selling_price;

    $productIsInStock = ! (bool) $product->should_track
        || (int) $product->stock_count > 0;

    $approvedReviewData = collect($product->reviews ?? [])
        ->filter(
            fn ($review): bool => (bool) $review->approved
        )
        ->map(function ($review): ?array {
            $ratingValues = collect($review->ratings ?? [])
                ->pluck('value')
                ->filter(function ($value): bool {
                    return is_numeric($value)
                        && (float) $value >= 1
                        && (float) $value <= 5;
                })
                ->map(
                    fn ($value): float => (float) $value
                );

            if ($ratingValues->isEmpty()) {
                return null;
            }

            return [
                'rating' => round(
                    (float) $ratingValues->average(),
                    2
                ),
                'author' => trim(
                    (string) ($review->user?->name ?? '')
                ),
                'body' => trim(
                    preg_replace(
                        '/\s+/u',
                        ' ',
                        strip_tags((string) $review->review)
                    )
                ),
                'date' => $review->created_at,
            ];
        })
        ->filter()
        ->values();

    $brandName = data_get(setting('company'), 'name') ?: config('app.name');
    $brandEmail = data_get(setting('company'), 'email') ?: ('support@' . parse_url($siteUrl, PHP_URL_HOST));
    $brandPhone = data_get(setting('company'), 'phone') ?: '';
    $logoUrl = file_exists(public_path('performance/images/hk-logo-640.webp'))
        ? asset('performance/images/hk-logo-640.webp')
        : (data_get(setting('logo'), 'desktop') ? asset(data_get(setting('logo'), 'desktop')) : null);

    $organizationSchema = [
        '@type' => 'OnlineStore',
        '@id' => $organizationId,
        'name' => $brandName,
        'url' => $siteUrl . '/',
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

    if ($brandPhone) {
        $organizationSchema['telephone'] = $brandPhone;
    }

    if ($brandEmail) {
        $organizationSchema['email'] = $brandEmail;
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

    $breadcrumbItems = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => $siteUrl . '/',
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Products',
            'item' => route('products.index'),
        ],
    ];

    if ($primaryCategory) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $primaryCategory->name,
            'item' => route(
                'categories.products',
                $primaryCategory
            ),
        ];
    }

    $breadcrumbItems[] = [
        '@type' => 'ListItem',
        'position' => count($breadcrumbItems) + 1,
        'name' => $product->name,
        'item' => $productUrl,
    ];

    $breadcrumbSchema = [
        '@type' => 'BreadcrumbList',
        '@id' => $breadcrumbId,
        'itemListElement' => $breadcrumbItems,
    ];

    $webpageSchema = [
        '@type' => 'WebPage',
        '@id' => $webpageId,
        'url' => $productUrl,
        'name' => $product->name,
        'description' => $productDescription,
        'isPartOf' => [
            '@id' => $websiteId,
        ],
        'breadcrumb' => [
            '@id' => $breadcrumbId,
        ],
        'mainEntity' => [
            '@id' => $productId,
        ],
        'publisher' => [
            '@id' => $organizationId,
        ],
        'inLanguage' => [
            'en',
            'bn',
        ],
    ];

    if ($productImages->isNotEmpty()) {
        $webpageSchema['primaryImageOfPage'] = [
            '@type' => 'ImageObject',
            'url' => $productImages->first(),
            'contentUrl' => $productImages->first(),
        ];
    }

    $productSchema = [
        '@type' => 'Product',
        '@id' => $productId,
        'url' => $productUrl,
        'name' => $product->name,
        'description' => $productDescription,
        'mainEntityOfPage' => [
            '@id' => $webpageId,
        ],
    ];

    if ($productImages->isNotEmpty()) {
        $productSchema['image'] = $productImages->all();
    }

    if (filled($product->sku)) {
        $productSchema['sku'] = (string) $product->sku;
    }

    if ($product->brand) {
        $productSchema['brand'] = [
            '@type' => 'Brand',
            'name' => $product->brand->name,
        ];
    }

    if ($primaryCategory) {
        $productSchema['category'] = $primaryCategory->name;
    }

    if ($productPrice > 0) {
        $productSchema['offers'] = [
            '@type' => 'Offer',
            '@id' => $productUrl . '#offer',
            'url' => $productUrl,
            'priceCurrency' => 'BDT',
            'price' => number_format(
                $productPrice,
                2,
                '.',
                ''
            ),
            'availability' => $productIsInStock
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
            'seller' => [
                '@id' => $organizationId,
            ],
        ];
    }

    if ($approvedReviewData->isNotEmpty()) {
        $productSchema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => round(
                (float) $approvedReviewData->avg('rating'),
                2
            ),
            'reviewCount' => $approvedReviewData->count(),
            'ratingCount' => $approvedReviewData->count(),
            'bestRating' => 5,
            'worstRating' => 1,
        ];

        $reviewSchemas = $approvedReviewData
            ->filter(
                fn (array $review): bool =>
                    $review['author'] !== ''
            )
            ->map(function (array $review): array {
                $schema = [
                    '@type' => 'Review',
                    'author' => [
                        '@type' => 'Person',
                        'name' => $review['author'],
                    ],
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => $review['rating'],
                        'bestRating' => 5,
                        'worstRating' => 1,
                    ],
                ];

                if ($review['body'] !== '') {
                    $schema['reviewBody'] = $review['body'];
                }

                if ($review['date']) {
                    $schema['datePublished'] = $review['date']
                        ->toDateString();
                }

                return $schema;
            })
            ->values();

        if ($reviewSchemas->isNotEmpty()) {
            $productSchema['review'] = $reviewSchemas->all();
        }
    }

    $structuredData = [
        '@context' => 'https://schema.org',
        '@graph' => [
            $organizationSchema,
            $websiteSchema,
            $webpageSchema,
            $breadcrumbSchema,
            $productSchema,
        ],
    ];
@endphp

<script type="application/ld+json">{!! json_encode(
    $structuredData,
    JSON_UNESCAPED_SLASHES
    | JSON_UNESCAPED_UNICODE
    | JSON_HEX_TAG
    | JSON_HEX_AMP
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
) !!}</script>