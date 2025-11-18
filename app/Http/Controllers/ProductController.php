<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\HomeSection;
use App\Models\Product;
use App\Models\Setting;
use App\Traits\HasProductFilters;
use Illuminate\Http\Request;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

class ProductController extends Controller
{
    use HasProductFilters;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (GoogleTagManagerFacade::isEnabled()) {
            if ($request->search) {
                GoogleTagManagerFacade::set([
                    'event' => 'search',
                    'search_term' => $request->search,
                ]);
            } else {
                GoogleTagManagerFacade::set([
                    'event' => 'page_view',
                    'page_type' => 'shop',
                ]);
            }
        }

        $section = null;
        $rows = 3;
        $cols = 5;
        if ($productsPage = Setting::whereName('products_page')->first()) {
            $rows = $productsPage->value->rows;
            $cols = $productsPage->value->cols;
        }
        $per_page = $request->get('per_page', $rows * $cols);
        if ($section = request('filter_section', 0)) {
            $section = HomeSection::with('categories')->findOrFail($section);
            $products = $section->products($per_page);
        } else {
            $query = Product::whereIsActive(1)->whereNull('parent_id');

            // Apply filters
            $this->applyProductFilters($query, $request);

            // Search
            if ($request->search) {
                $products = Product::search($request->search, function ($q) use ($request): void {
                    $q->whereIsActive(1)->whereNull('parent_id');
                    $this->applyProductFilters($q, $request);
                    $this->applyProductSorting($q);
                });
            } else {
                $this->applyProductSorting($query);
                $products = $query;
            }

            $products = $products->paginate($per_page);
        }
        $products = $products
            ->appends(request()->query());

        if ($request->is('api/*')) {
            return ProductResource::collection($products);
        }

        // Get filter data
        $filterData = $this->getProductFilterData();

        return $this->view(compact('products', 'per_page', 'rows', 'cols', 'section') + $filterData);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        if ($product->parent_id) {
            $product = $product->parent;
        }
        $product->load(['brand', 'categories', 'variations.options']);

        if (GoogleTagManagerFacade::isEnabled()) {
            GoogleTagManagerFacade::set([
                'event' => 'view_item',
                'ecommerce' => [
                    'currency' => 'BDT',
                    'value' => $product->selling_price,
                    'items' => [
                        [
                            'item_id' => $product->id,
                            'item_name' => $product->name,
                            'price' => $product->selling_price,
                            'item_category' => $product->category,
                            'quantity' => 1,
                        ],
                    ],
                ],
            ]);
        }

        return $this->view(compact('product'));
    }
}
