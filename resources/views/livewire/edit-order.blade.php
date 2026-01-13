@push('styles')
<style>
    #courier-report img {
        width: 50px;
    }
</style>
@endpush
<div class="row">
    <div class="col-12 col-lg-6 col-xl-7">
        <div class="shadow-sm card rounded-0">
            <div class="p-3 card-header">
                <h5 class="card-title">Billing details</h5>
            </div>
            <div class="p-3 card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <x-label for="name">Name</x-label> <span class="text-danger">*</span>
                        <x-input name="name" wire:model="name" placeholder="Type your name here" :disabled="isReseller() && !is_null($order->source_id)" />
                        <x-error field="name" />
                    </div>
                    <div class="form-group col-md-6">
                        <x-label for="phone">Phone</x-label> <span class="text-danger">*</span>
                        <x-input type="tel" name="phone" wire:model="phone"
                            placeholder="Type your phone number here" :disabled="isReseller() && !is_null($order->source_id)" />
                        <x-error field="phone" />
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <x-label for="email">Email Address</x-label>
                        <x-input type="email" name="email" wire:model="email" placeholder="Email Address" :disabled="isReseller() && !is_null($order->source_id)" />
                        <x-error field="email" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="d-block">Delivery Charge City <span class="text-danger">*</span></label>
                    <div class="form-control h-auto @error('shipping_area') is-invalid @enderror">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="inside-dhaka" name="shipping"
                                wire:model.live="shipping_area" value="Inside Dhaka" @disabled(isReseller() && !is_null($order->source_id))>
                            <label class="custom-control-label" for="inside-dhaka">Inside
                                Dhaka</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="outside-dhaka" name="shipping"
                                wire:model.live="shipping_area" value="Outside Dhaka" @disabled(isReseller() && !is_null($order->source_id))>
                            <label class="custom-control-label" for="outside-dhaka">Outside
                                Dhaka</label>
                        </div>
                    </div>
                    <x-error field="shipping_area" />
                </div>
                <div class="form-group">
                    <x-label for="address">Address</x-label> <span class="text-danger">*</span>
                    <x-input name="address" wire:model="address" placeholder="Enter Correct Address" :disabled="isReseller() && !is_null($order->source_id)" />
                    <x-error field="address" />
                </div>
                <div class="form-group">
                    <label class="d-block">Courier <span class="text-danger">*</span></label>
                    <div class="border p-2 @error('courier') is-invalid @enderror">
                        @foreach (couriers() as $provider)
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" id="{{ $provider }}"
                                    wire:model.live="courier" value="{{ $provider }}" @disabled(isReseller() && !is_null($order->source_id))>
                                <label class="custom-control-label"
                                    for="{{ $provider }}">{{ $provider }}</label>
                            </div>
                        @endforeach
                    </div>
                    <x-error field="courier" />
                </div>
                <div Pathao class="form-row @if ($courier != 'Pathao') d-none @endif">
                    <div class="form-group col-md-4">
                        <label for="">City</label>
                        <select class="form-control" wire:model.live="city_id" @disabled(isReseller() && !is_null($order->source_id))>
                            <option value="" selected>Select City</option>
                            @foreach ($order->pathaoCityList() as $city)
                                <option value="{{ $city->city_id }}">
                                    {{ $city->city_name }}
                                </option>
                            @endforeach
                        </select>
                        <x-error field="city_id" />
                    </div>
                    <div class="form-group col-md-4">
                        <label for="">Area</label>
                        <div wire:loading.class="d-flex" wire:target="city_id" class="d-none h-100 align-items-center">
                            Loading Area...
                        </div>
                        <select wire:loading.remove wire:target="city_id" class="form-control" wire:model="area_id" @disabled(isReseller() && !is_null($order->source_id))>
                            <option value="" selected>Select Area</option>
                            @foreach ($order->pathaoAreaList($city_id) as $area)
                                <option value="{{ $area->zone_id }}">
                                    {{ $area->zone_name }}
                                </option>
                            @endforeach
                        </select>
                        <x-error field="area_id" />
                    </div>
                    <div class="col-md-4">
                        <label for="weight">Weight</label>
                        <input type="number" wire:model="weight" class="form-control" placeholder="Weight in KG" @disabled(isReseller() && !is_null($order->source_id))>
                    </div>
                </div>
                <div Redx class="form-row @if ($courier != 'Redx') d-none @endif">
                    <div class="form-group col-md-6">
                        <label for="">Area</label>
                        <select selector class="form-control" wire:model="area_id" @disabled(isReseller() && !is_null($order->source_id))>
                            <option value="" selected>Select Area</option>
                            @foreach ($order->redxAreaList() as $area)
                                <option value="{{ $area->id }}" {{ $area->id == $area_id ? 'selected' : '' }}>
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-error field="area_id" />
                    </div>
                    <div class="col-md-6">
                        <label for="weight">Weight</label>
                        <input type="number" wire:model="weight" class="form-control" placeholder="Weight in grams" @disabled(isReseller() && !is_null($order->source_id))>
                    </div>
                </div>
            </div>
        </div>
        <div class="shadow-sm card rounded-0">
            <div class="p-3 card-header">
                <h5 class="card-title">Ordered Products</h5>
            </div>
            <div class="p-3 card-body">
                <div class="px-3 row">
                    <input type="search" wire:model.live.debounce.250ms="search" id="search"
                        placeholder="Search Product" class="col-md-6 form-control" @disabled(isReseller() && !is_null($order->source_id))>

                    @if (session()->has('error'))
                        <strong class="col-md-6 text-danger d-flex align-items-center">{{ session('error') }}</strong>
                    @endif
                </div>
                <div class="my-2 table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                @php
                                    $selectedVar = $product;
                                    if ($product->variations->isNotEmpty()) {
                                        $selectedVar = $product->variations->random();
                                    }

                                    if (isset($options[$product->id])) {
                                        $variation = $product->variations->first(function ($item) use (
                                            $options,
                                            $product,
                                        ) {
                                            return $item->options
                                                ->pluck('id')
                                                ->diff($options[$product->id])
                                                ->isEmpty();
                                        });
                                        if ($variation) {
                                            $selectedVar = $variation;
                                        }
                                    }

                                    $order->dataId = $selectedVar->id;
                                    $order->dataMax = $selectedVar->should_track ? $selectedVar->stock_count : -1;

                                    $optionGroup = $product->variations
                                        ->pluck('options')
                                        ->flatten()
                                        ->unique('id')
                                        ->groupBy('attribute_id');
                                    $attributes = \App\Models\Attribute::find($optionGroup->keys());
                                @endphp
                                <tr>
                                    <td>
                                        <img src="{{ asset(optional($selectedVar->base_image)->src) }}"
                                            width="100" height="100" alt="">
                                    </td>
                                    <td>
                                        <a class="mb-2 d-block"
                                            href="{{ route('products.show', $selectedVar->slug) }}">{{ $product->name }}</a>

                                        @foreach ($attributes as $attribute)
                                            <div class="mb-2 form-group product__option">
                                                <label class="product__option-label">{{ $attribute->name }}</label>
                                                <div class="input-radio-label">
                                                    <div class="input-radio-label__list">
                                                        @foreach ($optionGroup[$attribute->id] as $option)
                                                            <label>
                                                                <input type="radio"
                                                                    wire:model.live="options.{{ $product->id }}.{{ $attribute->id }}"
                                                                    value="{{ $option->id }}"
                                                                    class="option-picker">
                                                                <span>{{ $option->name }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="mb-2 w-100">Availability:
                                            <strong>
                                                @if (!$selectedVar->should_track)
                                                    <span class="text-success">In Stock</span>
                                                @else
                                                    <span
                                                        class="text-{{ $selectedVar->stock_count ? 'success' : 'danger' }}">{{ $selectedVar->stock_count }}
                                                        In Stock</span>
                                                @endif
                                            </strong>
                                        </div>
                                        <div
                                            class="mb-2 {{ $selectedVar->selling_price == $selectedVar->price ? '' : 'has-special' }}">
                                            Price:
                                            @if ($selectedVar->selling_price == $selectedVar->price)
                                                {!! theMoney($selectedVar->price) !!}
                                            @else
                                                <span class="font-weight-bold">{!! theMoney($selectedVar->selling_price) !!}</span>
                                                <del class="text-danger">{!! theMoney($selectedVar->price) !!}</del>
                                            @endif
                                        </div>

                                        @if ($available = !$selectedVar->should_track || $selectedVar->stock_count > 0)
                                            <button type="button" class="btn btn-primary"
                                                wire:click="addProduct({{ $selectedVar->id }})" @disabled(isReseller() && !is_null($order->source_id))>Add to Order</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @php
                                $retail = 0;
                            @endphp
                            @foreach ($selectedProducts as $product)
                                @php
                                    $parentId = $product['parent_id'] ?? $product['id'];
                                    $parentProduct = $selectedProductParents[$parentId] ?? null;
                                    $attributes = null;
                                    $optionGroup = null;

                                    if ($parentProduct && $parentProduct->variations->isNotEmpty()) {
                                        // Get option groups for this parent product
                                        $optionGroup = $parentProduct->variations
                                            ->pluck('options')
                                            ->flatten()
                                            ->unique('id')
                                            ->groupBy('attribute_id');
                                        $attributes = \App\Models\Attribute::find($optionGroup->keys());
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <img src="{{ asset($product['image']) }}" width="100" height="100"
                                            alt="">
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route('products.show', $product['slug']) }}">{{ $product['name'] }}</a>

                                        @if($parentProduct && $parentProduct->variations->isNotEmpty() && isset($attributes))
                                            @foreach ($attributes as $attribute)
                                                <div class="mt-2 mb-2 form-group product__option">
                                                    <label class="product__option-label">{{ $attribute->name }}</label>
                                                    <div class="input-radio-label">
                                                        <div class="input-radio-label__list">
                                                            @foreach ($optionGroup[$attribute->id] as $option)
                                                                <label>
                                                                    <input type="radio"
                                                                        name="selected_product_{{ $product['id'] }}_attribute_{{ $attribute->id }}"
                                                                        wire:change="updateSelectedProductVariation({{ $product['id'] }}, {{ $attribute->id }}, {{ $option->id }})"
                                                                        value="{{ $option->id }}"
                                                                        class="option-picker"
                                                                        @checked(isset($options[$parentId][$attribute->id]) && $options[$parentId][$attribute->id] == $option->id)
                                                                        @disabled(isReseller() && !is_null($order->source_id))>
                                                                    <span>{{ $option->name }}</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

                                        <div class="mt-2 d-flex flex-column">
                                            @if(isOninda() && config('app.resell'))
                                            <div class="text-nowrap">
                                                Unit Price: {{ $product['price'] }} (buy); {{ $product['retail_price'] ?? $product['price'] }} (sell)
                                            </div>
                                            <div class="text-nowrap">
                                                Total Price: {{ $product['price'] * $product['quantity'] }} (buy); {{ $amount = ($product['retail_price'] ?? $product['price']) * $product['quantity'] }} (sell)
                                            </div>
                                            @else
                                            <div class="text-nowrap">
                                                Total Price: {{ $amount = ($product[isOninda() ? 'retail_price' : 'price'] ?? $product['price']) * $product['quantity'] }}
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-number product__quantity">
                                            <input type="number" id="quantity-{{ $product['id'] }}"
                                                class="form-control input-number__input"
                                                name="quantity[{{ $product['id'] }}]"
                                                value="{{ old('quantity.' . $product['id'], $product['quantity']) }}"
                                                min="1" readonly style="border-radius: 2px;" @disabled(isReseller() && !is_null($order->source_id))>
                                            <div class="input-number__add"
                                                @unless(isReseller() && !is_null($order->source_id))
                                                wire:click="increaseQuantity({{ $product['id'] }})"
                                                @endunless
                                            >
                                            </div>
                                            <div class="input-number__sub"
                                                @unless(isReseller() && !is_null($order->source_id))
                                                wire:click="decreaseQuantity({{ $product['id'] }})"
                                                @endunless
                                            >
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @php($retail += (float) $amount)
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($order->exists && config('services.courier_report.cheap'))
                <h5 class="mt-3">Courier Report</h5>
                <div style="height: 645px; overflow: hidden; position: relative;">
                    @if (\Illuminate\Support\Carbon::parse(config('services.courier_report.expires'))->isPast())
                        <div class="alert alert-danger">Courier Report API Expired</div>
                    @else
                        <iframe src="https://www.bdcommerce.app/tools/delivery-fraud-check/{{ $order->phone }}" width="1200" height="800" scrolling="no" style="position: absolute; top: -110px; left: -580px; overflow: hidden;"></iframe>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="mt-4 col-12 col-lg-6 col-xl-5 mt-lg-0">
        <div class="shadow-sm card rounded-0">
            <div class="p-3 card-header">
                <h5 class="card-title">Your Order</h5>
            </div>
            <div class="p-3 card-body">
                <table class="table checkout__totals table-borderless">
                    <tbody class="checkout__totals-subtotals">
                        <tr>
                            <th style="vertical-align: middle;">Order Status</th>
                            <td>
                                <select wire:model="status" id="status" class="form-control" @disabled($order->status === 'RETURNED' || isReseller() && !is_null($order->source_id))>
                                    @foreach (config('app.orders', []) as $stat)
                                        @if($order->status === 'DELIVERED')
                                            <option value="{{ $stat }}" {{ $stat === 'RETURNED' ? '' : 'disabled' }}>{{ $stat }}</option>
                                        @elseif($order->status === 'SHIPPING')
                                            <option value="{{ $stat }}">{{ $stat }}</option>
                                        @else
                                            <option value="{{ $stat }}" {{ $stat === 'RETURNED' ? 'disabled' : '' }}>{{ $stat }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @if (isOninda() && config('app.resell'))
                        <tr>
                            <th style="vertical-align: middle;">Subtotal</th>
                            <td class="checkout-subtotal">{!! theMoney($subtotal) !!} (buy)<br>{!! theMoney($retail) !!} (sell)</td>
                        </tr>
                        <tr>
                            <th style="font-size: 12px; white-space: nowrap;">Reseller Discount</th>
                            <td>
                                {!! theMoney($order->data['retail_discount'] ?? 0) !!}
                            </td>
                        </tr>
                        <tr>
                            <th style="font-size: 12px; white-space: nowrap;">Reseller Advanced</th>
                            <td>
                                {!! theMoney($order->data['advanced'] ?? 0) !!}
                            </td>
                        </tr>
                        <tr>
                            <th style="font-size: 12px; white-space: nowrap;">Reseller Delivery Charge</th>
                            <td>
                                {!! theMoney($order->data['retail_delivery_fee'] ?? $order->data['shipping_cost']) !!}
                            </td>
                        </tr>
                        @else
                        <tr>
                            <th>Subtotal</th>
                            @if(isOninda())
                            <td class="checkout-subtotal">{!! theMoney($retail) !!}</td>
                            @else
                            <td class="checkout-subtotal">{!! theMoney($subtotal) !!}</td>
                            @endif
                        </tr>
                        <tr>
                            <th style="vertical-align: middle;">Advanced</th>
                            <td>
                                <input type="text" class="form-control" style="height: auto; padding: 2px 8px;" wire:model.live.debounce.500ms="advanced" @disabled(isReseller() && !is_null($order->source_id))>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th style="font-size:14px;white-space:nowrap;vertical-align:middle;">Our Delivery Charge</th>
                            <td class="shipping">
                                <input class="shipping form-control" style="height: auto; padding: 2px 8px;"
                                    type="text" wire:model.live.debounce.500ms="{{ (!isOninda() || config('app.resell')) ? 'shipping_cost' : 'retail_delivery_fee' }}"
                                    class="form-control" @disabled(isReseller() && !is_null($order->source_id))>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="checkout__totals-footer">
                        <tr>
                            <th style="font-size:14px;white-space:nowrap;vertical-align:middle;">Our Discount</th>
                            <td>
                                <input style="height: auto; padding: 2px 8px;" type="text"
                                    wire:model.live.debounce.500ms="discount" class="form-control" @disabled(isReseller() && !is_null($order->source_id))>
                            </td>
                        </tr>
                        @if(($couponDiscount = (float) ($order->data['coupon_discount'] ?? 0)) > 0)
                        <tr>
                            <th style="font-size:14px;white-space:nowrap;vertical-align:middle;">Coupon Discount</th>
                            <td class="text-success">{!! theMoney($couponDiscount) !!}</td>
                        </tr>
                        @endif
                        @if(isOninda() && config('app.resell'))
                        <tr>
                            <th style="font-size:14px;white-space:nowrap;vertical-align:middle;">Packaging Charge</th>
                            <td>
                                <input style="height: auto; padding: 2px 8px;" type="text"
                                    wire:model.live.debounce.500ms="packaging_charge" class="form-control" placeholder="25">
                            </td>
                        </tr>
                        @endif
                        @if(isOninda() && config('app.resell'))
                        <tr>
                            <th style="vertical-align: middle;">Grand Total</th>
                            <td class="checkout-subtotal">
                                <strong>{!! theMoney((float) $subtotal + (float) $shipping_cost + (float) ($order->data['packaging_charge'] ?? 25) - (float) $discount - (float) ($couponDiscount ?? 0)) !!}</strong> (buy)<br>
                                <strong>{!! theMoney((float) $retail + (float) ($order->data['retail_delivery_fee'] ?? $shipping_cost) - (float) $advanced - (float) ($order->data['retail_discount'] ?? 0)) !!}</strong> (sell)
                            </td>
                        </tr>
                        @else
                        <tr>
                            <th style="vertical-align: middle;">Grand Total</th>
                            <td class="checkout-subtotal">
                                @if(isOninda())
                                <strong>{!! theMoney((float) $retail + (float) ($order->data['retail_delivery_fee'] ?? 0) - (float) $advanced - (float) ($order->data['discount'] ?? 0)) !!}</strong>
                                @else
                                <strong>{!! theMoney((float) $subtotal + (float) $shipping_cost - (float) $discount - (float) $advanced - (float) ($couponDiscount ?? 0)) !!}</strong>
                                @endif
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th>Note <small>(Optional)</small></th>
                            <td>
                                <div class="form-group">
                                    <x-textarea name="note" wire:model="note" rows="4" :disabled="isReseller() && !is_null($order->source_id)" />
                                    <x-error field="note" />
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <button type="submit" wire:click="updateOrder"
                    class="btn btn-primary btn-xl btn-block" @disabled(isReseller() && !is_null($order->source_id))>Update</button>
            </div>
        </div>
        @if ($order->exists)
            <?php
            function getData($data)
            {
                if (isset($data['data'])) {
                    $data = array_merge($data, $data['data']);
                    unset($data['data']);
                }
                return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            ?>
            @if(isOninda())
            <div class="shadow-sm card rounded-0">
                <div class="p-3 card-header">
                    <h5 class="mb-0 card-title">Reseller</h5>
                </div>
                <div class="p-3 card-body">
                    <table class="table table-responsive table-borderless w-100">
                        <tbody>
                            <tr>
                                <th class="py-1">Name</th>
                                <td class="py-1">{{ $order->user->name }}</td>
                            </tr>
                            <tr>
                                <th class="py-1">Shop</th>
                                <td class="py-1">{{ $order->user->shop_name ?? 'Walk-in Customer' }}</td>
                            </tr>
                            <tr>
                                <th class="py-1">Phone</th>
                                <td class="py-1">{{ $order->user->phone_number }}</td>
                            </tr>
                            @if(config('app.resell'))
                            <tr>
                                <th class="py-1">Balance</th>
                                <td class="py-1">{{ $order->user->balance }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="shadow-sm card rounded-0" wire:init="loadActivities">
                <div class="p-3 card-header">
                    <h5 class="mb-0 card-title">Activities</h5>
                </div>
                <div class="p-3 card-body">
                    <div wire:loading wire:target="loadActivities" class="py-4 text-center text-muted">Loading activities...</div>
                    @if ($activitiesLoaded)
                        {{-- Accordion --}}
                        <div id="accordion">
                            @foreach ($activities as $activity)
                                <div class="mb-1 shadow-sm card rounded-0">
                                    <div class="px-3 py-2 card-header" id="heading{{ $activity->id }}">
                                        <a class="text-dark" data-toggle="collapse"
                                            href="#collapse-{{ $activity->id }}">
                                            <div class="pb-1 mb-1 border-bottom text-primary">{{ $activity->description }}
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <div><i
                                                        class="mr-1 fa fa-user"></i>{{ $activity->causer->name ?? 'System' }}
                                                </div>
                                                <div><i
                                                        class="mr-1 fa fa-clock-o"></i>{{ $activity->created_at->format('d-M-Y h:i A') }}
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <div id="collapse-{{ $activity->id }}" class="collapse" data-parent="#accordion">
                                        <div class="p-3 card-body">
                                            <table class="table table-responsive">
                                                <tbody>
                                                    @if ($activity->changes['old'] ?? false)
                                                        <tr>
                                                            <th class="text-center">OLD</th>
                                                            <th class="text-center">NEW</th>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        @if ($activity->changes['old'] ?? false)
                                                            <td>
                                                                <pre><div class="language-php">{{ getData($activity->changes['old'] ?? []) }}</div></pre>
                                                            </td>
                                                        @endif
                                                        <td>
                                                            <pre><div class="language-php">{{ getData($activity->changes['attributes'] ?? []) }}</div></pre>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-4 text-center text-muted" wire:loading.remove wire:target="loadActivities">Loading activities...</div>
                    @endif
                </div>
            </div>

            @if (config('services.courier_report.url') && config('services.courier_report.key'))
                <div id="courier-report" class="shadow-sm card rounded-0" wire:init="loadCourierReport">
                    <div class="p-3 card-header">
                        <h5 class="mb-0 card-title">Courier Report</h5>
                    </div>
                    <div class="p-0 card-body">
                        <div wire:loading wire:target="loadCourierReport" class="py-4 text-center text-muted">Loading courier report...</div>
                        @if ($courierReportLoaded)
                            @if (is_string($this->courier_report))
                                <div class="alert alert-danger">{{ $this->courier_report }}</div>
                                <div class="alert alert-danger">Please wait 5 minutes</div>
                            @else
                                {!! str_replace('New Customer', '', $this->courier_report['details']) !!}
                            @endif
                        @else
                            <div class="py-4 text-center text-muted" wire:loading.remove wire:target="loadCourierReport">Loading courier report...</div>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@push('scripts')
    <script>
        function selector() {
            // Check if Select2 is available and element exists
            if (typeof $.fn.select2 === 'undefined') {
                // Select2 not loaded yet, wait and retry
                setTimeout(selector, 100);
                return;
            }

            const $selector = $('[selector]');
            if ($selector.length === 0) {
                // No selector element found, nothing to initialize
                return;
            }

            // Initialize Select2 if not already initialized
            if (!$selector.hasClass('select2-hidden-accessible')) {
                $selector.select2();
            }

            // Remove existing change handlers to prevent duplicates
            $selector.off('change.select2-init');

            // Add change handler
            $selector.on('change.select2-init', function() {
                @this.set('area_id', this.value);
            });
        }

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', selector);
        } else {
            selector();
        }

        // Also initialize after Livewire updates
        if (typeof Livewire !== 'undefined') {
            Livewire.hook('morphed', function() {
                setTimeout(selector, 50);
            });
        }
    </script>
    <script>
        // Auto-trigger lazy loads without user interaction
        (function () {
            const loadLazy = function() {
                // Avoid repeated calls
                if (window.__editOrderLazyLoaded) {
                    return;
                }
                window.__editOrderLazyLoaded = true;

                if (typeof $wire !== 'undefined') {
                    if (typeof $wire.loadActivities === 'function') {
                        $wire.loadActivities();
                    }
                }
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(loadLazy, 50);
                }, { once: true });
            } else {
                setTimeout(loadLazy, 50);
            }
        })();
    </script>
@endpush
