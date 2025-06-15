<div x-data="sumPrices" class="row">
    @if (session()->has('error'))
        <div class="col-12">
            <div class="py-5 text-center text-danger">
                <h4>{{ session('error') }}</h4>
            </div>
        </div>
    @else
        <div class="pr-1 col-12 col-md-8">
            <div class="card mb-lg-0">
                <div class="p-3 card-body">
                    <div class="mb-2 text-center border text-danger" style="padding: 2px 10px; font-size: 1.25rem;">
                        নিচের তথ্যগুলো সঠিকভাবে পূরণ করে <strong>কনফার্ম অর্ডার</strong> বাটনে ক্লিক করুন।
                    </div>
                    <div class="form-row">
                        <div class="m-0 form-group col-md-3">
                            <label>কাস্টমারের নাম: <span class="text-danger">*</span></label>
                        </div>
                        <div class="form-group col-md-9">
                            <x-input name="name" wire:model="name"
                                @blur="$wire.updateField('name', $event.target.value)"
                                place-holder="এখানে কাস্টমারের নাম লিখুন।" placeholder="Type customer's name here." />
                            <x-error field="name" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="m-0 form-group col-md-3">
                            <label>মোবাইল নম্বর: <span class="text-danger">*</span></label>
                        </div>
                        <div class="form-group col-md-9">
                            <div class="input-group">
                                @unless (setting('show_option')->hide_phone_prefix ?? false)
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+880</span>
                                    </div>
                                @endunless
                                <x-input type="tel" name="phone" wire:model="phone"
                                    @blur="$wire.updateField('phone', $event.target.value)"
                                    place-holder="কাস্টমারের ফোন নম্বর লিখুন।" placeholder="Type customer's phone number." />
                                <x-error field="phone" />
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="m-0 form-group col-md-3">
                            <label class="d-block"><label>ডেলিভারি এরিয়া: <span class="text-danger">*</span></label>
                        </div>
                        <div class="form-group col-md-9">
                            <div class="form-control @error('shipping') is-invalid @enderror h-auto">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" wire:model.live="shipping"
                                        @change="$wire.updateField('shipping', $event.target.value)"
                                        class="custom-control-input" id="inside-dhaka" name="shipping"
                                        value="Inside Dhaka">
                                    <label class="custom-control-label" for="inside-dhaka">ঢাকা শহর
                                        ({{ $isFreeDelivery ? 'FREE' : $this->shippingCost('Inside Dhaka') }}
                                        টাকা)
                                    </label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" wire:model.live="shipping"
                                        @change="$wire.updateField('shipping', $event.target.value)"
                                        class="custom-control-input" id="outside-dhaka" name="shipping"
                                        value="Outside Dhaka">
                                    <label class="custom-control-label" for="outside-dhaka">ঢাকার বাইরে
                                        ({{ $isFreeDelivery ? 'FREE' : $this->shippingCost('Outside Dhaka') }}
                                        টাকা)
                                    </label>
                                </div>
                            </div>
                            <x-error field="shipping" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="m-0 form-group col-md-3">
                            <label>কাস্টমারের ঠিকানা: <span class="text-danger">*</span></label>
                        </div>
                        <div class="form-group col-md-9">
                            <x-textarea name="address" wire:model="address"
                                @blur="$wire.updateField('address', $event.target.value)"
                                place-holder="এখানে কাস্টমারের পুরো ঠিকানা লিখুন।"
                                placeholder="Type your address here."></x-textarea>
                            <x-error field="address" />
                        </div>
                    </div>
                    @unless (setting('show_option')->hide_checkout_note ?? false)
                        <div class="form-row">
                            <div class="m-0 form-group col-md-3">
                                <label>নোট (অপশনাল):</label>
                            </div>
                            <div class="form-group col-md-9">
                                <x-textarea name="note" wire:model="note"
                                    @blur="$wire.updateField('note', $event.target.value)"
                                    placeholder="আপনি চাইলে কোন নোট লিখতে পারেন।"></x-textarea>
                                <x-error field="note" />
                            </div>
                        </div>
                    @endunless
                </div>
                <div class="card-divider d-md-none"></div>
                <div class="card-body d-md-none">
                    <h3 class="mb-0 card-title">Your Order</h3>
                    <div class="ordered-products"></div>
                    <table class="checkout__totals">
                        <tbody class="checkout__totals-subtotals">
                            <tr>
                                <th>Buying Subtotal</th>
                                <td class="checkout-subtotal">{!! theMoney(cart()->subTotal()) !!}</td>
                            </tr>
                            <tr>
                                <th>Selling Subtotal</th>
                                <td x-text="format(subtotal)"></td>
                            </tr>
                            @if ($shipping && ($fee = cart()->getCost('deliveryFee')))
                                <tr>
                                    <th style="white-space:nowrap;">Our Delivery Charge</th>
                                    <td class="shipping">{!! theMoney($fee) !!}</td>
                                </tr>
                            @endif
                            <tr>
                                <th style="white-space:nowrap;">Your Delivery Charge</th>
                                <td>
                                    <input type="number" @focus="$event.target.select()" x-model="retail_delivery"
                                        step="10" class="form-control form-control-sm" x-model="retail_delivery" />
                                </td>
                            </tr>
                            <tr>
                                <th>Advanced</th>
                                <td>
                                    <input type="number" @focus="$event.target.select()" x-model="advanced"
                                        step="10" class="form-control form-control-sm" x-model="advanced" />
                                </td>
                            </tr>
                            <tr>
                                <th>Discount (TK)</th>
                                <td>
                                    <input type="number" @focus="$event.target.select()" wire:model="retailDiscount"
                                        @blur="$wire.updateField('retailDiscount', $event.target.value)"
                                        step="10" min="0" class="form-control form-control-sm" />
                                    <x-error field="retailDiscount" />
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="checkout__totals-footer">
                            <tr>
                                <th>Buying</th>
                                <td>{!! theMoney(cart()->total()) !!}</td>
                            </tr>
                            <tr>
                                <th>Selling</th>
                                <td x-text="format(subtotal + Number(retail_delivery) - Number(retailDiscount))"></td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="checkout__agree form-group">
                        <div class="form-check">
                            <span class="form-check-input input-check">
                                <span class="input-check__body">
                                    <input class="input-check__input" type="checkbox" id="checkout-terms-mobile"
                                        checked>
                                    <span class="input-check__box"></span>
                                    <svg class="input-check__icon" width="9px" height="7px">
                                        <use xlink:href="{{ asset('strokya/images/sprite.svg#check-9x7') }}"></use>
                                    </svg>
                                </span>
                            </span>
                            <label class="form-check-label" for="checkout-terms-mobile">I agree to the <span
                                    class="text-info" target="_blank" href="javascript:void(0);">terms and
                                    conditions</span>*</label>
                        </div>
                    </div>
                    <button type="button" wire:click="checkout" wire:loading.attr="disabled"
                        class="text-white btn btn-primary btn-xl btn-block">কনফার্ম অর্ডার</button>
                </div>
            </div>
        </div>
        <div class="pl-1 mt-4 d-none d-md-block col-12 col-md-4 mt-lg-0">
            <div class="mb-0 card">
                <div class="card-body">
                    <h3 class="card-title">Your Order</h3>
                    <div class="ordered-products"></div>
                    <table class="checkout__totals">
                        <tbody class="checkout__totals-subtotals">
                            <tr>
                                <th>Subtotal</th>
                                <td style="white-space:nowrap;" class="checkout-subtotal desktop">
                                    <span>{!! theMoney(cart()->subTotal()) !!}</span> (buy);
                                    <span x-text="subtotal"></span> (sell)
                                </td>
                            </tr>
                            @if ($shipping && ($fee = cart()->getCost('deliveryFee')))
                                <tr>
                                    <th style="white-space:nowrap;font-size:10px;">Our Delivery Charge</th>
                                    <td class="shipping">{!! theMoney($fee) !!}</td>
                                </tr>
                            @endif
                            <tr>
                                <th style="white-space:nowrap;font-size:10px;">Your Delivery Charge</th>
                                <td>
                                    <input type="number" @focus="$event.target.select()" x-model="retail_delivery"
                                        step="10" class="form-control form-control-sm"
                                        x-model="retail_delivery" />
                                </td>
                            </tr>
                            <tr>
                                <th>Advanced</th>
                                <td>
                                    <input type="number" @focus="$event.target.select()" x-model="advanced"
                                        step="10" class="form-control form-control-sm"
                                        x-model="advanced" />
                                </td>
                            </tr>
                            <tr>
                                <th>Discount (TK)</th>
                                <td>
                                    <input type="number" @focus="$event.target.select()" wire:model="retailDiscount"
                                        @blur="$wire.updateField('retailDiscount', $event.target.value)"
                                        step="10" min="0" class="form-control form-control-sm" />
                                    <x-error field="retailDiscount" />
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="checkout__totals-footer">
                            <tr>
                                <th>Total</th>
                                <td style="font-size:16px;">
                                    <div><span>{!! theMoney(cart()->total()) !!}</span> (buy)</div>
                                    <div><span x-text="format(subtotal + Number(retail_delivery) - Number(retailDiscount))"></span> (sell)</div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="d-none d-md-block">
                        <div class="checkout__agree form-group">
                            <div class="form-check">
                                <span class="form-check-input input-check">
                                    <span class="input-check__body">
                                        <input class="input-check__input" type="checkbox" id="checkout-terms-desktop"
                                            checked>
                                        <span class="input-check__box"></span>
                                        <svg class="input-check__icon" width="9px" height="7px">
                                            <use xlink:href="{{ asset('strokya/images/sprite.svg#check-9x7') }}"></use>
                                        </svg>
                                    </span>
                                </span>
                                <label class="form-check-label" for="checkout-terms-desktop">I agree to the <span
                                        class="text-info" target="_blank" href="javascript:void(0);">terms and
                                        conditions</span>*</label>
                            </div>
                        </div>
                        <button type="button" wire:click="checkout" wire:loading.attr="disabled"
                            class="text-white btn btn-primary btn-xl btn-block">কনফার্ম অর্ডার</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="p-1 card-body">
                    <h4 class="p-2">Product Overview</h4>
                    @include('partials.cart-table')
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sumPrices', () => ({
                retail: @entangle('retail'),
                advanced: @entangle('advanced'),
                retail_delivery: @entangle('retailDeliveryFee'),
                retailDiscount: @entangle('retailDiscount'),
                get subtotal() {
                    if (!this.retail || typeof this.retail !== 'object') return 0;
                    return Object.values(this.retail).reduce((a, b) => a + b.price * b.quantity, 0);
                },
                format(price) { return 'TK ' + price.toLocaleString('en-US', { maximumFractionDigits: 0 }) },
            }));
        });
    </script>
@endpush
