<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
        <thead>
            <tr>
                <th style="min-width: 50px;">SI</th>
                <th style="min-width: 120px;">Name</th>
                <th style="min-width: 100px;">Orders</th>
                <th style="min-width: 100px;">Quantity</th>
                <th style="min-width: 100px;">Price</th>
            </tr>
        </thead>
        @php $total = 0; $amount = 0; $orders = 0; @endphp
        <tbody>
            @foreach ($products as $id => $product)
                @php $total += $product['quantity']; $amount += $product['total']; @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $product['name'] }}</td>
                    <td>{{ count($productInOrders[$product['name']] ?? []) }}</td>
                    <td>{{ $product['quantity'] }}</td>
                    <td>{!!theMoney($product['total'])!!}</td>
                </tr>
                @php $orders += count($productInOrders[$product['name']] ?? []); @endphp
            @endforeach
        </tbody>
        <tfoot>
            <th colspan="2" class="text-right">Total</th>
            <th>{{ $orders }}</th>
            <th>{{ $total }}</th>
            <th>{!!theMoney($amount)!!}</th>
        </tfoot>
    </table>
</div>