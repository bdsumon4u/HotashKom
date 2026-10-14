<div>
    @if (session()->has('success'))
        <div class="alert alert-success shadow-sm mb-4">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="alert alert-success border-left-success shadow-sm mb-4" style="border-left: 4px solid #28a745;">
        <div class="d-flex align-items-center">
            <i class="fa fa-check-circle fa-2x mr-3 text-success"></i>
            <div>
                <strong class="d-block text-dark font-weight-bold">Courier Delivery & Payout Settlement (কুরিয়ার পেমেন্ট ও ডেলিভারি হিসাব)</strong>
                <span class="text-light small">
                    This form records delivered products from courier:
                    <strong>In Courier (#1006)</strong> is relieved & transferred to <strong>Product Purchase Cost (#5001)</strong> expense,
                    gross retail sales are credited to <strong>Sales Revenue (#4001)</strong>, and net cash/bank is deposited.
                    <em>Warehouse stock was already deducted at shipment and is not affected.</em>
                </span>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save">
        <!-- Payout & Deposit Details -->
        <div class="card shadow-sm border mb-4 bg-white">
            <div class="card-header bg-light border-bottom p-3">
                <h6 class="mb-0 font-weight-bold text-dark">
                    <i class="fa fa-money text-success mr-2"></i> Payout & Deposit Information
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="entry_date" class="font-weight-bold text-dark">Payout Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control font-weight-bold text-dark" id="entry_date" wire:model.defer="entry_date" required>
                        @error('entry_date') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label for="courier_name" class="font-weight-bold text-dark">Courier Service Name</label>
                        <input type="text" list="courier_list" class="form-control font-weight-bold text-dark" id="courier_name" wire:model.defer="courier_name" placeholder="e.g. Steadfast, Pathao">
                        <datalist id="courier_list">
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
                        <label for="deposit_account_id" class="font-weight-bold text-dark">Deposit To (Cash / Bank) <span class="text-danger">*</span></label>
                        <select class="form-control font-weight-bold text-dark" id="deposit_account_id" wire:model.defer="deposit_account_id" required>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} (৳{{ number_format((float)$acc->current_balance, 2) }})</option>
                            @endforeach
                        </select>
                        @error('deposit_account_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label for="courier_charge" class="font-weight-bold text-dark">Courier Delivery Fee Deducted</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text py-0 px-2 font-weight-bold text-danger">৳</span>
                            </div>
                            <input type="number" step="0.01" min="0" class="form-control font-weight-bold text-danger" id="courier_charge" wire:model.live.debounce.300ms="courier_charge" placeholder="0.00">
                        </div>
                        @error('courier_charge') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6 mb-md-0">
                        <label for="reference" class="font-weight-bold text-dark">Payout Reference / Statement #</label>
                        <input type="text" class="form-control text-dark" id="reference" wire:model.defer="reference" placeholder="e.g. STDF-PAYOUT-9021 or Bank Statement Ref">
                        @error('reference') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group col-md-6 mb-0">
                        <label for="notes" class="font-weight-bold text-dark">Notes / Memo</label>
                        <input type="text" class="form-control text-dark" id="notes" wire:model.defer="notes" placeholder="e.g. Weekly settlement received via bank transfer">
                        @error('notes') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Product / Variant Delivered Items -->
        <div class="card shadow-sm border mb-4 bg-white">
            <div class="card-header bg-light border-bottom p-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 font-weight-bold text-dark">
                    <i class="fa fa-box-open text-primary mr-2"></i> Delivered Products / Variants
                </h6>
                <span class="badge badge-light border text-muted">Optional: Search items to auto-calculate COGS & Revenue</span>
            </div>
            <div class="card-body p-3">
                <div class="form-group position-relative mb-3">
                    <label for="product-search-payout" class="font-weight-bold text-dark">Search Delivered Product or Variant</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                        </div>
                        <input type="text" class="form-control" id="product-search-payout" placeholder="Type product name or SKU to add delivered items..." wire:model.live.debounce.350ms="search" :key="$inputKey" autocomplete="off">
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
                                            Selling Price: <span class="font-weight-bold text-primary">৳{{ number_format((float) ($product->selling_price ?? 0), 2) }}</span> |
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
                                                        Selling: ৳{{ number_format((float) ($variation->selling_price ?? $product->selling_price ?? 0), 2) }} | Cost: ৳{{ number_format((float) ($variation->average_purchase_price ?? $product->average_purchase_price ?? 0), 2) }}
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

                <!-- Itemized Products Table -->
                @if(count($items) > 0)
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Delivered Product / Variant</th>
                                    <th style="width: 120px;" class="text-center">Delivered Qty</th>
                                    <th style="width: 160px;">Unit Cost (৳)</th>
                                    <th style="width: 160px;">Unit Retail (৳)</th>
                                    <th style="width: 150px;" class="text-right">Total Cost (COGS)</th>
                                    <th style="width: 150px;" class="text-right">Total Retail (Sales)</th>
                                    <th style="width: 60px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
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
                                        <td>
                                            <input type="number" class="form-control text-center font-weight-bold" wire:model.lazy="items.{{ $index }}.quantity" min="1" step="1" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" class="form-control font-weight-bold text-dark" wire:model.lazy="items.{{ $index }}.unit_purchase_cost" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" class="form-control font-weight-bold text-dark" wire:model.lazy="items.{{ $index }}.unit_retail_price" required>
                                        </td>
                                        <td class="text-right font-weight-bold text-danger">
                                            ৳{{ number_format((float) ($item['unit_purchase_cost'] ?? 0) * (int) ($item['quantity'] ?? 0), 2) }}
                                        </td>
                                        <td class="text-right font-weight-bold text-success">
                                            ৳{{ number_format((float) ($item['unit_retail_price'] ?? 0) * (int) ($item['quantity'] ?? 0), 2) }}
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm" wire:click="removeItem({{ $index }})">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="4" class="text-right font-weight-bold">Calculated Sums:</th>
                                    <th class="text-right h6 font-weight-bold text-danger mb-0">
                                        ৳{{ number_format($calculatedPurchaseCost, 2) }}
                                    </th>
                                    <th class="text-right h6 font-weight-bold text-success mb-0">
                                        ৳{{ number_format($calculatedTotalSales, 2) }}
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-muted border rounded bg-light mb-3">
                        <i class="fa fa-info-circle mr-1"></i> You can either add delivered products above to auto-sum purchase cost and retail sales, or enter your final settlement amounts directly below.
                    </div>
                @endif

                <!-- Override & Final Settlement Inputs -->
                <div class="row pt-2">
                    <div class="col-md-6">
                        <div class="card border bg-light shadow-none p-3">
                            <label for="override_purchase_cost" class="font-weight-bold text-dark mb-1">
                                Total Purchase Cost (Relieved from In Courier #1006)
                            </label>
                            <small class="text-muted d-block mb-2">
                                Auto-calculated: <strong>৳{{ number_format($calculatedPurchaseCost, 2) }}</strong>. Enter amount below to override if needed.
                            </small>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text py-0 px-2 font-weight-bold">৳</span>
                                </div>
                                <input type="number" step="0.01" min="0" class="form-control font-weight-bold text-dark" id="override_purchase_cost" wire:model.live.debounce.300ms="override_purchase_cost" placeholder="{{ $calculatedPurchaseCost > 0 ? number_format($calculatedPurchaseCost, 2, '.', '') : '0.00' }}">
                            </div>
                            @if($override_purchase_cost !== null && trim((string)$override_purchase_cost) !== '')
                                <small class="text-primary font-weight-bold mt-1 d-block"><i class="fa fa-pencil mr-1"></i> Overridden to: ৳{{ number_format((float)$override_purchase_cost, 2) }}</small>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border bg-light shadow-none p-3">
                            <label for="override_total_sales" class="font-weight-bold text-dark mb-1">
                                Total Retail Sales Revenue (Credited to Sales #4001) <span class="text-danger">*</span>
                            </label>
                            <small class="text-muted d-block mb-2">
                                Auto-calculated: <strong>৳{{ number_format($calculatedTotalSales, 2) }}</strong>. Enter amount below to override or specify lump-sum COD.
                            </small>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text py-0 px-2 font-weight-bold text-success">৳</span>
                                </div>
                                <input type="number" step="0.01" min="0" class="form-control font-weight-bold text-success" id="override_total_sales" wire:model.live.debounce.300ms="override_total_sales" placeholder="{{ $calculatedTotalSales > 0 ? number_format($calculatedTotalSales, 2, '.', '') : 'e.g. 50000.00' }}">
                            </div>
                            @if($override_total_sales !== null && trim((string)$override_total_sales) !== '')
                                <small class="text-primary font-weight-bold mt-1 d-block"><i class="fa fa-pencil mr-1"></i> Overridden to: ৳{{ number_format((float)$override_total_sales, 2) }}</small>
                            @endif
                            @error('override_total_sales') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary & Submission Card -->
        <div class="card shadow-sm border mb-4 bg-white">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-7 mb-3 mb-md-0">
                        <h6 class="font-weight-bold text-dark mb-2">Double-Entry Settlement Summary</h6>
                        <ul class="list-unstyled mb-0 small text-secondary">
                            <li><i class="fa fa-arrow-down text-success mr-1"></i> <strong>Net Deposited into Bank/Cash:</strong> <span class="text-success font-weight-bold">৳{{ number_format($netReceivedAmount, 2) }}</span> (Gross Sales ৳{{ number_format($finalGrossSales, 2) }} - Courier Charge ৳{{ number_format($courierChargeAmount, 2) }})</li>
                            <li><i class="fa fa-tag text-info mr-1"></i> <strong>Courier Fee Expense:</strong> <span class="text-danger font-weight-bold">৳{{ number_format($courierChargeAmount, 2) }}</span></li>
                            <li><i class="fa fa-plus-circle text-primary mr-1"></i> <strong>Gross Sales Revenue:</strong> <span class="text-primary font-weight-bold">৳{{ number_format($finalGrossSales, 2) }}</span></li>
                            <li><i class="fa fa-exchange text-warning mr-1"></i> <strong>In Courier (#1006) Relieved to COGS (#5001):</strong> <span class="text-danger font-weight-bold">৳{{ number_format($finalPurchaseCost, 2) }}</span></li>
                        </ul>
                    </div>
                    <div class="col-md-5 text-md-right">
                        <a href="{{ route('admin.accounting.transactions.index') }}" class="btn btn-secondary mr-2">Cancel</a>
                        <button type="submit" class="btn btn-success px-4 font-weight-bold" @if($finalGrossSales <= 0 && $finalPurchaseCost <= 0) disabled @endif>
                            <i class="fa fa-check mr-1"></i> Confirm & Post Settlement
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
