<div x-data="sumPrices({
        retail: @js($retail ?? []),
        advanced: @js($advanced ?? 0),
        retail_delivery: @js($retailDeliveryFee ?? 0),
        retailDiscount: @js($retailDiscount ?? 0),
        couponDiscount: @js($coupon_discount ?? 0),
    })" class="modern-checkout-container">

    @if (session()->has('error'))
        <div class="p-4 mb-4 text-center bg-white border rounded shadow-sm text-danger">
            <h4 class="mb-0">{{ session('error') }}</h4>
        </div>
    @else


        <div class="row modern-checkout-layout">
            {{-- Form Column (Desktop Left, Mobile Middle) --}}
            <div class="col-12 col-lg-7 modern-col-form">
                <div class="modern-card">
                    {{-- Delivery Info Section Header --}}
                    <div class="modern-card-header d-flex align-items-center">
                        <div class="mr-2 modern-header-icon modern-icon-user">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <h3 class="mb-0 modern-card-title">ডেলিভারি তথ্য দিন</h3>
                    </div>

                    <div class="modern-card-body">
                        {{-- Name Field --}}
                        <div class="modern-form-group">
                            <label class="modern-label">
                                আপনার নাম <span class="text-danger">*</span>
                            </label>
                            <x-input name="name"
                                wire:model="name"
                                class="modern-input"
                                place-holder="আপনার সম্পূর্ণ নাম লিখুন"
                                placeholder="আপনার সম্পূর্ণ নাম লিখুন" />
                            <x-error field="name" />
                        </div>

                        {{-- Phone Field --}}
                        <div class="modern-form-group">
                            <label class="modern-label">
                                আপনার মোবাইল <span class="text-danger">*</span>
                            </label>
                            <div class="modern-phone-wrapper d-flex @error('phone') is-invalid @enderror">
                                @unless (setting('show_option')->hide_phone_prefix ?? false)
                                    <div class="modern-phone-prefix d-flex align-items-center justify-content-center">
                                        +88
                                    </div>
                                @endunless
                                <div class="flex-grow-1">
                                    <x-input type="tel"
                                        name="phone"
                                        wire:model="phone"
                                        class="modern-input {{ !(setting('show_option')->hide_phone_prefix ?? false) ? 'modern-input-with-prefix' : '' }}"
                                        place-holder="আপনার মোবাইল নম্বর"
                                        placeholder="আপনার মোবাইল নম্বর" />
                                </div>
                            </div>
                            <x-error field="phone" />
                        </div>

                        {{-- Email Field (Optional) --}}
                        @if (setting('show_option')->email ?? false)
                            <div class="modern-form-group">
                                <label class="modern-label">
                                    আপনার ইমেইল
                                </label>
                                <x-input type="email"
                                    name="email"
                                    wire:model="email"
                                    class="modern-input"
                                    place-holder="আপনার ইমেইল ঠিকানা লিখুন"
                                    placeholder="আপনার ইমেইল ঠিকানা লিখুন" />
                                <x-error field="email" />
                            </div>
                        @endif

                        {{-- Address Field --}}
                        <div class="modern-form-group">
                            <label class="modern-label">
                                আপনার ঠিকানা <span class="text-danger">*</span>
                            </label>
                            <x-textarea name="address"
                                wire:model="address"
                                class="modern-input modern-textarea"
                                rows="3"
                                place-holder="বিস্তারিত ঠিকানা লিখুন (বাসা, রোড, এরিয়া, ল্যান্ডমার্ক)"
                                placeholder="বিস্তারিত ঠিকানা লিখুন (বাসা, রোড, এরিয়া, ল্যান্ডমার্ক)"></x-textarea>
                            <x-error field="address" />
                        </div>

                        {{-- Delivery Area Field (Interactive Radio Cards) --}}
                        <div class="modern-form-group">
                            <label class="mb-2 modern-label d-block">
                                ডেলিভারি এরিয়া <span class="text-danger">*</span>
                            </label>
                            <div class="modern-shipping-grid">
                                @foreach (app(\App\Services\DeliveryAreaService::class)->getDeliveryAreas() as $index => $area)
                                    @php
                                        $areaName = data_get($area, 'name');
                                        $isSelected = ($shipping === $areaName);
                                        $cost = $isFreeDelivery ? 0 : $this->shippingCost($areaName);
                                    @endphp
                                    <label class="modern-shipping-card {{ $isSelected ? 'is-selected' : '' }}" for="modern-shipping-{{ $index }}">
                                        <input type="radio"
                                            id="modern-shipping-{{ $index }}"
                                            wire:model.live="shipping"
                                            @change="$wire.updateField('shipping', $event.target.value)"
                                            name="shipping"
                                            value="{{ $areaName }}"
                                            class="modern-shipping-radio">
                                        
                                        <div class="mr-2 modern-shipping-custom-radio">
                                            <span class="modern-radio-dot"></span>
                                        </div>

                                        <div class="mr-2 modern-shipping-icon-badge {{ $isSelected ? 'icon-active' : '' }}">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                        </div>

                                        <div class="modern-shipping-info">
                                            <div class="modern-shipping-name">{{ $areaName }}</div>
                                            <div class="modern-shipping-cost">
                                                ডেলিভারি চার্জ: <strong>TK {{ $isFreeDelivery ? '0 (FREE)' : $cost }}</strong>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <x-error field="shipping" />
                        </div>

                        {{-- Optional Note Field --}}
                        @unless (setting('show_option')->hide_checkout_note ?? false)
                            <div class="modern-form-group">
                                <label class="modern-label">
                                    নোট (অপশনাল)
                                </label>
                                <x-textarea name="note"
                                    wire:model="note"
                                    class="modern-input modern-textarea-sm"
                                    rows="2"
                                    place-holder="আপনার অর্ডার সম্পর্কে কিছু জানাতে চাইলে লিখুন..."
                                    placeholder="আপনার অর্ডার সম্পর্কে কিছু জানাতে চাইলে লিখুন..."></x-textarea>
                                <x-error field="note" />
                            </div>
                        @endunless

                        {{-- Pathao City & Area selection (if configured) --}}
                        @if ((setting('Pathao')->enabled ?? false) && (setting('Pathao')->user_selects_city_area ?? false))
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="modern-form-group">
                                        <label class="modern-label">
                                            জেলা @if(setting('Pathao')->user_required_city_area ?? false)<span class="text-danger">*</span>@endif
                                        </label>
                                        <select class="form-control modern-input @error('city_id') is-invalid @enderror" wire:model.live="city_id">
                                            <option value="">জেলা নির্বাচন করুন</option>
                                            @foreach ($pathaoCities as $city)
                                                <option value="{{ $city->city_id }}">{{ $city->city_name }}</option>
                                            @endforeach
                                        </select>
                                        <x-error field="city_id" />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="modern-form-group">
                                        <label class="modern-label">
                                            এলাকা @if(setting('Pathao')->user_required_city_area ?? false)<span class="text-danger">*</span>@endif
                                        </label>
                                        <div wire:loading.class="d-flex" wire:target="city_id" class="p-2 d-none modern-input align-items-center">
                                            এলাকা লোড হচ্ছে...
                                        </div>
                                        <select wire:loading.remove
                                            wire:target="city_id"
                                            class="form-control modern-input @error('area_id') is-invalid @enderror"
                                            wire:model.live="area_id">
                                            <option value="">এলাকা নির্বাচন করুন</option>
                                            @foreach ($pathaoAreas as $area)
                                                <option value="{{ $area->zone_id }}">{{ $area->zone_name }}</option>
                                            @endforeach
                                        </select>
                                        <x-error field="area_id" />
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Cart & Summary Column (Desktop Right, Mobile Top + Bottom) --}}
            <div class="col-12 col-lg-5 modern-col-summary">
                {{-- Order Items Section --}}
                <div class="mb-3 modern-card modern-card-order-items">
                    <div class="modern-card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="mr-2 modern-header-icon modern-icon-cart">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                            </div>
                            <h3 class="mb-0 modern-card-title">
                                আপনার অর্ডার <span class="modern-item-count">({{ cart()->count() }} টি আইটেম)</span>
                            </h3>
                        </div>
                    </div>

                    <div class="modern-card-body">
                        <div class="modern-cart-list">
                            @forelse (cart()->content() as $product)
                                <div class="modern-cart-item" data-id="{{ $product->id }}">
                                    <div class="modern-cart-item-row d-flex">
                                        {{-- Thumbnail --}}
                                        <div class="mr-3 modern-cart-thumb">
                                            <img src="{{ asset($product->options->image) }}" alt="{{ $product->name }}">
                                        </div>

                                        {{-- Details --}}
                                        <div class="modern-cart-details flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="modern-cart-name">
                                                    {{ $product->name }}
                                                </div>
                                                <button type="button"
                                                    class="p-0 ml-2 border-0 btn btn-link modern-cart-remove text-muted"
                                                    title="Remove item"
                                                    wire:click="remove('{{ $product->rowId }}')">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="mt-2 modern-cart-meta d-flex align-items-center justify-content-between flex-wrap">
                                                <div class="modern-cart-price">
                                                    Price: <span class="modern-price-highlight">TK {{ number_format($product->price, 0) }}</span>
                                                </div>

                                                {{-- Inline Quantity Stepper --}}
                                                <div class="modern-qty-stepper d-inline-flex align-items-center">
                                                    <button type="button"
                                                        class="modern-qty-btn modern-qty-minus"
                                                        wire:click="decreaseQuantity('{{ $product->rowId }}')"
                                                        aria-label="Decrease quantity">
                                                        -
                                                    </button>
                                                    <input class="modern-qty-val"
                                                        type="number"
                                                        min="1"
                                                        value="{{ $product->qty }}"
                                                        readonly>
                                                    <button type="button"
                                                        class="modern-qty-btn modern-qty-plus"
                                                        wire:click="increaseQuantity('{{ $product->rowId }}')"
                                                        aria-label="Increase quantity">
                                                        +
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Item Subtotal --}}
                                            <div class="mt-1 text-right modern-cart-item-total">
                                                Total: <span class="modern-total-highlight">TK {{ number_format($product->price * $product->qty, 0) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-3 text-center rounded modern-empty-cart text-danger">
                                    কার্ট খালি রয়েছে।
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Order Summary Card --}}
                <div class="modern-card modern-card-summary">
                    <div class="modern-card-header d-flex align-items-center">
                        <div class="mr-2 modern-header-icon modern-icon-summary">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </div>
                        <h3 class="mb-0 modern-card-title">অর্ডার সামারি</h3>
                    </div>

                    <div class="modern-card-body">
                        @php
                            $subTotal = cart()->subTotal();
                            $deliveryFee = cart()->getCost('deliveryFee') ?: 0;
                            $couponDiscount = $coupon_discount ?? 0;
                            $finalTotal = max(cart()->total() - $couponDiscount, 0);
                        @endphp

                        {{-- Subtotal --}}
                        <div class="mb-2 modern-summary-row d-flex justify-content-between align-items-center">
                            <span class="modern-summary-label">সাবটোটাল ({{ cart()->count() }} আইটেম)</span>
                            <span class="modern-summary-value font-weight-semibold">TK {{ number_format($subTotal, 0) }}</span>
                        </div>

                        {{-- Delivery Fee --}}
                        <div class="mb-2 modern-summary-row d-flex justify-content-between align-items-center">
                            <span class="modern-summary-label">ডেলিভারি চার্জ</span>
                            <span class="modern-summary-value font-weight-semibold">TK {{ number_format($deliveryFee, 0) }}</span>
                        </div>

                        {{-- Coupon Code (Collapsible / Clean) --}}
                        <div class="my-2 modern-coupon-wrapper">
                            <div class="input-group input-group-sm">
                                <input type="text"
                                    wire:model.live="coupon_code"
                                    wire:change="applyCoupon"
                                    class="form-control modern-input-coupon @error('coupon_code') is-invalid @enderror"
                                    placeholder="কুপন কোড লিখুন" />
                                <div class="input-group-append">
                                    <button type="button"
                                        wire:click="applyCoupon"
                                        wire:loading.attr="disabled"
                                        class="btn modern-btn-coupon">
                                        প্রয়োগ
                                    </button>
                                </div>
                            </div>
                            <x-error field="coupon_code" />
                            @if ($applied_coupon)
                                <div class="mt-1 modern-coupon-applied text-success d-flex justify-content-between align-items-center">
                                    <span>কুপন "{{ $applied_coupon->name }}" ছাড়: - TK {{ number_format($couponDiscount, 0) }}</span>
                                    <button type="button" wire:click="removeCoupon" class="p-0 border-0 btn btn-link btn-sm text-danger font-weight-bold">বাতিল</button>
                                </div>
                            @endif
                        </div>

                        {{-- Dashed Separator --}}
                        <div class="my-3 modern-dashed-divider"></div>

                        {{-- Grand Total --}}
                        <div class="modern-summary-row modern-summary-total d-flex justify-content-between align-items-center">
                            <span class="modern-total-label font-weight-bold">মোট পরিশোধযোগ্য</span>
                            <span class="modern-total-amount text-danger font-weight-bold">TK {{ number_format($finalTotal, 0) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Terms & Confirmation Section --}}
                <div class="mt-3 modern-checkout-actions">
                    <div class="mb-3 modern-terms-box d-flex justify-content-center align-items-center">
                        <label class="mb-0 modern-terms-label d-flex align-items-center">
                            <input type="checkbox" checked class="mr-2 modern-terms-checkbox">
                            <span>
                                I agree with the
                                <a href="javascript:void(0);" class="modern-terms-link">Terms and Conditions</a>
                            </span>
                        </label>
                    </div>

                    <div class="modern-submit-wrapper">
                        <button type="button"
                            place-order
                            wire:click="checkout"
                            wire:loading.attr="disabled"
                            class="btn btn-block modern-confirm-btn d-flex align-items-center justify-content-center">
                            <svg class="mr-2 modern-lock-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <span>{{ setting('show_option')->checkout_button_text ?? 'অর্ডার কনফার্ম করুন' }}</span>
                        </button>
                    </div>

                    <div class="mt-2 text-center modern-security-footer d-flex align-items-center justify-content-center">
                        <svg class="mr-1 modern-lock-footer-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>আপনার তথ্য ১০০% নিরাপদ এবং গোপন রাখা হবে</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
