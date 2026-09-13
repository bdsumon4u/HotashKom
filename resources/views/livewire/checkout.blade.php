<div x-data="sumPrices({
        retail: @js($retail ?? []),
        advanced: @js($advanced ?? 0),
        retail_delivery: @js($retailDeliveryFee ?? 0),
        retailDiscount: @js($retailDiscount ?? 0),
        couponDiscount: @js($coupon_discount ?? 0),
    })" class="row">
    @if (session()->has('error'))
    <div class="col-12">
        <div class="py-5 text-center text-danger">
            <h4>{{ session('error') }}</h4>
        </div>
    </div>
    @else
    {{-- Left Column: Delivery Information Card --}}
    <div class="col-12 col-lg-7 pr-lg-2">
        <div class="bb-co-card">
            <div class="bb-co-card-header">
                <span class="bb-co-card-icon" style="background: #e0e7ff; color: #4338ca;">
                    <i class="fas fa-user"></i>
                </span>
                <h3 class="bb-co-card-title">ডেলিভারি তথ্য দিন</h3>
            </div>

            <div class="bb-co-form-group">
                <label class="bb-co-label">
                    আপনার নাম <span class="text-danger">*</span>
                </label>
                <x-input name="name" wire:model="name" class="bb-co-input"
                    place-holder="আপনার সম্পূর্ণ নাম লিখুন"
                    placeholder="আপনার সম্পূর্ণ নাম লিখুন" />
                <x-error field="name" />
            </div>

            <div class="bb-co-form-group">
                <label class="bb-co-label">
                    আপনার মোবাইল <span class="text-danger">*</span>
                </label>
                <div class="bb-co-phone-group @error('phone') is-invalid @enderror">
                    @unless (setting('show_option')->hide_phone_prefix ?? false)
                        <span class="bb-co-prefix">+88</span>
                    @endunless
                    <x-input type="tel" name="phone" wire:model="phone" class="bb-co-input"
                        place-holder="আপনার মোবাইল নম্বর"
                        placeholder="আপনার মোবাইল নম্বর" />
                </div>
                <x-error field="phone" />
            </div>

            @if (setting('show_option')->email ?? false)
            <div class="bb-co-form-group">
                <label class="bb-co-label">
                    আপনার ইমেইল <small class="text-muted">(ঐচ্ছিক)</small>
                </label>
                <x-input type="email" name="email" wire:model="email" class="bb-co-input"
                    place-holder="আপনার ইমেইল ঠিকানা লিখুন"
                    placeholder="আপনার ইমেইল ঠিকানা লিখুন" />
                <x-error field="email" />
            </div>
            @endif

            <div class="bb-co-form-group">
                <label class="bb-co-label">
                    আপনার ঠিকানা <span class="text-danger">*</span>
                </label>
                <x-textarea name="address" wire:model="address" class="bb-co-textarea" rows="2"
                    place-holder="বিস্তারিত ঠিকানা লিখুন (বাসা, রোড, এরিয়া, ল্যান্ডমার্ক)"
                    placeholder="বিস্তারিত ঠিকানা লিখুন (বাসা, রোড, এরিয়া, ল্যান্ডমার্ক)"></x-textarea>
                <x-error field="address" />
            </div>

            <div class="bb-co-form-group">
                <label class="bb-co-label">
                    ডেলিভারি এরিয়া <span class="text-danger">*</span>
                </label>
                <div class="bb-co-delivery-grid">
                    @foreach (app(\App\Services\DeliveryAreaService::class)->getDeliveryAreas() as $index => $area)
                        @php
                            $areaName = data_get($area, 'name');
                            $isSelected = ($shipping === $areaName);
                            $cost = $isFreeDelivery ? 'FREE' : 'TK ' . $this->shippingCost($areaName);
                        @endphp
                        <label class="bb-co-delivery-tile {{ $isSelected ? 'is-selected' : '' }}" style="cursor: pointer;">
                            <input type="radio" wire:model.live="shipping"
                                @change="$wire.updateField('shipping', $event.target.value)"
                                name="shipping" value="{{ $areaName }}" style="display: none;">
                            <div class="bb-co-radio-circle {{ $isSelected ? 'is-checked' : '' }}">
                                @if($isSelected)
                                    <div class="bb-co-radio-dot"></div>
                                @endif
                            </div>
                            <div class="bb-co-tile-icon">
                                <i class="fas fa-location-dot"></i>
                            </div>
                            <div class="bb-co-tile-info">
                                <div class="bb-co-tile-title">{{ $areaName }}</div>
                                <div class="bb-co-tile-charge">ডেলিভারি চার্জ: {{ $cost }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
                <x-error field="shipping" />
            </div>

            @unless (setting('show_option')->hide_checkout_note ?? false)
            <div class="bb-co-form-group">
                <label class="bb-co-label">
                    নোট <small class="text-muted">(অপশনাল)</small>
                </label>
                <x-textarea name="note" wire:model="note" class="bb-co-textarea" rows="2"
                    place-holder="আপনার অর্ডার সম্পর্কে কিছু জানাতে চাইলে লিখুন..."
                    placeholder="আপনার অর্ডার সম্পর্কে কিছু জানাতে চাইলে লিখুন..."></x-textarea>
                <x-error field="note" />
            </div>
            @endunless

            @if ((setting('Pathao')->enabled ?? false) && (setting('Pathao')->user_selects_city_area ?? false))
            <div class="bb-co-form-group">
                <label class="bb-co-label">
                    জেলা @if(setting('Pathao')->user_required_city_area ?? false)<span class="text-danger">*</span>@endif
                </label>
                <select class="bb-co-input @error('city_id') is-invalid @enderror" wire:model.live="city_id">
                    <option value="">জেলা নির্বাচন করুন</option>
                    @foreach ($pathaoCities as $city)
                        <option value="{{ $city->city_id }}">{{ $city->city_name }}</option>
                    @endforeach
                </select>
                <x-error field="city_id" />
            </div>
            <div class="bb-co-form-group">
                <label class="bb-co-label">
                    এলাকা @if(setting('Pathao')->user_required_city_area ?? false)<span class="text-danger">*</span>@endif
                </label>
                <div wire:loading.class="d-flex" wire:target="city_id" class="d-none py-1 align-items-center text-muted" style="font-size: 12px;">
                    এলাকা লোড হচ্ছে...
                </div>
                <select wire:loading.remove wire:target="city_id"
                    class="bb-co-input @error('area_id') is-invalid @enderror" wire:model.live="area_id">
                    <option value="">এলাকা নির্বাচন করুন</option>
                    @foreach ($pathaoAreas as $area)
                        <option value="{{ $area->zone_id }}">{{ $area->zone_name }}</option>
                    @endforeach
                </select>
                <x-error field="area_id" />
            </div>
            @endif
        </div>
    </div>

    {{-- Right Column: Your Order & Order Summary Cards --}}
    <div class="col-12 col-lg-5 pl-lg-2">
        {{-- Card 1: Your Order --}}
        <div class="bb-co-card">
            <div class="bb-co-card-header">
                <span class="bb-co-card-icon" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fas fa-bag-shopping"></i>
                </span>
                <h3 class="bb-co-card-title">আপনার অর্ডার ({{ cart()->count() }} টি আইটেম)</h3>
            </div>

            <div class="bb-co-items-list">
                @forelse (cart()->content() as $product)
                    <div class="bb-co-item-row" wire:key="cart-item-{{ $product->rowId }}">
                        <div class="bb-co-item-thumb">
                            <a href="{{ route('products.show', $product->options->slug) }}" wire:navigate.hover>
                                <img src="{{ asset($product->options->image) }}" alt="{{ $product->name }}">
                            </a>
                        </div>
                        <div class="bb-co-item-details">
                            <a href="{{ route('products.show', $product->options->slug) }}" class="bb-co-item-name" wire:navigate.hover>
                                {{ $product->name }}
                            </a>
                            <div class="bb-co-item-unit-price">
                                মূল্য: <strong>TK {{ number_format($product->price) }}</strong>
                            </div>
                        </div>
                        <div class="bb-co-item-actions">
                            <button type="button" class="bb-co-item-remove" wire:click="remove('{{ $product->rowId }}')" title="মুছে ফেলুন">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="bb-co-stepper">
                                <button type="button" class="bb-co-stepper-btn" wire:click="decreaseQuantity('{{ $product->rowId }}')">-</button>
                                <span class="bb-co-stepper-val">{{ $product->qty }}</span>
                                <button type="button" class="bb-co-stepper-btn" wire:click="increaseQuantity('{{ $product->rowId }}')">+</button>
                            </div>
                            <div class="bb-co-item-total">
                                মোট: <strong>TK {{ number_format($product->price * $product->qty) }}</strong>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-3 text-center text-muted" style="font-size: 12.5px;">
                        <i class="fas fa-cart-shopping fa-2x mb-1 d-block text-muted"></i>
                        কার্টে কোনো পণ্য নেই
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Card 2: Order Summary --}}
        <div class="bb-co-card">
            <div class="bb-co-card-header">
                <span class="bb-co-card-icon" style="background: #dcfce7; color: #16a34a;">
                    <i class="fas fa-receipt"></i>
                </span>
                <h3 class="bb-co-card-title">অর্ডার সামারি</h3>
            </div>

            <div class="bb-co-summary-row">
                <span>সাবটোটাল ({{ cart()->count() }} আইটেম)</span>
                <strong>{!! theMoney(cart()->subTotal()) !!}</strong>
            </div>

            @if (isOninda())
                <div class="bb-co-summary-row">
                    <span>Selling Subtotal</span>
                    <strong x-text="format(subtotal)"></strong>
                </div>
            @endif

            <div class="bb-co-summary-row">
                <span>ডেলিভারি চার্জ</span>
                <strong>
                    @if ($shipping && ($fee = cart()->getCost('deliveryFee')))
                        {!! theMoney($fee) !!}
                    @elseif ($isFreeDelivery)
                        <span class="text-success">FREE</span>
                    @else
                        TK 0
                    @endif
                </strong>
            </div>

            @if (isOninda())
                @if(config('app.resell'))
                    <div class="bb-co-summary-row">
                        <span>Packaging Charge</span>
                        <strong>{!! theMoney($packagingCharge) !!}</strong>
                    </div>
                @endif
                <div class="bb-co-summary-row">
                    <span>Your Delivery Charge</span>
                    <input type="text" @focus="$event.target.select()" step="10"
                        class="form-control form-control-sm text-right" style="width: 100px;"
                        x-model="retail_delivery"
                        wire:model.live="retailDeliveryFee" />
                </div>
                <div class="bb-co-summary-row">
                    <span>Advanced</span>
                    <input type="text" @focus="$event.target.select()" step="10"
                        class="form-control form-control-sm text-right" style="width: 100px;"
                        x-model="advanced"
                        wire:model.live="advanced" />
                </div>
                <div class="bb-co-summary-row">
                    <span>Discount (TK)</span>
                    <input type="text" @focus="$event.target.select()" x-model="retailDiscount" step="10"
                        min="0" class="form-control form-control-sm text-right" style="width: 100px;"
                        wire:model.live="retailDiscount" />
                </div>
            @endif

            @if($applied_coupon)
                <div class="bb-co-summary-row text-success">
                    <span>Coupon Discount</span>
                    <strong>-{!! theMoney($coupon_discount) !!}</strong>
                </div>
            @endif

            {{-- Coupon Input --}}
            <div class="bb-co-coupon-group">
                <div class="input-group">
                    <input type="text" wire:model.live="coupon_code" wire:change="applyCoupon"
                        class="form-control bb-co-coupon-input @error('coupon_code') is-invalid @enderror"
                        placeholder="কুপন কোড লিখুন" />
                    <div class="input-group-append">
                        <button type="button" wire:click="applyCoupon" wire:loading.attr="disabled"
                            class="btn bb-co-coupon-btn">প্রয়োগ</button>
                    </div>
                </div>
                <x-error field="coupon_code" />
                @if($applied_coupon)
                    <div class="mt-1 d-flex align-items-center justify-content-between text-success" style="font-size: 11.5px; font-weight: 700;">
                        <span><i class="fas fa-tag mr-1"></i> "{{ $applied_coupon->name }}" প্রয়োগ করা হয়েছে</span>
                        <button type="button" wire:click="removeCoupon" class="p-0 btn btn-link text-danger" style="font-size: 11.5px; text-decoration: none;">মুছুন</button>
                    </div>
                @endif
            </div>

            <div class="bb-co-divider"></div>

            <div class="bb-co-total-row">
                <span class="bb-co-total-label">মোট পরিশোধযোগ্য</span>
                <span class="bb-co-total-amount">{!! theMoney(max(cart()->total() - $coupon_discount, 0) + (isOninda() && config('app.resell') ? $packagingCharge : 0)) !!}</span>
            </div>

            @if (isOninda())
                <div class="bb-co-summary-row">
                    <span class="bb-co-total-label">Selling Total</span>
                    <span class="bb-co-total-amount" style="font-size: 17px;"
                        x-text="format(subtotal + Number(retail_delivery) - Number(advanced) - Number(retailDiscount))"></span>
                </div>
            @endif

            <div class="mb-2 d-flex align-items-center" style="font-size: 12px; color: #64748b;">
                <input type="checkbox" id="checkout-terms-cb" checked class="mr-2" style="width: 15px; height: 15px; accent-color: #dc2626;">
                <label for="checkout-terms-cb" class="mb-0" style="cursor: pointer;">
                    আমি <button type="button" class="terms-new-tab text-danger font-weight-bold" aria-label="Open Terms and Conditions in a new tab" style="padding:0;border:0;background:transparent;text-decoration:underline;cursor:pointer;font:inherit;" onclick="event.preventDefault(); event.stopPropagation(); var termsWindow=window.open(`{{ url('/terms-and-conditions') }}`, `_blank`); if(termsWindow){termsWindow.opener=null;} return false;">শর্তাবলী ও নিয়ামাবলীতে</button> সম্মত আছি।
                </label>
            </div>

            <button type="button" place-order wire:click="checkout" wire:loading.attr="disabled"
                class="bb-btn-order-confirm">
                <i class="fas fa-lock mr-2"></i> {{ setting('show_option')->checkout_button_text ?? 'অর্ডার কনফার্ম করুন' }}
            </button>

            <div class="bb-co-trust-footer">
                <i class="fas fa-shield-check text-success mr-1"></i> আপনার তথ্য ১০০% নিরাপদ এবং গোপন রাখা হবে
            </div>
        </div>
    </div>
    @endif
</div>

@script
<script>
    (function () {
        function getCookie(name) {
            var match = document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]*)'));
            return match ? decodeURIComponent(match[2]) : '';
        }

        function buildFbc() {
            var params = new URLSearchParams(window.location.search);
            var fbclid = params.get('fbclid');
            return fbclid ? 'fb.1.' + Date.now() + '.' + fbclid : '';
        }

        var fbp = getCookie('_fbp');
        var fbc = getCookie('_fbc') || buildFbc();
        var url = window.location.href;

        if (fbp) { $wire.set('fbp', fbp); }
        if (fbc) { $wire.set('fbc', fbc); }
        $wire.set('eventSourceUrl', url);
    })();
</script>
@endscript

