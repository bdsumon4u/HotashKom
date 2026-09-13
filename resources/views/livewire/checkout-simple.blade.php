<div x-data="sumPrices({
        retail: @js($retail ?? []),
        advanced: @js($advanced ?? 0),
        retail_delivery: @js($retailDeliveryFee ?? 0),
        retailDiscount: @js($retailDiscount ?? 0),
        couponDiscount: @js($coupon_discount ?? 0),
    })" class="asell-checkout-wrap">
    @if (session()->has('error'))
    <div class="col-12">
        <div class="py-5 text-center text-danger">
            <h4>{{ session('error') }}</h4>
        </div>
    </div>
    @else

    {{-- Top Section: Form on Left, Order Summary on Right --}}
    <div class="row asell-top-row">
        {{-- Left Column: Customer Information Form --}}
        <div class="col-12 col-lg-7 mb-3 mb-lg-0 pr-lg-2">
            <div class="asell-card asell-form-card">
                {{-- Red Notice Banner --}}
                <div class="asell-notice-banner">
                    নিচের তথ্যগুলো সঠিকভাবে পূরণ করে <span class="asell-notice-highlight">কনফার্ম অর্ডার</span> বাটনে ক্লিক করুন।
                </div>

                <div class="asell-form-body">
                    {{-- Customer Name --}}
                    <div class="asell-form-row">
                        <label class="asell-form-label">
                            কাস্টমারের নাম: <span class="text-danger">*</span>
                        </label>
                        <div class="asell-form-field">
                            <x-input name="name" wire:model="name" class="asell-input"
                                place-holder="Type customer's name here."
                                placeholder="Type customer's name here." />
                            <x-error field="name" />
                        </div>
                    </div>

                    {{-- Mobile Number --}}
                    <div class="asell-form-row">
                        <label class="asell-form-label">
                            মোবাইল নম্বর: <span class="text-danger">*</span>
                        </label>
                        <div class="asell-form-field">
                            <div class="asell-phone-group @error('phone') is-invalid @enderror">
                                @unless (setting('show_option')->hide_phone_prefix ?? false)
                                    <span class="asell-prefix">+880</span>
                                @endunless
                                <x-input type="tel" name="phone" wire:model="phone" class="asell-input"
                                    place-holder="Type customer's phone number."
                                    placeholder="Type customer's phone number." />
                            </div>
                            <x-error field="phone" />
                        </div>
                    </div>

                    {{-- Delivery Area --}}
                    <div class="asell-form-row">
                        <label class="asell-form-label">
                            ডেলিভারি এরিয়া: <span class="text-danger">*</span>
                        </label>
                        <div class="asell-form-field">
                            <div class="asell-shipping-options">
                                @foreach (app(\App\Services\DeliveryAreaService::class)->getDeliveryAreas() as $index => $area)
                                    @php
                                        $areaName = data_get($area, 'name');
                                        $isSelected = ($shipping === $areaName);
                                        $chargeAmount = $this->shippingCost($areaName);
                                        $costText = $isFreeDelivery ? 'ফ্রি' : ($chargeAmount . ' টাকা');
                                    @endphp
                                    <label class="asell-radio-label">
                                        <input type="radio" wire:model.live="shipping"
                                            @change="$wire.updateField('shipping', $event.target.value)"
                                            name="shipping" value="{{ $areaName }}"
                                            class="asell-radio-input">
                                        <span class="asell-radio-text">{{ $areaName }} ({{ $costText }})</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-error field="shipping" />
                        </div>
                    </div>

                    {{-- Customer Address --}}
                    <div class="asell-form-row">
                        <label class="asell-form-label">
                            কাস্টমারের ঠিকানা: <span class="text-danger">*</span>
                        </label>
                        <div class="asell-form-field">
                            <x-textarea name="address" wire:model="address" class="asell-textarea" rows="2"
                                place-holder="Type customer's address here."
                                placeholder="Type customer's address here."></x-textarea>
                            <x-error field="address" />
                        </div>
                    </div>

                    {{-- Note (Optional) --}}
                    @unless (setting('show_option')->hide_checkout_note ?? false)
                    <div class="asell-form-row">
                        <label class="asell-form-label">
                            নোট (অপশনাল):
                        </label>
                        <div class="asell-form-field">
                            <x-textarea name="note" wire:model="note" class="asell-textarea" rows="2"
                                place-holder="আপনি চাইলে কোন নোট লিখতে পারেন।"
                                placeholder="আপনি চাইলে কোন নোট লিখতে পারেন।"></x-textarea>
                            <x-error field="note" />
                        </div>
                    </div>
                    @endunless

                    {{-- Optional Email --}}
                    @if (setting('show_option')->email ?? false)
                    <div class="asell-form-row">
                        <label class="asell-form-label">
                            ইমেইল (অপশনাল):
                        </label>
                        <div class="asell-form-field">
                            <x-input type="email" name="email" wire:model="email" class="asell-input"
                                place-holder="Type customer's email here."
                                placeholder="Type customer's email here." />
                            <x-error field="email" />
                        </div>
                    </div>
                    @endif

                    {{-- Pathao District/City & Area --}}
                    @if ((setting('Pathao')->enabled ?? false) && (setting('Pathao')->user_selects_city_area ?? false))
                    <div class="asell-form-row">
                        <label class="asell-form-label">
                            জেলা: @if(setting('Pathao')->user_required_city_area ?? false)<span class="text-danger">*</span>@endif
                        </label>
                        <div class="asell-form-field">
                            <select class="asell-select @error('city_id') is-invalid @enderror" wire:model.live="city_id">
                                <option value="">জেলা নির্বাচন করুন</option>
                                @foreach ($pathaoCities as $city)
                                    <option value="{{ $city->city_id }}">{{ $city->city_name }}</option>
                                @endforeach
                            </select>
                            <x-error field="city_id" />
                        </div>
                    </div>
                    <div class="asell-form-row">
                        <label class="asell-form-label">
                            এলাকা: @if(setting('Pathao')->user_required_city_area ?? false)<span class="text-danger">*</span>@endif
                        </label>
                        <div class="asell-form-field">
                            <div wire:loading.class="d-flex" wire:target="city_id" class="d-none py-1 align-items-center text-muted" style="font-size: 12px;">
                                এলাকা লোড হচ্ছে...
                            </div>
                            <select wire:loading.remove wire:target="city_id"
                                class="asell-select @error('area_id') is-invalid @enderror" wire:model.live="area_id">
                                <option value="">এলাকা নির্বাচন করুন</option>
                                @foreach ($pathaoAreas as $area)
                                    <option value="{{ $area->zone_id }}">{{ $area->zone_name }}</option>
                                @endforeach
                            </select>
                            <x-error field="area_id" />
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Your Order & Cost Summary --}}
        <div class="col-12 col-lg-5 pl-lg-2">
            <div class="asell-card asell-summary-card">
                <h3 class="asell-summary-heading">Your Order</h3>

                {{-- Coupon Code --}}
                <div class="asell-coupon-box">
                    <label class="asell-coupon-label">Coupon Code</label>
                    <div class="asell-coupon-input-group">
                        <input type="text" wire:model.live="coupon_code" wire:change="applyCoupon"
                            class="asell-coupon-input @error('coupon_code') is-invalid @enderror"
                            placeholder="Enter coupon code" />
                        <button type="button" wire:click="applyCoupon" wire:loading.attr="disabled"
                            class="asell-coupon-btn">Apply</button>
                    </div>
                    <x-error field="coupon_code" />
                    @if($applied_coupon)
                        <div class="mt-1 d-flex align-items-center justify-content-between text-success" style="font-size: 12px; font-weight: 600;">
                            <span><i class="fas fa-tag mr-1"></i> "{{ $applied_coupon->name }}" applied</span>
                            <button type="button" wire:click="removeCoupon" class="p-0 btn btn-link text-danger" style="font-size: 11.5px; text-decoration: none;">Remove</button>
                        </div>
                    @endif
                </div>

                {{-- Summary Breakdown --}}
                <div class="asell-summary-rows">
                    <div class="asell-summary-line">
                        <span class="asell-line-label">Buying Subtotal</span>
                        <span class="asell-line-value">{!! theMoney(cart()->subTotal()) !!}</span>
                    </div>

                    @if (isOninda())
                        <div class="asell-summary-line">
                            <span class="asell-line-label">Selling Subtotal</span>
                            <span class="asell-line-value" x-text="format(subtotal)"></span>
                        </div>
                    @endif

                    <div class="asell-summary-line">
                        <span class="asell-line-label">Our Delivery Charge</span>
                        <span class="asell-line-value">
                            @if ($shipping && ($fee = cart()->getCost('deliveryFee')))
                                {!! theMoney($fee) !!}
                            @elseif ($isFreeDelivery)
                                <span class="text-success">FREE</span>
                            @else
                                TK 0
                            @endif
                        </span>
                    </div>

                    @if (isOninda())
                        @if(config('app.resell'))
                            <div class="asell-summary-line">
                                <span class="asell-line-label">Packaging Charge</span>
                                <span class="asell-line-value">{!! theMoney($packagingCharge) !!}</span>
                            </div>
                        @endif
                        <div class="asell-summary-line">
                            <span class="asell-line-label">Your Delivery Charge</span>
                            <input type="text" @focus="$event.target.select()" step="10"
                                class="form-control form-control-sm text-right" style="width: 90px;"
                                x-model="retail_delivery"
                                wire:model.live="retailDeliveryFee" />
                        </div>
                        <div class="asell-summary-line">
                            <span class="asell-line-label">Advanced</span>
                            <input type="text" @focus="$event.target.select()" step="10"
                                class="form-control form-control-sm text-right" style="width: 90px;"
                                x-model="advanced"
                                wire:model.live="advanced" />
                        </div>
                        <div class="asell-summary-line">
                            <span class="asell-line-label">Discount (TK)</span>
                            <input type="text" @focus="$event.target.select()" x-model="retailDiscount" step="10"
                                min="0" class="form-control form-control-sm text-right" style="width: 90px;"
                                wire:model.live="retailDiscount" />
                        </div>
                    @endif

                    @if($applied_coupon)
                        <div class="asell-summary-line text-success">
                            <span class="asell-line-label">Coupon Discount</span>
                            <span class="asell-line-value">-{!! theMoney($coupon_discount) !!}</span>
                        </div>
                    @endif

                    <div class="asell-summary-line asell-total-line">
                        <span class="asell-total-label">Buying Total</span>
                        <span class="asell-total-val">{!! theMoney(max(cart()->total() - $coupon_discount, 0) + (isOninda() && config('app.resell') ? $packagingCharge : 0)) !!}</span>
                    </div>

                    @if (isOninda())
                        <div class="asell-summary-line">
                            <span class="asell-total-label">Selling Total</span>
                            <span class="asell-total-val" style="font-size: 16px;"
                                x-text="format(subtotal + Number(retail_delivery) - Number(advanced) - Number(retailDiscount))"></span>
                        </div>
                    @endif
                </div>

                {{-- Terms Checkbox --}}
                <div class="asell-terms-wrap">
                    <label class="asell-terms-label">
                        <input type="checkbox" id="asell-terms-cb" checked class="asell-terms-cb">
                        <span>I agree to the <button type="button" class="asell-terms-link" onclick="event.preventDefault(); event.stopPropagation(); var termsWindow=window.open(`{{ url('/terms-and-conditions') }}`, `_blank`); if(termsWindow){termsWindow.opener=null;} return false;">terms and conditions</button><span class="text-danger">*</span></span>
                    </label>
                </div>

                {{-- Confirm Order CTA Button --}}
                <button type="button" place-order wire:click="checkout" wire:loading.attr="disabled"
                    class="asell-btn-confirm">
                    {{ setting('show_option')->checkout_button_text ?? 'কনফার্ম অর্ডার' }}
                </button>

                <div class="asell-trust-badge">
                    <i class="fas fa-lock mr-1 text-muted"></i> আপনার তথ্য ১০০% নিরাপদ এবং সুরক্ষিত
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Section: Product Overview Table --}}
    <div class="asell-product-overview-wrap mt-3">
        <h4 class="asell-overview-heading">Product Overview</h4>

        <div class="asell-overview-card">
            <div class="table-responsive">
                <table class="table asell-product-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 75px;">ছবি</th>
                            <th>পণ্য</th>
                            <th class="text-center" style="width: 120px;">মূল্য</th>
                            <th class="text-center" style="width: 130px;">পরিমাণ</th>
                            <th class="text-right" style="width: 120px;">মোট</th>
                            <th class="text-center" style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (cart()->content() as $product)
                            <tr wire:key="cart-row-{{ $product->rowId }}" class="asell-product-row">
                                <td class="asell-thumb-cell">
                                    <a href="{{ route('products.show', $product->options->slug) }}" wire:navigate.hover>
                                        <img src="{{ asset($product->options->image) }}" alt="{{ $product->name }}" class="asell-item-img">
                                    </a>
                                </td>
                                <td class="asell-name-cell">
                                    <a href="{{ route('products.show', $product->options->slug) }}" class="asell-item-title" wire:navigate.hover>
                                        {{ $product->name }}
                                    </a>
                                </td>
                                <td class="text-center asell-price-cell">
                                    TK {{ number_format($product->price) }}
                                </td>
                                <td class="text-center asell-qty-cell">
                                    <div class="asell-stepper">
                                        <button type="button" class="asell-stepper-btn" wire:click="decreaseQuantity('{{ $product->rowId }}')">-</button>
                                        <span class="asell-stepper-val">{{ $product->qty }}</span>
                                        <button type="button" class="asell-stepper-btn" wire:click="increaseQuantity('{{ $product->rowId }}')">+</button>
                                    </div>
                                </td>
                                <td class="text-right asell-subtotal-cell">
                                    <strong>TK {{ number_format($product->price * $product->qty) }}</strong>
                                </td>
                                <td class="text-center asell-remove-cell">
                                    <button type="button" class="asell-remove-btn" wire:click="remove('{{ $product->rowId }}')" title="মুছে ফেলুন">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-muted">
                                    <i class="fas fa-bag-shopping fa-2x mb-2 d-block text-muted"></i>
                                    কার্টে কোনো পণ্য নেই
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>cent-color: #dc2626;">
                <label for="checkout-terms-cb-simple" class="mb-0" style="cursor: pointer;">
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



