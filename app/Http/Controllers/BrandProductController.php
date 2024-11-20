<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandProductController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Brand $brand)
    {
        $per_page = $request->get('per_page', 50);
        $sorted = setting('show_option')->product_sort ?? 'random';
        $products = $brand->products()->whereIsActive(1);
        if ($sorted == 'random') {
            $products->inRandomOrder();
        } elseif ($sorted == 'updated_at') {
            $products->latest('updated_at');
        } elseif ($sorted == 'selling_price') {
            $products->orderBy('selling_price');
        }
        $products = $products->paginate($per_page)->appends(request()->query());

        return view('products.index', [
            'products' => $products,
            'per_page' => $per_page,
        ]);
    }
}
