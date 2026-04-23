@if (data_get($sections, 'reviews.enabled', true) && !empty($reviews))
    <section class="py-5 bg-gray-100 border-b md:py-10">
        <div class="max-w-6xl px-2 mx-auto md:px-4">
            <h3 class="mb-8 text-3xl font-black text-center text-gray-900">
                {{ data_get($sections, 'reviews.title', 'আমাদের কাস্টমারদের মতামত') }}</h3>
            @if ($useReviewCarousel)
                <div class="relative">
                    <button type="button" @click="prevReview"
                        class="absolute z-10 items-center justify-center hidden w-10 h-10 -translate-y-1/2 bg-white border border-gray-200 rounded-md shadow-lg -left-5 top-1/2 hover:bg-green-50 md:flex">
                        <i class="text-green-700 fas fa-chevron-left"></i>
                    </button>
                    <button type="button" @click="nextReview"
                        class="absolute z-10 items-center justify-center hidden w-10 h-10 -translate-y-1/2 bg-white border border-gray-200 rounded-md shadow-lg -right-5 top-1/2 hover:bg-green-50 md:flex">
                        <i class="text-green-700 fas fa-chevron-right"></i>
                    </button>

                    <div x-ref="reviewTrack"
                        class="flex gap-3 overflow-x-hidden md:gap-4 snap-x no-scrollbar scroll-smooth">
                        @foreach ($reviews as $review)
                            <div class="p-4 bg-white border rounded-md shadow-sm review-item snap-start md:p-6">
                                <div class="mb-2 text-yellow-400">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star"></i>
                                    @endfor
                                </div>
                                <p class="mb-3 text-sm italic text-gray-600">"{{ $review['text'] }}"</p>
                                <p class="text-sm font-black text-gray-900">- {{ $review['name'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-center gap-2 mt-6">
                    @foreach ($reviews as $index => $review)
                        <button type="button" @click="goToReview({{ $index }})"
                            class="h-2.5 w-2.5 rounded-md transition-all"
                            :class="reviewIndex === {{ $index }} ? 'w-8 bg-green-700' :
                                'bg-gray-300 hover:bg-gray-400'"></button>
                    @endforeach
                </div>
            @else
                @php
                    $reviewGridClasses = match ($reviewCount) {
                        1 => 'grid grid-cols-1 max-w-2xl mx-auto',
                        2 => 'grid grid-cols-1 md:grid-cols-2',
                        default => 'grid grid-cols-1 md:grid-cols-3',
                    };
                @endphp
                <div class="{{ $reviewGridClasses }} gap-4">
                    @foreach ($reviews as $review)
                        <div class="p-4 bg-white border rounded-md shadow-sm md:p-6">
                            <div class="mb-2 text-yellow-400">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor
                            </div>
                            <p class="mb-3 text-sm italic text-gray-600">"{{ $review['text'] }}"</p>
                            <p class="text-sm font-black text-gray-900">- {{ $review['name'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endif
