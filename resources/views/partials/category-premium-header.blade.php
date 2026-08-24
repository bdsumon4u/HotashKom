@php
    $nmPremiumCategory = $category ?? null;

    $nmConfig = $nmPremiumCategory instanceof \App\Models\Category
        ? $nmPremiumCategory->getPremiumHeaderData()
        : [
            'eyebrow' => $company->name ?? config('app.name'),
            'help_title' => 'আপনার প্রয়োজন অনুযায়ী বিভাগ বেছে নিন',
            'help_text' => 'প্রাসঙ্গিক subcategory থেকে প্রয়োজনীয় পণ্য সহজে খুঁজে নিন।',
            'section_title' => ($nmPremiumCategory?->name ? $nmPremiumCategory->name . '-এর বিভাগগুলো' : null),
            'section_text' => 'প্রয়োজনীয় product type দ্রুত খুঁজে পেতে নিচের বিভাগগুলো ব্যবহার করুন।',
        ];

    $nmChildren = collect();

    if ($nmPremiumCategory instanceof \App\Models\Category) {
        $nmChildren = $nmPremiumCategory->childrens()
            ->where('is_enabled', true)
            ->withCount([
                'products as active_products_count' => function ($query) {
                    $query
                        ->whereIsActive(1)
                        ->whereNull('products.parent_id');
                },
            ])
            ->orderBy('order')
            ->get();
    }

    $nmProductTotal =
        isset($products)
        && method_exists($products, 'total')
            ? $products->total()
            : null;
@endphp

<section class="nm-category-hero"
         aria-labelledby="nm-category-title">

    <div class="container">

        <nav class="nm-category-breadcrumb"
             aria-label="Breadcrumb">

            <a href="{{ url('/') }}">
                Home
            </a>

            <span aria-hidden="true">/</span>

            <a href="{{ route('categories') }}">
                Categories
            </a>

            <span aria-hidden="true">/</span>

            <span aria-current="page">
                {{ $nmPremiumCategory?->name }}
            </span>

        </nav>

        <div class="nm-category-hero-grid">

            <div class="nm-category-hero-copy">

                @if (!empty($nmConfig['eyebrow']))
                    <span class="nm-category-eyebrow">
                        {{ $nmConfig['eyebrow'] }}
                    </span>
                @endif

                <h1 id="nm-category-title">
                    {{ $categoryPageTitle ?? $nmPremiumCategory?->name }}
                </h1>

                @if (!empty($categoryPageIntro))
                    <p class="nm-category-intro">
                        {{ $categoryPageIntro }}
                    </p>
                @endif

                <div class="nm-category-quick-info">

                    @if ($nmProductTotal !== null)
                        <span>
                            <strong>
                                {{ number_format($nmProductTotal) }}
                            </strong>
                            পণ্য
                        </span>
                    @endif

                    <span>
                        বর্তমান দাম ও availability
                    </span>

                    <span>
                        সারা বাংলাদেশে Cash on Delivery
                    </span>

                </div>

                <div class="nm-category-actions">

                    <a href="#nm-category-products"
                       class="nm-category-primary-btn"
                       onclick="document.getElementById('nm-category-products')?.scrollIntoView({ behavior: 'smooth', block: 'start' }); return false;">
                        পণ্য দেখুন
                    </a>

                    <a href="#nm-category-buying-guide"
                       class="nm-category-secondary-btn"
                       onclick="document.getElementById('nm-category-buying-guide')?.scrollIntoView({ behavior: 'smooth', block: 'start' }); return false;">
                        Buying Guide
                    </a>

                </div>

            </div>

            @if (!empty($nmConfig['help_title']) || !empty($nmConfig['help_text']))
                <aside class="nm-category-help-card">

                    <span class="nm-category-help-kicker">
                        সহজে পণ্য খুঁজুন
                    </span>

                    @if (!empty($nmConfig['help_title']))
                        <strong>
                            {{ $nmConfig['help_title'] }}
                        </strong>
                    @endif

                    @if (!empty($nmConfig['help_text']))
                        <p>
                            {{ $nmConfig['help_text'] }}
                        </p>
                    @endif

                    <a href="{{ route('categories') }}">
                        সব বিভাগ দেখুন →
                    </a>

                </aside>
            @endif

        </div>

        @if ($nmChildren->isNotEmpty())

            <div id="nm-category-subcategories"
                 class="nm-category-subcategories">

                <div class="nm-category-section-heading">

                    <div>

                        <span>
                            Shop by Category
                        </span>

                        <h2>
                            {{ $nmConfig['section_title'] ?? ($nmPremiumCategory?->name . '-এর বিভাগগুলো') }}
                        </h2>

                    </div>

                    @if (!empty($nmConfig['section_text']))
                        <p>
                            {{ $nmConfig['section_text'] }}
                        </p>
                    @endif

                </div>

                <div class="nm-category-child-grid">

                    @foreach ($nmChildren as $nmChild)

                        <a
                            href="{{ route('category.show', $nmChild) }}"
                            class="nm-category-child-card"
                        >

                            <span
                                class="nm-category-child-icon"
                                aria-hidden="true"
                            >
                                {{
                                    mb_strtoupper(
                                        mb_substr(
                                            $nmChild->name,
                                            0,
                                            1
                                        )
                                    )
                                }}
                            </span>

                            <span class="nm-category-child-copy">

                                <strong>
                                    {{ $nmChild->name }}
                                </strong>

                                <small>
                                    {{
                                        number_format(
                                            $nmChild->active_products_count
                                        )
                                    }}
                                    পণ্য
                                </small>

                            </span>

                            <span
                                class="nm-category-child-arrow"
                                aria-hidden="true"
                            >
                                →
                            </span>

                        </a>

                    @endforeach

                </div>

            </div>

        @endif

    </div>

</section>
