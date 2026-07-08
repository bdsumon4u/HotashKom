<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Services\FacebookPixelService;
use Illuminate\Http\Response;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

class CheckoutController extends Controller
{
    public function __construct(protected FacebookPixelService $facebookPixelService) {}

    /**
     * Handle the incoming request.
     *
     * @return Response
     */
    public function __invoke(CheckoutRequest $request)
    {
        if ($request->isMethod('GET')) {
            if (GoogleTagManagerFacade::isEnabled()) {
                GoogleTagManagerFacade::set([
                    'event' => 'begin_checkout',
                    'ecommerce' => [
                        'currency' => 'BDT',
                        'value' => cart()->subTotal(),
                        'items' => cart()->content()->map(fn ($product): array => [
                            'item_id' => $product->id,
                            'item_name' => $product->name,
                            'item_category' => $product->options->category,
                            'price' => $product->price,
                            'quantity' => $product->qty,
                        ])->values(),
                    ],
                    'customer' => customer_info(),
                ]);
            }

            $trackingDetails = null;
            if (config('meta-pixel.meta_pixel')) {
                $trackingDetails = $this->facebookPixelService->trackInitiateCheckout();
            }

            return view('checkout', compact('trackingDetails'));
        }
    }
}
