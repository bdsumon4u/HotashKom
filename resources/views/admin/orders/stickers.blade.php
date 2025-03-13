<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices</title>
    <style>
        @page { size: 10cm 6.2cm; margin: 2px; }
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
        .header img { max-height: 40px; }
        .title { font-size: 12px; font-weight: bold; }
        p, .products th, .products td { font-size: 10px; margin: 0; }
        .products th, .products td { border: 1px solid black; padding: 1px; text-align: center; }
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
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->quantity * $product->price }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3"><strong>Subtotal</strong></td>
                    <td>{{ $order->data['subtotal'] }}</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Delivery</strong></td>
                    <td>{{ $order->data['shipping_cost'] }}</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Discount</strong></td>
                    <td>{{ $order->data['discount'] ?? 0 }}</td>
                </tr>
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
