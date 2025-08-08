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
                        <x-input name="name" wire:model="name" placeholder="Type your name here" />
                        <x-error field="name" />
                    </div>
                    <div class="form-group col-md-6">
                        <x-label for="phone">Phone</x-label> <span class="text-danger">*</span>
                        <x-input type="tel" name="phone" wire:model="phone"
                            placeholder="Type your phone number here" />
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
                    <label class="d-block">Delivery Charge City <span class="text-danger">*</span></label>
                    <div class="form-control h-auto @error('shipping_area') is-invalid @enderror">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="inside-dhaka" name="shipping"
                                wire:model.live="shipping_area" value="Inside Dhaka">
                            <label class="custom-control-label" for="inside-dhaka">Inside
                                Dhaka</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="outside-dhaka" name="shipping"
                                wire:model.live="shipping_area" value="Outside Dhaka">
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
                @if((setting('Pathao')->enabled ?? false) && (setting('Pathao')->user_selects_city_area ?? false))
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="">City</label>
                            <select class="form-control" wire:model.live="city_id">
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
                            <select wire:loading.remove wire:target="city_id" class="form-control" wire:model="area_id">
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
                            <input type="number" wire:model="weight" class="form-control" placeholder="Weight in KG">
                        </div>
                    </div>
                @else
                    <div class="form-group">
                        <label for="weight">Weight</label>
                        <input type="number" wire:model="weight" class="form-control" placeholder="Weight in KG" readonly>
                        <small class="text-muted">Location and weight are managed by admin</small>
                    </div>
                @endif
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
                                        <button type="button" class="btn btn-primary btn-sm" wire:click="addProduct({{ $product->id }})">
                                            Add to Order
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Selected Products Section -->
                <div class="mt-4">
                    <h6>Selected Products</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($selectedProducts as $id => $product)
                                    <tr>
                                        <td>
                                            <img src="{{ asset($product['image'] ?? 'assets/images/no-image.png') }}"
                                                width="50" height="50" alt="{{ $product['name'] }}">
                                        </td>
                                        <td>
                                            <a class="mb-2 d-block"
                                                href="{{ route('products.show', $product['slug']) }}">{{ $product['name'] }}</a>
                                        </td>
                                        <td>
                                            <div class="input-number product__quantity">
                                                <input type="number" id="quantity-{{ $id }}"
                                                    class="form-control input-number__input"
                                                    name="quantity[{{ $id }}]"
                                                    value="{{ $product['quantity'] }}"
                                                    min="1" readonly style="border-radius: 2px;">
                                                <div class="input-number__add" wire:click="increaseQuantity({{ $id }})">
                                                </div>
                                                <div class="input-number__sub" wire:click="decreaseQuantity({{ $id }})">
                                                </div>
                                            </div>
                                        </td>
                                        <td>{!! theMoney($product['price']) !!}</td>
                                        <td>{!! theMoney($product['total']) !!}</td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" wire:click="decreaseQuantity({{ $id }})">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6 col-xl-5">
        <div class="shadow-sm card rounded-0">
            <div class="p-3 card-header">
                <h5 class="card-title">Order Summary</h5>
            </div>
            <div class="p-3 card-body">
                <div class="form-group">
                    <x-label for="note">Note</x-label>
                    <x-input name="note" wire:model="note" placeholder="Add a note to this order" />
                    <x-error field="note" />
                </div>
                <div class="form-group">
                    <label for="retail_discount">Discount</label>
                    <input type="text" wire:model.live.debounce.500ms="retail_discount" class="form-control" placeholder="Discount amount">
                    <x-error field="retail_discount" />
                </div>
                <div class="form-group">
                    <label for="advanced">Advanced</label>
                    <input type="text" wire:model.live.debounce.500ms="advanced" class="form-control" placeholder="Advanced amount">
                    <x-error field="advanced" />
                </div>
                <div class="form-group">
                    <label for="retail_delivery_fee">Shipping Cost</label>
                    <input type="text" wire:model.live.debounce.500ms="retail_delivery_fee" class="form-control">
                    <x-error field="retail_delivery_fee" />
                </div>
                <div class="form-group">
                    <label for="subtotal">Subtotal</label>
                    <input type="text" wire:model="subtotal" class="form-control" readonly>
                    <x-error field="subtotal" />
                </div>
                <div class="form-group">
                    <label for="total">Total</label>
                    <input type="text" value="{{ $subtotal + $retail_delivery_fee - $retail_discount - $advanced }}" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-primary" wire:click="updateOrder">
                        Update Order
                    </button>
                    @if($canCancel)
                        <button type="button" class="btn btn-danger" wire:click="cancelOrder"
                                onclick="return confirm('Are you sure you want to cancel this order? This action cannot be undone.')">
                            Cancel Order
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
