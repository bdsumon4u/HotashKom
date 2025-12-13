<div>
    <div class="shadow-sm card rounded-0">
        <div class="p-3 card-header">
            <h5 class="card-title mb-0">Other Orders</h5>
        </div>
        <div class="p-3 card-body">
            @if ($otherOrders->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Products</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Courier</th>
                                <th>Staff</th>
                                <th>Date</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($otherOrders as $order)
                            <tr>
                                <td>
                                    <a class="px-2 btn btn-light btn-sm text-nowrap" target="_blank" href="{{ route('admin.orders.edit', $order) }}">{{ $order->id }} <i class="fa fa-eye"></i></a>
                                </td>
                                <td>
                                    @foreach ($order->products as $product)
                                        <div>{{ $product->quantity }} x {{ $product->name }}</div>
                                    @endforeach
                                </td>
                                <td>{{ intval($order->data['subtotal']) + intval($order->data['shipping_cost']) - intval($order->data['advanced'] ?? 0) - intval($order->data['discount'] ?? 0) }}</td>
                                <td>{{ $order->status }}</td>
                                <td>{{ $order->courier }}</td>
                                <td>{{ $order->admin->name }}</td>
                                <td>{{ $order->created_at->format('d-M-Y') }}</td>
                                <td>{{ $order->note }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-4 text-center text-muted">No other orders found.</div>
            @endif
        </div>
    </div>
</div>

