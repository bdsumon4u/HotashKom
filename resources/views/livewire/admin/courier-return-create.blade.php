<div>
    @if (session()->has('success'))
        <div class="alert alert-success shadow-sm mb-4">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="alert alert-danger border-left-danger shadow-sm mb-4" style="border-left: 4px solid #dc3545;">
        <div class="d-flex align-items-center">
            <i class="fa fa-undo fa-2x mr-3 text-danger"></i>
            <div>
                <strong class="d-block text-dark font-weight-bold">Courier Return & Restock (কুরিয়ার থেকে ফেরত আসা পণ্যের হিসাব)</strong>
                <span class="text-light small">
                    Submitting this form will automatically <strong>restore warehouse product stock (stock_count)</strong> and transfer the purchase cost from <strong>In Courier (#1006)</strong> back to the <strong>Inventory (#1004)</strong> asset account.
                </span>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save">
        <!-- Return Meta Information -->
        <div class="card shadow-sm border mb-4 bg-white">
            <div class="card-header bg-light border-bottom p-3">
                <h6 class="mb-0 font-weight-bold text-dark">
                    <i class="fa fa-undo text-danger mr-2"></i> Return & Courier Information
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="return_date" class="font-weight-bold text-dark">Return Received Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control font-weight-bold text-dark" id="return_date" wire:model.defer="return_date" required>
                        @error('return_date') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label for="courier_name" class="font-weight-bold text-dark">Courier Service Name</label>
                        <input type="text" list="courier_options_return" class="form-control font-weight-bold text-dark" id="courier_name" wire:model.defer="courier_name" placeholder="Select or type courier">
                        <datalist id="courier_options_return">
                            <option value="Steadfast Courier">
                            <option value="Pathao Courier">
                            <option value="RedX Logistics">
                            <option value="Paperfly">
                            <option value="Sundarban Courier">
                            <option value="eCourier">
                            <option value="SA Paribahan">
                        </datalist>
                        @error('courier_name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label for="tracking_code" class="font-weight-bold text-dark">Consignment / Return Tracking #</label>
                        <input type="text" class="form-control text-dark" id="tracking_code" wire:model.defer="tracking_code" placeholder="e.g. CID-98234 or RET-8723">
                        @error('tracking_code') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label for="return_charge" class="font-weight-bold text-dark">Return / Damage Fee (If Charged)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text py-0 px-2 font-weight-bold text-danger">৳</span>
                            </div>
                            <input type="number" step="0.01" min="0" class="form-control font-weight-bold text-danger" id="return_charge" wire:model.live.debounce.300ms="return_charge" placeholder="0.00">
                        </div>
                        @error('return_charge') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    @if($returnChargeAmount > 0)
                        <div class="form-group col-md-6">
                            <label for="paid_from_account_id" class="font-weight-bold text-dark">Paid Return Fee From <span class="text-danger">*</span></label>
                            <select class="form-control font-weight-bold text-dark" id="paid_from_account_id" wire:model.defer="paid_from_account_id">
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} (৳{{ number_format((float)$acc->current_balance, 2) }})</option>
                                @endforeach
                            </select>
                            @error('paid_from_account_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div class="form-group {{ $returnChargeAmount > 0 ? 'col-md-6' : 'col-md-12' }} mb-0">
                        <label for="notes" class="font-weight-bold text-dark">Notes / Reason (Optional)</label>
                        <input type="text" class="form-control text-dark" id="notes" wire:model.defer="notes" placeholder="e.g. Customer cancelled upon delivery / parcel returned in good condition">
                        @error('notes') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Returned Products Search & Table -->
        <div class="card shadow-sm border mb-4 bg-white">
            <div class="card-header bg-light border-bottom p-3">
                <h6 class="mb-0 font-weight-bold text-dark">
                    <i class="fa fa-boxes text-danger mr-2"></i> Select Returned Products to Restock
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="form-group position-relative mb-3">
                    <label for="product-search-return" class="font-weight-bold text-dark">Search Returned Product or Variant <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                        </div>
                        <input type="text" class="form-control" id="product-search-return" placeholder="Type product name or SKU to add returned items..." wire:model.live.debounce.350ms="search" :key="$inputKey" autocomplete="off">
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
                                            Current Warehouse Stock: <span class="font-weight-bold text-dark">{{ $product->stock_count }}</span> |
                                            Avg Cost: <span class="font-weight-bold text-success">৳{{ number_format((float) ($product->average_purchase_price ?? 0), 2) }}</span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm"><i class="fa fa-plus"></i> Add</button>
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
                                                <button type="button" class="btn btn-outline-danger btn-xs py-0 px-2"><i class="fa fa-plus"></i></button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                @error('items') <div class="alert alert-danger py-2 px-3 small">{{ $message }}</div> @enderror

                <!-- Returned Products Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Product / Variant</th>
                                <th style="width: 140px;" class="text-center">Current Stock</th>
                                <th style="width: 150px;" class="text-center">Returned Qty <span class="text-danger">*</span></th>
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
                                        <span class="badge badge-secondary px-2 py-1">{{ $item['stock_count'] }} in warehouse</span>
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
                                            <input type="number" step="0.01" min="0" class="form-control font-weight-bold text-dark" wire:model.lazy="items.{{ $index }}.price" placeholder="Cost" required>
                                        </div>
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
                                        <i class="fa fa-undo fa-3x mb-3 text-secondary d-block"></i>
                                        <strong>No returned products added yet.</strong>
                                        <p class="small mb-0">Search product or variant using the search box above to add items received back from courier.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($items) > 0)
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="2" class="text-right font-weight-bold">Summary Totals:</th>
                                    <th class="text-center h6 font-weight-bold text-danger mb-0">
                                        +{{ $totalQuantity }} pcs
                                    </th>
                                    <th class="text-right font-weight-bold text-muted">Total Restock Value:</th>
                                    <th class="text-right h5 font-weight-bold text-success mb-0">
                                        ৳{{ number_format($calculatedPurchaseCost, 2) }}
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                @if(count($items) > 0)
                    <div class="row pt-3">
                        <div class="col-md-6 ml-auto">
                            <div class="card border bg-light shadow-none p-3 mb-0">
                                <label for="override_total_cost" class="font-weight-bold text-dark mb-1">
                                    Override Total Restocked Value (Transferred back to Inventory)
                                </label>
                                <small class="text-muted d-block mb-2">
                                    Calculated: <strong>৳{{ number_format($calculatedPurchaseCost, 2) }}</strong>. Enter amount below if you want to override the total cost.
                                </small>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text py-0 px-2 font-weight-bold">৳</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" class="form-control font-weight-bold text-dark" id="override_total_cost" wire:model.live.debounce.300ms="override_total_cost" placeholder="{{ $calculatedPurchaseCost > 0 ? number_format($calculatedPurchaseCost, 2, '.', '') : '0.00' }}">
                                </div>
                                @if($override_total_cost !== null && trim((string)$override_total_cost) !== '')
                                    <small class="text-danger font-weight-bold mt-1 d-block"><i class="fa fa-pencil mr-1"></i> Overridden to: ৳{{ number_format((float)$override_total_cost, 2) }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Submit Actions & Accounting Preview -->
        <div class="card shadow-sm border mb-4 bg-white">
            <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="mb-3 mb-md-0">
                    <span class="d-block font-weight-bold text-dark" style="font-size: 16px;">
                        Restock {{ count($items) }} products (+{{ $totalQuantity }} pcs) into warehouse?
                    </span>
                    <small class="text-muted">
                        Journal Entry Preview: <strong>Dr. Inventory (#1004) ৳{{ number_format($finalPurchaseCost, 2) }}</strong> | <strong>Cr. In Courier (#1006) ৳{{ number_format($finalPurchaseCost, 2) }}</strong>
                        @if($returnChargeAmount > 0)
                            | <strong>Dr. Return Fee ৳{{ number_format($returnChargeAmount, 2) }}</strong>
                        @endif
                    </small>
                </div>
                <div>
                    <a href="{{ route('admin.reports.return') }}" class="btn btn-secondary mr-2">Cancel</a>
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold" @if(count($items) === 0) disabled @endif>
                        <i class="fa fa-undo mr-1"></i> Confirm Return & Restock Warehouse
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
