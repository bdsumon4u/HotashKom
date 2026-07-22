<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\FacebookPixelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddToCartTrackingController extends Controller
{
    public function __construct(protected FacebookPixelService $facebookPixelService) {}

    /**
     * Track an AddToCart event server-side via Conversions API.
     *
     * Called by the storefront JS after a successful /cart/add response
     * for PureGrid and InfiniteScroll product cards (non-Livewire context).
     */
    public function __invoke(Request $request): JsonResponse
    {
        if (! config('meta-pixel.meta_pixel') && ! setting('pixel_ids')) {
            return response()->json(['success' => true]);
        }

        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'value' => 'nullable|numeric|min:0',
            'fbp' => 'nullable|string|max:200',
            'fbc' => 'nullable|string|max:200',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $tracking = array_filter([
            'fbp' => $validated['fbp'] ?? null,
            'fbc' => $validated['fbc'] ?? null,
            'ip' => $request->ip(),
            'ua' => $request->userAgent(),
            'event_source_url' => $request->header('Referer') ?: url()->previous(),
        ]);

        $this->facebookPixelService->trackAddToCart([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $validated['value'] ?? $product->selling_price,
            'page_url' => route('products.show', $product->slug),
        ], null, $tracking);

        return response()->json(['success' => true]);
    }
}
