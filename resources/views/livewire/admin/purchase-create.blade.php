<div>
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form wire:submit.prevent="save">
        <div class="form-group position-relative">
            <label for="product">Add Product/Variant <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="product-search" placeholder="Search product by name or SKU..." wire:model.live.debounce.350ms="search" :key="$inputKey" autocomplete="off">
            @if(strlen($search) > 2 && count($products) > 0)
                <div class="bg-white border position-absolute w-100 shadow-lg" style="z-index: 10; max-height: 300px; overflow-y: auto;">
                    @foreach($products as $product)
                        <div class="dropdown-item cursor-pointer" wire:click="selectProduct({{ $product->id }})">
                            <strong>{{ $product->name }}</strong> <span class="text-muted">({{ $product->sku }})</span>
                            @if($product->brand)
                                <span class="badge badge-light">{{ $product->brand->name }}</span>
                            @endif
                        </div>
                        @if($product->variations->isNotEmpty())
                            <div class="pl-3">
                                @foreach($product->variations as $variation)
                                    <div class="dropdown-item small cursor-pointer" wire:click.stop="selectVariant({{ $variation->id }})">
                                        <span>{{ $product->name }} [{{ $variation->name }}]</span>
                                        <span class="text-muted">({{ $variation->sku }})</span>
                                        @foreach($variation->options as $option)
                                            <span class="badge badge-info">{{ $option->name }}</span>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
        @error('items') <span class="text-danger">{{ $message }}</span> @enderror
        <div class="mb-3 table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Options</th>
                        <th style="width: 140px;">Purchase Price</th>
                        <th style="width: 120px;">Quantity</th>
                        <th>Subtotal</th>
                        <th style="width: 60px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                        <tr>
                            <td>
                                {{ $item['name'] }}
                                @if(isset($item['selling_price']))
                                    <span class="text-muted">({{ number_format($item['selling_price'], 2) }} BDT)</span>
                                @endif
                                @if(isset($item['stock_count']))
                                    <span class="text-muted">[Stock: {{ $item['stock_count'] }}]</span>
                                @endif
                            </td>
                            <td>{{ $item['sku'] }}</td>
                            <td>
                                @foreach($item['options'] as $opt)
                                    <span class="badge badge-info">{{ $opt }}</span>
                                @endforeach
                            </td>
                            <td>
                                <input type="number" step="0.01" class="form-control" wire:model.lazy="items.{{ $index }}.price" min="0.01">
                                @error('items.'.$index.'.price') <span class="text-danger">{{ $message }}</span> @enderror
                            </td>
                            <td>
                                <input type="number" class="form-control" wire:model.lazy="items.{{ $index }}.quantity" min="1">
                                @error('items.'.$index.'.quantity') <span class="text-danger">{{ $message }}</span> @enderror
                            </td>
                            <td>{{ number_format((float) ($item['price'] ?? 0) * (float) ($item['quantity'] ?? 0), 2) }}</td>
                            <td><button type="button" class="btn btn-danger btn-sm" wire:click="removeItem({{ $index }})"><i class="fa fa-trash"></i></button></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No products added. Search and add products above.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-right">Total Amount:</th>
                        <th colspan="2" class="h5 font-weight-bold text-primary">{{ number_format($total, 2) }} BDT</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="row">
            <!-- Supplier Details -->
            <div class="col-md-6">
                <div class="card shadow-sm border mb-3" style="background: #ffffff;">
                    <div class="card-header p-3 bg-light border-bottom">
                        <h6 class="mb-0 font-weight-bold text-dark"><i class="fa fa-user text-primary mr-2"></i> Supplier Information</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="form-group">
                            <label for="supplier_id" class="font-weight-bold text-dark">Select Existing Supplier</label>
                            <select class="form-control text-dark font-weight-bold" id="supplier_id" wire:model.live="supplier_id">
                                <option value="">-- Choose Supplier (or enter new below) --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }} @if($sup->company_name) ({{ $sup->company_name }}) @endif - Due: {{ number_format($sup->current_due, 2) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="supplier_name" class="font-weight-bold text-dark">Supplier Name</label>
                                <input type="text" class="form-control text-dark" id="supplier_name" wire:model.defer="supplier_name" placeholder="Name">
                                @error('supplier_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="supplier_phone" class="font-weight-bold text-dark">Supplier Phone</label>
                                <input type="text" class="form-control text-dark" id="supplier_phone" wire:model.defer="supplier_phone" placeholder="Phone">
                                @error('supplier_phone') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment & Accounting Details -->
            <div class="col-md-6">
                <div class="card shadow-sm border mb-3" style="background: #ffffff;">
                    <div class="card-header p-3 bg-light border-bottom">
                        <h6 class="mb-0 font-weight-bold text-dark"><i class="fa fa-credit-card text-success mr-2"></i> Payment & Accounting</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="paid_amount">Paid Amount (BDT)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="paid_amount" wire:model.live="paid_amount">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" wire:click="payFull">Full</button>
                                    </div>
                                </div>
                                @error('paid_amount') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="payment_account_id">Payment Account</label>
                                <select class="form-control" id="payment_account_id" wire:model.defer="payment_account_id">
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }} ({{ number_format($acc->current_balance, 2) }})</option>
                                    @endforeach
                                </select>
                                @error('payment_account_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="p-2 mb-2 bg-light rounded d-flex justify-content-between">
                            <span class="text-dark">Remaining Due:</span>
                            <strong class="{{ $due > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($due, 2) }} BDT</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-row mt-3">
            <div class="form-group col-md-6">
                <label for="purchase_date">Purchase Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="purchase_date" wire:model.defer="purchase_date">
                @error('purchase_date') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="invoice_number">Invoice / Bill Number</label>
                <input type="text" class="form-control" id="invoice_number" wire:model.defer="invoice_number" placeholder="Optional Invoice #">
                @error('invoice_number') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="form-group">
            <label for="notes">Notes / Remarks</label>
            <textarea class="form-control" id="notes" rows="2" wire:model.defer="notes" placeholder="Additional notes regarding this purchase"></textarea>
            @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.purchases.index') }}" class="btn btn-light mr-2">Cancel</a>
            <button type="submit" class="btn btn-primary px-4"><i class="fa fa-save mr-1"></i> Save Purchase</button>
        </div>
    </form>
</div>
