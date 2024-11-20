<div class="row">
    <div class="col-12 col-lg-6 col-xl-7">
        <div class="shadow-sm card rounded-0">
            <div class="p-3 card-header">
                <h5 class="card-title">Billing details</h5>
            </div>
            <div class="p-3 card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <x-label for="name">Name</x-label> <span
                            class="text-danger">*</span>
                        <x-input name="name" wire:model="name" placeholder="Type your name here" />
                        <x-error field="name" />
                    </div>
                    <div class="form-group col-md-6">
                        <x-label for="phone">Phone</x-label> <span
                            class="text-danger">*</span>
                        <x-input type="tel" name="phone" wire:model="phone" placeholder="Type your phone number here" />
                        <x-error field="phone" />
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <x-label for="email">Email Address</x-label>
                        <x-input type="email" name="email" wire:model="email" placeholder="Email Address" />
                        <x-error field="email" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="d-block">Delivery Charge City <span
                            class="text-danger">*</span></label>
                    <div class="form-control h-auto @error('shipping_area') is-invalid @enderror">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="inside-dhaka"
                                name="shipping" wire:model.live="shipping_area" value="Inside Dhaka">
                            <label class="custom-control-label" for="inside-dhaka">Inside
                                Dhaka</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input"
                                id="outside-dhaka" name="shipping" wire:model.live="shipping_area" value="Outside Dhaka">
                            <label class="custom-control-label" for="outside-dhaka">Outside
                                Dhaka</label>
                        </div>
                    </div>
                    <x-error field="shipping_area" />
                </div>
                <div class="form-group">
                    <x-label for="address">Address</x-label> <span class="text-danger">*</span>
                    <x-input name="address" wire:model="address" placeholder="Enter Correct Address" />
                    <x-error field="address" />
                </div>
                <div class="form-group">
                    <label class="d-block">Courier <span class="text-danger">*</span></label>
                    <div class="border p-2 @error('courier') is-invalid @enderror">
                        @foreach (['Pathao', 'SteadFast', 'Other'] as $provider)
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input"
                                    id="{{ $provider }}" wire:model.live="courier"
                                    value="{{ $provider }}">
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
                        <select class="form-control" wire:model.live="city_id">
                            <option value="" selected>Select City</option>
                            @foreach ($order->getCityList() as $city)
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
                        <select wire:loading.remove wire:target="city_id" class="form-control" wire:model="area_id">
                            <option value="" selected>Select Area</option>
                            @foreach ($order->getAreaList() as $area)
                                <option value="{{ $area->zone_id }}">
                                    {{ $area->zone_name }}
                                </option>
                            @endforeach
                        </select>
                        <x-error field="area_id" />
                    </div>
                    <div class="col-md-4">
                        <label for="weight">Weight</label>
                        <input type="number" wire:model="weight" class="form-control" placeholder="Weight in KG">
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
                        placeholder="Search Product" class="col-md-6 form-control">
                    
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
                                        $variation = $product->variations->first(function ($item) use ($options, $product) {
                                            return $item->options->pluck('id')->diff($options[$product->id])->isEmpty();
                                        });
                                        if ($variation) {
                                            $selectedVar = $variation;
                                        }
                                    }
        
                                    $order->dataId = $selectedVar->id;
                                    $order->dataMax = $selectedVar->should_track ? $selectedVar->stock_count : -1;
        
                                    $optionGroup = $product->variations->pluck('options')->flatten()->unique('id')->groupBy('attribute_id');
                                    $attributes = \App\Models\Attribute::find($optionGroup->keys());
                                @endphp
                                <tr>
                                    <td>
                                        <img src="{{ asset(optional($selectedVar->base_image)->src) }}" width="100"
                                            height="100" alt="">
                                    </td>
                                    <td>
                                        <a class="mb-2 d-block"
                                            href="{{ route('products.show', $selectedVar->slug) }}">{{ $product->name }}</a>
                                        
                                        @foreach($attributes as $attribute)
                                        <div class="mb-2 form-group product__option">
                                            <label class="product__option-label">{{$attribute->name}}</label>
                                            <div class="input-radio-label">
                                                <div class="input-radio-label__list">
                                                    @foreach($optionGroup[$attribute->id] as $option)
                                                    <label>
                                                        <input type="radio" wire:model.live="options.{{$product->id}}.{{$attribute->id}}" value="{{$option->id}}" class="option-picker">
                                                        <span>{{$option->name}}</span>
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
                                                @if(! $selectedVar->should_track)
                                                    <span class="text-success">In Stock</span>
                                                @else
                                                    <span class="text-{{ $selectedVar->stock_count ? 'success' : 'danger' }}">{{ $selectedVar->stock_count }} In Stock</span>
                                                @endif
                                            </strong>
                                        </div>
                                        <div class="mb-2 {{$selectedVar->selling_price == $selectedVar->price ? '' : 'has-special'}}">
                                            Price:
                                            @if($selectedVar->selling_price == $selectedVar->price)
                                                {!!  theMoney($selectedVar->price)  !!}
                                            @else
                                                <span class="font-weight-bold">{!!  theMoney($selectedVar->selling_price)  !!}</span>
                                                <del class="text-danger">{!!  theMoney($selectedVar->price)  !!}</del>
                                            @endif
                                        </div>
        
                                        @if($available = !$selectedVar->should_track || $selectedVar->stock_count > 0)
                                        <button type="button" class="btn btn-primary"
                                            wire:click="addProduct({{ $selectedVar }})">Add to Order</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @foreach ($selectedProducts as $product)
                                <tr>
                                    <td>
                                        <img src="{{ asset($product['image']) }}" width="100"
                                            height="100" alt="">
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route('products.show', $product['slug']) }}">{{ $product['name'] }}</a>
                                        
                                        <div class="mt-2 d-flex flex-column flex-md-row">
                                            <div class="mr-md-2 text-nowrap">
                                                Unit Price: {{ $product['price'] }}
                                            </div>
                                            <div class="ml-md-2 text-nowrap">
                                                Total Price: {{ $product['price'] * $product['quantity'] }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-number product__quantity">
                                            <input type="number" id="quantity-{{ $product['id'] }}"
                                                class="form-control input-number__input"
                                                name="quantity[{{ $product['id'] }}]"
                                                value="{{ old('quantity.' . $product['id'], $product['quantity']) }}"
                                                min="1" readonly style="border-radius: 2px;"
                                            >
                                            <div class="input-number__add" wire:click="increaseQuantity({{$product['id']}})">

                                            </div>
                                            <div class="input-number__sub" wire:click="decreaseQuantity({{$product['id']}})">

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
                            <th>Order Status</th>
                            <td>
                                <select wire:model="status" id="status" class="form-control">
                                    @foreach (config('app.orders', []) as $stat)
                                        <option value="{{ $stat }}">{{ $stat }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Subtotal</th>
                            <td class="checkout-subtotal">{!! theMoney($subtotal) !!}</td>
                        </tr>
                        <tr>
                            <th>Delivery Charge</th>
                            <td class="shipping">
                                <input class="shipping form-control"
                                    style="height: auto; padding: 2px 8px;" type="text"
                                    wire:model.live.debounce.350ms="shipping_cost"
                                    class="form-control">
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="checkout__totals-footer">
                        <tr>
                            <th>Advanced</th>
                            <td>
                                <input style="height: auto; padding: 2px 8px;" type="text"
                                    wire:model.live.debounce.350ms="advanced"
                                    class="form-control">
                            </td>
                        </tr>
                        <tr>
                            <th>Discount</th>
                            <td>
                                <input style="height: auto; padding: 2px 8px;" type="text"
                                    wire:model.live.debounce.350ms="discount"
                                    class="form-control">
                            </td>
                        </tr>
                        <tr>
                            <th>Grand Total</th>
                            <th class="checkout-subtotal"><strong>{!! theMoney($subtotal + $shipping_cost - $advanced - $discount) !!}</strong></td>
                        </tr>
                        <tr>
                            <th>Note <small>(Optional)</small></th>
                            <td>
                                <div class="form-group">
                                    <x-textarea name="note" wire:model="note" rows="4"></x-textarea>
                                    <x-error field="note" />
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <button type="submit" wire:click="updateOrder"
                    class="btn btn-primary btn-xl btn-block">Update</button>
            </div>
        </div>
    </div>
</div>