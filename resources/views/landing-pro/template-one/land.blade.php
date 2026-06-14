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

                return [data_get($product, 'base_product_image')];
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
        ->flatMap(function ($item): array {
            $landingProductId = (int) data_get($item, 'landing_product_id');
            $freeDelivery = (bool) data_get($item, 'free_delivery', false);

            return collect(data_get($item, 'cards', []))
                ->map(function ($card) use ($landingProductId, $freeDelivery): array {
                    return [
                        'id' => data_get($card, 'card_id'),
                        'landing_product_id' => $landingProductId,
                        'name' => data_get($card, 'title'),
                        'selected_product_id' => (int) data_get($card, 'selected_product_id'),
                        'price' => (int) data_get($card, 'price', 0),
                        'image' => data_get($card, 'image'),
                        'attributes' => data_get($card, 'attributes', []),
                        'variants' => data_get($card, 'variants', []),
                        'selected' => false,
                        'attribute_warnings' => [],
                        'qty' => 1,
                        'free_delivery' => $freeDelivery,
                    ];
                })
                ->all();
        })
        ->values()
        ->all();

    $videoUrl = data_get($sections, 'video.url', 'https://www.youtube.com/embed/dQw4w9WgXcQ');
    $heroImageUrl = data_get($sections, 'hero.image_src');

    $defaultSectionOrder = array_keys(\App\Models\LandingPagePro::reorderableSectionLabels());
    $configuredSectionOrder = collect(data_get($sections, 'section_order', []))
        ->map(fn($section) => (string) $section)
        ->filter(fn($section) => in_array($section, $defaultSectionOrder, true))
        ->unique()
        ->values()
        ->concat(
            collect($defaultSectionOrder)
                ->reject(fn($section) => collect(data_get($sections, 'section_order', []))->contains($section))
                ->values(),
        )
        ->unique()
        ->values();

    $ctaSectionMap = [
        'gallery' => 'cta_after_gallery',
        'video' => 'cta_after_video',
        'size_guide' => 'cta_after_size_guide',
        'faq' => 'cta_after_faq',
    ];
@endphp

<body class="text-gray-900 bg-gray-50" x-data="landingProPage()" x-init="init()">
    @includeIf('landing-pro.template-one.sections.announcement_bar')

    @foreach ($configuredSectionOrder as $sectionKey)
        @includeIf('landing-pro.template-one.sections.' . $sectionKey)
        @if (isset($ctaSectionMap[$sectionKey]))
            @includeIf('landing-pro.template-one.sections.' . $ctaSectionMap[$sectionKey])
        @endif
    @endforeach

    @includeIf('landing-pro.template-one.sections.footer')

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
                    this.products.forEach((_, index) => this.selectVariantByAttributes(index));
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

                hasUnselectedAttributes(product) {
                    if (!product || !Array.isArray(product.attributes) || product.attributes.length === 0) {
                        return false;
                    }

                    return product.attributes.some((attribute) => {
                        return attribute.selected_option_id === null || attribute.selected_option_id === undefined || attribute
                            .selected_option_id === '';
                    });
                },

                getMissingAttributeNames(product) {
                    if (!product || !Array.isArray(product.attributes)) {
                        return [];
                    }

                    return product.attributes
                        .filter((attribute) => {
                            return attribute.selected_option_id === null || attribute.selected_option_id === undefined || attribute
                                .selected_option_id === '';
                        })
                        .map((attribute) => String(attribute.attribute_name || 'Attribute'));
                },

                toggleProductSelection(index) {
                    const product = this.products[index];
                    if (!product) {
                        return;
                    }

                    product.selected = !product.selected;
                    if (!product.selected) {
                        product.attribute_warnings = [];
                    }
                },

                selectVariantByAttributes(index) {
                    const product = this.products[index];
                    if (!product || !Array.isArray(product.variants) || product.variants.length === 0) {
                        return;
                    }

                    if (!Array.isArray(product.attributes) || product.attributes.length === 0) {
                        return;
                    }

                    const hasUnselectedAttribute = product.attributes.some((attribute) => {
                        return attribute.selected_option_id === null || attribute.selected_option_id === undefined || attribute
                            .selected_option_id === '';
                    });

                    if (hasUnselectedAttribute) {
                        return;
                    }

                    product.attribute_warnings = [];

                    const selectedOptionMap = product.attributes.reduce((carry, attribute) => {
                        carry[String(attribute.attribute_id)] = Number(attribute.selected_option_id);

                        return carry;
                    }, {});

                    let matchedVariant = product.variants.find((variant) => {
                        const optionIds = variant.option_ids || {};

                        return Object.entries(selectedOptionMap).every(([attributeId, optionId]) => {
                            return Number(optionIds[attributeId]) === Number(optionId);
                        });
                    });

                    if (!matchedVariant) {
                        return;
                    }

                    product.selected_product_id = Number(matchedVariant.id);
                    product.price = Number(matchedVariant.price || 0);
                    product.image = matchedVariant.image || product.image;
                    product.name = matchedVariant.name || product.name;
                },

                removeSelected(productId) {
                    const product = this.products.find((item) => String(item.id) === String(productId));
                    if (!product) {
                        return;
                    }

                    product.selected = false;
                    product.attribute_warnings = [];
                },

                validateSelectedProductAttributes() {
                    let hasMissingAttributes = false;

                    this.products.forEach((product) => {
                        if (!product.selected) {
                            product.attribute_warnings = [];
                            return;
                        }

                        const missingNames = this.getMissingAttributeNames(product);
                        product.attribute_warnings = missingNames;

                        if (missingNames.length > 0) {
                            hasMissingAttributes = true;
                        }
                    });

                    return !hasMissingAttributes;
                },

                get selectedItems() {
                    return this.products.filter((item) => item.selected);
                },

                get selectedCount() {
                    return this.selectedItems.length;
                },

                get checkoutAttributeWarnings() {
                    const warnings = this.selectedItems
                        .flatMap((item) => {
                            return (item.attribute_warnings || []).map((warning) => `${warning} সিলেক্ট করুন`);
                        });

                    return [...new Set(warnings)];
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
                    return shouldValidate && this.checkout.address.length < 5;
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

                    if (!this.validateSelectedProductAttributes()) {
                        return;
                    }

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
                                    landing_product_id: item.landing_product_id,
                                    product_id: item.selected_product_id,
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
