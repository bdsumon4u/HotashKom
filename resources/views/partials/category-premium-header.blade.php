@php
    $bbPremiumCategory = $category ?? null;

    $bbConfig = $bbPremiumCategory instanceof \App\Models\Category
        ? $bbPremiumCategory->getPremiumHeaderData()
        : [
            'eyebrow' => $company->name ?? config('app.name'),
            'help_title' => 'আপনার প্রয়োজন অনুযায়ী বিভাগ বেছে নিন',
            'help_text' => 'প্রাসঙ্গিক subcategory থেকে প্রয়োজনীয় পণ্য সহজে খুঁজে নিন।',
            'section_title' => ($bbPremiumCategory?->name ? $bbPremiumCategory->name . '-এর বিভাগগুলো' : null),
            'section_text' => 'প্রয়োজনীয় product type দ্রুত খুঁজে পেতে নিচের বিভাগগুলো ব্যবহার করুন।',
        ];

    $bbChildren = collect();

    if ($bbPremiumCategory instanceof \App\Models\Category) {
        $bbChildren = $bbPremiumCategory->childrens()
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

    $bbProductTotal =
        isset($products)
        && method_exists($products, 'total')
            ? $products->total()
            : null;
@endphp

<section class="bb-category-hero"
         aria-labelledby="bb-category-title">

    <div class="container">

        <nav class="bb-category-breadcrumb"
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
                {{ $bbPremiumCategory?->name }}
            </span>

        </nav>

        <div class="bb-category-hero-grid">

            <div class="bb-category-hero-copy">

                @if (!empty($bbConfig['eyebrow']))
                    <span class="bb-category-eyebrow">
                        {{ $bbConfig['eyebrow'] }}
                    </span>
                @endif

                <h1 id="bb-category-title">
                    {{ $categoryPageTitle ?? $bbPremiumCategory?->name }}
                </h1>

                @if (!empty($categoryPageIntro))
                    <p class="bb-category-intro">
                        {{ $categoryPageIntro }}
                    </p>
                @endif

                <div class="bb-category-quick-info">

                    @if ($bbProductTotal !== null)
                        <span>
                            <strong>
                                {{ number_format($bbProductTotal) }}
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

                <div class="bb-category-actions">

                    <a href="#bb-category-products"
                       class="bb-category-primary-btn"
                       onclick="document.getElementById('bb-category-products')?.scrollIntoView({ behavior: 'smooth', block: 'start' }); return false;">
                        পণ্য দেখুন
                    </a>

                    <a href="#bb-category-buying-guide"
                       class="bb-category-secondary-btn"
                       onclick="document.getElementById('bb-category-buying-guide')?.scrollIntoView({ behavior: 'smooth', block: 'start' }); return false;">
                        Buying Guide
                    </a>

                </div>

            </div>

            @if (!empty($bbConfig['help_title']) || !empty($bbConfig['help_text']))
                <aside class="bb-category-help-card">

                    <span class="bb-category-help-kicker">
                        সহজে পণ্য খুঁজুন
                    </span>

                    @if (!empty($bbConfig['help_title']))
                        <strong>
                            {{ $bbConfig['help_title'] }}
                        </strong>
                    @endif

                    @if (!empty($bbConfig['help_text']))
                        <p>
                            {{ $bbConfig['help_text'] }}
                        </p>
                    @endif

                    <a href="{{ route('categories') }}">
                        সব বিভাগ দেখুন →
                    </a>

                </aside>
            @endif

        </div>

        @if ($bbChildren->isNotEmpty())

            <div id="bb-category-subcategories"
                 class="bb-category-subcategories">

                <div class="bb-category-section-heading">

                    <div>

                        <span>
                            Shop by Category
                        </span>

                        <h2>
                            {{ $bbConfig['section_title'] ?? ($bbPremiumCategory?->name . '-এর বিভাগগুলো') }}
                        </h2>

                    </div>

                    @if (!empty($bbConfig['section_text']))
                        <p>
                            {{ $bbConfig['section_text'] }}
                        </p>
                    @endif

                </div>

                <div class="bb-category-child-grid">

                    @foreach ($bbChildren as $bbChild)

                        <a
                            href="{{ route('category.show', $bbChild) }}"
                            class="bb-category-child-card"
                        >

                            <span
                                class="bb-category-child-icon"
                                aria-hidden="true"
                            >
                                {{
                                    mb_strtoupper(
                                        mb_substr(
                                            $bbChild->name,
                                            0,
                                            1
                                        )
                                    )
                                }}
                            </span>

                            <span class="bb-category-child-copy">

                                <strong>
                                    {{ $bbChild->name }}
                                </strong>

                                <small>
                                    {{
                                        number_format(
                                            $bbChild->active_products_count
                                        )
                                    }}
                                    পণ্য
                                </small>

                            </span>

                            <span
                                class="bb-category-child-arrow"
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
