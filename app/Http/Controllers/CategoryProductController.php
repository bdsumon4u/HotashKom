<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\HasProductFilters;
use Illuminate\Http\Request;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

class CategoryProductController extends Controller
{
    use HasProductFilters;

    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Category $category)
    {
        $per_page = $request->get('per_page', 50);
        $query = $category->products()->whereIsActive(1)->whereNull('parent_id');

        // Apply filters
        $this->applyProductFilters($query, $request);

        // Apply sorting
        $this->applyProductSorting($query);

        $products = $query->paginate($per_page)->appends(request()->query());

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

        // Get filter data - only attributes for products in this category
        $filterData = $this->getProductFilterData($category);

        return view('products.index', [
            'products' => $products,
            'per_page' => $per_page,
            'hideCategoryFilter' => true,
        ] + $filterData);
    }
}
