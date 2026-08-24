@php
    $nmGuideData = $category instanceof \App\Models\Category
        ? $category->getPremiumGuideData()
        : null;

    $nmRelatedGuides = collect();

    if ($nmGuideData && !empty($nmGuideData['patterns'])) {
        $nmPatterns = $nmGuideData['patterns'];

        $nmRelatedGuides = \App\Models\Blog::query()
            ->where(function ($query) use ($nmPatterns) {
                foreach ($nmPatterns as $pattern) {
                    $query->orWhere('slug', 'like', '%' . $pattern . '%');
                }
            })
            ->latest()
            ->limit(4)
            ->get();
    }
@endphp

@if ($nmGuideData && $nmRelatedGuides->isNotEmpty())
    <section class="nm-category-guides"
             aria-labelledby="nm-category-guides-title">

        <div class="nm-category-section-heading">

            <div>

                <span>
                    Helpful Guides
                </span>

                <h2 id="nm-category-guides-title">
                    {{ $nmGuideData['title'] }}
                </h2>

            </div>

            <a href="{{ route('blogs.index') }}">
                সব Buying Guide →
            </a>

        </div>

        <div class="nm-category-guide-grid">

            @foreach ($nmRelatedGuides as $nmGuide)

                <article class="nm-category-guide-card">

                    <span class="nm-category-guide-label">
                        Buying Guide
                    </span>

                    <h3>

                        <a href="{{ route('blogs.show', $nmGuide) }}">
                            {{ $nmGuide->title }}
                        </a>

                    </h3>

                    <a
                        href="{{ route('blogs.show', $nmGuide) }}"
                        class="nm-category-guide-link"
                    >
                        গাইডটি পড়ুন →
                    </a>

                </article>

            @endforeach

        </div>

    </section>
@endif
