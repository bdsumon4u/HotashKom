<div class="product__info">
    <h1 class="mb-2 product__name font-weight-bold" data-name="{{ $selectedVar->var_name }}" style="font-size: clamp(22px, 3vw, 28px); color: #0f172a; line-height: 1.3;">{{ $product->name }}</h1>
    @if ($product->short_description)
        <p class="mb-3 text-muted" style="font-size: 14px; line-height: 1.6;">{{ $product->short_description }}</p>
    @endif
    @php
        // Use loaded reviews if available to avoid N+1 queries
        if ($product->relationLoaded('reviews')) {
            $approvedReviews = $product->reviews->where('approved', true);
            $totalReviews = $approvedReviews->count();
            $overallRatings = $approvedReviews->flatMap(
                fn($review) => $review->relationLoaded('ratings')
                    ? $review->ratings->where('key', 'overall')
                    : collect(),
            );
            $averageRating = $overallRatings->count() > 0 ? $overallRatings->avg('value') : 0;
        } else {
            $averageRating = $product->averageRating('overall') ?? 0;
            $totalReviews = $product->totalReviews() ?? 0;
        }
    @endphp
    @if ($averageRating > 0)
        <div class="gap-2 mb-3 d-flex align-items-center">
            <a href="#review-form-container" class="d-flex align-items-center text-decoration-none review-rating-link"
                style="margin-top: -1px; cursor: pointer;" onclick="scrollToReviews(event)">
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= floor($averageRating))
                        <i class="fa fa-star text-warning" style="font-size: 14px;"></i>
                    @elseif($i - 0.5 <= $averageRating)
                        <i class="fa fa-star-half-alt text-warning" style="font-size: 14px;"></i>
                    @else
                        <i class="far fa-star text-muted" style="font-size: 14px;"></i>
                    @endif
                @endfor
            </a>
            <a href="#review-form-container" class="text-muted small text-decoration-none review-rating-link"
                style="cursor: pointer;" onclick="scrollToReviews(event)">
                <strong>{{ number_format($averageRating, 1) }}</strong>
                ({{ $totalReviews }} {{ Str::plural('review', $totalReviews) }})
            </a>
        </div>
    @endif

    <div class="py-2 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2" style="border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #64748b;">
        <div>Code: <strong class="text-dark">{{ $selectedVar->sku }}</strong></div>
        <div>
            @if (!$product->is_active)
                <span class="badge badge-danger px-2 py-1" style="border-radius: 3px;">Inactive</span>
            @elseif (!$selectedVar->should_track)
                <span class="badge px-2 py-1" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 3px;">
                    <i class="fas fa-circle mr-1" style="font-size: 7px; vertical-align: middle;"></i> In Stock
                </span>
            @else
                <span class="badge px-2 py-1" style="background: {{ $selectedVar->stock_count ? '#ecfdf5' : '#fef2f2' }}; color: {{ $selectedVar->stock_count ? '#059669' : '#dc2626' }}; border: 1px solid {{ $selectedVar->stock_count ? '#a7f3d0' : '#fecaca' }}; border-radius: 3px;">
                    <i class="fas fa-circle mr-1" style="font-size: 7px; vertical-align: middle;"></i> {{ $selectedVar->stock_count ? $selectedVar->stock_count.' In Stock' : 'Out of Stock' }}
                </span>
            @endif
        </div>
    </div>

    @php $show_option = setting('show_option') @endphp
    @php
        $guest_can_see_price = (bool) ($show_option->guest_can_see_price ?? false);
        $should_hide_price =
            isOninda() &&
            !$guest_can_see_price &&
            (auth('user')->guest() || (auth('user')->check() && !auth('user')->user()->is_verified));
    @endphp

    <div class="product__prices mb-3 p-3" style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 6px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);">
        <div class="d-flex align-items-baseline flex-wrap gap-2">
            <span class="text-muted font-weight-bold" style="font-size: 14px;">Price:</span>
            @if ($should_hide_price)
                <span class="text-danger font-weight-bold">
                    {{ auth('user')->guest() ? 'Login to see price' : 'Verify account to see price' }}
                </span>
            @elseif (($selling = $selectedVar->getPrice($quantity)) == $selectedVar->price)
                <span class="product-card__new-price font-weight-bold" style="color: var(--brand-dark, #059669); font-size: 26px;">
                    {!! $selling ? theMoney($selectedVar->price) : 'Contact Us' !!}
                </span>
            @else
                <span class="product-card__new-price font-weight-bold" style="color: var(--brand-dark, #059669); font-size: 26px;">
                    {!! theMoney($selling) !!}
                </span>
                <span class="product-card__old-price ml-2" style="text-decoration: line-through; color: #94a3b8; font-size: 16px;">
                    {!! theMoney($selectedVar->price) !!}
                </span>
                @php
                    $discountAmt = $selectedVar->price - $selling;
                @endphp
                @if($discountAmt > 0)
                    <span class="badge ml-2" style="background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; font-size: 12px; font-weight: 700; border-radius: 4px; padding: 4px 8px;">
                        Save {!! theMoney($discountAmt) !!}
                    </span>
                @endif
            @endif
        </div>
    </div>

    @if (isOninda())
        <div class="px-3 py-2 mb-3 product__actions-item d-flex justify-content-between align-items-center"
            style="border: 1.5px solid var(--brand); border-radius: 5px; background: rgba(var(--brand-rgb), 0.04);">
            <div class="mr-2 font-weight-bold text-dark" style="white-space:nowrap; font-size: 14px;">Your Resell Price:</div>
            <div class="input-group input-group-sm" style="max-width: 140px;">
                <input type="number" class="form-control form-control-sm text-right font-weight-bold" wire:model="retailPrice" min="0"
                    @focus="$event.target.select()" required style="border-radius: 4px 0 0 4px; border: 1.5px solid #cbd5e1;">
                <div class="input-group-append">
                    <span class="input-group-text font-weight-bold" style="border-radius: 0 4px 4px 0; background: #f1f5f9; border: 1.5px solid #cbd5e1; border-left: none;">৳</span>
                </div>
            </div>
        </div>
        <div class="mb-3 small text-muted">
            <i class="fa fa-info-circle text-primary"></i> Suggested retail price:
            <strong>{{ $selectedVar->suggestedRetailPrice() }}</strong>
        </div>
    @endif

    @foreach ($attributes as $attribute)
        @php
            $attributeOptions = $optionGroup[$attribute->id] ?? [];
            if (empty($attributeOptions)) {
                continue;
            }
        @endphp
        <div class="mb-3 form-group product__option">
            <label class="product__option-label d-block font-weight-bold mb-2" style="font-size: 13px; color: #334155;">{{ $attribute->name }}:</label>
            @if (strtolower($attribute->name) == 'color')
                <div class="input-radio-color">
                    <div class="input-radio-color__list d-flex flex-wrap gap-2">
                        @foreach ($attributeOptions as $option)
                            <label
                                class="input-radio-color__item @if (strtolower($option->name) == 'white') input-radio-color__item--white @endif"
                                style="color: {{ $option->value }}; cursor: pointer;" data-toggle="tooltip" title="{{ $option->name }}">
                                <input type="radio" wire:model.live="options.{{ $attribute->id }}"
                                    name="options[{{ $attribute->id }}]" value="{{ $option->id }}"
                                    class="option-picker">
                                <span></span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="input-radio-label">
                    <div class="input-radio-label__list d-flex flex-wrap gap-2">
                        @foreach ($attributeOptions as $option)
                            <label style="cursor: pointer; margin-bottom: 0;">
                                <input type="radio" wire:model.live="options.{{ $attribute->id }}"
                                    name="options[{{ $attribute->id }}]" value="{{ $option->id }}"
                                    class="option-picker d-none">
                                <span class="px-3 py-1 d-inline-block font-weight-bold"
                                    style="border-radius: 4px; font-size: 13px; border: 1.5px solid {{ ($options[$attribute->id] ?? null) == $option->id ? 'var(--brand)' : '#e2e8f0' }}; background: {{ ($options[$attribute->id] ?? null) == $option->id ? 'rgba(var(--brand-rgb), 0.08)' : '#ffffff' }}; color: {{ ($options[$attribute->id] ?? null) == $option->id ? 'var(--brand-dark)' : '#334155' }}; transition: all 0.2s ease;">
                                    {{ $option->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endforeach

    <!-- .product__sidebar -->
    <div class="product__sidebar">
        <!-- .product__options -->
        <form class="product__options">
            <div class="mb-3 form-group product__option">
                <div class="p-3 mb-3 product__actions-item d-flex justify-content-between align-items-center" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <span class="font-weight-bold text-dark" style="font-size: 14px;">Quantity</span>
                    <div class="input-number product__quantity d-flex align-items-center" style="border: 1.5px solid #cbd5e1; border-radius: 4px; overflow: hidden; background: #fff;">
                        <input id="product-quantity" class="input-number__input form-control text-center font-weight-bold"
                            wire:model.live="quantity" type="number" min="1" max="{{ $maxQuantity }}"
                            value="1" readonly style="border: none; width: 48px; height: 38px; font-size: 15px;">
                        <div class="input-number__add" wire:click="increment" style="cursor: pointer;"></div>
                        <div class="input-number__sub" wire:click="decrement" style="cursor: pointer;"></div>
                    </div>
                </div>

                <div class="product__actions">
                    @php $available = !$selectedVar->should_track || $selectedVar->stock_count > 0 @endphp
                    <div class="product__buttons d-flex flex-row align-items-stretch gap-2 w-100">
                        @if ($show_option->product_detail_order_now ?? false)
                            <div class="product__actions-item product__actions-item--ordernow" style="flex: 1 1 50%; min-width: 0;">
                                <button type="button" wire:click="addToCart('kart')"
                                    class="btn product__ordernow bb-btn-ordernow-animated w-100 d-flex align-items-center justify-content-center gap-2"
                                    {{ $available ? '' : 'disabled' }}>
                                    <i class="fas fa-bolt"></i>
                                    <span>{{ $show_option->order_now_text ?? 'অর্ডার করুন' }}</span>
                                </button>
                            </div>
                        @endif
                        @if ($show_option->product_detail_add_to_cart ?? false)
                            <div class="product__actions-item product__actions-item--addtocart" style="flex: 1 1 50%; min-width: 0;">
                                <button type="button" wire:click="addToCart"
                                    class="btn product__addtocart bb-btn-addtocart-detail w-100 d-flex align-items-center justify-content-center gap-2"
                                    {{ $available ? '' : 'disabled' }}>
                                    <i class="fas fa-bag-shopping"></i>
                                    <span>{{ $show_option->add_to_cart_text ?? 'কার্টে রাখুন' }}</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if(setting('call_for_order'))
            <div class="p-3 my-3 text-center call-for-order" style="border: 1.5px dashed #cbd5e1; border-radius: 6px; background: #f8fafc;">
                <div style="font-size: 13px; color: #64748b; font-weight: 500;">এই পণ্য সম্পর্কে প্রশ্ন আছে? সরাসরি কল করুন:</div>
                @foreach (explode(' ', setting('call_for_order')) as $phone)
                    @if ($phone = trim($phone))
                        <a href="tel:{{ $phone }}" style="text-decoration: none;">
                            <div class="mt-1 lead font-weight-bold" style="color: var(--brand-dark); font-size: 18px;">
                                <i class="mr-2 fas fa-phone-volume"></i>
                                <span>{{ $phone }}</span>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
            @endif

            @php
                $company = setting('company');
                $phone = preg_replace('/[^\d]/', '', $company->whatsapp ?? '');
                $phone = strlen($phone) == 11 ? '88' . $phone : $phone;
                $messenger = $company->messenger ?? '';
                $whatsappMessage = rawurlencode(
                    "Hello\r\nI am interested in ordering \"{$product->name}\".\r\n\r\n" . url()->current(),
                );
                $whatsappLink = "https://api.whatsapp.com/send?phone={$phone}&text={$whatsappMessage}";
            @endphp
            <div class="gap-2 my-3 d-flex justify-content-center">
                @if (strlen($messenger) > 13)
                    <a href="{{ $messenger }}" target="_blank" rel="noopener"
                        data-contact-type="messenger"
                        class="btn d-flex align-items-center justify-content-center flex-fill" style="background: #0084ff; color: #fff; font-weight: 700; border-radius: 4px; min-height: 40px; font-size: 13px;">
                        <i class="mr-2 fab fa-facebook-messenger" style="font-size: 16px;"></i> Messenger
                    </a>
                @endif
                <a href="{{ $whatsappLink }}" rel="noopener"
                    data-contact-type="whatsapp"
                    class="btn d-flex align-items-center justify-content-center flex-fill whatsapp-link" style="background: #25d366; color: #fff; font-weight: 700; border-radius: 4px; min-height: 40px; font-size: 13px;"
                    data-whatsapp-url="{{ $whatsappLink }}">
                    <i class="mr-2 fab fa-whatsapp" style="font-size: 16px;"></i> WhatsApp
                </a>
            </div>

            @if (($free_delivery->enabled ?? false) && $deliveryText)
                <div class="p-3 my-3 text-center border font-weight-bold" style="background: #ecfdf5; border-color: #a7f3d0 !important; border-radius: 4px; color: #065f46; font-size: 13px;">
                    <p class="mb-1" style="font-size: 14px;">আজ অর্ডার করলে সারা বাংলাদেশে ডেলিভারি চার্জ <strong class="text-danger">ফ্রি</strong></p>
                    {!! $deliveryText !!}
                </div>
            @endif

            @if ($product->variations->isEmpty() || $showBrandCategory)
                <div class="p-3 my-3 border product__footer" style="background: #f8fafc; border-radius: 6px; border-color: #e2e8f0 !important;">
                    <div class="product__tags tags">
                        <div>
                            <span class="mb-0 mr-2 text-muted font-weight-bold d-inline-block" style="font-size: 13px;">Categories:</span>
                            @foreach ($product->categories as $category)
                                <a href="{{ route('category.show', $category) }}"
                                    class="badge px-2 py-1 mr-1" style="background: rgba(var(--brand-rgb), 0.1); color: var(--brand-dark); border-radius: 3px; font-size: 12px; font-weight: 600;" wire:navigate.hover>{{ $category->name }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            @once
                <link rel="stylesheet" href="{{ asset('css/hk-product-top-premium.css') }}">
            @endonce

            @php
                $areaColumns = app(\App\Services\DeliveryAreaService::class)->getProductDeliveryCharges($selectedVar);
                $isTwoColumns = $areaColumns->count() === 2;
            @endphp

            @if ($areaColumns->isNotEmpty())
                <section class="bb-delivery-charge-card" aria-label="Delivery Charge">
                    <div class="bb-delivery-charge-head">
                        <div class="bb-delivery-charge-heading">
                            <span class="bb-delivery-charge-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 6.5h11v9H3v-9Zm11 3h3.4l2.6 3v3H14v-6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <circle cx="7" cy="17" r="1.7" stroke="currentColor" stroke-width="1.8"/>
                                    <circle cx="17" cy="17" r="1.7" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                            </span>
                            <span class="bb-delivery-charge-copy">
                                <strong class="bb-delivery-charge-title">
                                    Delivery Charge
                                </strong>
                            </span>
                        </div>
                    </div>

                    <div class="bb-delivery-charge-body">
                        <div class="bb-delivery-charge-grid {{ $isTwoColumns ? 'bb-delivery-charge-grid--cols-2' : 'bb-delivery-charge-grid--stacked' }}">
                            @foreach ($areaColumns as $col)
                                <div class="bb-delivery-charge-item">
                                    <span class="bb-delivery-area-label">
                                        {{ $col['name'] }}
                                    </span>
                                    <span class="bb-delivery-charge-price">
                                        {!! theMoney($col['cost']) !!}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="bb-delivery-charge-trust">
                            <span class="bb-delivery-charge-check" aria-hidden="true">
                                ✓
                            </span>
                            <span>
                                সারা বাংলাদেশে Cash on Delivery
                            </span>
                        </div>
                    </div>
                </section>
            @endif
            @php
                $wholesale = $selectedVar->wholesale ?? ['quantity' => [], 'price' => []];
                $quantities = is_array($wholesale['quantity'] ?? null) ? $wholesale['quantity'] : [];
                $prices = is_array($wholesale['price'] ?? null) ? $wholesale['price'] : [];
            @endphp

            @if (!empty($quantities) && !empty($prices))
                <div class="mt-3">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th colspan="2" class="text-center">Wholesale Price</th>
                            </tr>
                            <tr>
                                <th width="50%">Min. Quantity</th>
                                <th width="50%">Unit Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($quantities as $i => $qty)
                                <tr>
                                    <td>{{ $qty }}</td>
                                    <td>{!! theMoney($prices[$i] ?? null) !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </form><!-- .product__options / end -->
    </div><!-- .product__end -->
</div>
