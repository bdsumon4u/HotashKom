@if(isOninda() && !config('app.resell'))
    <div class="alert alert-info mb-3">
        <i class="fa fa-info-circle mr-2"></i>
        <strong>Note:</strong> This report displays retail amounts (end customer pricing) as configured for this platform.
    </div>
@endif
<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
        <thead>
            <tr>
                <th style="min-width: 50px;">SI</th>
                <th style="min-width: 80px;">Image</th>
                <th style="min-width: 150px;">Name</th>
                <th style="min-width: 100px;">Orders</th>
                <th style="min-width: 100px;">Quantity</th>
                <th style="min-width: 100px;">Purchase</th>
                <th style="min-width: 100px;">Subtotal</th>
                <th style="min-width: 100px;">Profit</th>
            </tr>
        </thead>
                @php $total = 0; $purchaseAmount = 0; $subtotalAmount = 0; $orders = 0; @endphp
        <tbody>
            @foreach ($products as $name => $product)
                @php
                    $total += $product['quantity'];
                    $purchaseAmount += ($product['purchase_cost'] ?? 0);
                    $subtotalAmount += $product['total'];
                    $profit = $product['total'] - ($product['purchase_cost'] ?? 0);
                    
                    // Find product by slug to get image
                    $productModel = null;
                    if (!empty($product['slug'])) {
                        $productModel = \App\Models\Product::where('slug', $product['slug'])->first();
                    }
                    $imageUrl = $productModel && $productModel->base_image 
                        ? asset($productModel->base_image->src) 
                        : asset('assets/images/product-placeholder.jpg');
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-center">
                        <img src="{{ $imageUrl }}" 
                             alt="{{ $product['name'] }}" 
                             class="img-fluid rounded"
                             style="width: 50px; height: 50px; object-fit: cover;"
                             onerror="this.src='{{ asset('assets/images/product-placeholder.jpg') }}'">
                    </td>
                    <td>
                        @if($productModel)
                            <a href="{{ route('admin.products.edit', $productModel->parent_id ?? $productModel->id) }}" 
                               target="_blank" 
                               class="text-decoration-none">
                                {{ $product['name'] }}
                            </a>
                        @else
                            {{ $product['name'] }}
                        @endif
                    </td>
                    <td>{{ count($productInOrders[$name] ?? []) }}</td>
                    <td>{{ $product['quantity'] }}</td>
                    <td>{!!theMoney($product['purchase_cost'] ?? 0)!!}</td>
                    <td>{!!theMoney($product['total'])!!}</td>
                    <td class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                        {!!theMoney($profit)!!}
                    </td>
                </tr>
                @php $orders += count($productInOrders[$name] ?? []); @endphp
            @endforeach
        </tbody>
        <tfoot>
            <th colspan="3" class="text-right">Total</th>
            <th>{{ $orders }}</th>
            <th>{{ $total }}</th>
            <th>{!!theMoney($purchaseAmount)!!}</th>
            <th>{!!theMoney($subtotalAmount)!!}</th>
            <th class="{{ ($subtotalAmount - $purchaseAmount) >= 0 ? 'text-success' : 'text-danger' }}">
                {!!theMoney($subtotalAmount - $purchaseAmount)!!}
            </th>
        </tfoot>
    </table>
</div>
