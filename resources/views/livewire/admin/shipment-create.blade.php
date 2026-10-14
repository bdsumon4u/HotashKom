<div>
    @if (session()->has('success'))
        <div class="alert alert-success shadow-sm mb-4">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="alert alert-info border-left-info shadow-sm mb-4" style="border-left: 4px solid #17a2b8;">
        <div class="d-flex align-items-center">
            <i class="fa fa-info-circle fa-2x mr-3 text-info"></i>
            <div>
                <strong class="d-block text-dark font-weight-bold">Double-Entry Stock to Courier Transfer (কুরিয়ারে মাল পাঠানোর হিসাব)</strong>
                <span class="text-light small">
                    Submitting this form will automatically <strong>reduce product warehouse stock (stock_count)</strong> and transfer the total purchase cost from <strong>Inventory (#1004)</strong> to the <strong>In Courier (#1006)</strong> asset account.
                </span>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save">
        <!-- Shipment Meta Information -->
        <div class="card shadow-sm border mb-4 bg-white">
            <div class="card-header bg-light border-bottom p-3">
                <h6 class="mb-0 font-weight-bold text-dark">
                    <i class="fa fa-truck text-primary mr-2"></i> Shipment & Courier Details
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="shipment_date" class="font-weight-bold text-dark">Shipment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control font-weight-bold text-dark" id="shipment_date" wire:model.defer="shipment_date" required>
                        @error('shipment_date') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label for="courier_name" class="font-weight-bold text-dark">Courier Service <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" list="courier_options" class="form-control font-weight-bold text-dark" id="courier_name" wire:model.defer="courier_name" placeholder="Select or type courier name" required>
                            <datalist id="courier_options">
                                <option value="Steadfast Courier">
                                <option value="Pathao Courier">
                                <option value="RedX Logistics">
                                <option value="Paperfly">
                                <option value="Sundarban Courier">
                                <option value="eCourier">
                                <option value="SA Paribahan">
                                <option value="In-House Delivery">
                            </datalist>
                        </div>
                        @error('courier_name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label for="tracking_code" class="font-weight-bold text-dark">Consignment / Tracking #</label>
                        <input type="text" class="form-control text-dark" id="tracking_code" wire:model.defer="tracking_code" placeholder="e.g. CID-98234 or STDF-8723">
                        @error('tracking_code') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label for="notes" class="font-weight-bold text-dark">Notes / Reference (Optional)</label>
                    <input type="text" class="form-control text-dark" id="notes" wire:model.defer="notes" placeholder="e.g. Batch #4 dispatched for Chittagong & Dhaka Hubs">
                    @error('notes') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Product / Variant Search & Selection -->
        <div class="card shadow-sm border mb-4 bg-white">
            <div class="card-header bg-light border-bottom p-3">
                <h6 class="mb-0 font-weight-bold text-dark">
                    <i class="fa fa-boxes text-primary mr-2"></i> Select Products to Ship
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="form-group position-relative mb-3">
                    <label for="product-search" class="font-weight-bold text-dark">Search Product or Variant <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                        </div>
                        <input type="text" class="form-control" id="product-search" placeholder="Type product name or SKU (min 3 letters)..." wire:model.live.debounce.350ms="search" :key="$inputKey" autocomplete="off">
                    </div>

                    @if(strlen($search) > 2 && count($products) > 0)
                        <div class="bg-white border rounded shadow-lg position-absolute w-100 mt-1" style="z-index: 1050; max-height: 320px; overflow-y: auto;">
                            @foreach($products as $product)
                                <div class="dropdown-item py-2 border-bottom cursor-pointer d-flex justify-content-between align-items-center" wire:click="selectProduct({{ $product->id }})" style="cursor: pointer;">
                                    <div>
                                        <strong class="text-dark">{{ $product->name }}</strong>
                                        <span class="text-muted ml-1">({{ $product->sku }})</span>
                                        @if($product->brand)
                                            <span class="badge badge-light border ml-1">{{ $product->brand->name }}</span>
                                        @endif
                                        <div class="small text-muted">
                                            Stock: <span class="font-weight-bold text-dark">{{ $product->stock_count }}</span> |
                                            Avg Cost: <span class="font-weight-bold text-success">৳{{ number_format((float) ($product->average_purchase_price ?? 0), 2) }}</span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm"><i class="fa fa-plus"></i> Add</button>
                                </div>
                                @if(isset($product->variations) && count($product->variations) > 0)
                                    <div class="bg-light pl-4 pr-2 py-1">
                                        @foreach($product->variations as $variation)
                                            <div class="dropdown-item py-1 small rounded border-bottom cursor-pointer d-flex justify-content-between align-items-center" wire:click.stop="selectVariant({{ $variation->id }})" style="cursor: pointer;">
                                                <div>
                                                    <span class="font-weight-bold text-secondary">{{ $product->name }} [{{ $variation->name }}]</span>
                                                    <span class="text-muted">({{ $variation->sku }})</span>
                                                    @foreach($variation->options as $option)
                                                        <span class="badge badge-info">{{ $option->name }}</span>
                                                    @endforeach
                                                    <div class="text-muted" style="font-size: 11px;">
                                                        Stock: {{ $variation->stock_count }} | Cost: ৳{{ number_format((float) ($variation->average_purchase_price ?? $product->average_purchase_price ?? 0), 2) }}
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-outline-info btn-xs py-0 px-2"><i class="fa fa-plus"></i></button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                @error('items') <div class="alert alert-danger py-2 px-3 small">{{ $message }}</div> @enderror

                <!-- Shipped Products Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Product / Variant</th>
                                <th style="width: 130px;" class="text-center">Current Stock</th>
                                <th style="width: 140px;" class="text-center">Quantity to Ship <span class="text-danger">*</span></th>
                                <th style="width: 180px;">Unit Purchase Cost (৳) <span class="text-danger">*</span></th>
                                <th style="width: 160px;" class="text-right">Total Cost (৳)</th>
                                <th style="width: 60px;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $index => $item)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold text-dark">{{ $item['name'] }}</div>
                                        <div class="small text-muted">
                                            SKU: <span class="badge badge-light border">{{ $item['sku'] }}</span>
                                            @foreach($item['options'] as $opt)
                                                <span class="badge badge-info">{{ $opt }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($item['stock_count'] <= 0)
                                            <span class="badge badge-danger px-2 py-1">0 (Out of Stock)</span>
                                        @elseif($item['stock_count'] < ($item['quantity'] ?? 1))
                                            <span class="badge badge-warning px-2 py-1">Low: {{ $item['stock_count'] }}</span>
                                        @else
                                            <span class="badge badge-success px-2 py-1">{{ $item['stock_count'] }} in stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="number" class="form-control text-center font-weight-bold" wire:model.lazy="items.{{ $index }}.quantity" min="1" step="1" required>
                                        @error('items.'.$index.'.quantity') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text py-0 px-2">৳</span>
                                            </div>
                                            <input type="number" step="0.01" min="0.01" class="form-control font-weight-bold text-dark" wire:model.lazy="items.{{ $index }}.price" placeholder="Cost" required>
                                        </div>
                                        @if(!isset($item['price']) || $item['price'] <= 0)
                                            <small class="text-danger font-weight-bold d-block mt-1">⚠️ No avg cost set! Enter manually.</small>
                                        @endif
                                        @error('items.'.$index.'.price') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="text-right font-weight-bold text-dark" style="font-size: 15px;">
                                        ৳{{ number_format((float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0), 2) }}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm" wire:click="removeItem({{ $index }})" title="Remove item">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa fa-shopping-basket fa-3x mb-3 text-secondary d-block"></i>
                                        <strong>No products added to shipment yet.</strong>
                                        <p class="small mb-0">Search product or variant using the search box above to add items.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($items) > 0)
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="2" class="text-right font-weight-bold">Summary Totals:</th>
                                    <th class="text-center h6 font-weight-bold text-primary mb-0">
                                        {{ $totalQuantity }} pcs
                                    </th>
                                    <th class="text-right font-weight-bold text-muted">Total Shipped Value:</th>
                                    <th class="text-right h5 font-weight-bold text-success mb-0">
                                        ৳{{ number_format($totalPurchaseCost, 2) }}
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Submit Actions & Accounting Preview -->
        <div class="card shadow-sm border mb-4 bg-white">
            <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="mb-3 mb-md-0">
                    <span class="d-block font-weight-bold text-dark" style="font-size: 16px;">
                        Ready to dispatch {{ count($items) }} products ({{ $totalQuantity }} pcs) to courier?
                    </span>
                    <small class="text-muted">
                        Journal Entry Preview: <strong>Dr. In Courier (#1006) ৳{{ number_format($totalPurchaseCost, 2) }}</strong> | <strong>Cr. Inventory (#1004) ৳{{ number_format($totalPurchaseCost, 2) }}</strong>
                    </small>
                </div>
                <div>
                    <a href="{{ route('admin.accounting.transactions.index') }}" class="btn btn-secondary mr-2">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold" @if(count($items) === 0) disabled @endif>
                        <i class="fa fa-truck mr-1"></i> Confirm Shipment & Deduct Stock
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
