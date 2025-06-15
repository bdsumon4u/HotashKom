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
                <div class="form-group">
                    <label class="d-block">Courier <span class="text-danger">*</span></label>
                    <div class="border p-2 @error('courier') is-invalid @enderror">
                        @foreach (couriers() as $provider)
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" id="{{ $provider }}"
                                    wire:model.live="courier" value="{{ $provider }}">
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
                <div Redx class="form-row @if ($courier != 'Redx') d-none @endif">
                    <div class="form-group col-md-6">
                        <label for="">Area</label>
                        <select selector class="form-control" wire:model="area_id">
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
                        <input type="number" wire:model="weight" class="form-control" placeholder="Weight in grams">
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
                                                wire:click="addProduct({{ $selectedVar }})">Add to Order</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @php($retail = 0)
                            @foreach ($selectedProducts as $product)
                                <tr>
                                    <td>
                                        <img src="{{ asset($product['image']) }}" width="100" height="100"
                                            alt="">
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route('products.show', $product['slug']) }}">{{ $product['name'] }}</a>

                                        <div class="mt-2 d-flex flex-column">
                                            <div class="text-nowrap">
                                                Unit Price: {{ $product['price'] }} (buy); {{ $product['retail_price'] }} (sell)
                                            </div>
                                            <div class="text-nowrap">
                                                Total Price: {{ $product['price'] * $product['quantity'] }} (buy); {{ $amount = $product['retail_price'] * $product['quantity'] }} (sell)
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-number product__quantity">
                                            <input type="number" id="quantity-{{ $product['id'] }}"
                                                class="form-control input-number__input"
                                                name="quantity[{{ $product['id'] }}]"
                                                value="{{ old('quantity.' . $product['id'], $product['quantity']) }}"
                                                min="1" readonly style="border-radius: 2px;">
                                            <div class="input-number__add"
                                                wire:click="increaseQuantity({{ $product['id'] }})">

                                            </div>
                                            <div class="input-number__sub"
                                                wire:click="decreaseQuantity({{ $product['id'] }})">

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @php($retail += $amount)
                            @endforeach
                        </tbody>
                    </table>
                </div>


                <h5 class="mt-3">Courier Report</h5>
                <div style="height: 645px; overflow: hidden; position: relative;">
                    <iframe src="https://www.bdcommerce.app/tools/delivery-fraud-check/{{$order->phone}}" width="1200" height="800" scrolling="no" style="position: absolute; top: -110px; left: -580px; overflow: hidden;"></iframe>
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
                            <th style="vertical-align: middle;">Order Status</th>
                            <td>
                                <select wire:model="status" id="status" class="form-control" {{ $order->status === 'RETURNED' ? 'disabled' : '' }}>
                                    @foreach (config('app.orders', []) as $stat)
                                        @if($order->status === 'COMPLETED')
                                            <option value="{{ $stat }}" {{ $stat === 'RETURNED' ? '' : 'disabled' }}>{{ $stat }}</option>
                                        @else
                                            <option value="{{ $stat }}" {{ $stat === 'RETURNED' ? 'disabled' : '' }}>{{ $stat }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Subtotal</th>
                            <td class="checkout-subtotal">{!! theMoney($subtotal) !!} (buy); {!! theMoney($retail) !!} (sell)</td>
                        </tr>
                        <tr>
                            <th style="font-size: 12px; white-space: nowrap;">Reseller Discount</th>
                            <td>
                                {!! theMoney($order->data['retail_discount'] ?? 0) !!}
                            </td>
                        </tr>
                        <tr>
                            <th style="font-size: 12px; white-space: nowrap;">Reseller Delivery Charge</th>
                            <td>
                                {!! theMoney($order->data['retail_delivery_fee']) !!}
                            </td>
                        </tr>
                        <tr>
                            <th>Our Delivery Charge</th>
                            <td class="shipping">
                                <input class="shipping form-control" style="height: auto; padding: 2px 8px;"
                                    type="text" wire:model.live.debounce.350ms="shipping_cost"
                                    class="form-control">
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="checkout__totals-footer">
                        <tr>
                            <th>Reseller Advanced</th>
                            <td>
                                <input style="height: auto; padding: 2px 8px;" type="text"
                                    wire:model.live.debounce.350ms="advanced" class="form-control">
                            </td>
                        </tr>
                        <tr>
                            <th>Our Discount</th>
                            <td>
                                <input style="height: auto; padding: 2px 8px;" type="text"
                                    wire:model.live.debounce.350ms="discount" class="form-control">
                            </td>
                        </tr>
                        <tr>
                            <th>Grand Total</th>
                            <td class="checkout-subtotal">
                                <strong>{!! theMoney($subtotal + $shipping_cost - $discount) !!}</strong> (buy);
                                <strong>{!! theMoney($retail + $order->data['retail_delivery_fee'] - $advanced - ($order->data['retail_discount'] ?? 0)) !!}</strong> (sell)
                            </td>
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
            <div class="shadow-sm card rounded-0">
                <div class="p-3 card-header">
                    <h5 class="card-title">Reseller</h5>
                </div>
                <div class="p-3 card-body">
                    <table class="table table-responsive table-borderless w-100">
                        <tbody>
                            <tr>
                                <th class="py-1">Name</th>
                                <td class="py-1">{{ $order->user->name }}</td>
                            </tr>
                            <tr>
                                <th class="py-1">Phone</th>
                                <td class="py-1">{{ $order->user->phone_number }}</td>
                            </tr>
                            <tr>
                                <th class="py-1">Address</th>
                                <td class="py-1">{{ $order->user->address }}</td>
                            </tr>
                            <tr>
                                <th class="py-1">Balance</th>
                                <td class="py-1">{{ $order->user->balance }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="shadow-sm card rounded-0">
                <div class="p-3 card-header">
                    <h5 class="card-title">Activities</h5>
                </div>
                <div class="p-3 card-body">
                    {{-- Accordion --}}
                    <div id="accordion">
                        @foreach ($order->activities()->latest()->get() as $activity)
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
                </div>
            </div>
            @if (config('services.courier_report.url') && config('services.courier_report.key'))
                <div class="shadow-sm card rounded-0">
                    <div class="p-3 card-header">
                        <h5 class="card-title">Courier Report</h5>
                    </div>
                    <div class="p-3 card-body">
                        @if (is_string($this->courier_report))
                            <div class="alert alert-danger">{{ $this->courier_report }}</div>
                            <div class="alert alert-danger">Please wait 5 minutes</div>
                        @else
                            <div class="flex-wrap d-flex" style="column-gap: 1rem;">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Courier</th>
                                                <th>Total</th>
                                                <th class="bg-success">Delivered</th>
                                                <th class="bg-danger">Failed</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (['Pathao', 'SteadFast', 'RedX', 'PaperFly'] as $provider)
                                                @php($report = $this->courier_report['courierData'][strtolower($provider)])
                                                <tr>
                                                    <th>{{ $provider }}</th>
                                                    <td class="font-weight-bold">{{ $report['total_parcel'] }}</td>
                                                    <td class="font-weight-bold bg-success">
                                                        {{ $report['success_parcel'] }}</td>
                                                    <td class="font-weight-bold bg-danger">
                                                        {{ $report['cancelled_parcel'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div style="flex: 1;display: flex;flex-direction: column;justify-content: center;"
                                    class="p-2 border font-weight-bold">
                                    @php($summary = $this->courier_report['courierData']['summary'])
                                    @php($failure = $summary['total_parcel'] > 0 ? number_format(($summary['cancelled_parcel'] / $summary['total_parcel']) * 100, 2) : 0)
                                    <div class="px-3 py-1 my-1 text-center border border-secondary">Summary:</div>
                                    <div class="px-3 py-2 my-1 bg-success">Delivered: {{ $summary['success_parcel'] }}
                                        ({{ $summary['success_ratio'] }}%)</div>
                                    <div class="px-3 py-2 my-1 bg-danger">Failed: {{ $summary['cancelled_parcel'] }}
                                        ({{ $failure }}%)</div>
                                    <div class="d-flex">
                                        <div class="px-1 py-2 my-1 text-center bg-success text-nowrap w-100"
                                            @if (round($summary['success_ratio']) > 0) style="width: {{ $summary['success_ratio'] }}% !important;" @endif
                                            title="Success Rate: {{ $summary['success_ratio'] }}%">
                                            {{ $summary['success_ratio'] }}%</div>
                                        <div class="px-1 py-2 my-1 text-center bg-danger text-nowrap w-100"
                                            @if (round($failure) > 0) style="width: {{ $failure }}% !important;" @endif
                                            title="Failure Rate: {{ $failure }}%">{{ $failure }}%</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@push('scripts')
    <script>
        function selector () {
            $('[selector]').select2();
            $('[selector]').on('change', function() {
                @this.set('area_id', this.value);
            });
        }
        Livewire.hook('morphed', selector);
        selector();
    </script>
@endpush
