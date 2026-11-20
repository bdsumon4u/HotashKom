@php
    use App\Models\Image;

    $company = setting('company');
    $sectionSettings = old('section_settings', $landingPagePro->mergedSectionSettings());
    $isCreateForm = !$landingPagePro->exists;

    $createSectionDefaults ??= \App\Models\LandingPagePro::getCreateSectionDefaults();

    $sectionSettings = array_replace_recursive($createSectionDefaults, $sectionSettings);

    $defaultFormTitle = $isCreateForm ? '' : $landingPagePro->title;
    $defaultFormSlug = $isCreateForm ? '' : $landingPagePro->slug;
    $defaultSeoTitle = $isCreateForm
        ? ''
        : data_get($landingPagePro->seo, 'title');
    $defaultSeoDescription = $isCreateForm
        ? ''
        : data_get($landingPagePro->seo, 'description');

    $itemsInitial = collect(
        old(
            'items',
            $landingPagePro->items
                ->map(
                    fn($item) => [
                        'product_id' => $item->product_id,
                        'free_delivery' => (bool) $item->free_delivery,
                        'is_active' => (bool) $item->is_active,
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
                'is_active' => filter_var(data_get($item, 'is_active', true), FILTER_VALIDATE_BOOL),
            ];
        })
        ->values()
        ->all();

    if (blank($itemsInitial)) {
        $itemsInitial = [];
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

    $rowEditorFields = [
        'features' => ['text' => ['label' => 'Feature', 'placeholder' => 'e.g. Comfortable stretch fabric', 'max' => 1000]],
        'size_guide' => [
            'size' => ['label' => 'Size', 'placeholder' => 'e.g. L', 'max' => 100],
            'waist' => ['label' => 'Waist', 'placeholder' => 'e.g. 30–32 inches', 'max' => 100],
            'length' => ['label' => 'Length', 'placeholder' => 'e.g. 39 inches', 'max' => 100],
        ],
        'faq' => [
            'q' => ['label' => 'Question', 'placeholder' => 'e.g. Can I mix different colors?', 'max' => 500],
            'a' => ['label' => 'Answer', 'placeholder' => 'Write the answer customers will see', 'multiline' => true, 'max' => 10000],
        ],
        'reviews' => [
            'name' => ['label' => 'Customer name', 'placeholder' => 'Customer name', 'max' => 255],
            'text' => ['label' => 'Review', 'placeholder' => 'Paste a real customer review', 'multiline' => true, 'max' => 10000],
        ],
    ];
    $sizeGuideHeadersInitial = data_get($sectionSettings, 'size_guide.headers', [
        'size' => 'SIZE', 'waist' => 'WAIST (কোমর)', 'length' => 'LENGTH (লম্বা)',
    ]);
    $contentRowsInitial = [];
    foreach ($rowEditorFields as $sectionKey => $fields) {
        $savedRows = data_get($sectionSettings, "$sectionKey.rows");
        if (is_array($savedRows)) {
            $contentRowsInitial[$sectionKey] = array_values($savedRows);
            continue;
        }
        $textKey = $sectionKey === 'size_guide' ? 'rows_text' : 'items_text';
        $fieldKeys = array_keys($fields);
        $contentRowsInitial[$sectionKey] = collect(preg_split('/\r\n|\r|\n/', (string) data_get($sectionSettings, "$sectionKey.$textKey", '')))
            ->filter(fn (string $line): bool => trim($line) !== '')
            ->map(function (string $line) use ($fieldKeys): array {
                $values = count($fieldKeys) === 1 ? [trim($line)] : array_map('trim', explode('|', $line, count($fieldKeys)));

                return array_combine($fieldKeys, array_pad($values, count($fieldKeys), ''));
            })
            ->values()
            ->all();
    }
    $firstSizeRow = $contentRowsInitial['size_guide'][0] ?? [];
    if (!is_array(data_get($sectionSettings, 'size_guide.rows'))
        && preg_match('/size|সাইজ|waist|কোমর|length|লম্বা|লেন্থ/iu', implode(' ', $firstSizeRow))) {
        $sizeGuideHeadersInitial = array_shift($contentRowsInitial['size_guide']);
    }

    $sections = $accordionSections ?? \App\Models\LandingPagePro::sectionLabels();
    $sectionOrderLabels = $sectionOrderLabels ?? \App\Models\LandingPagePro::reorderableSectionLabels();
    $richTextSections ??= \App\Models\LandingPagePro::getRichTextSections();
    $sectionsWithoutSubtitle ??= \App\Models\LandingPagePro::getSectionsWithoutSubtitle();
    $reorderableSectionKeys = array_keys($sectionOrderLabels);
    $sectionOrderInitial = collect(old('section_settings.section_order', data_get($sectionSettings, 'section_order', [])))
        ->map(fn($section) => (string) $section)
        ->filter(fn($section) => in_array($section, $reorderableSectionKeys, true))
        ->unique()
        ->values()
        ->concat(
            collect($reorderableSectionKeys)
                ->reject(fn($section) => collect(old('section_settings.section_order', data_get($sectionSettings, 'section_order', [])))->contains($section))
                ->values(),
        )
        ->unique()
        ->values()
        ->all();

    // Reorder $sections according to the saved order
    $sortedSections = [];
    if (isset($sections['announcement_bar'])) {
        $sortedSections['announcement_bar'] = $sections['announcement_bar'];
    }
    foreach ($sectionOrderInitial as $key) {
        if (isset($sections[$key])) {
            $sortedSections[$key] = $sections[$key];
        }
    }
    if (isset($sections['footer'])) {
        $sortedSections['footer'] = $sections['footer'];
    }
    $sections = $sortedSections;

    $quantityPricingTypeInitial = data_get($sectionSettings, 'order_form.quantity_pricing_type', $isCreateForm ? 'fixed_total' : 'percentage');
    $discountTiersInitial = array_values(data_get($sectionSettings, 'order_form.quantity_discounts') ?? []);
    $savedPageUrl = $landingPagePro->exists ? route('landing-pro.show', $landingPagePro) : null;
    $accordionSectionKeys = array_keys($sections);

    $sectionOpenInitial = collect($accordionSectionKeys)
        ->mapWithKeys(fn($key) => [$key => collect($errors->keys())->contains(fn(string $field): bool => str_starts_with($field, "section_settings.$key."))])
        ->all();
@endphp

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/dropzone.css') }}">
@endpush

@push('styles')
    <style>
        .lp-builder [x-cloak] { display: none !important; }
        .lp-builder details > summary { cursor: pointer; font-weight: 700; }
        .lp-builder details[open] > summary { margin-bottom: 12px; }
        .lp-builder .lp-anchor { scroll-margin-top: 100px; }
        .lp-builder .lp-product-list { max-height: 320px; overflow-y: auto; }
        .lp-builder .lp-product-image { width: 56px; height: 56px; object-fit: cover; border-radius: 8px; }
        .lp-builder .lp-product-row { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid #e9ecef; }
        .lp-builder .lp-product-info { flex: 1; min-width: 0; overflow-wrap: anywhere; }
        .lp-builder .lp-product-actions { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
        .lp-builder .lp-gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
        .lp-builder .lp-editor-row { padding: 12px; border: 1px solid #e9ecef; border-radius: 8px; }
        .lp-builder .lp-editor-rows { display: grid; gap: 12px; }
        .lp-builder .lp-steps { display: flex; flex-wrap: wrap; gap: 8px; }
        @media (min-width: 992px) { .lp-builder .lp-publish { position: sticky; top: 90px; } }

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

<div x-data="landingProForm()" class="row lp-builder" @invalid.capture="revealInvalidField($event.target)">
    <div class="col-12">
        <div class="p-3 mb-3 lp-card">
            <h4>Build your combo campaign</h4>
            <p class="text-muted">Choose products, set quantity prices or discounts, then customize your page. Customers can mix products and colors and choose a size for each selection.</p>
            <nav class="lp-steps" aria-label="Builder sections">
                <a @click="openBuilderPanel('lp-basics')" href="#lp-basics" class="btn btn-sm btn-outline-primary">1. Campaign details</a>
                <a @click="openBuilderPanel('lp-products')" href="#lp-products" class="btn btn-sm btn-outline-primary">2. Products & offer</a>
                <a @click="openBuilderPanel('lp-content')" href="#lp-content" class="btn btn-sm btn-outline-primary">3. Page content & images</a>
                <button type="button" class="btn btn-sm btn-outline-secondary" @click="expandAllSections">Expand all options</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" @click="collapseAllSections">Collapse all options</button>
            </nav>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please fix the following before saving:</strong>
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
    </div>
    <div class="col-lg-8">
        <details id="lp-basics" class="p-3 mb-3 lp-card lp-anchor" open>
            <summary>1. Campaign details</summary>
            <div class="mb-3 form-group">
                <label for="title">Title</label><span class="text-danger">*</span>
                <input type="text" name="title" id="title" @input="updateSlug($event.target.value)" required
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
                        @click="visitPage" @disabled(!$landingPagePro->exists || !$landingPagePro->is_published)>View saved page</button>
                </div>
                <small class="form-text text-muted">Keep this link unchanged once your ads are running. Save and publish before viewing the page.</small>
                <x-error field="slug" />
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3 form-group">
                        <label for="phone">Call Phone Number</label>
                        <input type="text" name="section_settings[phone]" id="phone"
                            value="{{ old('section_settings.phone', data_get($sectionSettings, 'phone')) }}"
                            class="form-control"
                            placeholder="e.g. 01700000000">
                        <small class="form-text text-muted">Leave blank to use default fallback: {{ $company->phone ?? 'N/A' }}</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3 form-group">
                        <label for="whatsapp">WhatsApp Number</label>
                        <input type="text" name="section_settings[whatsapp]" id="whatsapp"
                            value="{{ old('section_settings.whatsapp', data_get($sectionSettings, 'whatsapp')) }}"
                            class="form-control"
                            placeholder="e.g. 01700000000">
                        <small class="form-text text-muted">Leave blank to use default fallback: {{ $company->whatsapp ?? ($company->phone ?? 'N/A') }}</small>
                    </div>
                </div>
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
        </details>

        <details id="lp-products" class="p-3 mb-3 lp-card lp-anchor" open>
            <summary>2. Products & quantity offer</summary>
            <p class="text-muted">Add each product once. Its colors and sizes appear automatically on the customer page. Prices come from your product catalog.</p>
            <label for="lp-product-search">Find a product by name or SKU</label>
            <input id="lp-product-search" type="search" x-model.debounce.200ms="productSearch" class="form-control" placeholder="Search your catalog…">
            <small class="d-block mt-2 text-muted">Showing up to 10 results. Refine your search to find a specific product.</small>
            <div class="lp-product-list mb-3">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div class="lp-product-row">
                        <img x-show="product.image" :src="product.image" alt="" class="lp-product-image" loading="lazy">
                        <div class="lp-product-info">
                            <strong x-text="product.label"></strong>
                            <small class="d-block text-muted" x-text="product.sku ? 'SKU: ' + product.sku : ''"></small>
                            <small x-text="priceLabel(product)"></small>
                            <small class="d-block text-danger" x-cloak x-show.important="!product.is_active">Inactive in catalog</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" @click="addProduct(product)" :disabled="isProductSelected(product.id) || !product.is_active" x-text="isProductSelected(product.id) ? 'Added' : 'Add'"></button>
                    </div>
                </template>
                <p class="py-3 text-muted" x-show="filteredProducts.length === 0">No matching products. Try another name or SKU.</p>
            </div>
            <strong class="d-block" aria-live="polite" x-text="items.length + ' products in this campaign'"></strong>
            <p x-show="items.length === 0" class="mt-2 text-muted">Add products above to start building your offer.</p>
            <template x-for="(item, index) in items" :key="item.key">
                <div class="lp-product-row">
                    <div class="lp-product-info">
                        <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id">
                        <strong x-text="productFor(item).label || 'Product no longer available'"></strong>
                        <small class="d-block text-muted" x-text="priceLabel(productFor(item))"></small>
                        <template x-for="attribute in (productFor(item).attributes || [])" :key="attribute.name">
                            <small class="d-block text-muted" x-text="attribute.name + ': ' + attribute.values.join(', ')"></small>
                        </template>
                        <small class="d-block text-warning" x-cloak x-show.important="productFor(item).has_variations && !productFor(item).variation_count">No active variations. Activate variations in the product catalog before advertising.</small>
                        <small class="d-block text-danger" x-cloak x-show.important="!productFor(item).is_active">Product is inactive or unavailable. Review it before publishing.</small>
                        <div class="lp-product-actions mt-2">
                            <label class="mb-0">
                                <input type="hidden" :name="`items[${index}][free_delivery]`" value="0">
                                <input type="checkbox" :name="`items[${index}][free_delivery]`" value="1" x-model="item.free_delivery"> Free delivery
                            </label>
                            <label class="mb-0">
                                <input type="hidden" :name="`items[${index}][is_active]`" value="0">
                                <input type="checkbox" :name="`items[${index}][is_active]`" value="1" x-model="item.is_active"> Show on page
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-danger" @click="removeItem(index)">Remove</button>
                        </div>
                    </div>
                </div>
            </template>
            <small class="d-block mt-2 text-muted">Selecting any product marked “Free delivery” makes delivery free for the entire order.</small>
            <x-error field="items" class="mt-2 d-block" />
            <hr>
            <details>
            <summary>Quantity pricing</summary>
            <div class="d-flex justify-content-end mb-2">
                <button type="button" class="btn btn-sm btn-outline-primary" @click="addDiscountTier" :disabled="discountTiers.length >= 10">Add tier</button>
            </div>
            <label for="lp-quantity-pricing-type">Offer type</label>
            <select id="lp-quantity-pricing-type" name="section_settings[order_form][quantity_pricing_type]" x-model="quantityPricingType" class="form-control mb-2">
                <option value="fixed_total">Fixed total price by quantity (৳)</option>
                <option value="percentage">Percentage discount (%)</option>
            </select>
            <p class="text-muted" x-show="quantityPricingType === 'fixed_total'">Set a minimum quantity and the total price for that quantity. The highest qualifying quantity sets the per-piece rate until the next tier applies. Example: minimum 2 pieces, total ৳1,490 means ৳745 per piece for 2 or more. Totals are rounded to the nearest taka. Below the first tier, regular product prices apply.</p>
            <p class="text-muted" x-show="quantityPricingType === 'percentage'">Count all selected pieces across products and colors. The highest eligible percentage applies to the product subtotal. Discounts do not stack.</p>
            <p class="text-muted">Delivery is separate. Catalog wholesale pricing is replaced by this offer when tiers are configured.</p>
            <p x-show="!discountTiers.length" class="text-muted">No quantity offer configured. Add a tier to set your campaign pricing.</p>
            <template x-for="(tier, index) in discountTiers" :key="tier.key">
                <div class="row mb-2">
                    <div class="col-5">
                        <label :for="'tier-quantity-' + tier.key">Minimum pieces</label>
                        <input :id="'tier-quantity-' + tier.key" type="number" min="1" max="100" step="1" required class="form-control" x-model.number="tier.min_quantity" :name="`section_settings[order_form][quantity_discounts][${index}][min_quantity]`">
                    </div>
                    <div class="col-4">
                        <div x-show="quantityPricingType === 'percentage'">
                            <label :for="'tier-percent-' + tier.key">Discount (%)</label>
                            <input :id="'tier-percent-' + tier.key" type="number" min="1" max="100" step="1" :required="quantityPricingType === 'percentage'" :disabled="quantityPricingType !== 'percentage'" class="form-control" x-model.number="tier.percentage" :name="`section_settings[order_form][quantity_discounts][${index}][percentage]`">
                        </div>
                        <div x-show="quantityPricingType === 'fixed_total'">
                            <label :for="'tier-total-' + tier.key">Tier total (৳)</label>
                            <input :id="'tier-total-' + tier.key" type="number" min="1" max="10000000" step="1" :required="quantityPricingType === 'fixed_total'" :disabled="quantityPricingType !== 'fixed_total'" class="form-control" x-model.number="tier.total_price" :name="`section_settings[order_form][quantity_discounts][${index}][total_price]`">
                        </div>
                    </div>
                    <div class="col-3 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-outline-danger" @click="discountTiers.splice(index, 1)" :aria-label="'Remove discount tier ' + (index + 1)">Remove</button>
                    </div>
                </div>
            </template>
            </details>
        </details>

        <details id="lp-content" class="p-3 mb-3 lp-card lp-anchor">
            <summary>3. Page content & images</summary>
            <p class="text-muted">Expand a section to edit its content. Use its checkbox to show or hide it, and drag the handle to change its position.</p>

            <template x-for="(sectionKey, index) in sectionOrder" :key="`section-order-${sectionKey}`">
                <input type="hidden" :name="`section_settings[section_order][${index}]`" :value="sectionKey">
            </template>

            <div class="lp-sections-sortable">
                @foreach ($sections as $key => $label)
                    <div class="lp-section-card" data-section-key="{{ $key }}"
                        data-reorderable="{{ in_array($key, $reorderableSectionKeys, true) ? '1' : '0' }}">
                        <div class="lp-section-head" @click="toggleSection('{{ $key }}')"
                            role="button" tabindex="0" :aria-expanded="sectionOpen['{{ $key }}']"
                            @keydown.enter.self.prevent="toggleSection('{{ $key }}')"
                            @keydown.space.self.prevent="toggleSection('{{ $key }}')">
                        <div class="lp-section-title">
                                @if (in_array($key, $reorderableSectionKeys, true))
                                    <span class="mr-1 text-muted lp-drag-handle" title="Drag to reorder" style="cursor: grab;">&#x2630;</span>
                                @endif
                                <span class="lp-section-toggle" x-text="sectionOpen['{{ $key }}'] ? '-' : '+'"></span>
                            <div>
                                <strong>{{ $label }}</strong>
                                @if (isset($rowEditorFields[$key]))
                                    <small class="d-block text-muted" x-text="contentRows.{{ $key }}.length + ' rows · click to edit'"></small>
                                @elseif ($key === 'gallery')
                                    <small class="d-block text-muted" x-text="galleryImages.length + ' custom images · click to edit'"></small>
                                @else
                                    <small class="d-block text-muted">Click to edit content and settings</small>
                                @endif
                            </div>
                        </div>
                        <div class="mb-0 checkbox checkbox-success">
                            <input type="hidden" name="section_settings[{{ $key }}][enabled]"
                                value="0">
                            <input id="section-{{ $key }}" type="checkbox"
                                name="section_settings[{{ $key }}][enabled]" value="1"
                                @checked((bool) data_get($sectionSettings, $key . '.enabled', true)) @click.stop>
                            <label for="section-{{ $key }}" @click.stop>Enabled</label>
                        </div>
                    </div>
                        <div class="lp-section-body" x-show="sectionOpen['{{ $key }}']" x-cloak>
                            <div class="row">
                                @if (isset($singleImageFieldMap[$key]))
                                    <div class="mb-3 col-12">
                                        <label class="d-block font-weight-bold">{{ $singleImageFieldMap[$key] }}</label>
                                        <div class="mb-2" x-show="sectionImages.{{ $key }}">
                                            <img :src="sectionImages.{{ $key }}?.src" alt="{{ $singleImageFieldMap[$key] }}" class="lp-preview-image">
                                        </div>
                                        <div class="lp-product-actions">
                                            <button type="button" class="btn btn-sm btn-outline-primary" @click="openSinglePicker('{{ $key }}')" x-text="sectionImages.{{ $key }} ? 'Replace image' : 'Choose image'"></button>
                                            <button type="button" class="btn btn-sm btn-light" @click="sectionImages.{{ $key }} = null" :disabled="!sectionImages.{{ $key }}">Remove image</button>
                                        </div>
                                        <input type="hidden" name="section_settings[{{ $key }}][image_id]" :value="sectionImages.{{ $key }}?.id || ''">
                                    </div>
                                @endif
                                @if ($key === 'gallery')
                                    <div class="mb-3 col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="mb-0 font-weight-bold">Gallery images</label>
                                            <button type="button" class="btn btn-sm btn-outline-primary" @click="openGalleryPicker">Add images</button>
                                        </div>
                                        <p class="text-muted">Choose images in display order, or use the arrows to reorder them. When empty, the gallery uses product images.</p>
                                        <div class="lp-gallery-grid">
                                            <template x-for="(image, index) in galleryImages" :key="`gallery-${image.id}`">
                                                <div class="p-2 border rounded">
                                                    <img :src="image.src" :alt="'Gallery image ' + (index + 1)" class="lp-preview-image mb-2">
                                                    <div class="lp-product-actions">
                                                        <button type="button" class="btn btn-sm btn-light" @click="moveRow(galleryImages, index, -1)" :disabled="index === 0" :aria-label="'Move gallery image ' + (index + 1) + ' earlier'">↑</button>
                                                        <button type="button" class="btn btn-sm btn-light" @click="moveRow(galleryImages, index, 1)" :disabled="index === galleryImages.length - 1" :aria-label="'Move gallery image ' + (index + 1) + ' later'">↓</button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" @click="removeGalleryImage(index)">Remove</button>
                                                    </div>
                                                    <input type="hidden" :name="`section_settings[gallery][images][${index}]`" :value="image.id">
                                                </div>
                                            </template>
                                        </div>
                                        <p class="text-muted" x-show="galleryImages.length === 0">No custom gallery images selected.</p>
                                    </div>
                                @endif
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
                                    @include('admin.landing-page-pros.row-editor', ['editorKey' => 'features', 'rowFields' => $rowEditorFields['features'], 'rowLabel' => 'feature'])
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
                                    <div class="mb-3 col-12">
                                        <h6>Table headings</h6>
                                        <p class="text-muted">Include units in the headings, for example Waist (inches).</p>
                                        <div class="row">
                                            @foreach ($rowEditorFields['size_guide'] as $field => $config)
                                                <div class="col-sm-4 mb-2">
                                                    <label for="size-heading-{{ $field }}">{{ $config['label'] }} column</label>
                                                    <input id="size-heading-{{ $field }}" name="section_settings[size_guide][headers][{{ $field }}]" x-model="sizeGuideHeaders.{{ $field }}" class="form-control" maxlength="100">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @include('admin.landing-page-pros.row-editor', ['editorKey' => 'size_guide', 'rowFields' => $rowEditorFields['size_guide'], 'rowLabel' => 'size'])

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
                                    <div class="mb-3 col-md-12">
                                        <label>CTA Subtitle</label>
                                        <textarea name="section_settings[cta_after_size_guide][subtitle]" rows="2" class="form-control"
                                            placeholder="Supporting text for the size guide CTA.">{{ old('section_settings.cta_after_size_guide.subtitle', data_get($sectionSettings, 'cta_after_size_guide.subtitle')) }}</textarea>
                                    </div>
                                @endif

                                @if ($key === 'faq')
                                    @include('admin.landing-page-pros.row-editor', ['editorKey' => 'faq', 'rowFields' => $rowEditorFields['faq'], 'rowLabel' => 'question'])

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
                                    <div class="mb-3 col-md-12">
                                        <label>CTA Subtitle</label>
                                        <textarea name="section_settings[cta_after_faq][subtitle]" rows="2" class="form-control"
                                            placeholder="Supporting text for the FAQ CTA.">{{ old('section_settings.cta_after_faq.subtitle', data_get($sectionSettings, 'cta_after_faq.subtitle')) }}</textarea>
                                    </div>
                                @endif

                                @if ($key === 'reviews')
                                    @include('admin.landing-page-pros.row-editor', ['editorKey' => 'reviews', 'rowFields' => $rowEditorFields['reviews'], 'rowLabel' => 'review'])
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </details>

    </div>

    <div class="col-lg-4">
        <div class="lp-publish">
        <details class="p-3 mb-3 lp-card" open>
            <summary>Publishing options</summary>
            <div class="mb-3 form-group">
                <input type="hidden" name="is_published" value="0">
                <div class="mb-0 checkbox checkbox-success">
                    <input id="is_published" type="checkbox" name="is_published" value="1"
                        @checked(old('is_published', $landingPagePro->is_published))>
                    <label for="is_published">Publish this landing page</label>
                </div>
            </div>
            <div class="mb-0 form-group">
                <label for="published_at">Published timestamp (optional)</label>
                <input type="datetime-local" name="published_at" id="published_at"
                    value="{{ old('published_at', optional($landingPagePro->published_at)->format('Y-m-d\\TH:i')) }}"
                    class="form-control">
                <small class="form-text text-muted">This records the publish time; it does not schedule a launch. The checkbox controls whether the page is live.</small>
            </div>
        </details>

        <details class="p-3 mb-3 lp-card" open>
            <summary>Offer at a glance</summary>
            <p class="mb-2" x-text="items.filter(item => item.is_active).length + ' products shown on the page'"></p>
            <template x-for="tier in discountTiers" :key="'summary-' + tier.key">
                <p class="mb-1" x-text="quantityPricingType === 'fixed_total' ? (tier.min_quantity || '…') + '+ pieces → ৳' + (tier.min_quantity && tier.total_price ? Math.round(tier.total_price / tier.min_quantity * 100) / 100 : '…') + ' per piece' : 'Buy ' + (tier.min_quantity || '…') + '+ pieces → ' + (tier.percentage || '…') + '% off'"></p>
            </template>
            <small class="text-muted">Save, then check the customer page on mobile before starting your ads.</small>
        </details>
        <div class="p-3 mb-3 lp-card">
            <details><summary class="font-weight-bold">Search engine settings (optional)</summary>
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
            </details>
        </div>

        <div class="p-3 mb-3 lp-card">
            <details><summary class="font-weight-bold">Custom CSS (advanced)</summary>
            <div class="mb-0 form-group">
                <label for="custom_css">CSS Code</label>
                <textarea name="custom_css" id="custom_css" rows="6" class="form-control"
                    style="font-family: monospace; font-size: 13px;"
                    placeholder="/* Write custom CSS here. e.g. .hero-title { color: #ff0000; } */">{{ old('custom_css', $landingPagePro->custom_css) }}</textarea>
                <small class="form-text text-muted">This CSS will be injected into the &lt;style&gt; tag of this landing page.</small>
                <x-error field="custom_css" />
            </div>
            </details>
        </div>

        <button type="submit" class="btn btn-success btn-block" :disabled="items.length === 0">{{ $isCreateForm ? 'Create landing page' : 'Save changes' }}</button>
        <a href="{{ route('admin.landing-page-pros.index') }}" class="btn btn-light btn-block mt-2">Back to landing pages</a>
        </div>
    </div>
</div>

@push('js')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dropzone/dropzone.js') }}" defer></script>
    <script src="{{ asset('js/tinymce.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js" defer></script>
@endpush

@push('scripts')
    <script>
        function landingProForm() {
            return {
                items: @json($itemsInitial),
                productChoices: @json($productChoices),
                productSearch: '',
                discountTiers: @json($discountTiersInitial),
                quantityPricingType: @json($quantityPricingTypeInitial),
                nextKey: 0,
                slugEdited: @json(!$isCreateForm || filled(old('slug'))),
                savedPageUrl: @json($savedPageUrl),
                galleryImages: @json($galleryImagesInitial),
                contentRows: @json($contentRowsInitial),
                sizeGuideHeaders: @json($sizeGuideHeadersInitial),
                sectionImages: @json($sectionImagesInitial),
                sectionOrder: @json($sectionOrderInitial),
                sectionOpen: @json($sectionOpenInitial),
                accordionSectionKeys: @json($accordionSectionKeys),
                reorderableSectionKeys: @json($reorderableSectionKeys),
                sortableInstance: null,

                init() {
                    this.items = this.items.filter(item => item.product_id).map(item => ({...item, key: ++this.nextKey}));
                    this.discountTiers = this.discountTiers.map(tier => ({...tier, key: ++this.nextKey}));
                    Object.keys(this.contentRows).forEach(section => {
                        this.contentRows[section] = this.contentRows[section].map(row => ({...row, key: ++this.nextKey}));
                    });
                    this.$root.querySelector('#slug').addEventListener('input', () => { this.slugEdited = true; });
                    this.$nextTick(() => {
                        if (@json($errors->any())) {
                            this.$root.querySelectorAll('details').forEach(panel => { panel.open = true; });
                        }
                        this.initSectionSortable();
                    });
                },

                initSectionSortable(retryCount = 0) {
                    const container = this.$root.querySelector('.lp-sections-sortable');
                    if (!container) {
                        return;
                    }

                    if (typeof Sortable === 'undefined') {
                        if (retryCount < 20) {
                            setTimeout(() => this.initSectionSortable(retryCount + 1), 100);
                        }

                        return;
                    }

                    this.sortableInstance = new Sortable(container, {
                        animation: 150,
                        draggable: '.lp-section-card[data-reorderable="1"]',
                        handle: '.lp-drag-handle',
                        onEnd: () => this.syncSectionOrderFromDom(),
                    });

                    this.syncSectionOrderFromDom();
                },

                syncSectionOrderFromDom() {
                    const cards = Array.from(this.$root.querySelectorAll('.lp-section-card[data-reorderable="1"]'));
                    this.sectionOrder = cards
                        .map((card) => String(card.dataset.sectionKey || ''))
                        .filter((key) => this.reorderableSectionKeys.includes(key));
                },

                openBuilderPanel(id) {
                    const panel = this.$root.querySelector('#' + id);
                    if (panel) { panel.open = true; }
                },

                revealInvalidField(field) {
                    let parent = field.parentElement;
                    while (parent && this.$root.contains(parent)) {
                        if (parent.tagName === 'DETAILS') { parent.open = true; }
                        if (parent.dataset.sectionKey) { this.sectionOpen[parent.dataset.sectionKey] = true; }
                        parent = parent.parentElement;
                    }
                },

                toggleSection(key) {
                    this.sectionOpen[key] = !this.sectionOpen[key];
                },

                expandAllSections() {
                    this.$root.querySelectorAll('details').forEach(panel => { panel.open = true; });
                    this.accordionSectionKeys.forEach((key) => {
                        this.sectionOpen[key] = true;
                    });
                },

                collapseAllSections() {
                    this.$root.querySelectorAll('details').forEach(panel => { panel.open = false; });
                    this.accordionSectionKeys.forEach((key) => {
                        this.sectionOpen[key] = false;
                    });
                },

                get filteredProducts() {
                    const query = this.productSearch.trim().toLowerCase();
                    return this.productChoices.filter(product =>
                        (product.label + ' ' + (product.sku || '')).toLowerCase().includes(query)
                    ).slice(0, 10);
                },

                productFor(item) {
                    return this.productChoices.find(product => Number(product.id) === Number(item.product_id)) || {};
                },

                priceLabel(product) {
                    if (product.min_price === undefined) { return ''; }
                    return product.min_price === product.max_price
                        ? product.min_price + '৳ per piece'
                        : product.min_price + '–' + product.max_price + '৳ per piece';
                },

                isProductSelected(id) {
                    return this.items.some(item => Number(item.product_id) === Number(id));
                },

                addProduct(product) {
                    if (this.isProductSelected(product.id) || !product.is_active) { return; }
                    this.items.push({key: ++this.nextKey, product_id: product.id, free_delivery: false, is_active: true});
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                },

                addDiscountTier() {
                    if (this.discountTiers.length >= 10) { return; }
                    this.discountTiers.push({key: ++this.nextKey, min_quantity: '', percentage: '', total_price: ''});
                },

                updateSlug(title) {
                    if (this.slugEdited) { return; }
                    this.$root.querySelector('#slug').value = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
                },

                visitPage() {
                    if (this.savedPageUrl) { window.open(this.savedPageUrl, '_blank', 'noopener'); }
                },

                addContentRow(section, fields) {
                    this.contentRows[section].push({
                        ...Object.fromEntries(fields.map(field => [field, ''])), key: ++this.nextKey,
                    });
                    this.$nextTick(() => {
                        const rows = this.$root.querySelectorAll(`[data-editor-section="${section}"] .lp-editor-row`);
                        const lastRow = rows[rows.length - 1];
                        if (lastRow) {
                            lastRow.open = true;
                            lastRow.querySelector('input, textarea')?.focus();
                        }
                    });
                },

                moveRow(rows, index, direction) {
                    const destination = index + direction;
                    if (destination < 0 || destination >= rows.length) { return; }
                    rows.splice(destination, 0, rows.splice(index, 1)[0]);
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
