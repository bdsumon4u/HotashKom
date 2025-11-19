<?php

namespace App\Http\Controllers;

use App\Models\HomeSection;
use Illuminate\Http\Request;

class HomeSectionProductController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, HomeSection $section)
    {
        $rows = 3;
        $cols = 5;
        $productsPage = setting('products_page');
        if ($productsPage) {
            $rows = $productsPage->rows ?? 3;
            $cols = $productsPage->cols ?? 5;
        }
        $per_page = $request->get('per_page', $rows * $cols);
        $products = $section->products($per_page)->appends(request()->query());

        return view('products.index', compact('section', 'products', 'per_page', 'rows', 'cols'));
    }
}
