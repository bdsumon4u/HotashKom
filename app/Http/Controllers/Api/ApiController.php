<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Pathao\Facade\Pathao;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function menus()
    {
        return cache()->remember('menus', now()->addMinute(), fn () => Menu::all()->mapWithKeys(fn ($menu) => [$menu->slug => $menu->menuItems]));
    }

    public function searchSuggestions(Request $request)
    {
        return Product::search($request->get('query'), fn ($query) => $query->whereNull('parent_id')->whereIsActive(1))
            ->take($request->get('limit'))->get()->transform(fn ($product) => array_merge($product->toArray(), [
                'images' => $product->images->pluck('src')->toArray(),
                'price' => $product->selling_price,
                'compareAtPrice' => $product->price,
                'badges' => [],
                'brand' => [],
                'categories' => [],
                'reviews' => 0,
                'rating' => 0,
                'attributes' => [],
                'availability' => $product->should_track ? $product->stock_count : 'In Stock',
            ]));
    }

    public function page(Page $page)
    {
        return $page->toArray();
    }

    public function slides()
    {
        return slides()->transform(fn ($slide) => $slide->only(['title', 'text', 'btn_name', 'btn_href']) + [
            'imageClassic' => [
                'ltr' => asset($slide->desktop_src),
                'rtl' => asset($slide->desktop_src),
            ],
            'imageFull' => [
                'ltr' => asset($slide->desktop_src),
                'rtl' => asset($slide->desktop_src),
            ],
            'imageMobile' => [
                'ltr' => asset($slide->mobile_src),
                'rtl' => asset($slide->mobile_src),
            ],
        ]);
    }

    public function sections(Request $request)
    {
        return sections()->transform(fn ($section) => array_merge($section->toArray(), [
            'categories' => $section->categories->map(fn ($category) => array_merge($category->toArray(), [
                'sectionId' => $section->id,
            ]))->prepend(['id' => 0, 'sectionId' => $section->id, 'name' => $section->type == 'pure-grid' ? 'View All' : 'All']),
        ]));
    }

    public function sectionProducts(Request $request, HomeSection $section)
    {
        return $section->products(category: $request->category)->transform(fn ($product) => array_merge($product->toArray(), [
            'images' => $product->images->pluck('src')->toArray(),
            'price' => $product->selling_price,
            'compareAtPrice' => $product->price,
            'badges' => [],
            'brand' => [],
            'categories' => [],
            'reviews' => 0,
            'rating' => 0,
            'attributes' => [],
            'availability' => $product->should_track ? $product->stock_count : 'In Stock',
        ]));
    }

    public function product(Request $request, $slug)
    {
        $product = Product::where('slug', rawurldecode((string) $slug))->firstOrFail();

        if ($product->parent_id) {
            $product = $product->parent;
        }

        $showBrandCategory = false;
        if ($product->variations->isNotEmpty()) {
            if ($request->options) {
                $selectedVar = $product->variations->first(fn ($item) => $item->options->pluck('id')->diff($request->options)->isEmpty());
            } else {
                $selectedVar = $product->variations->where('slug', request()->segment(2))->first()
                    ?? $product->variations->random();
            }
        } else {
            $selectedVar = $product;
            $showBrandCategory = true;
        }

        $maxPerProduct = setting('fraud')->max_qty_per_product ?? 3;
        $options = $selectedVar->options->pluck('id', 'attribute_id')->toArray();
        $maxQuantity = $selectedVar->should_track ? min($selectedVar->stock_count, $maxPerProduct) : $maxPerProduct;

        $optionGroup = $product->variations->pluck('options')->flatten()->unique('id')->groupBy('attribute_id');

        return array_merge($selectedVar->toArray(), [
            'name' => $selectedVar->var_name,
            'images' => $product->images->pluck('src')->toArray(),
            'price' => $selectedVar->selling_price,
            'compareAtPrice' => $selectedVar->price,
            'badges' => [],
            'brand' => $product->brand,
            'categories' => $product->categories,
            'reviews' => 0,
            'rating' => 0,
            'optionGroup' => $optionGroup,
            'attributes' => Attribute::find($optionGroup->keys()),
            'free_delivery' => setting('free_delivery'),
            'deliveryText' => $this->deliveryText($product, setting('free_delivery')),
            'availability' => $selectedVar->should_track ? $product->stock_count : 'In Stock',
            'showBrandCategory' => $showBrandCategory,
            'options' => $options,
            'maxQuantity' => $maxQuantity,
            'shipping_inside' => $selectedVar->shipping_inside,
            'shipping_outside' => $selectedVar->shipping_outside,
            'wholesale' => $selectedVar->wholesale,
        ]);
    }

    public function updatedOptions($value, $key): void
    {
        $variation = $this->product->variations->first(fn ($item) => $item->options->pluck('id')->diff($this->options)->isEmpty());

        if ($variation) {
            $this->selectedVar = $variation;
        }
    }

    private function deliveryText($product, $freeDelivery)
    {
        if ($freeDelivery->for_all ?? false) {
            $text = '<ul class="p-0 pl-4 mb-0 list-unstyled">';
            if ($freeDelivery->min_quantity > 0) {
                $text .= '<li>কমপক্ষে <strong class="text-danger">'.$freeDelivery->min_quantity.'</strong> টি প্রোডাক্ট অর্ডার করুন</li>';
            }
            if ($freeDelivery->min_amount > 0) {
                $text .= '<li>কমপক্ষে <strong class="text-danger">'.$freeDelivery->min_amount.'</strong> টাকার প্রোডাক্ট অর্ডার করুন</li>';
            }

            return $text.'</ul>';
        }

        if (array_key_exists($product->id, $products = ((array) ($freeDelivery->products ?? [])) ?? [])) {
            return 'কমপক্ষে <strong class="text-danger">'.$products[$product->id].'</strong> টি অর্ডার করুন';
        }

        return false;
    }

    public function relatedProducts(Request $request, $slug)
    {
        $product = Product::where('slug', rawurldecode((string) $slug))->firstOrFail();

        $categories = $product->categories->pluck('id')->toArray();

        return Product::whereIsActive(1)
            ->whereHas('categories', function ($query) use ($categories): void {
                $query->whereIn('categories.id', $categories);
            })
            ->whereNull('parent_id')
            ->where('id', '!=', $product->id)
            ->limit(config('services.products_count.related', 20))
            ->get()
            ->transform(fn ($product) => array_merge($product->toArray(), [
                'images' => $product->images->pluck('src')->toArray(),
                'price' => $product->selling_price,
                'compareAtPrice' => $product->price,
                'badges' => [],
                'brand' => [],
                'categories' => [],
                'reviews' => 0,
                'rating' => 0,
                'attributes' => [],
                'availability' => $product->should_track ? $product->stock_count : 'In Stock',
            ]));
    }

    public function areas($city_id)
    {
        return Pathao::area()->zone($city_id)->data;
    }

    public function categories(Request $request)
    {
        if ($request->nested) {
            return Category::nested($request->get('count', 0));
        }

        return Category::all()
            ->transform(fn ($category) => $category->toArray() + [
                'type' => 'shop',
            ])
            ->toJson();
    }

    public function category($slug)
    {
        return Category::where('slug', rawurldecode((string) $slug))->firstOrFail()->toArray();
    }

    public function products($search)
    {
        $products = Product::where('name', 'like', "%$search%")->take(5)->get();

        return view('admin.orders.searched', compact('products'))->render();
    }

    public function order(Order $order)
    {
        return array_merge($order->toArray(), [
            'created_at' => $order->created_at->format('Y-m-d H:i:s'),
        ]);
    }

    public function settings(Request $request)
    {
        $keys = array_values(array_intersect($request->get('keys', []), [
            'company', 'logo', 'social', 'scroll_text', 'services', 'call_for_order',
            'delivery_text', 'products_page', 'delivery_charge',
        ]));

        // return cache()->remember('settings:'.implode(';', $keys), now()->addMinute(), function () use ($keys) {
        return Setting::whereIn('name', $keys)->get(['name', 'value'])->pluck('value', 'name');
        // });
    }

    public function pendingCount(Admin $admin)
    {
        return Order::where('status', 'PENDING')->when($admin->role_id == Admin::SALESMAN, function ($query) use (&$admin): void {
            $query->where('admin_id', $admin->id);
        })->count();
    }

    public function pathaoWebhook(Request $request)
    {
        if ($request->event == 'webhook_integration') {
            return response()->json(['message' => 'Webhook processed'], 202)
                ->header('X-Pathao-Merchant-Webhook-Integration-Secret', 'f3992ecc-59da-4cbe-a049-a13da2018d51');
        }

        $Pathao = setting('Pathao');
        if ($request->header('X-PATHAO-Signature') != $Pathao->store_id) {
            return response()->json(['message' => 'Webhook processed'], 202)
                ->header('X-Pathao-Merchant-Webhook-Integration-Secret', 'f3992ecc-59da-4cbe-a049-a13da2018d51');
        }

        if (! $order = Order::find($request->merchant_order_id)/* ->orWhere('data->consignment_id', $request->consignment_id)->first() */) {
            return response()->json(['message' => 'Webhook processed'], 202)
                ->header('X-Pathao-Merchant-Webhook-Integration-Secret', 'f3992ecc-59da-4cbe-a049-a13da2018d51');
        }

        // $courier = $request->only([
        //     'consignment_id',
        //     'order_status',
        //     'reason',
        //     'invoice_id',
        //     'payment_status',
        //     'collected_amount',
        // ]);
        // $order->forceFill(['courier' => ['booking' => 'Pathao'] + $courier]);

        if ($request->event == 'order.pickup-requested') {
            $order->fill([
                'status' => 'SHIPPING',
                'data' => [
                    'consignment_id' => $request->consignment_id,
                ],
            ]);
        } elseif ($request->event == 'order.pickup-cancelled') {
            $order->status = 'CANCELLED';
            $order->status_at = now();
        } elseif ($request->event == 'order.on-hold') {
            $order->status = 'WAITING';
            $order->status_at = now();
        } elseif ($request->event == 'order.delivered') {
            $order->status = 'COMPLETED';
            $order->status_at = now();
        } elseif ($request->event == 'order.paid') {

        } elseif ($request->event == 'order.returned') {
            $order->status = 'RETURNED';
            $order->status_at = now();
            // TODO: add to stock
        }

        $order->save();

        return response()->json(['message' => 'Webhook processed'], 202)
            ->header('X-Pathao-Merchant-Webhook-Integration-Secret', 'f3992ecc-59da-4cbe-a049-a13da2018d51');
    }
}
