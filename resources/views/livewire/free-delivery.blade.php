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
            <div x-show="!free || !all" class="py-2 row borderr" x-data="{ 
                areas: @js($delivery_areas ?: []),
                addArea() {
                    this.areas.push({ name: '', cost: '', is_default: false });
                    this.$nextTick(() => {
                        this.ensureOneDefault();
                    });
                },
                removeArea(index) {
                    const wasDefault = this.areas[index].is_default;
                    this.areas.splice(index, 1);
                    if (wasDefault && this.areas.length > 0) {
                        this.setDefault(0);
                    }
                },
                setDefault(index) {
                    this.areas.forEach((area, i) => {
                        area.is_default = (i === index);
                    });
                },
                ensureOneDefault() {
                    if (this.areas.length > 0 && !this.areas.some(a => a.is_default)) {
                        this.setDefault(0);
                    }
                }
            }" x-init="ensureOneDefault()">
                <div class="col-12 mb-2 d-flex justify-content-between align-items-center">
                    <label class="font-weight-bold mb-0">Delivery Areas & Charges</label>
                    <button type="button" @click="addArea()" class="btn btn-primary btn-sm px-2 py-1">Add Area</button>
                </div>
                <div class="col-12">
                    <template x-for="(area, index) in areas" :key="index">
                        <div class="form-row align-items-center mb-2">
                            <div class="col-5">
                                <input type="text" :name="'delivery_areas[' + index + '][name]'" x-model="area.name" class="form-control form-control-sm" placeholder="Area Name" required>
                            </div>
                            <div class="col-4">
                                <input type="number" :name="'delivery_areas[' + index + '][cost]'" x-model="area.cost" class="form-control form-control-sm" placeholder="Charge" required min="0">
                            </div>
                            <div class="col-2 text-center">
                                <div class="custom-control custom-radio">
                                    <input type="radio" :id="'default_area_' + index" name="default_delivery_area" :value="index" :checked="area.is_default" @change="setDefault(index)" class="custom-control-input">
                                    <label class="custom-control-label small" :for="'default_area_' + index">Default</label>
                                </div>
                            </div>
                            <div class="col-1 text-right">
                                <button type="button" @click="removeArea(index)" class="btn btn-danger btn-sm p-1 px-2">&times;</button>
                            </div>
                        </div>
                    </template>
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