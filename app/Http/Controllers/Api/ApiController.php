<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Order;
use App\Models\Product;
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
        // sleep(15);
        return sections()->transform(function ($section) {
            return array_merge($section->toArray(), [
                'categories' => $section->categories->map(function ($category) use ($section) {
                    return array_merge($category->toArray(), [
                        'sectionId' => $section->id,
                    ]);
                })->prepend(['id' => 0, 'sectionId' => $section->id, 'name' => 'All']),
                'products' => [] /* $section->products()->transform(function ($product) {
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
                        'availability' => 'in-stock',
                    ]);
                }), */
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
                'availability' => 'in-stock',
            ]);
        });
    }

    public function product(Request $request, Product $product)
    {
        return array_merge($product->toArray(), [
            'images' => $product->images->pluck('src')->toArray(),
            'price' => $product->selling_price,
            'compareAtPrice' => $product->price,
            'badges' => [],
            'brand' => $product->brand,
            'categories' => $product->categories,
            'reviews' => 0,
            'rating' => 0,
            'attributes' => [],
            'availability' => $product->should_track ? $product->stock_count : 'In Stock',
        ]);
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
