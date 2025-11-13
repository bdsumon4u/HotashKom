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
                    {!! __('checkout.heading') !!}
                </div>
                <div class="form-row">
                    <div class="m-0 form-group col-md-3">
                        <label>{{ __('checkout.customer_name') }}: <span class="text-danger">*</span></label>
                    </div>
                    <div class="form-group col-md-9">
                        <x-input name="name" wire:model="name" place-holder="{{ __('checkout.customer_name_placeholder') }}"
                            placeholder="{{ __('checkout.customer_name_placeholder') }}" />
                        <x-error field="name" />
                    </div>
                </div>
                <div class="form-row">
                    <div class="m-0 form-group col-md-3">
                        <label>{{ __('checkout.mobile_number') }}: <span class="text-danger">*</span></label>
                    </div>
                    <div class="form-group col-md-9">
                        <div class="input-group">
                            @unless (setting('show_option')->hide_phone_prefix ?? false)
                            <div class="input-group-prepend">
                                <span class="input-group-text">+27</span>
                            </div>
                            @endunless
                            <x-input type="tel" name="phone" wire:model="phone"
                                place-holder="{{ __('checkout.mobile_placeholder') }}"
                                placeholder="{{ __('checkout.mobile_placeholder') }}" />
                            <x-error field="phone" />
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="m-0 form-group col-md-3">
                        <label class="d-block"><label>{{ __('checkout.delivery_area') }}: <span class="text-danger">*</span></label>
                    </div>
                    <div class="form-group col-md-9">
                        <div class="form-control @error('shipping') is-invalid @enderror h-auto">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" wire:model.live="shipping"
                                    @change="$wire.updateField('shipping', $event.target.value)"
                                    class="custom-control-input" id="inside-dhaka" name="shipping" value="Inside Dhaka">
                                <label class="custom-control-label" for="inside-dhaka">{{ __('checkout.inside_dhaka') }}
                                    ({{ $isFreeDelivery ? 'FREE' : $this->shippingCost('Inside Dhaka') }}
                                    {{ __('checkout.currency') }})
                                </label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" wire:model.live="shipping"
                                    @change="$wire.updateField('shipping', $event.target.value)"
                                    class="custom-control-input" id="outside-dhaka" name="shipping"
                                    value="Outside Dhaka">
                                <label class="custom-control-label" for="outside-dhaka">{{ __('checkout.outside_dhaka') }}
                                    ({{ $isFreeDelivery ? 'FREE' : $this->shippingCost('Outside Dhaka') }}
                                    {{ __('checkout.currency') }})
                                </label>
                            </div>
                        </div>
                        <x-error field="shipping" />
                    </div>
                </div>
                <div class="form-row">
                    <div class="m-0 form-group col-md-3">
                        <label>{{ __('checkout.customer_address') }}: <span class="text-danger">*</span></label>
                    </div>
                    <div class="form-group col-md-9">
                        <x-textarea name="address" wire:model="address"
                            place-holder="{{ __('checkout.address_placeholder') }}"
                            placeholder="{{ __('checkout.address_placeholder') }}"></x-textarea>
                        <x-error field="address" />
                    </div>
                </div>
                @unless (setting('show_option')->hide_checkout_note ?? false)
                <div class="form-row">
                    <div class="m-0 form-group col-md-3">
                        <label>{{ __('checkout.note') }}:</label>
                    </div>
                    <div class="form-group col-md-9">
                        <x-textarea name="note" wire:model="note" placeholder="{{ __('checkout.note_placeholder') }}">
                        </x-textarea>
                        <x-error field="note" />
                    </div>
                </div>
                @endunless

                @if ((setting('Pathao')->enabled ?? false) && (setting('Pathao')->user_selects_city_area ?? false))
                <div class="form-row">
                    <div class="m-0 form-group col-md-3">
                        <label>{{ __('checkout.city') }}: <span class="text-danger">*</span></label>
                    </div>
                    <div class="form-group col-md-9">
                        <select class="form-control @error('city_id') is-invalid @enderror" wire:model.live="city_id">
                            <option value="">{{ __('checkout.select_city') }}</option>
                            @foreach ($pathaoCities as $city)
                            <option value="{{ $city->city_id }}">{{ $city->city_name }}</option>
                            @endforeach
                        </select>
                        <x-error field="city_id" />
                    </div>
                </div>
                <div class="form-row">
                    <div class="m-0 form-group col-md-3">
                        <label>{{ __('checkout.area') }}: <span class="text-danger">*</span></label>
                    </div>
                    <div class="form-group col-md-9">
                        <div wire:loading.class="d-flex" wire:target="city_id" class="d-none h-100 align-items-center">
                            {{ __('checkout.area_loading') }}
                        </div>
                        <select wire:loading.remove wire:target="city_id"
                            class="form-control @error('area_id') is-invalid @enderror" wire:model.live="area_id">
                            <option value="">{{ __('checkout.select_area') }}</option>
                            @foreach ($pathaoAreas as $area)
                            <option value="{{ $area->zone_id }}">{{ $area->zone_name }}</option>
                            @endforeach
                        </select>
                        <x-error field="area_id" />
                    </div>
                </div>
                @endif
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
                        @if (isOninda())
                        <tr>
                            <th>Selling Subtotal</th>
                            <td x-text="format(subtotal)"></td>
                        </tr>
                        @endif
                        @if ($shipping && ($fee = cart()->getCost('deliveryFee')))
                        <tr>
                            <th style="white-space:nowrap;">Our Delivery Charge</th>
                            <td class="shipping">{!! theMoney($fee) !!}</td>
                        </tr>
                        @endif
                        @if (isOninda())
                        @if(config('app.resell'))
                        <tr>
                            <th style="white-space:nowrap;">Packaging Charge</th>
                            <td>{!! theMoney(25) !!}</td>
                        </tr>
                        @endif
                        <tr>
                            <th style="white-space:nowrap;">Your Delivery Charge</th>
                            <td>
                                <input type="number" @focus="$event.target.select()" step="10"
                                    class="form-control form-control-sm" x-model="retail_delivery" />
                            </td>
                        </tr>
                        <tr>
                            <th>Advanced</th>
                            <td>
                                <input type="number" @focus="$event.target.select()" step="10"
                                    class="form-control form-control-sm" x-model="advanced" />
                            </td>
                        </tr>
                        <tr>
                            <th>Discount ({{config('app.currency')}})</th>
                            <td>
                                <input type="number" @focus="$event.target.select()" x-model="retailDiscount" step="10"
                                    min="0" class="form-control form-control-sm" />
                                <x-error field="retailDiscount" />
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot class="checkout__totals-footer">
                        <tr>
                            <th>Buying</th>
                            <td>{!! theMoney(cart()->total() + (isOninda() && config('app.resell') ? 25 : 0)) !!}</td>
                        </tr>
                        @if (isOninda())
                        <tr>
                            <th>Selling</th>
                            <td
                                x-text="format(subtotal + Number(retail_delivery) - Number(advanced) - Number(retailDiscount))">
                            </td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
                <div class="checkout__agree form-group">
                    <div class="form-check">
                        <span class="form-check-input input-check">
                            <span class="input-check__body">
                                <input class="input-check__input" type="checkbox" id="checkout-terms-mobile" checked>
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
                    class="text-white btn btn-primary btn-xl btn-block">{{ __('checkout.confirm_order') }}</button>
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
                            <th style="white-space:nowrap;font-size:14px;">Buying Subtotal</th>
                            <td style="white-space:nowrap;" class="checkout-subtotal desktop">{!!
                                theMoney(cart()->subTotal()) !!}</td>
                        </tr>
                        @if (isOninda())
                        <tr>
                            <th style="white-space:nowrap;font-size:14px;">Selling Subtotal</th>
                            <td x-text="format(subtotal)"></td>
                        </tr>
                        @endif
                        @if ($shipping && ($fee = cart()->getCost('deliveryFee')))
                        <tr>
                            <th style="white-space:nowrap;font-size:14px;">Our Delivery Charge</th>
                            <td class="shipping">{!! theMoney($fee) !!}</td>
                        </tr>
                        @endif
                        @if (isOninda())
                        @if(config('app.resell'))
                        <tr>
                            <th style="white-space:nowrap;font-size:14px;">Packaging Charge</th>
                            <td>{!! theMoney(25) !!}</td>
                        </tr>
                        @endif
                        <tr>
                            <th style="white-space:nowrap;font-size:14px;">Your Delivery Charge</th>
                            <td>
                                <input type="number" @focus="$event.target.select()" step="10"
                                    class="form-control form-control-sm" x-model="retail_delivery" />
                            </td>
                        </tr>
                        <tr>
                            <th style="font-size:14px;">Advanced</th>
                            <td>
                                <input type="number" @focus="$event.target.select()" step="10"
                                    class="form-control form-control-sm" x-model="advanced" />
                            </td>
                        </tr>
                        <tr>
                            <th style="font-size:14px;">Discount ({{config('app.currency')}})</th>
                            <td>
                                <input type="number" @focus="$event.target.select()" x-model="retailDiscount" step="10"
                                    min="0" class="form-control form-control-sm" />
                                <x-error field="retailDiscount" />
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot class="checkout__totals-footer">
                        <tr>
                            <th style="white-space:nowrap;font-size:18px;">Buying Total</th>
                            <td style="font-size:14px;">
                                <span>{!! theMoney(cart()->total() + (isOninda() && config('app.resell') ? 25 : 0)) !!}</span>
                            </td>
                        </tr>
                        @if (isOninda())
                        <tr>
                            <th style="white-space:nowrap;font-size:18px;">Selling Total</th>
                            <td style="font-size:14px;">
                                <span
                                    x-text="format(subtotal + Number(retail_delivery) - Number(advanced) - Number(retailDiscount))"></span>
                            </td>
                        </tr>
                        @endif
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
                        class="text-white btn btn-primary btn-xl btn-block">{{ __('checkout.confirm_order') }}</button>
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
            format(price) { return '{{config('app.currency')}} ' + price.toLocaleString('en-US', { maximumFractionDigits: 0 }) },
        }));
        });
</script>
@endpush

