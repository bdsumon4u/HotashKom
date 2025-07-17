<div>
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form wire:submit.prevent="save">
        <div class="form-group position-relative">
            <label for="product">Add Product/Variant <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="product-search" placeholder="Search product by name or SKU..." wire:model.live.debounce.350ms="search" :key="$inputKey" autocomplete="off">
            @if(strlen($search) > 2 && count($products) > 0)
                <div class="bg-white border position-absolute w-100" style="z-index: 10; max-height: 300px; overflow-y: auto;">
                    @foreach($products as $product)
                        <div class="dropdown-item" wire:click="selectProduct({{ $product->id }})">
                            <strong>{{ $product->name }}</strong> <span class="text-muted">({{ $product->sku }})</span>
                            @if($product->brand)
                                <span class="badge badge-light">{{ $product->brand->name }}</span>
                            @endif
                        </div>
                        @if($product->variations->isNotEmpty())
                            <div class="pl-3">
                                @foreach($product->variations as $variation)
                                    <div class="dropdown-item small" wire:click.stop="selectVariant({{ $variation->id }})">
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
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th></th>
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
                            <td>{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                            <td><button type="button" class="btn btn-danger btn-sm" wire:click="removeItem({{ $index }})">Remove</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">No products added.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-right">Total</th>
                        <th colspan="2">{{ number_format($total, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="purchase_date">Purchase Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="purchase_date" wire:model.defer="purchase_date">
                @error('purchase_date') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="invoice_number">Invoice Number</label>
                <input type="text" class="form-control" id="invoice_number" wire:model.defer="invoice_number">
                @error('invoice_number') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="supplier_name">Supplier Name</label>
                <input type="text" class="form-control" id="supplier_name" wire:model.defer="supplier_name">
                @error('supplier_name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="supplier_phone">Supplier Phone</label>
                <input type="text" class="form-control" id="supplier_phone" wire:model.defer="supplier_phone">
                @error('supplier_phone') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea class="form-control" id="notes" rows="2" wire:model.defer="notes"></textarea>
            @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="btn btn-primary">Save Purchase</button>
    </form>
</div>
