<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
        <thead>
            <tr>
                <th style="min-width: 50px;">SI</th>
                <th style="min-width: 120px;">Name</th>
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
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $product['name'] }}</td>
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
            <th colspan="2" class="text-right">Total</th>
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
