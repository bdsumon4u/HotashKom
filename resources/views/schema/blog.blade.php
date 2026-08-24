@php
    $siteUrl = rtrim(config('app.url'), '/');
    $blogUrl = route('blogs.show', $blog);

    $organizationId = $siteUrl . '/#organization';
    $websiteId = $siteUrl . '/#website';
    $logoId = $siteUrl . '/#logo';

    $webpageId = $blogUrl . '#webpage';
    $articleId = $blogUrl . '#article';
    $breadcrumbId = $blogUrl . '#breadcrumb';
    $faqPageId = $blogUrl . '#faq';

    $plainContent = trim(
        preg_replace(
            '/\s+/u',
            ' ',
            strip_tags((string) $blog->content)
        )
    );

    $blogDescription = trim(
        (string) (
            $blog->seo?->description
            ?: \Illuminate\Support\Str::limit(
                $plainContent,
                160,
                ''
            )
        )
    );

    $blogImageUrl = filled($blog->image)
        ? asset($blog->image)
        : null;

    $validFaqs = collect($blog->faqs ?? [])
        ->filter(fn ($faq): bool => is_array($faq))
        ->map(function (array $faq): array {
            return [
                'question' => trim(
                    (string) ($faq['question'] ?? '')
                ),
                'answer' => trim(
                    (string) ($faq['answer'] ?? '')
                ),
            ];
        })
        ->filter(
            fn (array $faq): bool =>
                $faq['question'] !== ''
                && $faq['answer'] !== ''
        )
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
                'name' => 'Blogs',
                'item' => route('blogs.index'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $blog->title,
                'item' => $blogUrl,
            ],
        ],
    ];

    $webpageSchema = [
        '@type' => 'WebPage',
        '@id' => $webpageId,
        'url' => $blogUrl,
        'name' => $blog->title,
        'description' => $blogDescription,
        'isPartOf' => [
            '@id' => $websiteId,
        ],
        'breadcrumb' => [
            '@id' => $breadcrumbId,
        ],
        'mainEntity' => [
            '@id' => $articleId,
        ],
        'publisher' => [
            '@id' => $organizationId,
        ],
        'datePublished' => $blog->created_at->toIso8601String(),
        'dateModified' => $blog->updated_at->toIso8601String(),
        'inLanguage' => [
            'en',
            'bn',
        ],
    ];

    if ($blogImageUrl) {
        $webpageSchema['primaryImageOfPage'] = [
            '@type' => 'ImageObject',
            'url' => $blogImageUrl,
            'contentUrl' => $blogImageUrl,
        ];
    }

    $articleSchema = [
        '@type' => 'BlogPosting',
        '@id' => $articleId,
        'url' => $blogUrl,
        'mainEntityOfPage' => [
            '@id' => $webpageId,
        ],
        'headline' => $blog->title,
        'description' => $blogDescription,
        'datePublished' => $blog->created_at->toIso8601String(),
        'dateModified' => $blog->updated_at->toIso8601String(),
        'author' => [
            '@id' => $organizationId,
        ],
        'publisher' => [
            '@id' => $organizationId,
        ],
        'isPartOf' => [
            '@id' => $webpageId,
        ],
        'inLanguage' => [
            'en',
            'bn',
        ],
    ];

    if ($blogImageUrl) {
        $articleSchema['image'] = [
            $blogImageUrl,
        ];
    }

    if ($validFaqs->isNotEmpty()) {
        $articleSchema['hasPart'] = [
            [
                '@id' => $faqPageId,
            ],
        ];
    }

    $schemaGraph = [
        $organizationSchema,
        $websiteSchema,
        $webpageSchema,
        $breadcrumbSchema,
        $articleSchema,
    ];

    if ($validFaqs->isNotEmpty()) {
        $schemaGraph[] = [
            '@type' => 'FAQPage',
            '@id' => $faqPageId,
            'url' => $blogUrl . '#frequently-asked-questions',
            'name' => $blog->title . ' Frequently Asked Questions',
            'isPartOf' => [
                '@id' => $websiteId,
            ],
            'mainEntity' => $validFaqs
                ->map(function (array $faq): array {
                    return [
                        '@type' => 'Question',
                        'name' => $faq['question'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $faq['answer'],
                        ],
                    ];
                })
                ->all(),
            'inLanguage' => [
                'en',
                'bn',
            ],
        ];
    }

    $blogSchema = [
        '@context' => 'https://schema.org',
        '@graph' => $schemaGraph,
    ];
@endphp

<script type="application/ld+json">{!! json_encode(
    $blogSchema,
    JSON_UNESCAPED_SLASHES
    | JSON_UNESCAPED_UNICODE
    | JSON_HEX_TAG
    | JSON_HEX_AMP
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
) !!}</script>