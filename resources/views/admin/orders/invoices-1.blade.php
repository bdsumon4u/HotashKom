<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" href="{{asset($logo->favicon ?? '')}}" type="image/x-icon">
    <link rel="shortcut icon" href="{{asset($logo->favicon ?? '')}}" type="image/x-icon">
    <title>{{ $company->name ?? '' }} - @yield('title')</title>
    @include('layouts.light.css')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/print.css')}}">
    <style>
    .invoice th,
    .invoice td {
        padding: 0.25rem;
    }
@media print {
    html, body {
        /* height:100vh; */
        margin: 0 !important;
        padding: 0 !important;
        /* overflow: hidden; */
    }
    .main-nav {
        display: none !important;
        width: 0 !important;
    }
    .print-edit-buttons,
    .footer {
        display: none !important;
    }
    script,
    .light-only > script,
    body > script {
        display: none !important;
        height: 0 !important;
        width: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        visibility: hidden !important;
    }
    .page-body {
        font-size: 20px;
        margin-top: 0 !important;
        margin-left: 0 !important;
    }
    .invoice {
        padding-top: 3rem;
        height: 100vh;
        page-break-after: avoid;
        page-break-inside: avoid;
        font-size: 20px !important;
    }
    .invoice h1,
    .invoice h2,
    .invoice h3,
    .invoice h4,
    .invoice h5,
    .invoice h6 {
        font-size: 22px !important;
    }
    .invoice.page-break {
        page-break-after: always !important;
    }
    .invoice:last-child {
        page-break-after: avoid !important;
    }
    body > *:last-child {
        page-break-after: avoid !important;
    }
    body > script,
    body > *:not(.invoice) {
        page-break-after: avoid !important;
        page-break-before: avoid !important;
    }
    .page-body p {
        font-size: 20px !important;
    }
    .customer-info {
        font-size: 20px !important;
        border: 2px dashed #000;
        padding: 0.75rem;
        border-radius: 4px;
    }
    .customer-info h6 {
        font-size: 22px !important;
        font-weight: bold;
    }
    .customer-info div {
        font-size: 20px !important;
    }
    .company-info {
        font-size: 16px !important;
    }
    .company-info h6 {
        font-size: 18px !important;
        font-weight: bold;
    }
    .company-info div {
        font-size: 16px !important;
    }
    .invoice-note {
        display: inline-block;
        font-size: 18px;
        font-weight: 600;
        border: 1px dashed currentColor;
        padding: 0.35rem 0.5rem;
        margin-top: 0.25rem;
    }
}
</style>
  </head>
  <body class="light-only" main-theme-layout="ltr">
    @foreach ($orders as $order)
        @php require resource_path('views/admin/orders/reseller-info.php') @endphp
        <div class="invoice {{ !$loop->last ? 'page-break' : '' }}">
            <div>
                <div>
                    <div class="row">
                        <div class="col-4">
                            @if($logoUrl)
                                <img class="media-object" src="{{ $logoUrl }}" alt="{{ $companyName }}" style="max-width: 100%; max-height: 54px;">
                            @endif
                        </div>
                        <div class="col-4">
                            <div class="text-md-center">
                                <h3 class="mb-0">Invoice #<span class="digits counter">{{ $order->id }}</span></h3>
                                @if(isOninda() && $order->source_id)
                                    <strong>Source ID: #{{ $order->user->order_prefix.$order->source_id }}</strong><br>
                                @endif
                                <p>
                                    Ordered At: {{ $order->created_at->format('M') }}<span class="digits"> {{ $order->created_at->format('d, Y') }}</span>
                                    {{--                                            <br> Invoiced At: {{ date('M') }}<span class="digits"> {{ date('d, Y') }}</span>--}}
                                </p>
                            </div>
                            <!-- End Title-->
                        </div>
                        <div class="col-4">
                            <div class="text-md-right" id="project">
                                <img height="80" src="https://barcode.tec-it.com/barcode.ashx?data={{$order->barcode}}&code=Code128&translate-esc=true" alt="Barcode">
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-2">
                <!-- End InvoiceTop-->
                <div class="row">
                    <div class="col-6">
                        <div class="media customer-info">
                            <div class="media-body m-l-20">
                                <h6 class="mb-0">Customer Information:</h6>
                                <div class="media-heading">Name: {{ $order->name }}</div>
                                <div>Phone: {{ without88($order->phone) }}</div>
                                <div>Address: {{ $order->address }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="media company-info">
                            <div class="media-body">
                                <h6 class="mb-0">Company Information:</h6>
                                <div class="media-heading">{{ $companyName }}</div>
                                <div>Phone: <span class="digits">{{ without88($phoneNumber) }}</span></div>
                                <div>Address: {{ $address }}</div>
                            </div>
                        </div>
                        @if(isOninda() && !(setting('show_option')->resellers_invoice ?? false))
                        <div class="mt-3 media company-info">
                            <div class="media-body">
                                <h6 class="mb-0">Sender's Information:</h6>
                                <div class="media-heading">Name: {{ $senderName }}</div>
                                <div>Phone: {{ without88($senderPhone) }}</div>
                                <div>Address: {{ $senderAddress }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <!-- End Invoice Mid-->
                <div>
                    <div class="table-responsive invoice-table" id="table">
                        <table class="table table-sm table-bordered table-striped">
                            <thead>
                            <tr>
                                @unless (setting('show_option')->hide_invoice_image ?? false)
                                <th>Image</th>
                                @endunless
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
                                    @unless (setting('show_option')->hide_invoice_image ?? false)
                                    <td>
                                        <img src="{{ asset($product->image) }}" alt="Image" width="70" height="60">
                                    </td>
                                    @endunless
                                    <td>{{ $product->name }}</td>
                                    <td>{{ (isOninda() && config('app.resell')) ? ($product->retail_price ?? $product->price) : $product->price }}</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td>{{ $amount = $product->quantity * ((isOninda() && config('app.resell')) ? ($product->retail_price ?? $product->price) : $product->price) }}</td>
                                </tr>
                            @php($retail += $amount)
                            @endforeach
                            <tr>
                                <th class="py-1" rowspan="5" colspan="{{(setting('show_option')->hide_invoice_image ?? false)?2:3}}" style="text-align: center; vertical-align: middle; font-size: 24px;">
                                    <span style="font-weight: 400;">Condition</span>: TK. {{ $order->condition }}
                                    @if($order->note)
                                        <br><span class="text-danger invoice-note">{{ $order->note }}</span>
                                    @endif
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
                                <th class="py-1">{{ (isOninda() && config('app.resell')) ? ($order->data['retail_delivery_fee'] ?? $order->data['shipping_cost']) : $order->data['shipping_cost'] }}</th>
                            </tr>
                            <tr>
                                <th class="py-1">Discount</th>
                                <th class="py-1">{{ (isOninda() && config('app.resell')) ? ($order->data['retail_discount'] ?? 0) : ($order->data['discount'] ?? 0) }}</th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- End Table-->
                </div>
                <!-- End InvoiceBot-->
            </div>
        </div>
    @endforeach
    @include('layouts.light.js')
    <script>
        window.onload = function () {
            window.print();
        };
        window.onafterprint = function() {
            if (confirm('Update status to PACKAGING?')) {
                $.ajax({
                    url: '{{ route('admin.orders.status') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order_id: {!! json_encode(explode(',', request('order_id'))) !!},
                        status: 'PACKAGING',
                    },
                    success: function (response) {
                        console.log('Status updated successfully');
                        window.location.href = '{{ route('admin.orders.index', ['status' => 'PACKAGING']) }}';
                    },
                    error: function (xhr, status, error) {
                        console.error('Error updating status:', error);
                        alert('Failed to update status. Please try again.');
                        window.close();
                    }
                });
            } else {
                window.close();
            }
        };
    </script>
  </body>
</html>


