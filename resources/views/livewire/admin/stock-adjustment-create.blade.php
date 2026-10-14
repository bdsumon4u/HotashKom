<div>
    @if (session()->has('success'))
        <div class="alert alert-success shadow-sm mb-4">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="alert alert-warning border-left-warning shadow-sm mb-4" style="border-left: 4px solid #f39c12; background-color: #fffdf5;">
        <div class="d-flex align-items-center">
            <i class="fa fa-exclamation-triangle fa-2x mr-3 text-warning"></i>
            <div>
                <strong class="d-block text-dark font-weight-bold">Stock Adjustment & Write-Off (স্টক সমন্বয় ও পণ্য অপচয়/গিফট হিসাব)</strong>
                <span class="text-secondary small">
                    Submitting this form will automatically <strong>reduce warehouse product stock (stock_count)</strong> and transfer the purchase cost out of <strong>Inventory (#1004)</strong> [Cr -] into the selected <strong>Expense or Owner's Drawings account</strong> [Dr +].
                    (এই ফর্ম সাবমিট করলে গুদামের স্টক_কাউন্ট হ্রাস পাবে এবং পণ্যের ক্রয়মূল্য ইনভেন্টরি অ্যাকাউন্ট থেকে নির্বাচিত খরচ বা উত্তোলন অ্যাকাউন্টে ডেবিট হবে।)
                </span>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save">
        <!-- Adjustment Meta Details -->
        <div class="card shadow-sm border mb-4 bg-white">
            <div class="card-header bg-light border-bottom p-3 d-flex align-items-center justify-content-between flex-wrap" style="gap: 12px;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 14px;">
                    <h6 class="mb-0 font-weight-bold text-dark d-flex align-items-center">
                        <i class="fa fa-sliders text-warning mr-2"></i> Adjustment Reason & Target Account
                    </h6>
                    <div class="d-flex align-items-center">
                        <div class="input-group input-group-sm" style="width: 175px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0"><i class="fa fa-calendar text-muted"></i></span>
                            </div>
                            <input type="date" class="form-control form-control-sm font-weight-bold text-dark border-left-0 pl-1" id="entry_date" wire:model.defer="entry_date" required>
                        </div>
                        @error('entry_date') <span class="text-danger small ml-2">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="form-group mb-3">
                    <label class="font-weight-bold text-dark d-block">Adjustment Reason (কারণ) <span class="text-danger">*</span></label>
                    <div class="d-flex flex-wrap" style="gap: 10px;">
                        <label class="btn btn-outline-danger btn-sm mb-0 {{ $reason === 'damage' ? 'active font-weight-bold shadow-sm' : '' }}">
                            <input type="radio" wire:model.live="reason" value="damage" class="d-none">
                            <i class="fa fa-trash mr-1"></i> Damaged / Broken / Expired (নষ্ট পণ্য)
                        </label>
                        <label class="btn btn-outline-primary btn-sm mb-0 {{ $reason === 'gift' ? 'active font-weight-bold shadow-sm' : '' }}">
                            <input type="radio" wire:model.live="reason" value="gift" class="d-none">
                            <i class="fa fa-gift mr-1"></i> Promotional Gift / Sample
                        </label>
                        <label class="btn btn-outline-dark btn-sm mb-0 {{ $reason === 'drawing' ? 'active font-weight-bold shadow-sm' : '' }}">
                            <input type="radio" wire:model.live="reason" value="drawing" class="d-none">
                            <i class="fa fa-user mr-1"></i> Owner's Personal Use
                        </label>
                        <label class="btn btn-outline-secondary btn-sm mb-0 {{ $reason === 'other' ? 'active font-weight-bold shadow-sm' : '' }}">
                            <input type="radio" wire:model.live="reason" value="other" class="d-none">
                            <i class="fa fa-cog mr-1"></i> Other
                        </label>
                    </div>
                    @error('reason') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="debit_account_id" class="font-weight-bold text-dark">
                            Debit Account (চার্জ/খরচ অ্যাকাউন্ট) <span class="text-danger">*</span>
                            <span class="badge badge-light-danger text-danger ml-1">Dr (+)</span>
                        </label>
                        <select class="form-control font-weight-bold text-dark" id="debit_account_id" wire:model="debit_account_id" required>
                            <option value="">-- Select Debit Account --</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">
                                    [{{ strtoupper($account->type) }}] {{ $account->name }} (#{{ $account->code }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            Automatically selected based on reason, but you can change it if needed.
                        </small>
                        @error('debit_account_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="notes" class="font-weight-bold text-dark">Notes / Reason Details</label>
                        <input type="text" class="form-control text-dark" id="notes" wire:model.defer="notes" placeholder="e.g. Broken packaging during transport, or gifted to influencer XYZ">
                        @error('notes') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Product / Variant Search & Selection -->
        <div class="card shadow-sm border mb-4 bg-white">
            <div class="card-header bg-light border-bottom p-3">
                <h6 class="mb-0 font-weight-bold text-dark">
                    <i class="fa fa-search text-warning mr-2"></i> Select Products to Adjust / Write Off
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="form-group position-relative mb-3">
                    <label for="product-search" class="font-weight-bold text-dark">Search Product or Variant <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                        </div>
                        <input
                            type="text"
                            class="form-control text-dark"
                            id="product-search"
                            wire:model.live.debounce.350ms="search"
                            placeholder="Type product name, SKU, or variation..."
                            autocomplete="off"
                        >
                    </div>

                    @if (!empty($products))
                        <div class="dropdown-menu show shadow-lg w-100 mt-1 p-0 border" style="max-height: 380px; overflow-y: auto; z-index: 1050; display: block;">
                            <div class="bg-light px-3 py-2 border-bottom font-weight-bold small text-muted">
                                Search Results (Click to add to adjustment list)
                            </div>
                            @foreach ($products as $prod)
                                @if (count($prod['variations'] ?? []) > 0)
                                    <div class="px-3 py-2 bg-light text-dark font-weight-bold border-bottom small">
                                        {{ $prod['name'] }} <span class="badge badge-secondary ml-1">{{ count($prod['variations']) }} Variations</span>
                                    </div>
                                    @foreach ($prod['variations'] as $variant)
                                        <button
                                            type="button"
                                            wire:key="var-{{ $variant['id'] }}-{{ $inputKey }}"
                                            wire:click="selectVariant({{ $variant['id'] }})"
                                            class="dropdown-item d-flex justify-content-between align-items-center py-2 px-4 border-bottom"
                                        >
                                            <div>
                                                <i class="fa fa-angle-right text-muted mr-1"></i>
                                                <strong class="text-dark">{{ $variant['name'] }}</strong>
                                                <span class="text-muted small ml-1">({{ $variant['sku'] ?? 'No SKU' }})</span>
                                            </div>
                                            <div class="text-right">
                                                <span class="badge badge-light-primary text-primary font-weight-bold mr-2">
                                                    Stock: {{ $variant['stock_count'] ?? 0 }}
                                                </span>
                                                <span class="badge badge-light-warning text-dark font-weight-bold">
                                                    Cost: ৳{{ number_format((float) ($variant['average_purchase_price'] ?? $prod['average_purchase_price'] ?? 0), 2) }}
                                                </span>
                                            </div>
                                        </button>
                                    @endforeach
                                @else
                                    <button
                                        type="button"
                                        wire:key="prod-{{ $prod['id'] }}-{{ $inputKey }}"
                                        wire:click="selectProduct({{ $prod['id'] }})"
                                        class="dropdown-item d-flex justify-content-between align-items-center py-2 px-3 border-bottom"
                                    >
                                        <div>
                                            <strong class="text-dark">{{ $prod['name'] }}</strong>
                                            <span class="text-muted small ml-1">({{ $prod['sku'] ?? 'No SKU' }})</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="badge badge-light-primary text-primary font-weight-bold mr-2">
                                                Stock: {{ $prod['stock_count'] ?? 0 }}
                                            </span>
                                            <span class="badge badge-light-warning text-dark font-weight-bold">
                                                Cost: ৳{{ number_format((float) ($prod['average_purchase_price'] ?? 0), 2) }}
                                            </span>
                                        </div>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                @error('items')
                    <div class="alert alert-danger py-2 small mb-3">
                        <i class="fa fa-exclamation-circle mr-1"></i> {{ $message }}
                    </div>
                @enderror

                <!-- Selected Items Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 38%;">Product / Variant</th>
                                <th style="width: 14%;" class="text-center">Current Stock</th>
                                <th style="width: 16%;" class="text-center">Deduct Qty <span class="text-danger">*</span></th>
                                <th style="width: 16%;" class="text-right">Unit Cost (৳) <span class="text-danger">*</span></th>
                                <th style="width: 16%;" class="text-right">Subtotal Cost (৳)</th>
                                <th style="width: 50px;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $index => $item)
                                <tr wire:key="item-row-{{ $item['product_id'] }}">
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-dark">{{ $item['name'] }}</div>
                                        @if (!empty($item['sku']))
                                            <small class="text-muted">SKU: {{ $item['sku'] }}</small>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-light-info font-weight-bold" style="font-size: 13px;">
                                            {{ $item['stock_count'] }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <input
                                            type="number"
                                            min="1"
                                            class="form-control text-center font-weight-bold text-dark"
                                            wire:model.live.debounce.300ms="items.{{ $index }}.quantity"
                                            required
                                        >
                                        @error("items.{$index}.quantity")
                                            <span class="text-danger small d-block text-center">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td class="align-middle">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0.01"
                                            class="form-control text-right font-weight-bold text-dark"
                                            wire:model.live.debounce.300ms="items.{{ $index }}.price"
                                            placeholder="0.00"
                                            required
                                        >
                                        @error("items.{$index}.price")
                                            <span class="text-danger small d-block text-right">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td class="align-middle text-right font-weight-bold text-danger font-roboto" style="font-size: 15px;">
                                        ৳{{ number_format((float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0), 2) }}
                                    </td>
                                    <td class="align-middle text-center">
                                        <button
                                            type="button"
                                            class="btn btn-outline-danger btn-xs"
                                            wire:click="removeItem({{ $index }})"
                                            title="Remove item"
                                        >
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fa fa-boxes fa-2x text-secondary mb-2 d-block"></i>
                                        No products selected yet. Search above to add items to this stock adjustment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary & Confirmation -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border h-100 bg-white">
                    <div class="card-header bg-light border-bottom p-3">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fa fa-balance-scale text-primary mr-2"></i> Double-Entry Impact Preview
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="text-danger font-weight-bold">
                                <i class="fa fa-plus-circle text-danger mr-1"></i> Debit Account [Dr (+)]:
                            </span>
                            <span class="badge badge-light-danger font-weight-bold text-danger" style="font-size: 13px;">
                                @php
                                    $selectedAcc = $accounts->firstWhere('id', (int) $debit_account_id);
                                @endphp
                                {{ $selectedAcc ? $selectedAcc->name : 'Target Expense / Drawing Account' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="text-secondary font-weight-bold">
                                <i class="fa fa-minus-circle text-secondary mr-1"></i> Credit Account [Cr (-)]:
                            </span>
                            <span class="badge badge-light-secondary font-weight-bold text-secondary" style="font-size: 13px;">
                                Inventory / Product Stock (#1004)
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-dark font-weight-bold">
                                <i class="fa fa-archive text-info mr-1"></i> Warehouse Physical Stock:
                            </span>
                            <span class="text-danger font-weight-bold font-roboto">
                                -{{ $totalQuantity }} Units Deducted
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border h-100 bg-white">
                    <div class="card-header bg-light border-bottom p-3">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fa fa-calculator text-success mr-2"></i> Total Adjustment Valuation
                        </h6>
                    </div>
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-dark font-weight-bold">Total Quantity to Write Off:</span>
                                <span class="font-weight-bold text-dark font-roboto" style="font-size: 18px;">
                                    {{ $totalQuantity }} pcs
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3 pt-2 border-top">
                                <span class="text-dark font-weight-bold" style="font-size: 16px;">Total Written-Off Cost:</span>
                                <span class="font-weight-bold text-danger font-roboto" style="font-size: 22px;">
                                    ৳{{ number_format($totalPurchaseCost, 2) }}
                                </span>
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="btn btn-warning btn-block font-weight-bold text-dark py-2 shadow-sm"
                            @if (empty($items) || $totalPurchaseCost <= 0) disabled @endif
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>
                                <i class="fa fa-check-circle mr-1"></i> Confirm & Record Stock Adjustment
                            </span>
                            <span wire:loading>
                                <i class="fa fa-spinner fa-spin mr-1"></i> Processing Adjustment...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
