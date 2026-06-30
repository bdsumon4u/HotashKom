<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Slide;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StorefrontController extends Controller
{
    /**
     * GET /api/storefront/settings
     * Returns site settings needed by the frontend (company info, delivery charges, etc.)
     */
    public function settings(): JsonResponse
    {
        $keys = ['company', 'logo', 'social', 'delivery_charge', 'free_delivery', 'scroll_text', 'call_for_order', 'gtm_id', 'pixel_ids'];
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
            ->with(['images', 'reviews.ratings', 'categories:id,name,slug', 'brand:id,name']);

        // Filter by category slug(s)
        if ($request->category) {
            $categorySlugs = (array) $request->category;
            $query->whereHas('categories', function ($q) use ($categorySlugs) {
                $q->whereIn('categories.slug', array_map('rawurldecode', $categorySlugs));
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
        $product = Product::with([
            'images',
            'reviews.ratings',
            'categories:id,name,slug',
            'brand:id,name',
            'variations.images',
            'variations.options.attribute',
        ])
            ->where('slug', rawurldecode($slug))
            ->whereIsActive(1)
            ->whereNull('parent_id')
            ->firstOrFail();

        $images = $product->images->pluck('src')->map(fn ($src) => asset($src))->toArray();
        $baseImage = $product->base_image ? asset($product->base_image->src) : ($images[0] ?? '/images/placeholder.jpg');

        $deliveryCharge = setting('delivery_charge');

        $deliveryText = setting('show_option')->productwise_delivery_charge ?? false
            ? $product->delivery_text ?? setting('delivery_text')
            : setting('delivery_text');

        $ratingData = $this->getProductRatingData($product);

        // Build attributes and variations payload (used by the frontend variation picker)
        $optionGroup = $product->variations->pluck('options')->flatten()->unique('id')->groupBy('attribute_id');
        $attributes = $optionGroup->keys()->map(function ($attrId) use ($optionGroup) {
            $attr = $optionGroup->get($attrId)->first()->attribute;

            return [
                'id' => $attr->id,
                'name' => $attr->name,
                'options' => $optionGroup->get($attrId)->map(fn ($opt) => [
                    'id' => $opt->id,
                    'name' => $opt->name,
                    'value' => $opt->value,
                ])->values(),
            ];
        })->values();

        $variations = $product->variations->map(function ($variation) {
            $variationImages = $variation->images->isNotEmpty()
                ? $variation->images->pluck('src')->map(fn ($src) => asset($src))->toArray()
                : ($variation->parent?->images->pluck('src')->map(fn ($src) => asset($src))->toArray() ?? []);

            $baseImage = $variation->images->first()
                ? asset($variation->images->first()->src)
                : (isset($variationImages[0]) ? $variationImages[0] : null);

            return [
                'id' => (string) $variation->id,
                'name' => $variation->name,
                'sku' => $variation->sku,
                'slug' => $variation->slug,
                'image' => $baseImage,
                'images' => $variationImages,
                'regularPrice' => (int) $variation->price,
                'salePrice' => (int) $variation->selling_price,
                'inStock' => $variation->should_track ? $variation->stock_count > 0 : true,
                'stockCount' => $variation->should_track ? max(0, $variation->stock_count) : -1,
                'optionIds' => $variation->options->pluck('id')->map(fn ($id) => (int) $id)->toArray(),
            ];
        })->values();

        return response()->json([
            'data' => [
                'id' => (string) $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'category' => $product->categories->first()?->name ?? 'Uncategorized',
                'categories' => $product->categories->map(fn ($cat) => [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                ])->toArray(),
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
                'stockCount' => $product->should_track ? max(0, $product->stock_count) : -1,
                'averageRating' => $ratingData['averageRating'],
                'reviewsCount' => $ratingData['reviewsCount'],
                'description' => str_replace('../../../storage', url('/storage'), $product->description ?? ''),
                'shortDescription' => $product->short_description ?? '',
                'deliveryText' => $deliveryText,
                'shippingInside' => (int) ($deliveryCharge->inside_dhaka ?? 80),
                'shippingOutside' => (int) ($deliveryCharge->outside_dhaka ?? 150),
                'attributes' => $attributes,
                'variations' => $variations,
                'hasVariations' => $product->variations->isNotEmpty(),
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
            ->when(! empty($categoryIds), function ($q) use ($categoryIds) {
                $q->whereHas('categories', function ($cq) use ($categoryIds) {
                    $cq->whereIn('categories.id', $categoryIds);
                });
            })
            ->with(['images', 'reviews.ratings'])
            ->limit(10)
            ->get()
            ->map(fn (Product $p) => $this->transformProduct($p));

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
            'items.*.variation_id' => ['nullable'],
        ]);

        $deliveryCharge = setting('delivery_charge');
        $shippingCost = $data['shipping'] === 'Inside Dhaka'
            ? (float) ($deliveryCharge->inside_dhaka ?? 80)
            : (float) ($deliveryCharge->outside_dhaka ?? 150);

        // Build products JSON. Prefer the requested variation when supplied so the
        // ordered line item matches what the customer picked in the UI.
        $orderProducts = [];
        $subtotal = 0;

        $variationIds = collect($data['items'])->pluck('variation_id')->filter()->unique()->toArray();
        $productIds = collect($data['items'])->pluck('id')->filter()->unique()->toArray();

        $variations = empty($variationIds) ? collect() : Product::with(['parent.categories'])->whereIn('id', $variationIds)->get()->keyBy('id');
        $products = empty($productIds) ? collect() : Product::with(['categories'])->whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($data['items'] as $item) {
            $variationId = $item['variation_id'] ?? null;
            $product = $variationId ? $variations->get($variationId) : null;
            if (! $product) {
                $product = $products->get($item['id']);
            }
            if (! $product) {
                continue;
            }

            $price = $product->selling_price;
            $total = $price * $item['quantity'];
            $subtotal += $total;

            $orderProducts[$product->id] = (object) [
                'id' => $product->id,
                'name' => $product->varName, // "Parent Name [Variation Name]" for variations, else product name
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
            $phone = '+88'.$phone;
        } elseif (Str::startsWith($phone, '8801')) {
            $phone = '+'.$phone;
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
                'area_name' => 'N/A',
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
        $ratingData = $this->getProductRatingData($product);

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
            'stockCount' => $product->should_track ? max(0, $product->stock_count) : -1,
            'averageRating' => $ratingData['averageRating'],
            'reviewsCount' => $ratingData['reviewsCount'],
            'shortDescription' => $product->short_description,
            'description' => str_replace('../../../storage', url('/storage'), $product->description ?? ''),
            'retail_price' => $product->retailPrice(),
        ];
    }

    /**
     * GET /api/storefront/products/{slug}/reviews
     */
    public function reviews(Product $product): JsonResponse
    {
        $reviews = $product->reviews()
            ->where('approved', true)
            ->with('user:id,name', 'ratings')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'data' => $reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'review' => $review->review,
                    'rating' => (int) ($review->ratings->where('key', 'overall')->first()?->value ?? 5),
                    'user_name' => $review->user->name ?? 'Anonymous',
                    'created_at' => $review->created_at->diffForHumans(),
                ];
            }),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    /**
     * POST /api/storefront/products/{slug}/reviews
     */
    public function submitReview(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'review' => ['required', 'string', 'min:5', 'max:1000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string'],
            'order_id' => ['required', 'string', 'max:20'],
        ]);

        $isSecretCode = $data['order_id'] === '--0' && $data['phone'] === '--0';
        $user = null;

        if ($isSecretCode) {
            // Bypass: Use random user or create guest
            $user = User::inRandomOrder()->first();
            if (! $user) {
                $user = User::create([
                    'name' => 'Guest User '.rand(1000, 9999),
                    'phone_number' => 'guest_'.rand(1000000, 9999999),
                    'password' => bcrypt(Str::random(32)),
                    'is_active' => true,
                ]);
            }
        } else {
            // Normal: Validate order exists and matches phone
            $order = Order::find($data['order_id']);
            if (! $order) {
                return response()->json(['message' => 'The order ID you provided does not exist.'], 422);
            }

            if ($order->phone !== $data['phone']) {
                return response()->json(['message' => 'The phone number does not match the order.'], 422);
            }

            // Check if order contains the product
            // Assuming products is an array or json in Order model
            $orderProducts = is_array($order->products) ? $order->products : json_decode($order->products, true);
            if (! $orderProducts || ! isset($orderProducts[$product->id])) {
                // If it's a flat array of IDs
                if (! in_array($product->id, (array) $orderProducts) && ! in_array($product->id, array_keys((array) $orderProducts))) {
                    return response()->json(['message' => 'This order does not contain the product you are reviewing.'], 422);
                }
            }

            // Find or create user
            $user = User::firstOrCreate(
                ['phone_number' => $data['phone']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt(Str::random(16)),
                    'is_active' => true,
                ]
            );
        }

        // Check if user already reviewed this product
        $existing = $product->reviews()
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You have already reviewed this product.'], 422);
        }

        // Add review
        $product->addReview([
            'review' => $data['review'],
            'approved' => $isSecretCode, // Auto-approve if secret code
            'ratings' => [
                'overall' => (int) $data['rating'],
            ],
        ], $user->id);

        $message = $isSecretCode
            ? 'Your review has been submitted and approved.'
            : 'Your review has been submitted and is pending approval.';

        return response()->json(['message' => $message]);
    }

    /**
     * GET /api/storefront/pages/{slug}
     * Returns a single page detail.
     */
    public function page(string $slug): JsonResponse
    {
        $page = Page::where('slug', rawurldecode($slug))->firstOrFail();

        return response()->json([
            'data' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'content' => $page->content,
            ],
        ]);
    }

    /**
     * GET /api/storefront/menus
     * Returns all menus with their items.
     */
    public function menus(): JsonResponse
    {
        $menus = Menu::with('menuItems')->get();

        return response()->json([
            'data' => $menus->map(fn ($menu) => [
                'id' => $menu->id,
                'name' => $menu->name,
                'slug' => $menu->slug,
                'items' => $menu->menuItems->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'href' => $item->href,
                    'order' => $item->order,
                ]),
            ]),
        ]);
    }

    /**
     * GET /api/storefront/categories/nested
     * Returns nested categories for navigation.
     */
    public function categoriesNested(): JsonResponse
    {
        $categories = Category::nested();

        return response()->json([
            'data' => $categories->map(fn ($cat) => $this->transformCategory($cat)),
        ]);
    }

    /**
     * GET /api/storefront/home-sections
     * Returns home sections definitions (without products).
     */
    public function homeSections(Request $request): JsonResponse
    {
        $sections = HomeSection::orderBy('order')->get();

        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'type' => $section->type,
            ];
        });

        return response()->json([
            'data' => $data,
        ]);
    }

    /**
     * GET /api/storefront/home-sections/{section}/products
     * Returns products for a specific home section.
     */
    public function homeSectionProducts(Request $request, HomeSection $section): JsonResponse
    {
        $products = $section->products(15); // Pass 15 for pagination

        $data = $products->getCollection()->map(fn ($p) => $this->transformProduct($p));

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

    protected function transformCategory($cat)
    {
        return [
            'id' => $cat->id,
            'name' => $cat->name,
            'slug' => $cat->slug,
            'image' => $cat->image_url,
            'children' => $cat->childrens ? $cat->childrens->map(fn ($child) => $this->transformCategory($child)) : [],
        ];
    }

    /**
     * Compute product reviews count and average rating from in-memory collection.
     */
    private function getProductRatingData(Product $product): array
    {
        $reviews = $product->reviews ?? collect();
        $reviewsCount = $reviews->count();
        $averageRating = 0.0;

        if ($reviewsCount > 0) {
            $reviews->loadMissing('ratings');
            $ratings = $reviews->flatMap->ratings->where('key', 'overall');
            $averageRating = $ratings->count() > 0 ? (float) $ratings->avg('value') : 0.0;
        }

        return [
            'averageRating' => (float) $averageRating,
            'reviewsCount' => (int) $reviewsCount,
        ];
    }

    /**
     * POST /api/storefront/save-checkout-progress
     * Saves partial checkout progress from the frontend.
     */
    public function saveCheckoutProgress(Request $request): JsonResponse
    {
        if (empty($request->all()) && !empty($request->getContent())) {
            $json = json_decode($request->getContent(), true);
            if (is_array($json)) {
                $request->merge($json);
            }
        }

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array'],
            'items.*.id' => ['required'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.variation_id' => ['nullable'],
        ]);

        $phone = $data['phone'];
        if (Str::startsWith($phone, '01')) {
            $phone = '+88'.$phone;
        } elseif (Str::startsWith($phone, '1')) {
            $phone = '+880'.$phone;
        }

        if (strlen($phone) != 14) {
            return response()->json(['message' => 'Invalid phone number.'], 422);
        }

        $items = $data['items'];
        $cartContent = collect();

        if (!empty($items)) {
            $variationIds = collect($items)->pluck('variation_id')->filter()->unique()->toArray();
            $productIds = collect($items)->pluck('id')->filter()->unique()->toArray();

            $variations = empty($variationIds) ? collect() : Product::with(['parent.categories', 'images' => function ($query) {
                $query->wherePivot('img_type', 'base')->limit(1);
            }])->whereIn('id', $variationIds)->get()->keyBy('id');
            $products = empty($productIds) ? collect() : Product::with(['categories', 'images' => function ($query) {
                $query->wherePivot('img_type', 'base')->limit(1);
            }])->whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($items as $item) {
                $variationId = $item['variation_id'] ?? null;
                $product = $variationId ? $variations->get($variationId) : null;
                if (! $product) {
                    $product = $products->get($item['id']);
                }
                if (! $product) {
                    continue;
                }

                $price = $product->selling_price;
                $slug = $product->slug;
                
                // Base image resolution
                $imageSrc = '';
                if ($product->base_image) {
                    $imageSrc = $product->base_image->src;
                } elseif ($product->parent && $product->parent->base_image) {
                    $imageSrc = $product->parent->base_image->src;
                } elseif ($product->images->isNotEmpty()) {
                    $imageSrc = $product->images->first()->src;
                } elseif ($product->parent && $product->parent->images->isNotEmpty()) {
                    $imageSrc = $product->parent->images->first()->src;
                }

                // Construct a stdClass to match Azmolla/Shoppingcart CartItem fields
                $cartItem = new \stdClass();
                $cartItem->id = $product->id;
                $cartItem->name = $product->varName; // Uses Parent [Variation] name
                $cartItem->qty = $item['quantity'];
                $cartItem->price = $price;
                
                $options = new \stdClass();
                $options->slug = $slug;
                $options->image = $imageSrc;
                $cartItem->options = $options;

                $cartContent->put($product->id, $cartItem);
            }
        }

        if ($cartContent->isEmpty()) {
            return response()->json(['message' => 'Cart is empty.'], 400);
        }

        $identifier = 'api_' . str_replace('+', '', $phone);
        $instance = 'default';

        \Illuminate\Support\Facades\DB::table('shopping_cart')->updateOrInsert(
            [
                'identifier' => $identifier,
                'instance' => $instance,
            ],
            [
                'name' => $data['name'] ?? '',
                'phone' => $phone,
                'address' => $data['address'] ?? null,
                'content' => serialize($cartContent),
                'updated_at' => now(),
            ]
        );

        // Clean up any other carts with the same phone number to avoid duplicates
        \Illuminate\Support\Facades\DB::table('shopping_cart')
            ->where('phone', $phone)
            ->where('instance', $instance)
            ->where('identifier', '!=', $identifier)
            ->delete();

        return response()->json(['message' => 'Progress saved successfully.']);
    }
}
