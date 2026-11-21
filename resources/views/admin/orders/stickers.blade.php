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
    @php
        $loadImage = static function (?string $path, ?string $fallback = null): ?string {
            if (! $path) {
                return $fallback;
            }

            try {
                $url = storage_url($path);
                $key = storage_key($url);
                if ($key !== null) {
                    $content = \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->get($key);
                } else {
                    $content = @file_get_contents($url, false, stream_context_create([
                        'http' => ['timeout' => 10],
                    ]));
                }

                $image = $content ? @getimagesizefromstring($content) : false;
                if ($image) {
                    return 'data:'.$image['mime'].';base64,'.base64_encode($content);
                }
            } catch (\Throwable) {
                return $fallback;
            }

            return $fallback;
        };
    @endphp

    @foreach ($orders as $order)
    @php require resource_path('views/admin/orders/reseller-info.php') @endphp
    <div class="invoice-container" @unless($loop->last) style="page-break-after:always;" @endunless>
        <div class="header">
            <table width="100%">
                <tr>
                    <td align="left">
                        @php
                            // First try to load the reseller logo (could be remote or local)
                            $logoImage = $loadImage($logoUrl ?? null, null);

                            // If no reseller logo, try the fallback logo
                            if (!$logoImage) {
                                $logoImage = $loadImage($logo->mobile ?? null);
                            }

                            // If still no logo, we'll show company name
                        @endphp
                        @if($logoImage)
                            <img src="{{ $logoImage }}" alt="Logo">
                        @else
                            {{-- No logo available - show company name instead --}}
                            <div style="font-size: 12px; font-weight: bold; color: #333; min-height: 40px; display: flex; align-items: center;">
                                {{ $companyName ?? 'Company Logo' }}
                            </div>
                        @endif
                    </td>
                    <td align="center">
                        <p><small>{{ $order->created_at->format('M d, Y') }}</small></p>
                    </td>
                    <td align="right">
                        @php
                            $barcodeImage = $loadImage('https://barcode.tec-it.com/barcode.ashx?data='.rawurlencode($order->barcode).'&code=Code128', '<div style="width: 150px; text-align: center; font-family: monospace; font-size: 8px; border: 1px solid #000; padding: 2px;">' . $order->barcode . '</div>');
                        @endphp

                        @if(str_starts_with($barcodeImage, 'data:image'))
                            <img style="width: 150px;" src="{{ $barcodeImage }}" alt="Barcode">
                        @else
                            {!! $barcodeImage !!}
                        @endif
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
                @php $retail = 0 @endphp
                @foreach ($order->products as $product)
                <tr>
                    <td>
                        @php
                            $productImage = $loadImage($product->image, '<div style="height: 36px; width: 36px; float: left; margin: 0; background-color: #f0f0f0; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 8px; color: #666;">IMG</div>');
                        @endphp
                        <div style="clear: both;">
                            @if(str_starts_with($productImage, 'data:image'))
                                <img style="height: 36px; width: 36px; float: left; margin: 0;" src="{{ $productImage }}" alt="Image">
                            @else
                                {!! $productImage !!}
                            @endif
                            <div style="min-height: 36px;">{{ $product->name }}</div>
                        </div>
                    </td>
                    <td>{{ $product->quantity }}</td>
                    <td>{{ (isOninda() && config('app.resell')) ? ($product->retail_price ?? $product->price) : $product->price }}</td>
                    <td>{{ $amount = $product->quantity * ((isOninda() && config('app.resell')) ? ($product->retail_price ?? $product->price) : $product->price) }}</td>
                </tr>
                @php $retail += $amount @endphp
                @endforeach
            </tbody>
        </table>
        <table class="summary" width="50%;" style="float: right; margin-top: 2px;">
            <tbody>
                <tr>
                    <td colspan="3"><strong>Subtotal</strong></td>
                    <td>{{ $retail }}</td>
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
