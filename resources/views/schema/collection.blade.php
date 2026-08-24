@php
    $siteUrl = rtrim(config('app.url'), '/');
    $routeName = request()->route()?->getName();
    $pageNumber = max(
        1,
        (int) request()->query('page', 1)
    );

    $pageUrl = $siteUrl
        . '/'
        . ltrim(request()->path(), '/');

    if ($pageNumber > 1) {
        $pageUrl .= '?page=' . $pageNumber;
    }

    $organizationId = $siteUrl . '/#organization';
    $websiteId = $siteUrl . '/#website';
    $logoId = $siteUrl . '/#logo';

    $webpageId = $pageUrl . '#webpage';
    $breadcrumbId = $pageUrl . '#breadcrumb';
    $itemListId = $pageUrl . '#itemlist';

    $siteBrand = data_get(setting('company'), 'name') ?: config('app.name');
    $pageName = $siteBrand;
    $pageDescription = 'Browse products and helpful content from ' . $siteBrand . '.';
    $listingType = 'products';
    $listingSource = collect();

    $categoryModel = request()->route('category');
    $brandModel = request()->route('brand');

    if (request()->routeIs('blogs.index')) {
        $pageName = $siteBrand . ' Blog';
        $pageDescription = 'Read shopping guides, product tips and helpful articles from ' . $siteBrand . '.';
        $listingType = 'blogs';
        $listingSource = isset($blogs)
            ? $blogs
            : collect();
    } elseif (request()->routeIs('products.index')) {
        $pageName = 'All Products';
        $pageDescription = 'Browse products available from ' . $siteBrand . ' with cash on delivery across Bangladesh.';
        $listingType = 'products';
        $listingSource = isset($products)
            ? $products
            : collect();
    } elseif (
        request()->routeIs(
            'category.show',
            'categories.products'
        )
    ) {
        $categoryName = is_object($categoryModel)
            ? (string) $categoryModel->name
            : 'Category';

        $pageName = $categoryName . ' Products';
        $pageDescription = 'Browse ' . $categoryName . ' products available from ' . $siteBrand . '.';
        $listingType = 'products';
        $listingSource = isset($products)
            ? $products
            : collect();
    } elseif (
        request()->routeIs(
            'brand.show',
            'brands.products'
        )
    ) {
        $brandTitle = is_object($brandModel)
            ? (string) $brandModel->name
            : 'Brand';

        $pageName = $brandTitle . ' Products';
        $pageDescription = 'Browse ' . $brandTitle . ' products available from ' . $siteBrand . '.';
        $listingType = 'products';
        $listingSource = isset($products)
            ? $products
            : collect();
    } elseif (request()->routeIs('categories')) {
        $pageName = 'Product Categories';
        $pageDescription = 'Browse ' . $siteBrand . ' product categories to find products for everyday needs.';
        $listingType = 'categories';
        $listingSource = isset($categories)
            ? $categories
            : collect();
    } elseif (request()->routeIs('brands')) {
        $pageName = 'Brands';
        $pageDescription = 'Browse brands and their available products at ' . $siteBrand . '.';
        $listingType = 'brands';
        $listingSource = isset($brands)
            ? $brands
            : collect();
    }

    if ($pageNumber > 1) {
        $pageName .= ' - Page ' . $pageNumber;
    }

    if (
        $listingSource instanceof
        \Illuminate\Contracts\Pagination\Paginator
    ) {
        $listingItems = collect($listingSource->items());
        $firstPosition = $listingSource->firstItem() ?: 1;
    } else {
        $listingItems = collect($listingSource);
        $firstPosition = 1;
    }

    $itemListElements = $listingItems
        ->values()
        ->map(function ($item, int $index) use (
            $listingType,
            $firstPosition
        ): ?array {
            $name = trim(
                (string) data_get($item, 'name', '')
            );

            if ($listingType === 'blogs') {
                $name = trim(
                    (string) data_get($item, 'title', '')
                );
            }

            $slug = trim(
                (string) data_get($item, 'slug', '')
            );

            if ($name === '' || $slug === '') {
                return null;
            }

            $itemUrl = match ($listingType) {
                'blogs' => route('blogs.show', $slug),
                'categories' => route(
                    'categories.products',
                    $slug
                ),
                'brands' => route(
                    'brands.products',
                    $slug
                ),
                default => route(
                    'products.show',
                    $slug
                ),
            };

            return [
                '@type' => 'ListItem',
                'position' => $firstPosition + $index,
                'name' => $name,
                'url' => $itemUrl,
            ];
        })
        ->filter()
        ->values()
        ->all();

    $breadcrumbItems = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => $siteUrl . '/',
        ],
    ];

    if (
        request()->routeIs(
            'category.show',
            'categories.products'
        )
    ) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Categories',
            'item' => route('categories'),
        ];
    } elseif (
        request()->routeIs(
            'brand.show',
            'brands.products'
        )
    ) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Brands',
            'item' => route('brands'),
        ];
    }

    $breadcrumbItems[] = [
        '@type' => 'ListItem',
        'position' => count($breadcrumbItems) + 1,
        'name' => $pageName,
        'item' => $pageUrl,
    ];

    $brandEmail = data_get(setting('company'), 'email') ?: ('support@' . parse_url($siteUrl, PHP_URL_HOST));
    $brandPhone = data_get(setting('company'), 'phone') ?: '';
    $logoUrl = file_exists(public_path('performance/images/hk-logo-640.webp'))
        ? asset('performance/images/hk-logo-640.webp')
        : (data_get(setting('logo'), 'desktop') ? asset(data_get(setting('logo'), 'desktop')) : null);

    $organizationSchema = [
        '@type' => 'OnlineStore',
        '@id' => $organizationId,
        'name' => $siteBrand,
        'url' => $siteUrl . '/',
    ];

    if ($logoUrl) {
        $organizationSchema['logo'] = [
            '@type' => 'ImageObject',
            '@id' => $logoId,
            'url' => $logoUrl,
            'contentUrl' => $logoUrl,
            'caption' => $siteBrand,
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
        'name' => $siteBrand,
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
        'itemListElement' => $breadcrumbItems,
    ];

    $itemListSchema = [
        '@type' => 'ItemList',
        '@id' => $itemListId,
        'url' => $pageUrl,
        'name' => $pageName,
        'numberOfItems' => count($itemListElements),
        'itemListOrder' => 'https://schema.org/ItemListOrderAscending',
        'itemListElement' => $itemListElements,
    ];

    $collectionPageSchema = [
        '@type' => 'CollectionPage',
        '@id' => $webpageId,
        'url' => $pageUrl,
        'name' => $pageName,
        'description' => $pageDescription,
        'isPartOf' => [
            '@id' => $websiteId,
        ],
        'breadcrumb' => [
            '@id' => $breadcrumbId,
        ],
        'mainEntity' => [
            '@id' => $itemListId,
        ],
        'publisher' => [
            '@id' => $organizationId,
        ],
        'inLanguage' => [
            'en',
            'bn',
        ],
    ];

    $collectionSchema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            $organizationSchema,
            $websiteSchema,
            $collectionPageSchema,
            $breadcrumbSchema,
            $itemListSchema,
        ],
    ];
@endphp

<script type="application/ld+json">{!! json_encode(
    $collectionSchema,
    JSON_UNESCAPED_SLASHES
    | JSON_UNESCAPED_UNICODE
    | JSON_HEX_TAG
    | JSON_HEX_AMP
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
) !!}</script>