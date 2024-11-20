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
    .page-body {
        font-size: 20px;
        margin-top: 0 !important;
        margin-left: 0 !important;
    }
    .invoice {
        page-break-after: avoid;
        padding-top: 3rem;
        height: 33%;
    }
    .page-break {
        page-break-after: always;
    }
    .bt {
        border-top: 2px dashed #000;
    }
    .page-body p {
        font-size: 16px !important;
    }
}
</style>
  </head>
  <body class="light-only" main-theme-layout="ltr">
    @foreach ($orders as $order)
        <div class="invoice {{ ['page-break bt', 'pb-2', 'bt pb-2'][$loop->iteration % 3] }}">
            <div>
                <div>
                    <div class="row">
                        <div class="col-5">
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
                        <div class="col-3">
                            <div class="text-md-right">
                                <h3>Invoice #<span class="digits counter">{{ $order->id }}</span></h3>
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
                <hr>
                <!-- End InvoiceTop-->
                <div class="row">
                    <div class="col-7">
                        <div class="media">
                            <div class="media-body m-l-20">
                                <h6 class="mb-0">Customer Information:</h6>
                                <div class="media-heading">Name: {{ $order->name }}</div>
                                <div>Phone: {{ $order->phone }}</div>
                                <div>Address: {{ $order->address }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <span class="text-danger">{{$order->note ?? null}}</span>
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
                            @foreach($order->products as $product)
                                <tr>
                                    @unless (setting('show_option')->hide_invoice_image ?? false)
                                    <td>
                                        <img src="{{ asset($product->image) }}" alt="Image" width="70" height="60">
                                    </td>
                                    @endunless
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->price }}</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td>{{ $product->quantity * $product->price }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <th class="py-1" rowspan="5" colspan="{{(setting('show_option')->hide_invoice_image ?? false)?2:3}}" style="text-align: center; vertical-align: middle; font-size: 24px;">
                                    <span style="font-weight: 400;">Condition</span>: TK. {{ $order->condition }}
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
                <!-- End InvoiceBot-->
            </div>
        </div>
    @endforeach
    @include('layouts.light.js')
    <script>
        window.onload = function () {
            window.print();
        };
        window.onfocus = function() {
            window.close();
        };
        window.onafterprint = function() {
            if (confirm('Update status to INVOCED?')) {
                $.post({
                    url: '{{ route('admin.orders.status') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order_id: {!! json_encode(explode(',', request('order_id'))) !!},
                        status: 'INVOICED',
                    },
                    success: function (response) {
                        window.location.href = '{{ route('admin.orders.index', ['status' => 'INVOICED']) }}';
                    },
                    complete: function () {
                        
                    }
                });
            } else {
                window.close();
            }
        };
    </script>
  </body>
</html>
