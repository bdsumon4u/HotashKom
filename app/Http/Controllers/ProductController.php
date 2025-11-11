<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

class ProductController extends Controller
{
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

            // Filter by categories
            if ($request->filter_category) {
                $categoryFilter = $request->filter_category;
                if (is_array($categoryFilter)) {
                    $query->whereHas('categories', function ($q) use ($categoryFilter): void {
                        $q->whereIn('categories.id', array_filter($categoryFilter));
                    });
                } elseif (is_numeric(str_replace(',', '', $categoryFilter))) {
                    $query->whereHas('categories', function ($q) use ($categoryFilter): void {
                        $q->whereIn('categories.id', explode(',', $categoryFilter));
                    });
                } else {
                    $query->whereHas('categories', function ($q) use ($categoryFilter): void {
                        $q->where('categories.slug', rawurldecode($categoryFilter));
                    });
                }
            }

            // Filter by brands
            if ($request->filter_brand) {
                $brandIds = is_array($request->filter_brand)
                    ? $request->filter_brand
                    : explode(',', $request->filter_brand);
                $query->whereIn('brand_id', array_filter($brandIds));
            }

            // Filter by price range
            if ($request->min_price) {
                $query->where('selling_price', '>=', $request->min_price);
            }
            if ($request->max_price) {
                $query->where('selling_price', '<=', $request->max_price);
            }

            // Search
            if ($request->search) {
                $products = Product::search($request->search, function ($q) use ($request): void {
                    $q->whereIsActive(1)->whereNull('parent_id');

                    // Apply filters to search query
                    if ($request->filter_category) {
                        $categoryFilter = $request->filter_category;
                        if (is_array($categoryFilter)) {
                            $q->whereHas('categories', function ($catQuery) use ($categoryFilter): void {
                                $catQuery->whereIn('categories.id', array_filter($categoryFilter));
                            });
                        } elseif (is_numeric(str_replace(',', '', $categoryFilter))) {
                            $q->whereHas('categories', function ($catQuery) use ($categoryFilter): void {
                                $catQuery->whereIn('categories.id', explode(',', $categoryFilter));
                            });
                        }
                    }
                    if ($request->filter_brand) {
                        $brandIds = is_array($request->filter_brand)
                            ? $request->filter_brand
                            : explode(',', $request->filter_brand);
                        $q->whereIn('brand_id', array_filter($brandIds));
                    }
                    if ($request->min_price) {
                        $q->where('selling_price', '>=', $request->min_price);
                    }
                    if ($request->max_price) {
                        $q->where('selling_price', '<=', $request->max_price);
                    }

                    $sorted = setting('show_option')->product_sort ?? 'random';
                    if ($sorted == 'random') {
                        $q->inRandomOrder();
                    } elseif ($sorted == 'updated_at') {
                        $q->latest('updated_at');
                    } elseif ($sorted == 'selling_price') {
                        $q->orderBy('selling_price');
                    }
                });
            } else {
                $sorted = setting('show_option')->product_sort ?? 'random';
                if ($sorted == 'random') {
                    $query->inRandomOrder();
                } elseif ($sorted == 'updated_at') {
                    $query->latest('updated_at');
                } elseif ($sorted == 'selling_price') {
                    $query->orderBy('selling_price');
                }
                $products = $query;
            }

            $products = $products->paginate($per_page);
        }
        $products = $products
            ->appends(request()->query());

        if ($request->is('api/*')) {
            return ProductResource::collection($products);
        }

        // Get filter data - only categories and brands with products
        $categories = Category::nested(0, true)
            ->filter(function ($category) {
                // Check if category has products
                $hasProducts = $category->products()
                    ->whereIsActive(1)
                    ->whereNull('parent_id')
                    ->exists();

                // Also check if any child category has products
                $hasChildProducts = $category->childrens->some(function ($child) {
                    return $child->products()
                        ->whereIsActive(1)
                        ->whereNull('parent_id')
                        ->exists();
                });

                return $hasProducts || $hasChildProducts;
            })
            ->map(function ($category) {
                // Filter children to only those with products
                $category->setRelation('childrens', $category->childrens->filter(function ($child) {
                    return $child->products()
                        ->whereIsActive(1)
                        ->whereNull('parent_id')
                        ->exists();
                }));

                return $category;
            })
            ->values(); // Re-index the collection

        // Get brands that have products
        $brands = Brand::cached()
            ->filter(function ($brand) {
                return $brand->products()
                    ->whereIsActive(1)
                    ->whereNull('parent_id')
                    ->exists();
            })
            ->values(); // Re-index the collection

        // Get price range
        $priceRange = Product::whereIsActive(1)
            ->whereNull('parent_id')
            ->selectRaw('MIN(selling_price) as min_price, MAX(selling_price) as max_price')
            ->first();

        return $this->view(compact('products', 'per_page', 'rows', 'cols', 'section', 'categories', 'brands', 'priceRange'));
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
        $categories = $product->categories->pluck('id')->toArray();
        $products = Product::whereIsActive(1)
            ->whereHas('categories', function ($query) use ($categories): void {
                $query->whereIn('categories.id', $categories);
            })
            ->whereNull('parent_id')
            ->where('id', '!=', $product->id)
            ->limit(config('services.products_count.related', 20))
            ->get();

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

        return $this->view(compact('product', 'products'));
    }
}
