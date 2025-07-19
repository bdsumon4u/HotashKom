<div class="{{ $class ?? '' }}">
    <div>
        <div class="row">
            <div class="col-6">
                <div class="media">
                    <div class="media-left">
                        @if($logoUrl)
                            <img class="media-object" src="{{ $logoUrl }}" alt="{{ $companyName }}" style="max-width: 100%; max-height: 54px;">
                        @endif
                    </div>
                    <div class="media-body m-l-20">
                        <h4 class="media-heading">{{ $companyName }}</h4>
                        <p class="m-0"><span class="digits">{{ $phoneNumber }}</span></p>
                        <p class="m-0">{{ $address }}</p>
                    </div>
                </div>
                <!-- End Info-->
            </div>
            <div class="col-3">
                <div class="text-md-right">
                    <h3 class="mb-0">Invoice #<span class="digits counter">{{ $order->id }}</span></h3>
                    @if(isOninda() && $order->source_id)
                        <strong>Source ID: #{{ $order->user->order_prefix.$order->source_id }}</strong><br>
                    @endif
                    <p>
                        Ordered At: {{ $order->created_at->format('M') }}<span class="digits"> {{ $order->created_at->format('d, Y') }}</span>
                    </p>
                </div>
                <!-- End Title-->
            </div>
            <div class="col-3">
                <div class="text-md-right" id="project">
                    <img height="80" src="https://barcode.tec-it.com/barcode.ashx?data={{$order->barcode}}&code=Code128&translate-esc=true" alt="Barcode">
                </div>
            </div>
        </div>
    </div>
    <hr>
    <!-- End InvoiceTop-->
    <div class="row">
        <div class="col-6">
            <div class="media">
                <div class="media-body m-l-20">
                    <h6 class="mb-0">Customer Information:</h6>
                    <div class="media-heading">Name: {{ $order->name }}</div>
                    <div>Phone: {{ $order->phone }}</div>
                    <div>Address: {{ $order->address }}</div>
                </div>
            </div>
        </div>
        @if(isOninda() && !(setting('show_option')->resellers_invoice ?? false))
        <div class="col-6">
            <div class="media">
                <div class="media-body m-l-20">
                    <h6 class="mb-0">Sender's Information:</h6>
                    <div class="media-heading">Name: {{ $senderName }}</div>
                    <div>Phone: {{ $senderPhone }}</div>
                    <div>Address: {{ $senderAddress }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @if(isOninda() && (setting('show_option')->resellers_invoice ?? false))
    <div class="row">
        <div class="col-12">
            <span class="text-danger">{{$order->note ?? null}}</span>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <span class="text-danger">{{$order->note ?? null}}</span>
        </div>
    </div>
    @endif
    <!-- End Invoice Mid-->
    <div>
        <div class="table-responsive invoice-table" id="table">
            <table class="table table-sm table-bordered table-striped">
                <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th width="95">Price</th>
                    <th width="10">Quantity</th>
                    <th width="95">Total</th>
                </tr>
                </thead>
                <tbody>
                @php($retail = 0)
                @foreach($order->products as $product)
                    <tr>
                        <td>
                            <img src="{{ asset($product->image) }}" alt="Image" width="70" height="60">
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ isOninda() ? $product->retail_price : $product->price }}</td>
                        <td>{{ $product->quantity }}</td>
                        <td>{{ $amount = $product->quantity * (isOninda() ? $product->retail_price : $product->price) }}</td>
                    </tr>
                @php($retail += $amount)
                @endforeach
                <tr>
                    <th class="py-1" rowspan="5" colspan="3" style="text-align: center; vertical-align: middle; font-size: 24px;">
                        <span style="font-weight: 400;">Condition</span>: TK. {{ $retail + (isOninda() ? $order->data['retail_delivery_fee'] : $order->data['shipping_cost']) - (isOninda() ? ($order->data['retail_discount'] ?? 0) : ($order->data['discount'] ?? 0)) - ($order->data['advanced'] ?? 0) }}
                    </th>
                </tr>
                <tr>
                    <th class="py-1">Subtotal</th>
                    <th class="py-1">{{ $retail }}</th>
                </tr>
                <tr>
                    <th class="py-1">Advanced</th>
                    <th class="py-1">{{ $order->data['advanced'] ?? 0 }}</th>
                </tr>
                <tr>
                    <th class="py-1">Delivery</th>
                    <th class="py-1">{{ isOninda() ? $order->data['retail_delivery_fee'] : $order->data['shipping_cost'] }}</th>
                </tr>
                <tr>
                    <th class="py-1">Discount</th>
                    <th class="py-1">{{ isOninda() ? ($order->data['retail_discount'] ?? 0) : ($order->data['discount'] ?? 0) }}</th>
                </tr>
                </tbody>
            </table>
        </div>
        <!-- End Table-->
    </div>
</div>
