<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\FacebookPixelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ViewContentTrackingController extends Controller
{
    public function __construct(protected FacebookPixelService $facebookPixelService) {}

    /**
     * Track a ViewContent event server-side via Conversions API.
     *
     * Called by the client-side JavaScript when a product detail page is actually loaded
     * (either on initial load or after a wire:navigate SPA navigation transition).
     */
    public function __invoke(Request $request): JsonResponse
    {
        if (! config('meta-pixel.meta_pixel')) {
            return response()->json(['success' => true]);
        }

        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'value' => 'nullable|numeric|min:0',
            'fbp' => 'nullable|string|max:200',
            'fbc' => 'nullable|string|max:200',
            'event_id' => 'nullable|string|max:100',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $tracking = array_filter([
            'fbp' => $validated['fbp'] ?? null,
            'fbc' => $validated['fbc'] ?? null,
            'ip' => $request->ip(),
            'ua' => $request->userAgent(),
            'event_source_url' => $request->header('Referer') ?: url()->previous(),
        ]);

        // Trigger Conversions API event
        $this->facebookPixelService->trackViewContent($product, $validated['event_id'] ?? null, $tracking);

        return response()->json(['success' => true]);
    }
}
