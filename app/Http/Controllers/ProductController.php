<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
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
        // \LaravelFacebookPixel::createEvent('PageView', $parameters = []);
        $categories = collect();
        $section = null;
        $rows = 3;
        $cols = 5;
        if ($productsPage = Setting::whereName('products_page')->first()) {
            $rows = $productsPage->value->rows;
            $cols = $productsPage->value->cols;
        }
        $per_page = $request->get('per_page', $rows * $cols);
        if ($section = request('filter_section', 0)) {
            $section = HomeSection::with('categories.childrens.image')->findOrFail($section);
            $products = $section->products($per_page);
            $categories = $section->categories;
        } else {
            if ($request->filter_category) {
                // if filter_category is a comma separated ids(numeric) then use it as category ids
                if (is_numeric(str_replace(',', '', $request->filter_category))) {
                    $products = Product::whereHas('categories', function ($query) use ($request) {
                        $query->whereIn('categories.id', explode(',', $request->filter_category));
                    });
                    $categories = Category::with('childrens.image')->whereIn('id', explode(',', $request->filter_category))->get();
                } else {
                    $products = Product::whereHas('categories', function ($query) use ($request) {
                        $query->where('categories.slug', rawurldecode($request->filter_category));
                    });
                    $categories = Category::with('childrens.image')->where('slug', rawurldecode($request->filter_category))->get();
                }
                $products = $products->whereIsActive(1)->whereNull('parent_id');

                $sorted = setting('show_option')->product_sort ?? 'random';
                if ($sorted == 'random') {
                    $products->inRandomOrder();
                } elseif ($sorted == 'updated_at') {
                    $products->latest('updated_at');
                } elseif ($sorted == 'selling_price') {
                    $products->orderBy('selling_price');
                }
            } else {
                $products = Product::search($request->search, function ($query) {
                    $query->whereIsActive(1)->whereNull('parent_id');

                    $sorted = setting('show_option')->product_sort ?? 'random';
                    if ($sorted == 'random') {
                        $query->inRandomOrder();
                    } elseif ($sorted == 'updated_at') {
                        $query->latest('updated_at');
                    } elseif ($sorted == 'selling_price') {
                        $query->orderBy('selling_price');
                    }
                });
            }

            $products = $products->paginate($per_page);
        }
        $products = $products
            ->appends(request()->query());

        if ($request->is('api/*')) {
            return ProductResource::collection($products);
        }

        $categories = $categories->map(fn($cat)=>$cat->childrens)->filter->map(function ($category) {
            if ($category->relationLoaded('image')) {
                $image = $category->image;
            } else {
                $image = null;
                // $images = $category->products->pluck('images')->filter();
                // $image = $images->isEmpty() ? null : $images->random()->first();
            }

            // Set the image_src property with a fallback placeholder
            $category->image_src = asset($image->path ?? 'https://placehold.co/600x600?text='.$category->name);

            return $category;
        })->flatten();

        return $this->view(compact('categories', 'products', 'per_page', 'rows', 'cols', 'section'));
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
            ->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('categories.id', $categories);
            })
            ->whereNull('parent_id')
            ->where('id', '!=', $product->id)
            ->limit(config('services.products_count.related', 20))
            ->get();
        //  \LaravelFacebookPixel::createEvent('ViewContent', $parameters = []);
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

        return $this->view(compact('product', 'products'));
    }
}
