<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Slide;
use App\Pathao\Facade\Pathao;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function slides()
    {
        return slides()->transform(function ($slide) {
            return $slide->only(['title', 'text', 'btn_name', 'btn_href']) + [
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
            ];
        });
    }

    public function sections(Request $request)
    {
        return sections()->transform(function ($section) {
            return array_merge($section->toArray(), [
                'categories' => $section->categories->map(function ($category) use ($section) {
                    return array_merge($category->toArray(), [
                        'sectionId' => $section->id,
                    ]);
                })->prepend(['id' => 0, 'sectionId' => $section->id, 'name' => 'All']),
            ]);
        });
    }

    public function sectionProducts(Request $request, HomeSection $section) {
        return $section->products(category: $request->category)->transform(function ($product) {
            return array_merge($product->toArray(), [
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
            ]);
        });
    }

    public function product(Request $request, Product $product)
    {
        if ($product->parent_id) {
            $product = $product->parent;
        }

        $showBrandCategory = false;
        if ($product->variations->isNotEmpty()) {
            if ($request->options) {
                $selectedVar = $product->variations->first(function ($item) use ($request) {
                    return $item->options->pluck('id')->diff($request->options)->isEmpty();
                });
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

    public function updatedOptions($value, $key)
    {
        $variation = $this->product->variations->first(function ($item) {
            return $item->options->pluck('id')->diff($this->options)->isEmpty();
        });

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
            $text .= '</ul>';

            return $text;
        }

        if (array_key_exists($product->id, $products = ((array) ($freeDelivery->products ?? [])) ?? [])) {
            return 'কমপক্ষে <strong class="text-danger">'.$products[$product->id].'</strong> টি অর্ডার করুন';
        }

        return false;
    }

    public function relatedProducts(Request $request, Product $product)
    {
        $categories = $product->categories->pluck('id')->toArray();
        return Product::whereIsActive(1)
            ->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('categories.id', $categories);
            })
            ->whereNull('parent_id')
            ->where('id', '!=', $product->id)
            ->limit(config('services.products_count.related', 20))
            ->get()
            ->transform(function ($product) {
                return array_merge($product->toArray(), [
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
                ]);
            });
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
            ->transform(function ($category) {
                return $category->toArray() + [
                    'type' => 'shop',
                ];
            })
            ->toJson();
    }

    public function products($search)
    {
        $products = Product::where('name', 'like', "%$search%")->take(5)->get();

        return view('admin.orders.searched', compact('products'))->render();
    }

    public function settings(Request $request)
    {
        $keys = array_values(array_intersect($request->get('keys', []), ['company', 'logo', 'social']));

        return cache()->rememberForever('settings:'.implode(';', $keys), function () use ($keys) {
            return Setting::whereIn('name', $keys)->get(['name', 'value'])->pluck('value', 'name');
        });
    }

    public function pendingCount(Admin $admin)
    {
        return Order::where('status', 'PENDING')->when($admin->role_id == Admin::SALESMAN, function ($query) use (&$admin) {
            $query->where('admin_id', $admin->id);
        })->count();
    }

    public function pathaoWebhook(Request $request)
    {
        $Pathao = setting('Pathao');
        if ($request->header('X-PATHAO-Signature') != $Pathao->store_id) {
            return;
        }

        if (! $order = Order::find($request->merchant_order_id)/*->orWhere('data->consignment_id', $request->consignment_id)->first()*/) {
            return;
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

        if ($request->order_status_slug == 'Pickup_Requested') {
            $order->fill([
                'status' => 'SHIPPING',
                'data' => [
                    'consignment_id' => $request->consignment_id,
                ],
            ]);
        } elseif ($request->order_status_slug == 'Pickup_Cancelled') {
            $order->status = 'CANCELLED';
            $order->status_at = now();
        } elseif ($request->order_status_slug == 'On_Hold') {
            $order->status = 'WAITING';
            $order->status_at = now();
        } elseif ($request->order_status_slug == 'Delivered') {
            $order->status = 'COMPLETED';
            $order->status_at = now();
        } elseif ($request->order_status_slug == 'Payment_Invoice') {

        } elseif ($request->order_status_slug == 'Return') {
            $order->status = 'RETURNED';
            $order->status_at = now();
            // TODO: add to stock
        }

        $order->save();
    }
}
