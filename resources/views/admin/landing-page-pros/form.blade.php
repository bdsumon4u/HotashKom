@php
    use App\Models\Image;

    $sectionSettings = old('section_settings', $landingPagePro->mergedSectionSettings());
    $isCreateForm = !$landingPagePro->exists;

    $createSectionDefaults = [
        'announcement_bar' => [
            'title' => 'Limited Time Offer! Free Shipping on Orders Over 3 Pieces.',
        ],
        'hero' => [
            'title' => 'Original Exported Guess Trouser',
            'subtitle' => '100% China Dobbi Fabric | Soft & Comfortable',
        ],
        'gallery' => [
            'title' => 'পণ্যটির আরও কিছু ছবি',
        ],
        'cta_after_gallery' => [
            'title' => 'ছবি দেখলেন, এখন অর্ডার করুন',
            'subtitle' => 'স্টক সীমিত, অফার শেষ হওয়ার আগে অর্ডার করুন',
        ],
        'video' => [
            'title' => 'ভিডিওতে বিস্তারিত দেখুন',
            'url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
        ],
        'cta_after_video' => [
            'title' => 'ভিডিও দেখলেন, এখন অর্ডার করুন',
            'subtitle' => 'স্টক সীমিত, অফার শেষ হওয়ার আগে অর্ডার করুন',
        ],
        'features' => [
            'title' => 'কেন আমাদের ট্রাউজার সেরা?',
            'items_text' => implode(PHP_EOL, [
                'অরিজিনাল চায়না ডবি ফেব্রিক (১০০%)',
                'এক্সপোর্ট কোয়ালিটি ফিনিশিং এবং স্টিচিং',
                'প্রিমিয়াম ফিটিং ও এশিয়ান সাইজ চার্ট',
                'চেইন সহ গভীর পকেট মোবাইল রাখার জন্য নিরাপদ',
                'অত্যন্ত আরামদায়ক এবং দীর্ঘস্থায়ী ফেব্রিক',
            ]),
        ],
        'size_guide' => [
            'title' => 'সাইজ গাইড (Size Chart)',
            'rows_text' => implode(PHP_EOL, [
                'L|30-32|38',
                'XL|32-34|39',
                '2X|34-36|40',
                '3XL|36-38|41',
                '4XL|38-42|42',
            ]),
        ],
        'cta_after_size_guide' => [
            'title' => 'সাইজ মিলেছে? এখন অর্ডার করুন',
        ],
        'faq' => [
            'title' => 'সাধারণ জিজ্ঞাসা',
            'items_text' => implode(PHP_EOL, [
                'ফেব্রিক কি ধোয়ার পর কালার নষ্ট হবে?|জি না, আমরা ১০০% চায়না ডবি প্রিমিয়াম কাপড় ব্যবহার করি যার কালার গ্যারান্টি আছে।',
                'ঢাকার বাইরে হোম ডেলিভারি পাওয়া যাবে?|জি অবশ্যই, আমরা সারা বাংলাদেশে কুরিয়ারের মাধ্যমে হোম ডেলিভারি দিয়ে থাকি।',
                'আমি কি ট্রাউজারটি ট্রায়াল দিয়ে নিতে পারবো?|ডেলিভারি ম্যান থাকাকালীন আপনি চেক করে নিতে পারবেন, কোনো সমস্যা থাকলে সাথে সাথেই রিটার্ন করতে পারবেন।',
                'এটির কোমর কি ইলাস্টিক?|জি, এটিতে হাই-কোয়ালিটি ইলাস্টিক এবং অ্যাডজাস্টেবল ফিতা রয়েছে যা আপনাকে দিবে সর্বোচ্চ আরাম।',
            ]),
        ],
        'cta_after_faq' => [
            'title' => 'আর প্রশ্ন নয়, অর্ডার দিন',
        ],
        'order_form' => [
            'title' => 'পণ্য ও পরিমাণ নির্বাচন করুন',
            'subtitle' => 'Choose Products & Quantity',
        ],
        'reviews' => [
            'title' => 'আমাদের কাস্টমারদের মতামত',
            'items_text' => implode(PHP_EOL, [
                '1 আসাদুল্লাহ বিন সাইদ|খুবই আরামদায়ক ট্রাউজার। প্রিমিয়াম কোয়ালিটি এক্সপোর্ট কাপড়। রেকমেন্ডেড!',
                '2 মাশরুর আহমেদ|কোয়ালিটি অনেক ভালো। কালার একদম ছবির মতোই। ডেলিভারিও খুব দ্রুত পেয়েছি।',
                '3 তানজিল ইসলাম|চায়না ডবি ফেব্রিক টা সত্যিই অনেক সফট। এই বাজেটে সেরা ট্রাউজার।',
                '4 সাইফুল ইসলাম|২টি অর্ডার করেছিলাম, সাইজ এবং ফিটিং একদম পারফেক্ট হয়েছে। ধন্যবাদ!',
                '5 রাকিবুল ইসলাম|প্রিমিয়াম ফিনিশিং এবং স্টিচিং দেখে আমি সত্যিই অবাক হয়েছি। এই দামেই এত ভালো কোয়ালিটি পাওয়া যায়, ভাবতাম না!',
                '6 মেহেদী হাসান|ডেলিভারি ম্যান এসে ট্রাউজার চেক করার সুযোগ দেয়ায় আমি খুবই খুশি। কোনো সমস্যা ছিল না, তাই সাথে সাথেই কনফার্ম করে দিয়েছি।',
            ]),
        ],
        'final_cta' => [
            'title' => 'রিভিউ দেখলেন, এবার অর্ডার কনফার্ম করুন',
            'subtitle' => 'আপনার পছন্দের কালার ও সাইজ বেছে এখনই অর্ডার দিন',
        ],
    ];

    if ($isCreateForm) {
        $sectionSettings = array_replace_recursive($createSectionDefaults, $sectionSettings);
    }

    $defaultFormTitle = $isCreateForm ? 'Hotash Clothing | Premium Exported Trousers' : $landingPagePro->title;
    $defaultFormSlug = $isCreateForm ? '' : $landingPagePro->slug;
    $defaultSeoTitle = $isCreateForm
        ? 'Hotash Clothing | Premium Exported Trousers'
        : data_get($landingPagePro->seo, 'title');
    $defaultSeoDescription = $isCreateForm
        ? 'আমাদের প্রতিটি পণ্য এক্সপোর্ট কোয়ালিটি সম্পন্ন। Premium Trousers for Premium Customers.'
        : data_get($landingPagePro->seo, 'description');

    $itemsInitial = collect(
        old(
            'items',
            $landingPagePro->items
                ->map(
                    fn($item) => [
                        'product_id' => $item->product_id,
                        'free_delivery' => (bool) $item->free_delivery,
                    ],
                )
                ->values()
                ->all(),
        ),
    )
        ->map(function ($item) {
            return [
                'product_id' => data_get($item, 'product_id'),
                'free_delivery' => filter_var(data_get($item, 'free_delivery', false), FILTER_VALIDATE_BOOL),
            ];
        })
        ->values()
        ->all();

    if (blank($itemsInitial)) {
        $itemsInitial = [['product_id' => null, 'free_delivery' => false]];
    }

    $galleryImageIds = collect(old('section_settings.gallery.images', data_get($sectionSettings, 'gallery.images', [])))
        ->filter()
        ->map(fn($id) => (int) $id)
        ->unique()
        ->values();

    $singleImageFieldMap = [
        'hero' => 'Hero Image',
        'features' => 'Features Image',
        'final_cta' => 'Final CTA Image',
    ];

    $singleImageIds = collect($singleImageFieldMap)
        ->keys()
        ->mapWithKeys(
            fn($key) => [
                $key => (int) old("section_settings.$key.image_id", data_get($sectionSettings, "$key.image_id")),
            ],
        )
        ->filter(fn($id) => $id > 0);

    $allImageIds = $galleryImageIds->merge($singleImageIds->values())->unique()->values();

    $imageMap = Image::query()
        ->whereIn('id', $allImageIds)
        ->get(['id', 'path'])
        ->mapWithKeys(
            fn(Image $image) => [
                $image->id => [
                    'id' => $image->id,
                    'src' => $image->src,
                ],
            ],
        )
        ->toArray();

    $galleryImagesInitial = $galleryImageIds->map(fn($id) => $imageMap[$id] ?? null)->filter()->values()->all();

    $sectionImagesInitial = collect($singleImageFieldMap)
        ->mapWithKeys(function ($label, $key) use ($singleImageIds, $imageMap) {
            $id = $singleImageIds->get($key);

            return [$key => $id ? $imageMap[$id] ?? null : null];
        })
        ->all();

    $sections = [
        'announcement_bar' => 'Announcement Bar',
        'hero' => 'Hero',
        'gallery' => 'Gallery',
        'video' => 'Video',
        'features' => 'Features',
        'size_guide' => 'Size Guide',
        'faq' => 'FAQ',
        'order_form' => 'Order Form',
        'reviews' => 'Reviews',
        'final_cta' => 'Final CTA',
    ];

    $richTextSections = ['hero', 'video', 'final_cta'];
    $sectionsWithoutSubtitle = ['gallery', 'video', 'features', 'size_guide', 'faq', 'reviews'];
@endphp

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/select2.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/dropzone.css') }}">
@endpush

@push('styles')
    <style>
        .select2 {
            width: 100% !important;
        }

        .lp-card {
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            background: #fff;
        }

        .lp-section-card {
            border: 1px solid #ebedf0;
            border-radius: 0.5rem;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .lp-section-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            background: #f8f9fa;
            border-bottom: 1px solid #ebedf0;
            cursor: pointer;
        }

        .lp-section-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .lp-section-toggle {
            width: 1.25rem;
            text-align: center;
            color: #495057;
            font-size: 0.875rem;
        }

        .lp-section-body {
            padding: 1rem;
            background: #fff;
        }

        .lp-preview-image {
            width: 100%;
            max-width: 180px;
            border-radius: 0.5rem;
            border: 1px solid #dee2e6;
        }
    </style>
@endpush

<div x-data="landingProForm()" x-init="init()" class="row">
    <div class="col-lg-8">
        <div class="p-3 mb-3 lp-card">
            <h5 class="mb-3">Landing Basics</h5>
            <div class="mb-3 form-group">
                <label for="title">Title</label><span class="text-danger">*</span>
                <input type="text" name="title" id="title" data-target="#slug"
                    value="{{ old('title', $defaultFormTitle) }}"
                    class="form-control @error('title') is-invalid @enderror"
                    placeholder="e.g. Premium Cotton T-Shirt Offer">
                <x-error field="title" />
            </div>

            <div class="mb-3 form-group">
                <label for="slug">Link</label><span class="text-danger">*</span>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">{{ url('/lp') }}/</div>
                    </div>
                    <x-input id="slug" name="slug" :value="old('slug', $defaultFormSlug)"
                        placeholder="e.g. premium-cotton-tshirt-offer" />
                    <button class="btn btn-secondary input-group-append align-items-center" type="button"
                        onclick="window.open('/lp/' + document.getElementById('slug').value, '_blank')">VISIT</button>
                </div>
                <x-error field="slug" />
            </div>

            <div class="row">
                <div class="col d-none">
                    <div class="mb-3 form-group">
                        <label for="template_key">Template</label><span class="text-danger">*</span>
                        <select name="template_key" id="template_key"
                            class="form-control @error('template_key') is-invalid @enderror">
                            @foreach ($templates as $key => $label)
                                <option value="{{ $key }}" @selected(old('template_key', $landingPagePro->template_key) === $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <x-error field="template_key" />
                    </div>
                </div>
            </div>
        </div>

        <div class="p-3 mb-3 lp-card">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Selected Product</h5>
                <button type="button" class="btn btn-sm btn-primary" @click="addItem">Add Row</button>
            </div>

            <div class="table-responsive">
                <table class="table mb-0 table-bordered">
                    <thead>
                        <tr>
                            <th style="min-width: 280px; max-width: 280px; width: 280px;">Product</th>
                            <th style="min-width: 150px;">Free Delivery</th>
                            <th style="min-width: 90px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td style="width: 280px; max-width: 640px;">
                                    <select class="form-control js-product-select w-100"
                                        :name="`items[${index}][product_id]`" x-model="item.product_id">
                                        <option value="">Select product</option>
                                        @foreach ($productChoices as $product)
                                            <option value="{{ $product['id'] }}">{{ $product['label'] }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="align-middle">
                                    <input type="hidden" :name="`items[${index}][free_delivery]`" value="0">
                                    <div class="mt-2 mb-0 checkbox checkbox-success">
                                        <input :id="`item-free-delivery-${index}`" type="checkbox"
                                            :name="`items[${index}][free_delivery]`" value="1"
                                            x-model="item.free_delivery">
                                        <label :for="`item-free-delivery-${index}`">Free</label>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <button type="button" class="btn btn-sm btn-danger"
                                        @click="removeItem(index)">Remove</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <x-error field="items" class="mt-2 d-block" />
        </div>

        <div class="p-3 mb-3 lp-card">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Image Picker Integration</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" @click="openGalleryPicker">Add Gallery
                    Images</button>
            </div>

            <div class="mb-3">
                <label class="d-block font-weight-bold">Gallery Images</label>
                <template x-for="(image, index) in galleryImages" :key="`gallery-${image.id}`">
                    <div class="mb-2 mr-2 d-inline-block position-relative">
                        <img :src="image.src" alt="Gallery" class="lp-preview-image" style="max-width: 120px;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute"
                            style="top: 2px; right: 2px;" @click="removeGalleryImage(index)">x</button>
                        <input type="hidden" :name="`section_settings[gallery][images][${index}]`"
                            :value="image.id">
                    </div>
                </template>
                <div class="text-muted" x-show="galleryImages.length === 0">No gallery images selected.</div>
            </div>

            <div class="row">
                @foreach ($singleImageFieldMap as $field => $label)
                    <div class="mb-3 col-md-4">
                        <label class="d-block font-weight-bold">{{ $label }}</label>
                        <div class="mb-2" x-show="sectionImages.{{ $field }}">
                            <img :src="sectionImages.{{ $field }}?.src" alt="{{ $label }}"
                                class="lp-preview-image">
                        </div>
                        <div class="gap-2 d-flex">
                            <button type="button" class="mr-2 btn btn-sm btn-primary"
                                @click="openSinglePicker('{{ $field }}')">Choose Image</button>
                            <button type="button" class="btn btn-sm btn-light"
                                @click="sectionImages.{{ $field }} = null">Clear</button>
                        </div>
                        <input type="hidden" name="section_settings[{{ $field }}][image_id]"
                            :value="sectionImages.{{ $field }}?.id || ''">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="p-3 mb-3 lp-card">
            <h5 class="mb-3">Per-Section Controls</h5>

            @foreach ($sections as $key => $label)
                <div class="lp-section-card" x-data="{ open: {{ (bool) data_get($sectionSettings, $key . '.enabled', true) ? 'true' : 'false' }} }">
                    <div class="lp-section-head" @click="open = !open">
                        <div class="lp-section-title">
                            <span class="lp-section-toggle" x-text="open ? '-' : '+'"></span>
                            <div>
                                <strong>{{ $label }}</strong>
                                <small class="d-block text-muted">Toggle + custom content</small>
                            </div>
                        </div>
                        <div class="mb-0 checkbox checkbox-success">
                            <input type="hidden" name="section_settings[{{ $key }}][enabled]"
                                value="0">
                            <input id="section-{{ $key }}" type="checkbox"
                                name="section_settings[{{ $key }}][enabled]" value="1"
                                @checked((bool) data_get($sectionSettings, $key . '.enabled', true)) @click.stop>
                            <label for="section-{{ $key }}">Enabled</label>
                        </div>
                    </div>
                    <div class="lp-section-body" x-show="open" x-cloak>
                        <div class="row">
                            @if ($key === 'announcement_bar')
                                <div class="mb-3 col-md-12">
                                    <label>Announcement Text</label>
                                    <input type="text" name="section_settings[announcement_bar][title]"
                                        value="{{ old('section_settings.announcement_bar.title', data_get($sectionSettings, 'announcement_bar.title')) }}"
                                        class="form-control"
                                        placeholder="Limited Time Offer! Free Shipping on Orders Over 3 Pieces.">
                                </div>
                            @else
                                <div class="mb-3 col-md-12">
                                    <label>Title</label>
                                    <input type="text" name="section_settings[{{ $key }}][title]"
                                        value="{{ old("section_settings.$key.title", data_get($sectionSettings, $key . '.title')) }}"
                                        class="form-control" placeholder="Section heading">
                                </div>
                                @if (!in_array($key, $sectionsWithoutSubtitle, true))
                                    <div class="mb-3 col-md-12">
                                        <label>Subtitle / Description</label>
                                        <textarea @if (in_array($key, $richTextSections, true)) editor @endif name="section_settings[{{ $key }}][subtitle]"
                                            rows="3" class="form-control" placeholder="Short supporting text for this section">{{ old("section_settings.$key.subtitle", data_get($sectionSettings, $key . '.subtitle')) }}</textarea>
                                    </div>
                                @endif
                            @endif

                            @if ($key === 'video')
                                <div class="mb-3 col-md-12">
                                    <label>Video Embed URL</label>
                                    <input type="text" name="section_settings[video][url]"
                                        value="{{ old('section_settings.video.url', data_get($sectionSettings, 'video.url')) }}"
                                        class="form-control" placeholder="https://www.youtube.com/embed/...">
                                </div>

                                <div class="mb-2 col-md-12">
                                    <div class="pt-3 border-top font-weight-bold">CTA After Video</div>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <div class="mt-2 mb-0 checkbox checkbox-success">
                                        <input type="hidden" name="section_settings[cta_after_video][enabled]"
                                            value="0">
                                        <input id="section-cta-after-video" type="checkbox"
                                            name="section_settings[cta_after_video][enabled]" value="1"
                                            @checked((bool) data_get($sectionSettings, 'cta_after_video.enabled', true))>
                                        <label for="section-cta-after-video">Show CTA After Video</label>
                                    </div>
                                </div>
                                <div class="mb-3 col-md-8">
                                    <label>CTA Title</label>
                                    <input type="text" name="section_settings[cta_after_video][title]"
                                        value="{{ old('section_settings.cta_after_video.title', data_get($sectionSettings, 'cta_after_video.title')) }}"
                                        class="form-control" placeholder="Ready to order?">
                                </div>
                                <div class="mb-3 col-md-12">
                                    <label>CTA Subtitle</label>
                                    <textarea name="section_settings[cta_after_video][subtitle]" rows="2" class="form-control"
                                        placeholder="Complete your order in a few clicks.">{{ old('section_settings.cta_after_video.subtitle', data_get($sectionSettings, 'cta_after_video.subtitle')) }}</textarea>
                                </div>
                            @endif

                            @if ($key === 'features')
                                <div class="mb-3 col-md-12">
                                    <label>Feature List (one per line)</label>
                                    <textarea name="section_settings[features][items_text]" rows="4" class="form-control"
                                        placeholder="Premium quality fabric&#10;Comfort fit&#10;Durable stitching">{{ old('section_settings.features.items_text', data_get($sectionSettings, 'features.items_text')) }}</textarea>
                                </div>
                            @endif

                            @if ($key === 'gallery')
                                <div class="mb-2 col-md-12">
                                    <div class="pt-3 border-top font-weight-bold">CTA After Gallery</div>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <div class="mt-2 mb-0 checkbox checkbox-success">
                                        <input type="hidden" name="section_settings[cta_after_gallery][enabled]"
                                            value="0">
                                        <input id="section-cta-after-gallery" type="checkbox"
                                            name="section_settings[cta_after_gallery][enabled]" value="1"
                                            @checked((bool) data_get($sectionSettings, 'cta_after_gallery.enabled', true))>
                                        <label for="section-cta-after-gallery">Show CTA After Gallery</label>
                                    </div>
                                </div>
                                <div class="mb-3 col-md-8">
                                    <label>CTA Title</label>
                                    <input type="text" name="section_settings[cta_after_gallery][title]"
                                        value="{{ old('section_settings.cta_after_gallery.title', data_get($sectionSettings, 'cta_after_gallery.title')) }}"
                                        class="form-control" placeholder="Like what you see? Order now.">
                                </div>
                                <div class="mb-3 col-md-12">
                                    <label>CTA Subtitle</label>
                                    <textarea name="section_settings[cta_after_gallery][subtitle]" rows="2" class="form-control"
                                        placeholder="Stock is limited.">{{ old('section_settings.cta_after_gallery.subtitle', data_get($sectionSettings, 'cta_after_gallery.subtitle')) }}</textarea>
                                </div>
                            @endif

                            @if ($key === 'size_guide')
                                <div class="mb-3 col-md-12">
                                    <label>Size Guide Rows (format: SIZE|WAIST|LENGTH, one per line)</label>
                                    <textarea name="section_settings[size_guide][rows_text]" rows="4" class="form-control"
                                        placeholder="M|28-30|38&#10;L|30-32|39">{{ old('section_settings.size_guide.rows_text', data_get($sectionSettings, 'size_guide.rows_text')) }}</textarea>
                                </div>

                                <div class="mb-2 col-md-12">
                                    <div class="pt-3 border-top font-weight-bold">CTA After Size Guide</div>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <div class="mt-2 mb-0 checkbox checkbox-success">
                                        <input type="hidden" name="section_settings[cta_after_size_guide][enabled]"
                                            value="0">
                                        <input id="section-cta-after-size-guide" type="checkbox"
                                            name="section_settings[cta_after_size_guide][enabled]" value="1"
                                            @checked((bool) data_get($sectionSettings, 'cta_after_size_guide.enabled', true))>
                                        <label for="section-cta-after-size-guide">Show CTA After Size Guide</label>
                                    </div>
                                </div>
                                <div class="mb-3 col-md-8">
                                    <label>CTA Title</label>
                                    <input type="text" name="section_settings[cta_after_size_guide][title]"
                                        value="{{ old('section_settings.cta_after_size_guide.title', data_get($sectionSettings, 'cta_after_size_guide.title')) }}"
                                        class="form-control" placeholder="Size matched? Place your order now.">
                                </div>
                            @endif

                            @if ($key === 'faq')
                                <div class="mb-3 col-md-12">
                                    <label>FAQ Rows (format: Question|Answer, one per line)</label>
                                    <textarea name="section_settings[faq][items_text]" rows="4" class="form-control"
                                        placeholder="How long is delivery?|Inside Dhaka 1-2 days">{{ old('section_settings.faq.items_text', data_get($sectionSettings, 'faq.items_text')) }}</textarea>
                                </div>

                                <div class="mb-2 col-md-12">
                                    <div class="pt-3 border-top font-weight-bold">CTA After FAQ</div>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <div class="mt-2 mb-0 checkbox checkbox-success">
                                        <input type="hidden" name="section_settings[cta_after_faq][enabled]"
                                            value="0">
                                        <input id="section-cta-after-faq" type="checkbox"
                                            name="section_settings[cta_after_faq][enabled]" value="1"
                                            @checked((bool) data_get($sectionSettings, 'cta_after_faq.enabled', true))>
                                        <label for="section-cta-after-faq">Show CTA After FAQ</label>
                                    </div>
                                </div>
                                <div class="mb-3 col-md-8">
                                    <label>CTA Title</label>
                                    <input type="text" name="section_settings[cta_after_faq][title]"
                                        value="{{ old('section_settings.cta_after_faq.title', data_get($sectionSettings, 'cta_after_faq.title')) }}"
                                        class="form-control" placeholder="No more questions. Place your order now.">
                                </div>
                            @endif

                            @if ($key === 'reviews')
                                <div class="mb-3 col-md-12">
                                    <label>Review Rows (format: Name|Review text, one per line)</label>
                                    <textarea name="section_settings[reviews][items_text]" rows="4" class="form-control"
                                        placeholder="Customer One|Amazing quality and fit.">{{ old('section_settings.reviews.items_text', data_get($sectionSettings, 'reviews.items_text')) }}</textarea>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="p-3 mb-3 lp-card">
            <h5 class="mb-3">SEO</h5>
            <div class="mb-3 form-group">
                <label>SEO Title</label>
                <input type="text" name="seo[title]" value="{{ old('seo.title', $defaultSeoTitle) }}"
                    class="form-control" placeholder="Meta title for search engines">
            </div>
            <div class="mb-0 form-group">
                <label>SEO Description</label>
                <textarea name="seo[description]" rows="4" class="form-control"
                    placeholder="Short SEO description (recommended around 150-160 chars)">{{ old('seo.description', $defaultSeoDescription) }}</textarea>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="p-3 mb-3 lp-card">
            <h5 class="mb-3">Publish Controls</h5>
            <div class="mb-3 form-group">
                <input type="hidden" name="is_published" value="0">
                <div class="mb-0 checkbox checkbox-success">
                    <input id="is_published" type="checkbox" name="is_published" value="1"
                        @checked(old('is_published', $landingPagePro->is_published))>
                    <label for="is_published">Publish this landing page</label>
                </div>
            </div>
            <div class="mb-0 form-group">
                <label for="published_at">Publish Date Time</label>
                <input type="datetime-local" name="published_at" id="published_at"
                    value="{{ old('published_at', optional($landingPagePro->published_at)->format('Y-m-d\\TH:i')) }}"
                    class="form-control">
            </div>
        </div>

        @foreach ($errors->all() as $error)
            <div class="mb-2 alert alert-danger">{{ $error }}</div>
        @endforeach

        <button type="submit" class="btn btn-success btn-block">Save Landing Page Pro</button>
    </div>
</div>

@push('js')
    <script src="{{ asset('assets/js/select2/select2.full.min.js') }}" defer></script>
    <script src="{{ asset('assets/js/select2/select2-custom.js') }}" defer></script>
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dropzone/dropzone.js') }}" defer></script>
    <script src="{{ asset('js/tinymce.js') }}" defer></script>
@endpush

@push('scripts')
    <script>
        function landingProForm() {
            return {
                items: @json($itemsInitial),
                galleryImages: @json($galleryImagesInitial),
                sectionImages: @json($sectionImagesInitial),

                init() {
                    this.$nextTick(() => {
                        this.initSlugSync();
                        this.initSelect2();
                    });
                },

                initSlugSync() {
                    runWhenJQueryReady(function($) {
                        $('[name="title"]').off('keyup.landingPageProSlug').on('keyup.landingPageProSlug',
                            function() {
                                const $slug = $($(this).data('target'));
                                $slug.val(slugify($(this).val()));
                            });
                    });
                },

                initSelect2() {
                    runWhenJQueryReady(() => {
                        this.$nextTick(() => {
                            const $selects = $('.js-product-select');
                            $selects.each(function() {
                                if ($(this).hasClass('select2-hidden-accessible')) {
                                    return;
                                }

                                $(this).select2({
                                    placeholder: 'Search product',
                                    allowClear: true,
                                });
                            });
                        });
                    });
                },

                addItem() {
                    this.items.push({
                        product_id: null,
                        free_delivery: false,
                    });

                    this.$nextTick(() => this.initSelect2());
                },

                removeItem(index) {
                    if (this.items.length === 1) {
                        return;
                    }

                    this.items.splice(index, 1);
                    this.$nextTick(() => this.initSelect2());
                },

                openGalleryPicker() {
                    window.landingProImagePicker.open({
                        closeOnSelect: false,
                        onSelect: (image) => {
                            if (this.galleryImages.some((item) => Number(item.id) === Number(image.id))) {
                                return;
                            }

                            this.galleryImages.push(image);
                        },
                    });
                },

                removeGalleryImage(index) {
                    this.galleryImages.splice(index, 1);
                },

                openSinglePicker(field) {
                    window.landingProImagePicker.open({
                        onSelect: (image) => {
                            this.sectionImages[field] = image;
                        },
                    });
                },
            };
        }
    </script>
@endpush
