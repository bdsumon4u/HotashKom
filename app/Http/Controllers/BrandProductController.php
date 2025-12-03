<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Traits\HasProductFilters;
use Illuminate\Http\Request;

class BrandProductController extends Controller
{
    use HasProductFilters;

    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Brand $brand)
    {
        $per_page = $request->get('per_page', 50);
        $query = $brand->products()->whereIsActive(1)->whereNull('parent_id');

        // Apply filters
        $this->applyProductFilters($query, $request);

        // Apply sorting
        $this->applyProductSorting($query);

        // Eager load reviews to prevent N+1 queries
        $products = $query->with([
            'reviews' => function ($q): void {
                $q->where('approved', true)->with('ratings');
            },
        ])->paginate($per_page)->appends(request()->query());

        // Get filter data
        $filterData = $this->getProductFilterData();

        return view('products.index', [
            'products' => $products,
            'per_page' => $per_page,
            'brand' => $brand,
        ] + $filterData);
    }
}
