<div class="col-md-12">
    <div class="row" x-data="{free: {{$free_delivery ?? 0}}, all: {{$free_for_all ?? 0}}}">
        <div class="py-2 col-md-6">
            <div class="d-flex">
                <label for="">Delivery Charge</label>
                <div class="ml-2 custom-control custom-checkbox checkbox-inline">
                    <input type="hidden" name="free_delivery[enabled]" x-model="free" value="0">
                    <input id="free" class="custom-control-input" type="checkbox" wire:model.live="free_delivery" name="free_delivery[enabled]" x-model="free" value="1" x-bind:checked="free">
                    <label for="free" class="custom-control-label">Free Delivery</label>
                </div>
                <div x-show="free" class="ml-2 custom-control custom-checkbox checkbox-inline">
                    <input type="hidden" name="free_delivery[for_all]" x-model="all" value="0">
                    <input id="all" class="custom-control-input" type="checkbox" wire:model.live="free_for_all" name="free_delivery[for_all]" x-model="all" value="1" x-bind:checked="all">
                    <label for="all" class="custom-control-label">For All Products</label>
                </div>
            </div>
            <div x-show="free && !all" class="px-3 row">
                <input type="search" wire:model.live.debounce.250ms="search" id="search"
                    placeholder="Search Product" class="form-control">
                
                @if (session()->has('error'))
                    <strong class="text-danger d-flex align-items-center">{{ session('error') }}</strong>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div x-show="!free || !all" class="py-2 row borderr">
                @php
                    $default_area = setting('default_area');
                @endphp
                <div class="col-md-6 pr-md-1">
                    <label for="products_page-rows" class="d-flex justify-content-between">
                        <div>Inside Dhaka</div>
                        <div>
                            <input type="hidden" name="default_area[inside]" value="0">
                            <input type="checkbox" name="default_area[inside]" id="delivery-inside" @if($default_area->inside ?? false) checked @endif>
                            <label class="mb-0 ml-1" for="delivery-inside">Default</label>
                        </div>
                    </label>
                    <x-input name="delivery_charge[inside_dhaka]" id="delivery_charge-inside_dhaka" :value="$delivery_charge['inside_dhaka'] ?? config('services.shipping')['Inside Dhaka']" />
                    <x-error field="delivery_charge.inside_dhaka" />
                </div>
                <div class="col-md-6 pl-md-1">
                    <label for="products_page-cols" class="d-flex justify-content-between">
                        <div>Outside Dhaka</div>
                        <div>
                            <input type="hidden" name="default_area[outside]" value="0">
                            <input type="checkbox" name="default_area[outside]" id="delivery-outside" @if($default_area->outside ?? false) checked @endif>
                            <label class="mb-0 ml-1" for="delivery-outside">Default</label>
                        </div>
                    </label>
                    <x-input name="delivery_charge[outside_dhaka]" id="delivery_charge-outside_dhaka" :value="$delivery_charge['outside_dhaka'] ?? config('services.shipping')['Outside Dhaka']" />
                    <x-error field="delivery_charge.outside_dhaka" />
                </div>
            </div>
            <div x-show="free && all" class="py-2 row borderr">
                <div class="pr-0 col-md-6">
                    <label for="products_page-rows">Minimum No. of Products</label>
                    <x-input name="free_delivery[min_quantity]" id="free_delivery-min_quantity" :value="$min_quantity ?? false" />
                    <x-error field="free_delivery.min_quantity" />
                </div>
                <div class="pl-0 col-md-6">
                    <label for="products_page-cols">Minimum Total Amount</label>
                    <x-input name="free_delivery[min_amount]" id="free_delivery-min_amount" :value="$min_amount ?? false" />
                    <x-error field="free_delivery.min_amount" />
                </div>
            </div>
        </div>
        <div class="col-md-12" x-show="free && !all">
            <div class="my-2 table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Min Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>
                                    <img src="{{ asset(optional($product->base_image)->src) }}" width="100"
                                        height="100" alt="">
                                </td>
                                <td>
                                    <a class="mb-2 d-block"
                                        href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary"
                                        wire:click="addProduct({{ $product }})">Enable</button>
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
                                </td>
                                <td>
                                    
                                    <div class="input-number product__quantity">
                                        <input type="number" id="quantity-{{ $product['id'] }}"
                                            class="form-control input-number__input"
                                            name="free_delivery[products][{{$product['id']}}]"
                                            wire:model.live="selectedProducts.{{$product['id']}}.quantity"
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