<div x-data="sumPrices({
        retail: @js($retail ?? []),
        advanced: @js($advanced ?? 0),
        retail_delivery: @js($retailDeliveryFee ?? 0),
        retailDiscount: @js($retailDiscount ?? 0),
        couponDiscount: @js($coupon_discount ?? 0),
    })" class="row simple-checkout-row no-gutters">
    @if (session()->has('error'))
        <div class="col-12">
            <div class="py-5 text-center text-danger">
                <h4>{{ session('error') }}</h4>
            </div>
        </div>
    @else
        <div class="col-12 col-lg-6 pr-lg-3 simple-checkout-left">
            <div class="simple-checkout-card h-100">
                <div class="mb-3 text-center simple-checkout-header">
                    <p class="mb-1 simple-checkout-subtitle">
                        * অর্ডার করতে আপনার সম্পূর্ণ নাম, মোবাইল নম্বর ও ঠিকানা লিখে
                    </p>
                    <h2 class="mb-0 simple-checkout-title">
                        অর্ডার কনফার্ম করুন বাটনে ক্লিক করুন !
                    </h2>
                </div>

                <div class="simple-form-group">
                    <label class="simple-label">
                        আপনার নাম<span class="text-danger">*</span>
                    </label>
                    <x-input name="name"
                        wire:model="name"
                        place-holder="আপনার সম্পূর্ণ নাম লিখুন" />
                    <x-error field="name" />
                </div>

                <div class="simple-form-group">
                    <label class="simple-label">
                        আপনার মোবাইল<span class="text-danger">*</span>
                    </label>
                    <div class="d-flex @error('phone') is-invalid @enderror">
                        @unless (setting('show_option')->hide_phone_prefix ?? false)
                            <div class="simple-phone-prefix d-flex align-items-center justify-content-center">
                                +88
                            </div>
                        @endunless
                        <div class="flex-grow-1">
                            <x-input type="tel"
                                name="phone"
                                wire:model="phone"
                                place-holder="১১ ডিজিটের মোবাইল নম্বর লিখুন" />
                        </div>
                    </div>
                    <x-error field="phone" />
                </div>

                <div class="simple-form-group">
                    <label class="simple-label">
                        আপনার ঠিকানা<span class="text-danger">*</span>
                    </label>
                    <x-textarea name="address"
                        wire:model="address"
                        place-holder="আপনার সম্পূর্ণ ঠিকানা যেমন গ্রাম, থানা, জেলা লিখুন"></x-textarea>
                    <x-error field="address" />
                </div>

                <div class="simple-form-group">
                    <label class="mb-2 simple-label d-block">
                        ডেলিভারি এরিয়া<span class="text-danger">*</span>
                    </label>
                    <div class="simple-shipping-options">
                        <label class="simple-shipping-option">
                            <input type="radio"
                                wire:model.live="shipping"
                                @change="$wire.updateField('shipping', $event.target.value)"
                                name="shipping"
                                value="Inside Dhaka">
                            <span class="simple-shipping-content">
                                <span class="simple-shipping-title">
                                    ঢাকার ভিতরে
                                    @if (cart()->subTotal())
                                        ({{ $isFreeDelivery ? 'FREE' : $this->shippingCost('Inside Dhaka').' Tk' }})
                                    @endif
                                </span>
                            </span>
                        </label>
                        <label class="simple-shipping-option">
                            <input type="radio"
                                wire:model.live="shipping"
                                @change="$wire.updateField('shipping', $event.target.value)"
                                name="shipping"
                                value="Outside Dhaka">
                            <span class="simple-shipping-content">
                                <span class="simple-shipping-title">
                                    ঢাকার বাহিরে
                                    @if (cart()->subTotal())
                                        ({{ $isFreeDelivery ? 'FREE' : $this->shippingCost('Outside Dhaka').' Tk' }})
                                    @endif
                                </span>
                            </span>
                        </label>
                    </div>
                    <x-error field="shipping" />
                </div>

                @unless (setting('show_option')->hide_checkout_note ?? false)
                    <div class="simple-form-group">
                        <label class="simple-label">
                            নোট (অপশনাল)
                        </label>
                        <x-textarea name="note"
                            wire:model="note"
                            placeholder="আপনি চাইলে কোন নোট লিখতে পারেন।"></x-textarea>
                        <x-error field="note" />
                    </div>
                @endunless

                @if ((setting('Pathao')->enabled ?? false) && (setting('Pathao')->user_selects_city_area ?? false))
                    <div class="simple-form-group">
                        <label class="simple-label">
                            সিটি<span class="text-danger">*</span>
                        </label>
                        <select class="form-control @error('city_id') is-invalid @enderror" wire:model.live="city_id">
                            <option value="">সিটি নির্বাচন করুন</option>
                            @foreach ($pathaoCities as $city)
                                <option value="{{ $city->city_id }}">{{ $city->city_name }}</option>
                            @endforeach
                        </select>
                        <x-error field="city_id" />
                    </div>

                    <div class="simple-form-group">
                        <label class="simple-label">
                            এলাকা<span class="text-danger">*</span>
                        </label>
                        <div wire:loading.class="d-flex" wire:target="city_id" class="d-none h-100 align-items-center">
                            এলাকা লোড হচ্ছে...
                        </div>
                        <select wire:loading.remove
                            wire:target="city_id"
                            class="form-control @error('area_id') is-invalid @enderror"
                            wire:model.live="area_id">
                            <option value="">এলাকা নির্বাচন করুন</option>
                            @foreach ($pathaoAreas as $area)
                                <option value="{{ $area->zone_id }}">{{ $area->zone_name }}</option>
                            @endforeach
                        </select>
                        <x-error field="area_id" />
                    </div>
                @endif

                <div class="mt-3 mb-3 simple-terms">
                    <label class="mb-0 d-flex align-items-center">
                        <input type="checkbox" checked class="mr-2">
                        <span>
                            I agree with the
                            <a href="javascript:void(0);" class="simple-terms-link">Terms and Conditions</a>
                        </span>
                    </label>
                </div>

                <div class="simple-submit-wrapper">
                    <button type="button"
                        place-order
                        wire:click="checkout"
                        wire:loading.attr="disabled"
                        class="btn btn-block simple-submit-btn">
                        {{ setting('show_option')->checkout_button_text ?? 'অর্ডার কনফার্ম করুন' }}
                    </button>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6 pl-lg-3 simple-checkout-right">
            <div class="simple-order-card h-100">
                <h3 class="mb-3 simple-order-title">আপনার অর্ডার</h3>

                @include('partials.cart-table-simple')

                <div class="simple-form-group">
                    <label class="simple-label d-block">Coupon Code</label>
                    <div class="input-group">
                        <input type="text"
                            wire:model.live="coupon_code"
                            wire:change="applyCoupon"
                            class="form-control @error('coupon_code') is-invalid @enderror"
                            placeholder="Enter coupon code" />
                        <div class="input-group-append">
                            <button type="button"
                                wire:click="applyCoupon"
                                wire:loading.attr="disabled"
                                class="btn btn-outline-primary">
                                Apply
                            </button>
                        </div>
                    </div>
                    <x-error field="coupon_code" />
                    @if ($applied_coupon)
                        <div class="mt-1 text-success">
                            Coupon "{{ $applied_coupon->name }}" applied. Discount: {!! theMoney($coupon_discount) !!}
                            <button type="button" wire:click="removeCoupon" class="p-0 btn btn-link btn-sm text-danger">Remove</button>
                        </div>
                    @endif
                </div>

                <div class="mt-3 simple-order-totals">
                    @php
                        $deliveryFee = cart()->getCost('deliveryFee') ?: 0;
                        $sellingSubtotal = 0;
                        $sellingTotal = 0;
                        $couponDiscount = $coupon_discount ?? 0;

                        if (isOninda()) {
                            $cartItems = cart()->content();
                            $sellingSubtotal = $cartItems->sum(function ($item): float|int {
                                $id = $item->id;
                                $price = $this->retail[$id]['price'] ?? $item->options->retail_price ?? $item->price;

                                return (float) $price * $item->qty;
                            });

                            $sellingTotal = $sellingSubtotal
                                + (float) $this->retailDeliveryFee
                                - (float) $this->advanced
                                - (float) $this->retailDiscount;
                        }
                    @endphp

                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <span class="simple-total-label">
                            {{ isOninda() ? 'Buying Subtotal' : 'Subtotal' }}
                        </span>
                        <span class="simple-total-value simple-total-value--green">
                            {!! theMoney(cart()->subTotal()) !!}
                        </span>
                    </div>

                    @if (isOninda())
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span class="simple-total-label">Selling Subtotal</span>
                            <span class="simple-total-value simple-total-value--green">
                                {!! theMoney($sellingSubtotal) !!}
                            </span>
                        </div>
                    @endif

                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <span class="simple-total-label">{{ isOninda() ? 'Our Delivery Charge' : 'Delivery Charge' }}</span>
                        <span class="simple-total-value">
                            {!! theMoney($deliveryFee) !!}
                        </span>
                    </div>

                    @if ($applied_coupon)
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span class="simple-total-label">Coupon Discount</span>
                            <span class="simple-total-value text-success">
                                {!! theMoney($couponDiscount) !!}
                            </span>
                        </div>
                    @endif

                    @unless (isOninda())
                        <div class="d-flex justify-content-between align-items-center simple-total-final">
                            <span class="simple-total-label">Total</span>
                            <span class="simple-total-value simple-total-value--red">
                                {!! theMoney(max(cart()->total() - $couponDiscount, 0) + (isOninda() && config('app.resell') ? 25 : 0)) !!}
                            </span>
                        </div>
                    @endunless

                    @if (isOninda())
                        <div class="mt-3 simple-oninda-extra">
                            @if (config('app.resell'))
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <span class="simple-total-label">Packaging Charge</span>
                                    <span class="simple-total-value">
                                        {!! theMoney(25) !!}
                                    </span>
                                </div>
                            @endif

                            <div class="mb-2 simple-form-group">
                                <label class="mb-1 simple-label">Your Delivery Charge</label>
                                <input type="text"
                                    @focus="$event.target.select()"
                                    step="10"
                                    class="form-control form-control-sm"
                                    x-model="retail_delivery"
                                    wire:model.live="retailDeliveryFee" />
                            </div>

                            <div class="mb-2 simple-form-group">
                                <label class="mb-1 simple-label">Advanced</label>
                                <input type="text"
                                    @focus="$event.target.select()"
                                    step="10"
                                    class="form-control form-control-sm"
                                    x-model="advanced"
                                    wire:model.live="advanced" />
                            </div>

                            <div class="mb-2 simple-form-group">
                                <label class="mb-1 simple-label">Discount (TK)</label>
                                <input type="text"
                                    @focus="$event.target.select()"
                                    x-model="retailDiscount"
                                    step="10"
                                    min="0"
                                    class="form-control form-control-sm"
                                    wire:model.live="retailDiscount" />
                                <x-error field="retailDiscount" />
                            </div>
                        </div>
                    @endif

                    @if (isOninda())
                        <div class="mt-2 d-flex justify-content-between align-items-center simple-total-final">
                            <span class="simple-total-label">Buying Total</span>
                            <span class="simple-total-value simple-total-value--red">
                                {!! theMoney(max(cart()->total() - $couponDiscount, 0) + (isOninda() && config('app.resell') ? 25 : 0)) !!}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center simple-total-final">
                            <span class="simple-total-label">Selling Total</span>
                            <span class="simple-total-value simple-total-value--red">
                                {!! theMoney($sellingTotal) !!}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>


