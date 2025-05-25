<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

class CategoryProductController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Category $category)
    {
        $per_page = $request->get('per_page', 50);
        $sorted = setting('show_option')->product_sort ?? 'random';
        $products = $category->products()->whereIsActive(1);
        if ($sorted == 'random') {
            $products->inRandomOrder();
        } elseif ($sorted == 'updated_at') {
            $products->latest('updated_at');
        } elseif ($sorted == 'selling_price') {
            $products->orderBy('selling_price');
        }
        $products = $products->paginate($per_page)->appends(request()->query());

        if (GoogleTagManagerFacade::isEnabled()) {
            GoogleTagManagerFacade::set([
                'event' => 'view_item_list',
                'ecommerce' => [
                    'item_list_id' => $category->id,
                    'item_list_name' => $category->name,
                    'items' => $products->map(fn ($product): array => [
                        'item_id' => $product->id,
                        'item_name' => $product->name,
                        'price' => $product->selling_price,
                        'item_category' => $product->category,
                        'quantity' => 1,
                    ])->toArray(),
                ],
            ]);
        }

        return view('products.index', [
            'products' => $products,
            'per_page' => $per_page,
        ]);
    }
}
