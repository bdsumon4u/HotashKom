@if (data_get($sections, 'gallery.enabled', true) && !empty($galleryImages))
    <section class="py-5 bg-white border-b md:py-10">
        <div class="px-2 mx-auto md:px-4 max-w-7xl">
            <h2 class="mb-8 text-2xl font-black text-center text-gray-900">
                {{ data_get($sections, 'gallery.title', 'পণ্যটির আরও কিছু ছবি') }}</h2>
            @if ($useGalleryCarousel)
                <div class="relative">
                    <button type="button" @click="prevGallery"
                        class="absolute left-0 z-10 items-center justify-center hidden w-10 h-10 -translate-y-1/2 bg-white border border-gray-200 rounded-md shadow-lg top-1/2 hover:bg-green-50 md:-left-5 md:flex">
                        <i class="text-green-700 fas fa-chevron-left"></i>
                    </button>
                    <button type="button" @click="nextGallery"
                        class="absolute right-0 z-10 items-center justify-center hidden w-10 h-10 -translate-y-1/2 bg-white border border-gray-200 rounded-md shadow-lg top-1/2 hover:bg-green-50 md:-right-5 md:flex">
                        <i class="text-green-700 fas fa-chevron-right"></i>
                    </button>

                    <div x-ref="galleryTrack"
                        @touchstart="handleTouchStart($event)"
                        @touchend="handleTouchEnd($event, 'gallery')"
                        class="flex gap-3 pb-6 overflow-x-hidden md:gap-4 snap-x no-scrollbar scroll-smooth">
                        @foreach ($galleryImages as $index => $image)
                            <div
                                class="overflow-hidden border border-gray-100 rounded-md shadow-lg gallery-item snap-start">
                                <img src="{{ $image }}" alt="Gallery image {{ $index + 1 }}"
                                    class="object-cover w-full" loading="lazy">
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-center gap-2 mt-2">
                        @foreach ($galleryImages as $index => $image)
                            <button type="button" @click="goToGallery({{ $index }})"
                                class="h-2.5 w-2.5 rounded-md transition-all"
                                :class="galleryIndex === {{ $index }} ? 'w-8 bg-green-700' :
                                    'bg-gray-300 hover:bg-gray-400'"></button>
                        @endforeach
                    </div>
                </div>
            @else
                @php
                    $galleryGridClasses = match ($galleryImageCount) {
                        1 => 'grid grid-cols-1 max-w-2xl mx-auto',
                        2 => 'grid grid-cols-1 md:grid-cols-2',
                        default => 'grid grid-cols-1 md:grid-cols-3',
                    };
                @endphp
                <div class="{{ $galleryGridClasses }} gap-4">
                    @foreach ($galleryImages as $index => $image)
                        <div class="overflow-hidden border border-gray-100 rounded-md shadow-lg">
                            <img src="{{ $image }}" alt="Gallery image {{ $index + 1 }}"
                                class="object-cover w-full" loading="lazy">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endif
