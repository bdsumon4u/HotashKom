<div class="{{ $class ?? '' }}">
    <div>
        <div class="row">
            <div class="col-6">
                <div class="media">
                    <div class="media-left">
                        <img class="media-object" src="{{asset($logo->mobile)}}" alt="{{$company->name}}" style="max-width: 100%; max-height: 54px;">
                    </div>
                    <div class="media-body m-l-20">
                        <h4 class="media-heading">{{ $company->name }}</h4>
                        <p class="m-0"><span class="digits">{{ $company->phone }}</span></p>
                        <p class="m-0">{{ $company->address }}</p>
                    </div>
                </div>
                <!-- End Info-->
            </div>
            <div class="col-6">
                <div class="text-md-right">
                    <h3>Invoice #<span class="digits counter">{{ $order->id }}</span></h3>
                    <p>
                        Ordered At: {{ $order->created_at->format('M') }}<span class="digits"> {{ $order->created_at->format('d, Y') }}</span>
                        {{--                                            <br> Invoiced At: {{ date('M') }}<span class="digits"> {{ date('d, Y') }}</span>--}}
                    </p>
                </div>
                <!-- End Title-->
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
        <div class="col-6">
            <div class="text-md-right" id="project">
                <img height="80" src="https://barcode.tec-it.com/barcode.ashx?data={{$order->id}}&code=Code128&translate-esc=true" alt="Barcode">
            </div>
        </div>
    </div>
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
                @foreach($order->products as $product)
                    <tr>
                        <td>
                            <img src="{{ asset($product->image) }}" alt="Image" width="70" height="60">
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->price }}</td>
                        <td>{{ $product->quantity }}</td>
                        <td>{{ $product->quantity * $product->price }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th class="py-1" rowspan="5" colspan="3" style="text-align: center; vertical-align: middle; font-size: 24px;">
                        <span style="font-weight: 400;">Condition</span>: TK. {{ intval($order->data['subtotal']) + intval($order->data['shipping_cost']) - intval($order->data['advanced'] ?? 0) - intval($order->data['discount'] ?? 0) }}
                    </th>
                </tr>
                <tr>
                    <th class="py-1">Subtotal</th>
                    <th class="py-1">{{ $order->data['subtotal'] }}</th>
                </tr>
                <tr>
                    <th class="py-1">Advanced</th>
                    <th class="py-1">{{ $order->data['advanced'] ?? 0 }}</th>
                </tr>
                <tr>
                    <th class="py-1">Delivery</th>
                    <th class="py-1">{{ $order->data['shipping_cost'] }}</th>
                </tr>
                <tr>
                    <th class="py-1">Discount</th>
                    <th class="py-1">{{ $order->data['discount'] ?? 0 }}</th>
                </tr>
                </tbody>
            </table>
        </div>
        <!-- End Table-->
    </div>
</div>