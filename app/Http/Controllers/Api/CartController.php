<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:10',
            'instance' => 'string|in:default,kart,landing',
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;
        $instance = $request->instance ?? 'default';

        try {
            // Follow the HasCart trait logic but avoid problematic services
            session(['kart' => $instance]);
            if ($instance == 'landing') {
                cart()->destroy();
            }

            $fraudQuantity = setting('fraud')->max_qty_per_product ?? 3;
            $maxQuantity = $product->should_track ? min($product->stock_count, $fraudQuantity) : $fraudQuantity;
            $quantity = min($quantity, $maxQuantity);

            // Use ProductResource to get proper cart item data
            $productData = (new \App\Http\Resources\ProductResource($product))->toCartItem($quantity);
            $productData['max'] = $maxQuantity;

            cart()->instance($instance)->add(
                $product->id,
                $product->varName,
                $quantity,
                $productData['price'], // this is the wholesale price
                $productData
            );

            // Store cart in session
            storeOrUpdateCart();

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cart_count' => cart()->instance($instance)->count(),
                'cart_total' => cart()->instance($instance)->total(),
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $quantity,
                ],
                'instance' => $instance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart: '.$e->getMessage(),
            ], 500);
        }
    }

    public function get(Request $request)
    {
        $instance = $request->get('instance', 'default');

        return response()->json([
            'success' => true,
            'cart' => cart()->instance($instance)->content(),
            'cart_count' => cart()->instance($instance)->count(),
            'cart_total' => cart()->instance($instance)->total(),
        ]);
    }
}
