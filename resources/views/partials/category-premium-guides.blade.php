@php
    $bbGuideData = $category instanceof \App\Models\Category
        ? $category->getPremiumGuideData()
        : null;

    $bbRelatedGuides = collect();

    if ($bbGuideData && !empty($bbGuideData['patterns'])) {
        $bbPatterns = $bbGuideData['patterns'];

        $bbRelatedGuides = \App\Models\Blog::query()
            ->where(function ($query) use ($bbPatterns) {
                foreach ($bbPatterns as $pattern) {
                    $query->orWhere('slug', 'like', '%' . $pattern . '%');
                }
            })
            ->latest()
            ->limit(4)
            ->get();
    }
@endphp

@if ($bbGuideData && $bbRelatedGuides->isNotEmpty())
    <section class="bb-category-guides"
             aria-labelledby="bb-category-guides-title">

        <div class="bb-category-section-heading">

            <div>

                <span>
                    Helpful Guides
                </span>

                <h2 id="bb-category-guides-title">
                    {{ $bbGuideData['title'] }}
                </h2>

            </div>

            <a href="{{ route('blogs.index') }}">
                সব Buying Guide →
            </a>

        </div>

        <div class="bb-category-guide-grid">

            @foreach ($bbRelatedGuides as $bbGuide)

                <article class="bb-category-guide-card">

                    <span class="bb-category-guide-label">
                        Buying Guide
                    </span>

                    <h3>

                        <a href="{{ route('blogs.show', $bbGuide) }}">
                            {{ $bbGuide->title }}
                        </a>

                    </h3>

                    <a
                        href="{{ route('blogs.show', $bbGuide) }}"
                        class="bb-category-guide-link"
                    >
                        গাইডটি পড়ুন →
                    </a>

                </article>

            @endforeach

        </div>

    </section>
@endif
