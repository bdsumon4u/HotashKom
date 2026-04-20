<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ data_get($landingPagePro->seo, 'title', $landingPagePro->title) }}</title>
    @if (filled(data_get($landingPagePro->seo, 'description')))
        <meta name="description" content="{{ data_get($landingPagePro->seo, 'description') }}">
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        [x-cloak] {
            display: none !important;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .scroll-smooth {
            scroll-behavior: smooth;
        }

        .snap-x {
            scroll-snap-type: x mandatory;
        }

        .snap-start {
            scroll-snap-align: start;
        }

        .gallery-item,
        .review-item {
            flex-shrink: 0;
            scroll-snap-align: start;
        }
    </style>
</head>

@php
    $company = setting('company');
    $logo = setting('logo');
    $normalizePhone = function (?string $value): string {
        $normalized = preg_replace('/[^\d]/', '', (string) $value);

        if (strlen($normalized) === 11) {
            $normalized = '88' . $normalized;
        }

        return $normalized;
    };

    $normalizedCallPhone = $normalizePhone($company->phone ?? '');
    $normalizedWhatsappPhone = $normalizePhone($company->whatsapp ?? '');
    $social = setting('social');

    $callUrl = $normalizedCallPhone ? 'tel:+' . $normalizedCallPhone : '#order';
    $whatsappUrl = $normalizedWhatsappPhone ? 'https://wa.me/' . $normalizedWhatsappPhone : '#order';
    $rawFacebookUrl = (string) data_get($social, 'facebook.link', '#');
    $facebookUrl = '#';

    if ($rawFacebookUrl !== '#' && filled($rawFacebookUrl)) {
        $facebookUrl = \Illuminate\Support\Str::startsWith($rawFacebookUrl, ['http://', 'https://'])
            ? $rawFacebookUrl
            : url($rawFacebookUrl);
    }

    $showAnnouncementBar = data_get($sections, 'announcement_bar.enabled', true);
    $deliveryCharge = setting('delivery_charge');
    $insideDhakaDeliveryCharge = (int) data_get($deliveryCharge, 'inside_dhaka', 0);
    $outsideDhakaDeliveryCharge = (int) data_get($deliveryCharge, 'outside_dhaka', 0);

    $parseLines = function (?string $value): array {
        return collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(fn($line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();
    };

    $parsePairs = function (?string $value, int $parts): array {
        return collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(function ($line) use ($parts) {
                $segments = array_map('trim', explode('|', (string) $line));

                return count($segments) >= $parts ? $segments : null;
            })
            ->filter()
            ->values()
            ->all();
    };

    $galleryImages = data_get($sections, 'gallery.image_urls', []);
    if (empty($galleryImages)) {
        $galleryImages = collect($selectedProducts)
            ->flatMap(function (array $product): array {
                $images = data_get($product, 'gallery_images', []);

                if (!empty($images)) {
                    return $images;
                }

                return [data_get($product, 'image')];
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
    $galleryImageCount = count($galleryImages);
    $useGalleryCarousel = $galleryImageCount > 3;

    $features = $parseLines(data_get($sections, 'features.items_text'));
    if (empty($features)) {
        $features = [
            'অরিজিনাল চায়না ডবি ফেব্রিক (১০০%)',
            'এক্সপোর্ট কোয়ালিটি ফিনিশিং এবং স্টিচিং',
            'প্রিমিয়াম ফিটিং ও এশিয়ান সাইজ চার্ট',
            'চেইন সহ গভীর পকেট মোবাইল রাখার জন্য নিরাপদ',
            'অত্যন্ত আরামদায়ক এবং দীর্ঘস্থায়ী ফেব্রিক',
        ];
    }

    $sizeRows = collect($parsePairs(data_get($sections, 'size_guide.rows_text'), 3))
        ->map(fn($row) => ['size' => $row[0], 'waist' => $row[1], 'length' => $row[2]])
        ->values()
        ->all();

    if (empty($sizeRows)) {
        $sizeRows = [
            ['size' => 'M', 'waist' => '28-30', 'length' => '38'],
            ['size' => 'L', 'waist' => '30-32', 'length' => '39'],
            ['size' => 'XL', 'waist' => '32-34', 'length' => '40'],
            ['size' => '2XL', 'waist' => '34-36', 'length' => '41'],
        ];
    }

    $faqs = collect($parsePairs(data_get($sections, 'faq.items_text'), 2))
        ->map(fn($item) => ['q' => $item[0], 'a' => $item[1]])
        ->values()
        ->all();

    if (empty($faqs)) {
        $faqs = [
            [
                'q' => 'ফেব্রিক কি ধোয়ার পর কালার নষ্ট হবে?',
                'a' => 'জি না, আমরা ১০০% চায়না ডবি প্রিমিয়াম কাপড় ব্যবহার করি যার কালার গ্যারান্টি আছে।',
            ],
            [
                'q' => 'ঢাকার বাইরে হোম ডেলিভারি পাওয়া যাবে?',
                'a' => 'জি অবশ্যই, আমরা সারা বাংলাদেশে কুরিয়ারের মাধ্যমে হোম ডেলিভারি দিয়ে থাকি।',
            ],
            [
                'q' => 'আমি কি ট্রাউজারটি ট্রায়াল দিয়ে নিতে পারবো?',
                'a' =>
                    'ডেলিভারি ম্যান থাকাকালীন আপনি চেক করে নিতে পারবেন, কোনো সমস্যা থাকলে সাথে সাথেই রিটার্ন করতে পারবেন।',
            ],
            [
                'q' => 'এটির কোমর কি ইলাস্টিক?',
                'a' => 'জি, এটিতে হাই-কোয়ালিটি ইলাস্টিক এবং অ্যাডজাস্টেবল ফিতা রয়েছে যা আপনাকে দিবে সর্বোচ্চ আরাম।',
            ],
        ];
    }

    $reviews = collect($parsePairs(data_get($sections, 'reviews.items_text'), 2))
        ->map(fn($item, $index) => ['id' => $index + 1, 'name' => $item[0], 'text' => $item[1]])
        ->values()
        ->all();

    if (empty($reviews)) {
        $reviews = [
            [
                'id' => 1,
                'name' => 'আসাদুল্লাহ বিন সাইদ',
                'text' => 'খুবই আরামদায়ক ট্রাউজার। প্রিমিয়াম কোয়ালিটি এক্সপোর্ট কাপড়। রেকমেন্ডেড!',
            ],
            [
                'id' => 2,
                'name' => 'মাশরুর আহমেদ',
                'text' => 'কোয়ালিটি অনেক ভালো। কালার একদম ছবির মতোই। ডেলিভারিও খুব দ্রুত পেয়েছি।',
            ],
            [
                'id' => 3,
                'name' => 'তানজিল ইসলাম',
                'text' => 'চায়না ডবি ফেব্রিক টা সত্যিই অনেক সফট। এই বাজেটে সেরা ট্রাউজার।',
            ],
        ];
    }

    $reviewCount = count($reviews);
    $useReviewCarousel = $reviewCount > 3;

    $productsPayload = collect($selectedProducts)
        ->map(function ($item, $key): array {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'image' => $item['image'],
                'selected' => !$key, // select the first one by default
                'qty' => 1,
                'free_delivery' => $item['free_delivery'] ?? false,
            ];
        })
        ->values()
        ->all();

    $videoUrl = data_get($sections, 'video.url', 'https://www.youtube.com/embed/dQw4w9WgXcQ');
    $heroImageUrl = data_get($sections, 'hero.image_src');
@endphp

<body class="text-gray-900 bg-gray-50" x-data="landingProPage()" x-init="init()">
    @if ($showAnnouncementBar)
        <div class="sticky top-0 z-50 p-2 text-sm font-semibold text-center text-white bg-red-600">
            {{ data_get($sections, 'announcement_bar.title', 'সীমিত সময়ের অফার! ৩ পিসের বেশি অর্ডারে ফ্রি শিপিং।') }}
        </div>
    @endif

    <header class="bg-white shadow-sm">
        <div class="flex items-center justify-between px-2 py-4 mx-auto md:px-4 max-w-7xl">
            @if (filled(data_get($logo, 'desktop')) || filled(data_get($logo, 'dashboard')) || filled(data_get($logo, 'login')))
                <img src="{{ asset(data_get($logo, 'desktop') ?? (data_get($logo, 'dashboard') ?? data_get($logo, 'login'))) }}"
                    alt="Company Logo" class="object-contain h-12 max-w-[180px]">
            @else
                <div class="text-2xl font-black tracking-tight text-green-800">
                    {{ config('app.name', 'Hotash CLOTHING') }}
                </div>
            @endif
            <a href="#order"
                class="px-6 py-2 text-sm font-bold text-white transition bg-green-700 rounded-md hover:bg-green-800">
                অর্ডার করুন
            </a>
        </div>
    </header>

    @if (data_get($sections, 'hero.enabled', true))
        <section class="py-5 text-center text-white bg-green-800 md:py-20">
            <div class="max-w-5xl px-2 mx-auto md:px-4">
                <h1 class="text-3xl font-extrabold uppercase md:text-5xl">
                    {{ data_get($sections, 'hero.title', $landingPagePro->title) }}
                </h1>
                @if (filled(data_get($sections, 'hero.subtitle')))
                    <div class="mt-4 text-lg text-green-100 md:text-2xl">{!! data_get($sections, 'hero.subtitle') !!}</div>
                @endif
                <div class="flex justify-center gap-4 my-4 md:my-8">
                    <template x-for="(val, unit) in timer" :key="unit">
                        <div class="min-w-[75px] rounded-md bg-white p-3 text-green-800 shadow-2xl">
                            <span class="text-3xl font-black" x-text="val">00</span>
                            <p class="text-[10px] font-black uppercase tracking-widest" x-text="unit"></p>
                        </div>
                    </template>
                </div>
                <a href="#order"
                    class="inline-block px-10 py-3 text-lg font-black text-white transition bg-red-600 rounded-md shadow-xl md:mt-8 hover:bg-red-700">
                    অর্ডার করতে চাই
                </a>
            </div>
        </section>
    @endif

    @if (filled($heroImageUrl))
        <section class="py-5 bg-white border-b md:py-10">
            <div class="max-w-3xl px-2 mx-auto md:px-4">
                <img src="{{ $heroImageUrl }}" alt="Hero image"
                    class="object-cover w-full border border-gray-100 rounded-md shadow-lg">
            </div>
        </section>
    @endif

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

    @if (data_get($sections, 'cta_after_gallery.enabled', true))
        <section class="flex justify-center py-5 bg-white md:py-10">
            <div class="max-w-5xl p-3 mx-2 text-white bg-green-800 rounded-md md:p-6">
                <div class="flex flex-col items-center justify-between gap-5 md:flex-row">
                    <div class="text-center md:text-left">
                        <p class="text-lg font-black md:text-2xl">
                            {{ data_get($sections, 'cta_after_gallery.title', 'ছবি দেখলেন, এখন অর্ডার করুন') }}</p>
                        @if (filled(data_get($sections, 'cta_after_gallery.subtitle')))
                            <div class="mt-1 text-sm text-green-100 md:text-base">{!! data_get($sections, 'cta_after_gallery.subtitle') !!}</div>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <a href="#order"
                            class="px-3 py-3 text-sm font-black tracking-wide text-white transition bg-red-600 rounded-md hover:bg-red-700">অর্ডার
                            করুন</a>
                        <a href="{{ $callUrl }}"
                            class="px-3 py-3 text-sm font-black tracking-wide text-green-800 transition bg-white rounded-md hover:bg-green-50">কল
                            করুন</a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if (data_get($sections, 'video.enabled', true))
        <section class="py-5 border-b md:py-10 bg-gray-50">
            <div class="max-w-4xl px-2 mx-auto text-center md:px-4">
                <h2 class="mb-8 text-2xl font-black text-green-900">
                    {{ data_get($sections, 'video.title', 'ভিডিওতে বিস্তারিত দেখুন') }}</h2>
                <div class="aspect-video overflow-hidden rounded-md border-[10px] border-white shadow-2xl">
                    <iframe class="w-full h-full" src="{{ $videoUrl }}" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </section>
    @endif

    @if (data_get($sections, 'cta_after_video.enabled', true))
        <section class="flex justify-center py-5 bg-white border-b md:py-10">
            <div class="max-w-5xl p-3 mx-2 text-white bg-green-800 rounded-md md:p-6">
                <div class="flex flex-col items-center justify-between gap-5 md:flex-row">
                    <div class="text-center md:text-left">
                        <p class="text-lg font-black md:text-2xl">
                            {{ data_get($sections, 'cta_after_video.title', 'ভিডিও দেখলেন, এখন অর্ডার করুন') }}</p>
                        @if (filled(data_get($sections, 'cta_after_video.subtitle')))
                            <div class="mt-1 text-sm text-green-100 md:text-base">{!! data_get($sections, 'cta_after_video.subtitle') !!}</div>
                        @endif
                    </div>
                    <a href="#order"
                        class="px-3 py-3 text-sm font-black tracking-wide text-white transition bg-red-600 rounded-md hover:bg-red-700">অর্ডার
                        করুন</a>
                </div>
            </div>
        </section>
    @endif

    @if (data_get($sections, 'features.enabled', true))
        @php
            $hasImage = filled(data_get($sections, 'features.image_src'));
        @endphp
        <section class="py-5 bg-white md:py-10">
            <div
                class="grid {{ $hasImage ? 'max-w-6xl' : 'max-w-3xl' }} gap-8 px-2 mx-auto md:px-4 @if ($hasImage) md:grid-cols-2 @endif md:items-center">
                <div>
                    <h3 class="mb-6 text-3xl font-black text-green-900">
                        {{ data_get($sections, 'features.title', 'কেন আমাদের ট্রাউজার সেরা?') }}</h3>
                    <ul class="space-y-3">
                        @foreach ($features as $feature)
                            <li class="flex items-start gap-3 text-lg font-semibold text-gray-700">
                                <i class="pt-1 text-green-600 fas fa-check-circle"></i>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @if ($hasImage)
                    <div>
                        <img src="{{ data_get($sections, 'features.image_src') }}" alt="Feature image"
                            class="object-cover w-full bg-gray-100 border rounded-md shadow-md">
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if (data_get($sections, 'size_guide.enabled', true) && !empty($sizeRows))
        <section class="py-5 border-green-100 md:py-10 border-y-2 bg-green-50">
            <div class="max-w-3xl px-2 mx-auto md:px-4">
                <div class="p-8 bg-white border-2 border-green-700 rounded-md shadow-xl">
                    <h3 class="mb-6 text-2xl font-black text-center text-gray-900 uppercase">
                        {{ data_get($sections, 'size_guide.title', 'সাইজ গাইড (Size Chart)') }}</h3>
                    <div class="overflow-hidden border-2 border-gray-100 rounded-md">
                        <table class="w-full text-center bg-white">
                            <thead class="text-white bg-green-800">
                                <tr>
                                    <th class="p-4">SIZE</th>
                                    <th class="p-4">WAIST (কোমর)</th>
                                    <th class="p-4">LENGTH (লেন্থ)</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-800 divide-y">
                                @foreach ($sizeRows as $row)
                                    <tr>
                                        <td class="p-4 bg-gray-50">{{ $row['size'] }}</td>
                                        <td class="p-4">{{ $row['waist'] }}</td>
                                        <td class="p-4">{{ $row['length'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if (data_get($sections, 'cta_after_size_guide.enabled', true))
        <section class="flex justify-center py-5 bg-white border-b md:py-10">
            <div class="max-w-5xl p-3 mx-2 border-2 border-red-200 rounded-md md:p-6 bg-red-50">
                <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                    <p class="text-lg font-black text-center text-red-700 md:text-left md:text-2xl">
                        {{ data_get($sections, 'cta_after_size_guide.title', 'সাইজ মিলেছে? এখন অর্ডার করুন') }}</p>
                    <a href="#order"
                        class="px-8 py-3 text-sm font-black text-white uppercase transition bg-green-700 rounded-md hover:bg-green-800">অর্ডার
                        সম্পন্ন করুন</a>
                </div>
            </div>
        </section>
    @endif

    @if (data_get($sections, 'faq.enabled', true) && !empty($faqs))
        <section class="py-5 bg-white md:py-10">
            <div class="max-w-4xl px-2 mx-auto md:px-4" x-data="{ activeFaq: 0 }">
                <h2 class="mb-8 text-3xl font-black text-center text-green-900">
                    {{ data_get($sections, 'faq.title', 'সাধারণ জিজ্ঞাসা') }}</h2>
                <div class="space-y-3">
                    @foreach ($faqs as $index => $faq)
                        <div class="overflow-hidden border-2 rounded-md shadow-sm"
                            :class="activeFaq === {{ $index }} ? 'border-green-600' : 'border-gray-100'">
                            <button @click="activeFaq = activeFaq === {{ $index }} ? -1 : {{ $index }}"
                                class="flex items-center justify-between w-full px-6 py-4 font-bold text-left hover:bg-green-50">
                                <span>{{ $faq['q'] }}</span>
                                <i class="fas"
                                    :class="activeFaq === {{ $index }} ? 'fa-minus text-red-500' :
                                        'fa-plus text-green-600'"></i>
                            </button>
                            <div x-show="activeFaq === {{ $index }}" x-cloak
                                class="px-6 py-4 text-gray-700 border-t bg-gray-50">
                                {{ $faq['a'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (data_get($sections, 'cta_after_faq.enabled', true))
        <section class="flex justify-center py-5 bg-white border-b md:py-10">
            <div class="max-w-5xl p-3 mx-2 border border-gray-200 rounded-md md:p-6 bg-gray-50">
                <div class="flex flex-col items-center justify-between gap-5 md:flex-row">
                    <p class="text-lg font-black text-center text-gray-900 md:text-left md:text-2xl">
                        {{ data_get($sections, 'cta_after_faq.title', 'আর প্রশ্ন নয়, অর্ডার দিন') }}</p>
                    <div class="flex flex-wrap items-center justify-center gap-3">
                        <a href="#order"
                            class="px-6 py-3 text-sm font-black text-white uppercase transition bg-red-600 rounded-md hover:bg-red-700">এখনই
                            অর্ডার</a>
                        <a href="{{ $callUrl }}"
                            class="px-6 py-3 text-sm font-black text-gray-900 uppercase transition bg-white border border-gray-300 rounded-md hover:bg-gray-100">কল
                            করুন</a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if (data_get($sections, 'order_form.enabled', true))
        <section id="order" class="bg-white border-t-8 border-green-800 py-7 md:py-14">
            <div class="grid gap-8 px-2 mx-auto md:px-4 max-w-7xl lg:grid-cols-12">
                <div class="lg:col-span-7">
                    <h2 class="mb-4 text-3xl font-black text-gray-900">
                        {{ data_get($sections, 'order_form.title', 'পণ্য ও পরিমাণ নির্বাচন করুন') }}</h2>
                    <p class="mb-6 text-sm font-semibold text-gray-600">
                        {{ data_get($sections, 'order_form.subtitle', 'Choose Products & Quantity') }}</p>

                    <div class="grid gap-4" :class="products.length > 4 ? 'md:grid-cols-2' : ''">
                        <template x-for="(product, index) in products" :key="product.id">
                            <div class="p-3 transition-all bg-white border rounded-md shadow-sm cursor-pointer group"
                                :class="product.selected ? 'border-blue-500 bg-blue-50' : 'border-gray-200'"
                                @click="product.selected = !product.selected">
                                <div class="flex gap-3">
                                    <input type="checkbox" x-model="product.selected"
                                        class="w-4 h-4 mt-1 cursor-pointer accent-blue-600" @click.stop>
                                    <img :src="product.image" alt=""
                                        class="object-cover w-16 h-16 transition border rounded-lg group-hover:scale-105">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-800 line-clamp-2" x-text="product.name">
                                        </p>
                                        <p class="mt-1 text-sm font-black text-green-700"
                                            x-text="product.price + '৳'"></p>
                                        <p x-show="product.free_delivery"
                                            class="mt-1 text-xs font-bold text-emerald-700">
                                            ফ্রি ডেলিভারি প্রযোজ্য
                                        </p>
                                        <div class="inline-flex items-center mt-2 text-sm bg-white border rounded"
                                            @click.stop>
                                            <button @click="decrement(index)" type="button"
                                                class="px-3 py-1 hover:bg-gray-100">−</button>
                                            <span class="w-10 font-bold text-center" x-text="product.qty"></span>
                                            <button @click="increment(index)" type="button"
                                                class="px-3 py-1 hover:bg-gray-100">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="sticky overflow-hidden bg-white border border-gray-200 rounded-md shadow-2xl top-28">
                        <div class="p-3 text-white md:py-5 md:px-6 bg-gradient-to-r from-green-700 to-green-800">
                            <h3 class="text-2xl font-black text-center">ডেলিভারি তথ্য</h3>
                            <p class="mt-1 text-sm text-center text-green-100">দ্রুত ডেলিভারির জন্য সঠিক তথ্য দিন</p>
                        </div>
                        <form class="p-3 space-y-4 md:p-6 bg-gray-50" @submit.prevent="goCheckout">
                            <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                                <div>
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wide text-gray-700 uppercase">আপনার
                                        নাম</label>
                                    <input type="text" x-model.trim="checkout.name"
                                        @blur="checkout.touched.name = true" placeholder="যেমন: মোহাম্মদ রাহাত"
                                        class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition"
                                        :class="showNameError ? 'border-red-400 focus:border-red-500' :
                                            'border-gray-200 focus:border-green-600'">
                                    <p x-cloak class="mt-1 text-xs font-semibold text-red-600" x-show="showNameError">
                                        নাম
                                        লিখুন।</p>
                                </div>

                                <div>
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wide text-gray-700 uppercase">মোবাইল
                                        নাম্বার</label>
                                    <input type="tel" x-model.trim="checkout.phone"
                                        @blur="checkout.touched.phone = true" placeholder="01XXXXXXXXX"
                                        class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition"
                                        :class="showPhoneError ? 'border-red-400 focus:border-red-500' :
                                            'border-gray-200 focus:border-green-600'">
                                    <p x-cloak class="mt-1 text-xs font-semibold text-red-600"
                                        x-show="showPhoneError">সঠিক
                                        মোবাইল নাম্বার দিন (01XXXXXXXXX)।</p>
                                </div>

                                <div class="p-3 bg-white border rounded-md lg:col-span-2">
                                    <p class="mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase">ডেলিভারি
                                        এরিয়া</p>
                                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                        <label
                                            class="flex items-center justify-between p-2 border rounded-lg cursor-pointer"
                                            :class="checkout.deliveryArea === 'inside' ? 'border-green-500 bg-green-50' :
                                                'border-gray-200'">
                                            <div class="flex items-center gap-2">
                                                <input type="radio" value="inside" x-model="checkout.deliveryArea"
                                                    class="accent-green-600">
                                                <span class="text-sm font-semibold text-gray-800">Inside Dhaka</span>
                                            </div>
                                            <span class="text-sm font-black text-green-700"
                                                x-text="hasFreeDeliveryItem ? 'FREE' : `${insideDhakaDeliveryCharge}৳`"></span>
                                        </label>
                                        <label
                                            class="flex items-center justify-between p-2 border rounded-lg cursor-pointer"
                                            :class="checkout.deliveryArea === 'outside' ? 'border-green-500 bg-green-50' :
                                                'border-gray-200'">
                                            <div class="flex items-center gap-2">
                                                <input type="radio" value="outside" x-model="checkout.deliveryArea"
                                                    class="accent-green-600">
                                                <span class="text-sm font-semibold text-gray-800">Outside Dhaka</span>
                                            </div>
                                            <span class="text-sm font-black text-green-700"
                                                x-text="hasFreeDeliveryItem ? 'FREE' : `${outsideDhakaDeliveryCharge}৳`"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="lg:col-span-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wide text-gray-700 uppercase">সম্পূর্ণ
                                        ঠিকানা</label>
                                    <textarea x-model.trim="checkout.address" @blur="checkout.touched.address = true" rows="2"
                                        placeholder="এরিয়া, থানা, জেলা সহ পূর্ণ ঠিকানা লিখুন"
                                        class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition"
                                        :class="showAddressError ? 'border-red-400 focus:border-red-500' :
                                            'border-gray-200 focus:border-green-600'"></textarea>
                                    <p x-cloak class="mt-1 text-xs font-semibold text-red-600"
                                        x-show="showAddressError">
                                        সম্পূর্ণ ঠিকানা লিখুন।</p>
                                </div>
                            </div>

                            <div x-cloak x-show="products.length > 4 && selectedCount > 2"
                                class="p-4 bg-white border-2 border-green-300 border-dashed rounded-md">
                                <div class="flex items-center justify-between text-sm font-bold text-gray-700">
                                    <span>Selected Items</span>
                                    <span class="px-2 py-1 text-white bg-green-600 rounded-sm"
                                        x-text="selectedCount"></span>
                                </div>
                                <div class="pr-1 space-y-2 overflow-y-auto max-h-32">
                                    <template x-for="item in selectedItems" :key="item.id">
                                        <div
                                            class="flex items-center justify-between p-2 text-xs border border-gray-100 rounded bg-gray-50">
                                            <div class="min-w-0">
                                                <span class="font-semibold line-clamp-1"
                                                    x-text="item.name + ' x ' + item.qty"></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-black text-green-700"
                                                    x-text="(item.price * item.qty) + '৳'"></span>
                                                <button type="button" @click="removeSelected(item.id)"
                                                    class="px-2 py-1 text-[11px] font-bold text-red-700 bg-red-100 rounded hover:bg-red-200">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <p class="mt-3 text-xs text-red-600" x-show="selectedCount === 0">অন্তত একটি প্রোডাক্ট
                                    সিলেক্ট করুন।</p>
                            </div>

                            <div class="p-4 bg-white border rounded-md">
                                <div class="flex items-center justify-between mb-1 text-sm font-bold text-gray-700">
                                    <span>Subtotal</span>
                                    <span x-text="totalPrice + '৳'"></span>
                                </div>
                                <div class="flex items-center justify-between mb-2 text-sm font-bold text-gray-700">
                                    <span>Delivery</span>
                                    <span x-text="deliveryCharge + '৳'"></span>
                                </div>
                                <div
                                    class="flex items-center justify-between pt-2 mb-3 text-sm font-black text-gray-900 border-t">
                                    <span>Grand Total</span>
                                    <span class="text-lg text-green-700" x-text="grandTotal + '৳'"></span>
                                </div>
                                <button type="submit"
                                    class="w-full py-3 text-base font-black text-white transition bg-red-600 rounded-md hover:bg-red-700 disabled:bg-gray-400"
                                    :disabled="loading || !isCheckoutValid">
                                    <span x-show="!loading">অর্ডার কনফার্ম করুন</span>
                                    <span x-show="loading">প্রসেস হচ্ছে...</span>
                                </button>
                                <a href="{{ $callUrl }}"
                                    class="block mt-3 text-sm font-bold text-center text-gray-700 hover:text-green-700">সাহায্য
                                    দরকার? কল করুন</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    @endif

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

    @if (data_get($sections, 'final_cta.enabled', true))
        <section class="flex justify-center py-5 bg-white border-b md:py-10">
            <div class="max-w-5xl p-3 mx-2 text-center text-white bg-gray-900 rounded-md md:p-6">
                <h3 class="text-2xl font-black">
                    {{ data_get($sections, 'final_cta.title', 'রিভিউ দেখলেন, এবার অর্ডার কনফার্ম করুন') }}</h3>
                @if (filled(data_get($sections, 'final_cta.subtitle')))
                    <div class="mt-2 text-sm text-gray-300">{!! data_get($sections, 'final_cta.subtitle') !!}</div>
                @endif
                @if (filled(data_get($sections, 'final_cta.image_src')))
                    <img src="{{ data_get($sections, 'final_cta.image_src') }}" alt="Final CTA"
                        class="object-cover mx-auto mt-5 border rounded-md max-h-52 border-white/20">
                @endif
                <div class="flex flex-wrap justify-center gap-3 mt-6">
                    <a href="#order"
                        class="px-8 py-3 text-sm font-black uppercase transition bg-red-600 rounded-md hover:bg-red-700">এখনই
                        অর্ডার করুন</a>
                    <a href="#order"
                        class="px-8 py-3 text-sm font-black text-green-900 uppercase transition bg-green-300 rounded-md hover:bg-green-200">অর্ডার
                        লিস্ট দেখুন</a>
                </div>
            </div>
        </section>
    @endif

    <footer class="py-5 text-center text-white bg-gray-900 md:py-10">
        <div class="max-w-4xl px-2 mx-auto md:px-4">
            @if (filled(data_get($logo, 'desktop')) || filled(data_get($logo, 'dashboard')) || filled(data_get($logo, 'login')))
                <img src="{{ asset(data_get($logo, 'desktop') ?? (data_get($logo, 'dashboard') ?? data_get($logo, 'login'))) }}"
                    alt="Company Logo" class="object-contain mx-auto max-h-20">
            @else
                <div class="text-3xl font-black text-green-500">
                    {{ data_get($sections, 'footer.title', config('app.name', 'Hotash CLOTHING')) }}</div>
            @endif
            <div class="flex justify-center gap-8 mt-5 text-3xl">
                <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer"
                    class="transition hover:text-blue-500"><i class="fab fa-facebook"></i></a>
                <a href="{{ $whatsappUrl }}" class="transition hover:text-green-500"><i
                        class="fab fa-whatsapp"></i></a>
            </div>
            <p class="max-w-2xl pt-5 mx-auto mt-6 text-sm leading-relaxed text-gray-400 border-t border-gray-800">
                {!! data_get(
                    $sections,
                    'footer.subtitle',
                    data_get(
                        $sections,
                        'footer.description',
                        'আমাদের প্রতিটি পণ্য এক্সপোর্ট কোয়ালিটি সম্পন্ন। Premium Trousers for Premium Customers.',
                    ),
                ) !!}
            </p>
        </div>
    </footer>

    <script>
        function landingProPage() {
            return {
                loading: false,
                timer: {
                    hours: '00',
                    minutes: '00',
                    seconds: '00',
                },
                galleryIndex: 0,
                reviewIndex: 0,
                checkout: {
                    name: '',
                    phone: '',
                    address: '',
                    deliveryArea: '',
                    touched: {
                        name: false,
                        phone: false,
                        address: false,
                    },
                    submitted: false,
                },
                insideDhakaDeliveryCharge: @json($insideDhakaDeliveryCharge),
                outsideDhakaDeliveryCharge: @json($outsideDhakaDeliveryCharge),
                products: @json($productsPayload),

                init() {
                    this.startTimer(new Date().getTime() + 86400000);
                    this.$nextTick(() => {
                        this.initCarouselWidths();
                        this.goToReview(0);
                        this.goToGallery(0);
                        window.addEventListener('resize', () => this.initCarouselWidths());
                    });
                },

                initCarouselWidths() {
                    const galleryTrack = this.$refs.galleryTrack;
                    if (galleryTrack) {
                        const galleryWidth = galleryTrack.clientWidth;
                        const galleryItemsToShow = window.innerWidth >= 1024 ? 3 : 1;
                        const galleryGap = window.innerWidth >= 768 ? 16 : 12;
                        const galleryTotalGap = galleryGap * (galleryItemsToShow - 1);
                        const galleryItemWidth = (galleryWidth - galleryTotalGap) / galleryItemsToShow;

                        Array.from(galleryTrack.children).forEach((item) => {
                            item.style.width = `${galleryItemWidth}px`;
                            item.style.minWidth = `${galleryItemWidth}px`;
                            item.style.flex = `0 0 ${galleryItemWidth}px`;
                        });

                        this.scrollToIndex(galleryTrack, this.galleryIndex, 'auto');
                    }

                    const reviewTrack = this.$refs.reviewTrack;
                    if (reviewTrack) {
                        const reviewWidth = reviewTrack.clientWidth;
                        const reviewItemsToShow = window.innerWidth >= 1024 ? 3 : 1;
                        const reviewGap = window.innerWidth >= 768 ? 16 : 12;
                        const reviewTotalGap = reviewGap * (reviewItemsToShow - 1);
                        const reviewItemWidth = (reviewWidth - reviewTotalGap) / reviewItemsToShow;

                        Array.from(reviewTrack.children).forEach((item) => {
                            item.style.width = `${reviewItemWidth}px`;
                            item.style.minWidth = `${reviewItemWidth}px`;
                            item.style.flex = `0 0 ${reviewItemWidth}px`;
                        });

                        this.scrollToIndex(reviewTrack, this.reviewIndex, 'auto');
                    }
                },

                startTimer(expiry) {
                    setInterval(() => {
                        const diff = expiry - new Date().getTime();
                        if (diff < 0) {
                            return;
                        }

                        this.timer.hours = String(Math.floor((diff / (1000 * 60 * 60)) % 24)).padStart(2, '0');
                        this.timer.minutes = String(Math.floor((diff / 1000 / 60) % 60)).padStart(2, '0');
                        this.timer.seconds = String(Math.floor((diff / 1000) % 60)).padStart(2, '0');
                    }, 1000);
                },

                increment(index) {
                    this.products[index].qty++;
                },

                decrement(index) {
                    if (this.products[index].qty > 1) {
                        this.products[index].qty--;
                    }
                },

                removeSelected(productId) {
                    const product = this.products.find((item) => Number(item.id) === Number(productId));
                    if (!product) {
                        return;
                    }

                    product.selected = false;
                },

                get selectedItems() {
                    return this.products.filter((item) => item.selected);
                },

                get selectedCount() {
                    return this.selectedItems.length;
                },

                get hasFreeDeliveryItem() {
                    return this.selectedItems.some((item) => Boolean(item.free_delivery));
                },

                get totalPrice() {
                    return this.selectedItems.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },

                get deliveryCharge() {
                    if (this.hasFreeDeliveryItem) {
                        return 0;
                    }

                    return this.checkout.deliveryArea === 'outside' ? this.outsideDhakaDeliveryCharge : this
                        .insideDhakaDeliveryCharge;
                },

                get grandTotal() {
                    return this.totalPrice + this.deliveryCharge;
                },

                get showNameError() {
                    const shouldValidate = this.checkout.touched.name || this.checkout.submitted;
                    return shouldValidate && this.checkout.name.length < 2;
                },

                get showPhoneError() {
                    const shouldValidate = this.checkout.touched.phone || this.checkout.submitted;
                    return shouldValidate && !/^01\d{9}$/.test(this.checkout.phone);
                },

                get showAddressError() {
                    const shouldValidate = this.checkout.touched.address || this.checkout.submitted;
                    return shouldValidate && this.checkout.address.length < 10;
                },

                get isCheckoutValid() {
                    return this.selectedCount > 0 && !this.showNameError && !this.showPhoneError && !this
                        .showAddressError && Boolean(this.checkout.deliveryArea);
                },

                nextReview() {
                    const total = {{ count($reviews) }};
                    this.reviewIndex = (this.reviewIndex + 1) % total;
                    this.scrollToIndex(this.$refs.reviewTrack, this.reviewIndex);
                },

                prevReview() {
                    const total = {{ count($reviews) }};
                    this.reviewIndex = (this.reviewIndex - 1 + total) % total;
                    this.scrollToIndex(this.$refs.reviewTrack, this.reviewIndex);
                },

                nextGallery() {
                    const total = {{ count($galleryImages) }};
                    this.galleryIndex = (this.galleryIndex + 1) % total;
                    this.scrollToIndex(this.$refs.galleryTrack, this.galleryIndex);
                },

                prevGallery() {
                    const total = {{ count($galleryImages) }};
                    this.galleryIndex = (this.galleryIndex - 1 + total) % total;
                    this.scrollToIndex(this.$refs.galleryTrack, this.galleryIndex);
                },

                goToGallery(index) {
                    this.galleryIndex = index;
                    this.scrollToIndex(this.$refs.galleryTrack, this.galleryIndex);
                },

                goToReview(index) {
                    this.reviewIndex = index;
                    this.scrollToIndex(this.$refs.reviewTrack, this.reviewIndex);
                },

                scrollToIndex(track, index, behavior = 'smooth') {
                    if (!track || !track.children[index]) {
                        return;
                    }

                    track.scrollTo({
                        left: track.children[index].offsetLeft,
                        behavior,
                    });
                },

                async goCheckout() {
                    this.checkout.submitted = true;
                    this.checkout.touched.name = true;
                    this.checkout.touched.phone = true;
                    this.checkout.touched.address = true;

                    if (!this.isCheckoutValid || this.loading) {
                        return;
                    }

                    this.loading = true;

                    try {
                        const response = await fetch(@json(route('landing-pro.checkout', $landingPagePro)), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                name: this.checkout.name,
                                phone: this.checkout.phone,
                                address: this.checkout.address,
                                delivery_area: this.checkout.deliveryArea,
                                items: this.selectedItems.map((item) => ({
                                    product_id: item.id,
                                    quantity: item.qty,
                                })),
                            }),
                        });

                        if (!response.ok) {
                            const payload = await response.json().catch(() => ({}));
                            throw new Error(payload.message || 'Failed to place order.');
                        }

                        const payload = await response.json();
                        window.location.href = payload.redirect_url;
                    } catch (error) {
                        alert(error.message || 'Could not place order. Please try again.');
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
    </script>
</body>

</html>
