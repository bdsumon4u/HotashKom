<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stickers</title>
    <style>
        @page { size: 10cm 6.2cm; margin: 2px; }
        body { font-size: 10px; margin: 0; padding: 0; }
        .header img { max-height: 40px; }
        .title { font-size: 12px; font-weight: bold; }
        table { border-collapse: collapse; }
        p, th, td { font-size: 10px; margin: 0; }
        .products th, .products td, .summary th, .summary td { border: 1px solid black; padding: 1px; text-align: center; }
    </style>
</head>
<body>
    @foreach ($orders as $order)
    <div class="invoice-container" @unless($loop->last) style="page-break-after:always;" @endunless>
        <div class="header">
            <table width="100%">
                <tr>
                    <td align="left">
                        <img src="{{ public_path($logo->mobile) }}" alt="Logo">
                        <p class="title">{{ $company->name }}</p>
                        <p>{{ $company->phone }}</p>
                        <p>{{ $company->address }}</p>
                    </td>
                    <td align="right">
                        <img src="data:image/jpeg;base64, {{base64_encode(file_get_contents('https://barcode.tec-it.com/barcode.ashx?data='.$order->barcode.'&code=Code128'))}}" alt="Barcode">
                        <p><small>{{ $order->created_at->format('M d, Y') }}</small></p>
                        <p><strong>{{ $order->name }}</strong></p>
                        <p>{{ $order->phone }}</p>
                        <p>{{ $order->address }}</p>
                    </td>
                </tr>
            </table>
        </div>
        <table class="products" width="100%">
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
                            <img style="height: 40px; width: 40px; float: left; margin: 0;" src="data:image/jpeg;base64, {{base64_encode(file_get_contents(storage_path($path)))}}" alt="Barcode">
                            <div style="min-height: 40px;">{{ $product->name }}</div>
                        </div>
                    </td>
                    <td>{{ $product->quantity }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->quantity * $product->price }}</td>
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
                    <td>{{ $order->data['shipping_cost'] }}</td>
                </tr>
                @if($discount = $order->data['discount'] ?? 0)
                <tr>
                    <td colspan="3"><strong>Discount</strong></td>
                    <td>{{ $discount }}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td><strong>{{ $order->condition }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endforeach
</body>
</html>
