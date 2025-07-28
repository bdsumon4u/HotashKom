<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stickers</title>
    <style>
        @font-face {
            font-family: 'Nikosh';
            font-weight: normal;
            font-style: normal;
            font-variant: normal;
            src: url("fonts/Nikosh.ttf") format('truetype');
        }

        @page { size: 10cm 6.2cm; margin: 0.25cm; }
        body { font-family: 'Nikosh', serif; font-size: 9.75px; margin: 0; padding: 0; }
        .header img { max-height: 40px; }
        .title { font-weight: bold; }
        table { border-collapse: collapse; }
        p, th, td { margin: 0; margin-bottom: -1.25px; }
        .products th, .products td, .summary th, .summary td { border: 0.25px solid black; padding: 0.25px 2px; text-align: center; }
    </style>
</head>
<body>
    @foreach ($orders as $order)
    @php require resource_path('views/admin/orders/reseller-info.php') @endphp
    <div class="invoice-container" @unless($loop->last) style="page-break-after:always;" @endunless>
        <div class="header">
            <table width="100%">
                <tr>
                    <td align="left">
                        @if($logoUrl)
                            @if(str_starts_with($logoUrl, 'http'))
                                {{-- External URL (reseller's domain) - convert to data URI for PDF --}}
                                <img src="data:image/jpeg;base64, {{base64_encode(file_get_contents($logoUrl))}}" alt="Logo">
                            @else
                                {{-- Local URL - convert to public path for PDF --}}
                                <img src="{{ public_path(str_replace(asset(''), '', $logoUrl)) }}" alt="Logo">
                            @endif
                        @else
                            <img src="{{ public_path($logo->mobile) }}" alt="Logo">
                        @endif
                    </td>
                    <td align="center">
                        <p><small>{{ $order->created_at->format('M d, Y') }}</small></p>
                    </td>
                    <td align="right">
                        <img style="width: 150px;" src="data:image/jpeg;base64, {{base64_encode(file_get_contents('https://barcode.tec-it.com/barcode.ashx?data='.$order->barcode.'&code=Code128'))}}" alt="Barcode">
                    </td>
                </tr>
            </table>
            <table width="100%">
                <tr>
                    <td align="left">
                        <p class="title">{{ $companyName }}</p>
                        <p>{{ $phoneNumber }}</p>
                        <p>{{ $address }}</p>
                    </td>
                    <td align="right">
                        <p>{{ $order->name }}</p>
                        <p>{{ $order->phone }}</p>
                        <p>{{ $order->address }}</p>
                    </td>
                </tr>
            </table>
        </div>
        <table class="products" width="100%" style="margin-top: 2px;">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->products as $product)
                <tr>
                    <td>
                        @php
                            $path = str($product->image)->after('storage/')->prepend('app/public/');
                        @endphp
                        <div style="clear: both;">
                            <img style="height: 36px; width: 36px; float: left; margin: 0;" src="data:image/jpeg;base64, {{base64_encode(file_get_contents(storage_path($path)))}}" alt="Image">
                            <div style="min-height: 36px;">{{ $product->name }}</div>
                        </div>
                    </td>
                    <td>{{ $product->quantity }}</td>
                    <td>{{ (isOninda() && config('app.resell')) ? ($product->retail_price ?? $product->price) : $product->price }}</td>
                    <td>{{ $product->quantity * ((isOninda() && config('app.resell')) ? ($product->retail_price ?? $product->price) : $product->price) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table class="summary" width="50%;" style="float: right; margin-top: 2px;">
            <tbody>
                <tr>
                    <td colspan="3"><strong>Subtotal</strong></td>
                    <td>{{ $order->data['subtotal'] }}</td>
                </tr>
                @if($advanced = $order->data['advanced'] ?? 0)
                <tr>
                    <td colspan="3"><strong>Advanced</strong></td>
                    <td>{{ $advanced }}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="3"><strong>Delivery</strong></td>
                    <td>{{ (isOninda() && config('app.resell')) ? ($order->data['retail_delivery_fee'] ?? $order->data['shipping_cost']) : $order->data['shipping_cost'] }}</td>
                </tr>
                @if($discount = ((isOninda() && config('app.resell')) ? ($order->data['retail_discount'] ?? 0) : ($order->data['discount'] ?? 0)))
                <tr>
                    <td colspan="3"><strong>Discount</strong></td>
                    <td>{{ $discount }}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="3"><strong>Condition</strong></td>
                    <td><strong>{{ $order->condition }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endforeach
</body>
</html>
