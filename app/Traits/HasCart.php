<?php

namespace App\Traits;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\FacebookPixelService;
use Azmolla\Shoppingcart\Facades\Cart;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

trait HasCart
{
    public function addToKart(Product $product, int $quantity = 1, string $instance = 'default', $retailPrice = null)
    {
        $redirectToCheckout = $instance !== 'default' && $instance !== 'landing';
        $instance = resolveShoppingCartInstance($instance);
        $product->refresh();

        if (! ($product->parent ?? $product)->is_active) {
            $this->dispatch('notify', ['message' => 'Product is inactive and cannot be added to cart', 'type' => 'danger']);

            return;
        }

        $fraudQuantity = setting('fraud')->max_qty_per_product ?? 3;
        $maxQuantity = $product->purchaseQuantityLimit((int) $fraudQuantity);
        $cart = Cart::instance($instance);
        $existingQuantity = in_array($instance, ['kart', 'landing'], true) ? 0 : (int) $cart->content()->where('id', $product->id)->sum('qty');
        $quantity = min($quantity, max(0, $maxQuantity - $existingQuantity));

        if ($quantity < 1) {
            $this->dispatch('notify', ['message' => 'This product is out of stock or its maximum cart quantity has been reached.', 'type' => 'danger']);

            return;
        }

        session(['kart' => $instance]);
        if (in_array($instance, ['kart', 'landing'], true)) {
            $cart->destroy();
        }

        $productData = (new ProductResource($product))->toCartItem($quantity);
        $productData['max'] = $maxQuantity;
        $productData['retail_price'] = $retailPrice;

        $matchingItems = $cart->content()->where('id', $product->id);

        if (! orderNowIsOnetime() && $instance === 'default' && $matchingItems->isNotEmpty()) {
            $existingItem = $matchingItems->first();
            foreach ($matchingItems->slice(1) as $duplicate) {
                $cart->remove($duplicate->rowId);
            }
            $cart->update($existingItem->rowId, [
                'qty' => $existingQuantity + $quantity,
                'price' => $product->getPrice($existingQuantity + $quantity),
            ]);
        } else {
            $cart->add(
                $product->id,
                $product->varName,
                $quantity,
                $productData['price'],
                $productData
            );
        }

        storeOrUpdateCart();

        if (setting('meta_pixel') || config('meta-pixel.meta_pixel') || setting('pixel_ids')) {
            $this->facebookService ??= app(FacebookPixelService::class);
            $this->facebookService->trackAddToCart([
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->selling_price,
                'page_url' => route('products.show', $product->slug),
            ], $this);
        }

        if (GoogleTagManagerFacade::isEnabled()) {
            $this->dispatch('dataLayer', [
                'event' => 'add_to_cart',
                'ecommerce' => [
                    'currency' => 'BDT',
                    'value' => $retailPrice,
                    'items' => [
                        [
                            'item_id' => $product->id,
                            'item_name' => $product->varName,
                            'item_category' => $product->category,
                            'price' => $retailPrice,
                            'quantity' => $quantity,
                        ],
                    ],
                ],
                'customer' => customer_info(),
            ]);
        }

        $this->dispatch('cartUpdated');
        $this->dispatch('notify', ['message' => 'Product added to cart']);

        if ($redirectToCheckout) {
            return to_route('checkout');
        }
    }
}
