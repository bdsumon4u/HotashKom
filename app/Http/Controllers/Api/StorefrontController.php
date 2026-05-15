<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Slide;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    /**
     * GET /api/storefront/settings
     * Returns site settings needed by the frontend (company info, delivery charges, etc.)
     */
    public function settings(): JsonResponse
    {
        $keys = ['company', 'logo', 'social', 'delivery_charge', 'free_delivery', 'scroll_text', 'call_for_order'];
        $settings = Setting::whereIn('name', $keys)->get(['name', 'value'])->pluck('value', 'name');

        return response()->json([
            'data' => $settings,
        ]);
    }

    /**
     * GET /api/storefront/slides
     * Returns active hero banner slides.
     */
    public function slides(): JsonResponse
    {
        $slides = Slide::whereIsActive(1)->get()->map(fn ($slide) => [
            'id' => $slide->id,
            'title' => $slide->title,
            'text' => $slide->text,
            'btn_name' => $slide->btn_name,
            'btn_href' => $slide->btn_href,
            'desktop_image' => asset($slide->desktop_src),
            'mobile_image' => asset($slide->mobile_src),
        ]);

        return response()->json(['data' => $slides]);
    }

    /**
     * GET /api/storefront/categories
     * Returns categories with product counts for the storefront.
     */
    public function categories(): JsonResponse
    {
        $categories = Category::whereNull('parent_id')
            ->where('is_enabled', true)
            ->withCount(['products' => function ($q) {
                $q->whereIsActive(1)->whereNull('parent_id');
            }])
            ->with(['image'])
            ->orderBy('order')
            ->get()
            ->map(fn ($cat) => [
                'id' => (string) $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'image' => $cat->image ? asset($cat->image->src) : '/images/placeholder-cat.jpg',
                'productCount' => $cat->products_count,
            ]);

        return response()->json(['data' => $categories]);
    }

    /**
     * GET /api/storefront/products
     * Returns paginated product listings for the shop page.
     * Supports: ?category=slug&search=term&page=1&per_page=20&sort=latest|price_asc|price_desc
     */
    public function products(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 20), 50);

        $query = Product::whereIsActive(1)
            ->whereNull('parent_id')
            ->with(['images', 'categories:id,name,slug', 'brand:id,name']);

        // Filter by category slug
        if ($request->category) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.slug', rawurldecode($request->category));
            });
        }

        // Search
        if ($request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('sku', 'like', "%{$searchTerm}%");
            });
        }

        // Sorting
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('selling_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('selling_price', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'latest':
            default:
                // 'latest' global scope is already applied
                break;
        }

        $products = $query->paginate($perPage);

        $data = $products->getCollection()->map(fn ($product) => $this->transformProduct($product));

        return response()->json([
            'data' => $data,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'has_more' => $products->hasMorePages(),
            ],
        ]);
    }

    /**
     * GET /api/storefront/products/{slug}
     * Returns a single product detail.
     */
    public function product(string $slug): JsonResponse
    {
        $product = Product::with(['images', 'categories:id,name,slug', 'brand:id,name'])
            ->where('slug', rawurldecode($slug))
            ->whereIsActive(1)
            ->whereNull('parent_id')
            ->firstOrFail();

        $images = $product->images->pluck('src')->map(fn ($src) => asset($src))->toArray();
        $baseImage = $product->base_image ? asset($product->base_image->src) : ($images[0] ?? '/images/placeholder.jpg');

        $deliveryCharge = setting('delivery_charge');

        return response()->json([
            'data' => [
                'id' => (string) $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'category' => $product->categories->first()?->name ?? 'Uncategorized',
                'categorySlug' => $product->categories->first()?->slug ?? '',
                'brand' => $product->brand?->name ?? '',
                'regularPrice' => (int) $product->price,
                'salePrice' => (int) $product->selling_price,
                'discountPercentage' => $product->price > 0
                    ? round((($product->price - $product->selling_price) / $product->price) * 100, 2)
                    : 0,
                'image' => $baseImage,
                'images' => $images,
                'thumbnails' => $images,
                'inStock' => $product->should_track ? $product->stock_count > 0 : true,
                'stockCount' => $product->should_track ? $product->stock_count : 999,
                'description' => $product->description ?? '',
                'shortDescription' => $product->short_description ?? '',
                'shippingInside' => (int) ($deliveryCharge->inside_dhaka ?? 80),
                'shippingOutside' => (int) ($deliveryCharge->outside_dhaka ?? 150),
            ],
        ]);
    }

    /**
     * GET /api/storefront/products/{slug}/related
     * Returns related products for a given product slug.
     */
    public function relatedProducts(string $slug): JsonResponse
    {
        $product = Product::with('categories')
            ->where('slug', rawurldecode($slug))
            ->whereIsActive(1)
            ->whereNull('parent_id')
            ->firstOrFail();

        $categoryIds = $product->categories->pluck('id')->toArray();

        $related = Product::whereIsActive(1)
            ->whereNull('parent_id')
            ->where('id', '!=', $product->id)
            ->when(!empty($categoryIds), function ($q) use ($categoryIds) {
                $q->whereHas('categories', function ($cq) use ($categoryIds) {
                    $cq->whereIn('categories.id', $categoryIds);
                });
            })
            ->with(['images'])
            ->limit(10)
            ->get()
            ->map(fn ($p) => $this->transformProduct($p));

        return response()->json(['data' => $related]);
    }

    /**
     * POST /api/storefront/checkout
     * Places a retail order (Cash on Delivery).
     */
    public function checkout(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string'],
            'address' => ['required', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:1000'],
            'shipping' => ['required', 'in:Inside Dhaka,Outside Dhaka'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $deliveryCharge = setting('delivery_charge');
        $shippingCost = $data['shipping'] === 'Inside Dhaka'
            ? (float) ($deliveryCharge->inside_dhaka ?? 80)
            : (float) ($deliveryCharge->outside_dhaka ?? 150);

        // Build products JSON
        $orderProducts = [];
        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $product = Product::find($item['id']);
            if (!$product) continue;

            $price = $product->selling_price;
            $total = $price * $item['quantity'];
            $subtotal += $total;

            $orderProducts[$product->id] = (object) [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'image' => $product->base_image?->src ?? '',
                'price' => $price,
                'purchase_price' => $product->average_purchase_price ?? $price,
                'quantity' => $item['quantity'],
                'total' => $total,
                'parent_id' => $product->parent_id,
                'shipping_inside' => $product->shipping_inside,
                'shipping_outside' => $product->shipping_outside,
                'category' => $product->category,
            ];
        }

        if (empty($orderProducts)) {
            return response()->json(['message' => 'No valid products found.'], 422);
        }

        // Format phone
        $phone = $data['phone'];
        if (Str::startsWith($phone, '01')) {
            $phone = '+88' . $phone;
        } elseif (Str::startsWith($phone, '8801')) {
            $phone = '+' . $phone;
        }

        // Get or Create User
        $user = User::query()->firstOrCreate(
            ['phone_number' => $phone],
            [
                'name' => $data['name'],
                'address' => $data['address'],
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ]
        );

        // Assign Admin
        $oldOrders = Order::select(['id', 'admin_id', 'status'])->where('phone', $phone)->get();
        $adminIds = $oldOrders->pluck('admin_id')->unique()->toArray();

        if (config('app.round_robin_order_receiving')) {
            $adminQ = Admin::orderByRaw('CASE WHEN is_active = 1 THEN 0 ELSE 1 END, role_id desc, last_order_received_at asc');
            $admin = count($adminIds) > 0 ? $adminQ->whereIn('id', $adminIds)->first() ?? $adminQ->first() : $adminQ->first();
        } else {
            $adminQ = Admin::where('role_id', Admin::SALESMAN)->where('is_active', true)->inRandomOrder();
            if (count($adminIds) > 0) {
                $admin = $adminQ->whereIn('id', $adminIds)->first() ?? $adminQ->first() ?? Admin::where('is_active', true)->inRandomOrder()->first();
            } else {
                $admin = $adminQ->first() ?? Admin::where('is_active', true)->inRandomOrder()->first();
            }
        }
        $adminId = $admin->id ?? Admin::query()->inRandomOrder()->first()->id;

        $isFraud = $oldOrders->whereIn('status', ['CANCELLED', 'RETURNED', 'PAID_RETURN'])->count() > 0;
        $isRepeat = $oldOrders->count() > 0;
        
        $purchaseCost = 0;
        foreach ($orderProducts as $productItem) {
            $purchaseCost += $productItem->purchase_price * $productItem->quantity;
        }

        $order = Order::create([
            'source_id' => config('app.instant_order_forwarding') ? 0 : null,
            'admin_id' => $adminId,
            'user_id' => $user->id,
            'type' => Order::ONLINE,
            'name' => $data['name'],
            'phone' => $phone,
            'address' => $data['address'],
            'note' => $data['note'] ?? null,
            'status' => 'PENDING',
            'status_at' => now()->toDateTimeString(),
            'products' => $orderProducts,
            'data' => [
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'retail_delivery_fee' => $shippingCost,
                'advanced' => 0,
                'discount' => 0,
                'retail_discount' => 0,
                'courier' => 'Pathao',
                'city_id' => '',
                'area_id' => '',
                'weight' => 0.5,
                'packaging_charge' => 25,
                'is_fraud' => $isFraud,
                'is_repeat' => $isRepeat,
                'shipping_area' => $data['shipping'],
                'coupon_discount' => 0,
                'coupon_id' => null,
                'coupon_code' => null,
                'purchase_cost' => $purchaseCost,
                'city_name' => 'N/A',
                'area_name' => 'N/A'
            ],
        ]);
        
        if ($admin) {
            $admin->update(['last_order_received_at' => now()]);
        }

        return response()->json([
            'message' => 'Order placed successfully!',
            'order' => [
                'id' => $order->id,
                'total' => $subtotal + $shippingCost,
            ],
        ], 201);
    }

    /**
     * Transform a Product model into the shape the frontend expects.
     */
    private function transformProduct(Product $product): array
    {
        $baseImage = $product->base_image ? asset($product->base_image->src) : '/images/placeholder.jpg';

        return [
            'id' => (string) $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'category' => $product->categories->first()?->name ?? 'Uncategorized',
            'brand' => $product->brand?->name ?? '',
            'regularPrice' => (int) $product->price,
            'salePrice' => (int) $product->selling_price,
            'discountPercentage' => $product->price > 0
                ? round((($product->price - $product->selling_price) / $product->price) * 100, 2)
                : 0,
            'image' => $baseImage,
            'thumbnails' => $product->images->pluck('src')->map(fn ($src) => asset($src))->toArray(),
            'inStock' => $product->should_track ? $product->stock_count > 0 : true,
            'stockCount' => $product->should_track ? $product->stock_count : 999,
        ];
    }
}
