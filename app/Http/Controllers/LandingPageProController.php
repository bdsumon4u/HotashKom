<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Admin;
use App\Models\Image;
use App\Models\LandingPagePro;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\User\OrderPlaced;
use App\Services\LandingPageProTemplateRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

use function Illuminate\Support\defer;

class LandingPageProController extends Controller
{
    public function __construct(private readonly LandingPageProTemplateRegistry $templateRegistry) {}

    public function show(LandingPagePro $landingPagePro): View
    {
        abort_unless($landingPagePro->is_published, 404);

        $landingPagePro->load([
            'items' => function ($query): void {
                $query->where('is_active', true)->orderBy('sort_order');
            },
            'items.product.images',
            'items.product.variations' => function ($query): void {
                $query->orderBy('name');
            },
            'items.product.variations.options.attribute',
            'items.product.variations.images',
        ]);

        $templateView = $this->templateRegistry->viewFor($landingPagePro->template_key);

        abort_if(blank($templateView), 404);

        $sections = $this->hydrateSectionMedia($landingPagePro->mergedSectionSettings());

        return view($templateView, [
            'landingPagePro' => $landingPagePro,
            'sections' => $sections,
            'selectedProducts' => $this->buildProductCards($landingPagePro),
        ]);
    }

    private function buildProductCards(LandingPagePro $landingPagePro): Collection
    {
        return $landingPagePro->items
            ->map(function ($item): array {
                /** @var Product $product */
                $product = $item->product;

                $productImages = $product->images->pluck('src')->filter();

                $variationSource = $product->variations->where('is_active', true)->values();
                if ($variationSource->isEmpty()) {
                    $variationSource = $product->variations;
                }

                $variations = $variationSource->map(function (Product $variation): array {
                    $optionMap = $variation->options
                        ->mapWithKeys(fn ($option): array => [
                            (string) $option->attribute_id => [
                                'attribute_id' => (int) $option->attribute_id,
                                'attribute_name' => (string) data_get($option, 'attribute.name', ''),
                                'option_id' => (int) $option->id,
                                'option_name' => (string) $option->name,
                            ],
                        ])
                        ->toArray();

                    return [
                        'id' => $variation->id,
                        'name' => $variation->varName,
                        'price' => (int) $variation->selling_price,
                        'retail_price' => (int) $variation->retailPrice(),
                        'image' => $variation->base_image?->src,
                        'option_map' => $optionMap,
                    ];
                })->values();

                if ($variations->isEmpty()) {
                    return [
                        'landing_product_id' => $product->id,
                        'base_name' => $product->name,
                        'base_product_image' => $product->base_image?->src,
                        'gallery_images' => $productImages->unique()->values()->all(),
                        'free_delivery' => (bool) $item->free_delivery,
                        'cards' => [[
                            'card_id' => sprintf('%d-main', $product->id),
                            'title' => $product->name,
                            'selected_product_id' => $product->id,
                            'price' => (int) $product->selling_price,
                            'retail_price' => (int) $product->retailPrice(),
                            'image' => $product->base_image?->src,
                            'attributes' => [],
                            'variants' => [],
                            'selected' => false,
                            'qty' => 1,
                        ]],
                    ];
                }

                $colorOptionIds = $variations
                    ->flatMap(function (array $variation): Collection {
                        return collect($variation['option_map'])
                            ->filter(fn (array $option): bool => strtolower($option['attribute_name']) === 'color')
                            ->pluck('option_id');
                    })
                    ->unique()
                    ->values();

                $cards = collect();

                if ($colorOptionIds->isNotEmpty()) {
                    foreach ($colorOptionIds as $colorOptionId) {
                        $groupVariations = $variations
                            ->filter(function (array $variation) use ($colorOptionId): bool {
                                return collect($variation['option_map'])
                                    ->contains(fn (array $option): bool => strtolower($option['attribute_name']) === 'color' && $option['option_id'] === (int) $colorOptionId);
                            })
                            ->values();

                        if ($groupVariations->isEmpty()) {
                            continue;
                        }

                        $firstVariant = $groupVariations->first();
                        $colorOption = collect($firstVariant['option_map'])
                            ->first(fn (array $option): bool => strtolower($option['attribute_name']) === 'color');

                        $attributeGroup = $groupVariations
                            ->flatMap(fn (array $variation): array => array_values($variation['option_map']))
                            ->filter(fn (array $option): bool => strtolower($option['attribute_name']) !== 'color')
                            ->groupBy('attribute_id');

                        $attributes = $attributeGroup
                            ->map(function (Collection $options) use ($firstVariant): array {
                                $first = $options->first();

                                return [
                                    'attribute_id' => (int) data_get($first, 'attribute_id'),
                                    'attribute_name' => (string) data_get($first, 'attribute_name'),
                                    'options' => $options
                                        ->unique('option_id')
                                        ->map(fn (array $option): array => [
                                            'id' => (int) $option['option_id'],
                                            'name' => $option['option_name'],
                                        ])
                                        ->values()
                                        ->all(),
                                    'selected_option_id' => (int) data_get($firstVariant, 'option_map.'.data_get($first, 'attribute_id').'.option_id', 0),
                                ];
                            })
                            ->values()
                            ->all();

                        $cards->push([
                            'card_id' => sprintf('%d-color-%d', $product->id, (int) $colorOptionId),
                            'title' => $colorOption ? sprintf('%s - %s', $product->name, $colorOption['option_name']) : $product->name,
                            'selected_product_id' => $firstVariant['id'],
                            'price' => $firstVariant['price'],
                            'retail_price' => $firstVariant['retail_price'],
                            'image' => $firstVariant['image'] ?: $product->base_image?->src,
                            'attributes' => $attributes,
                            'variants' => $groupVariations
                                ->map(fn (array $variation): array => [
                                    'id' => $variation['id'],
                                    'name' => $variation['name'],
                                    'price' => $variation['price'],
                                    'retail_price' => $variation['retail_price'],
                                    'image' => $variation['image'] ?: $product->base_image?->src,
                                    'option_ids' => collect($variation['option_map'])->mapWithKeys(fn (array $option): array => [
                                        (string) $option['attribute_id'] => (int) $option['option_id'],
                                    ])->toArray(),
                                ])
                                ->values()
                                ->all(),
                            'selected' => false,
                            'qty' => 1,
                        ]);
                    }
                } else {
                    $firstVariant = $variations->first();
                    $attributeGroup = $variations
                        ->flatMap(fn (array $variation): array => array_values($variation['option_map']))
                        ->groupBy('attribute_id');

                    $attributes = $attributeGroup
                        ->map(function (Collection $options) use ($firstVariant): array {
                            $first = $options->first();

                            return [
                                'attribute_id' => (int) data_get($first, 'attribute_id'),
                                'attribute_name' => (string) data_get($first, 'attribute_name'),
                                'options' => $options
                                    ->unique('option_id')
                                    ->map(fn (array $option): array => [
                                        'id' => (int) $option['option_id'],
                                        'name' => $option['option_name'],
                                    ])
                                    ->values()
                                    ->all(),
                                'selected_option_id' => (int) data_get($firstVariant, 'option_map.'.data_get($first, 'attribute_id').'.option_id', 0),
                            ];
                        })
                        ->values()
                        ->all();

                    $cards->push([
                        'card_id' => sprintf('%d-attrs', $product->id),
                        'title' => $product->name,
                        'selected_product_id' => $firstVariant['id'],
                        'price' => $firstVariant['price'],
                        'retail_price' => $firstVariant['retail_price'],
                        'image' => $firstVariant['image'] ?: $product->base_image?->src,
                        'attributes' => $attributes,
                        'variants' => $variations
                            ->map(fn (array $variation): array => [
                                'id' => $variation['id'],
                                'name' => $variation['name'],
                                'price' => $variation['price'],
                                'retail_price' => $variation['retail_price'],
                                'image' => $variation['image'] ?: $product->base_image?->src,
                                'option_ids' => collect($variation['option_map'])->mapWithKeys(fn (array $option): array => [
                                    (string) $option['attribute_id'] => (int) $option['option_id'],
                                ])->toArray(),
                            ])
                            ->values()
                            ->all(),
                        'selected' => false,
                        'qty' => 1,
                    ]);
                }

                return [
                    'landing_product_id' => $product->id,
                    'base_name' => $product->name,
                    'base_product_image' => $product->base_image?->src,
                    'gallery_images' => $productImages->merge($variations->pluck('image'))->filter()->unique()->values()->all(),
                    'free_delivery' => (bool) $item->free_delivery,
                    'cards' => $cards->values()->all(),
                ];
            })
            ->values();
    }

    public function checkout(Request $request, LandingPagePro $landingPagePro): JsonResponse
    {
        abort_unless($landingPagePro->is_published, 404);

        $landingItems = $landingPagePro->items()
            ->where('is_active', true)
            ->get(['product_id', 'free_delivery'])
            ->keyBy('product_id');

        $validated = $request->validate([
            'name' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'address' => ['required', 'string'],
            'delivery_area' => ['required', 'in:inside,outside'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.landing_product_id' => ['required', 'integer'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $normalizedPhone = preg_replace('/[^\d]/', '', (string) $validated['phone']);
        if (Str::startsWith($normalizedPhone, '8801') && strlen($normalizedPhone) === 13) {
            $validated['phone'] = '+'.$normalizedPhone;
        } elseif (Str::startsWith($normalizedPhone, '01') && strlen($normalizedPhone) === 11) {
            $validated['phone'] = '+88'.$normalizedPhone;
        } else {
            return response()->json([
                'message' => 'সঠিক মোবাইল নাম্বার দিন (01XXXXXXXXX)।',
            ], 422);
        }

        $shippingArea = $validated['delivery_area'] === 'outside' ? 'Outside Dhaka' : 'Inside Dhaka';

        $fraud = setting('fraud');

        if (
            cacheMemo()->get('fraud:hourly:'.request()->ip()) >= ($fraud->allow_per_hour ?? 3)
            || cacheMemo()->get('fraud:hourly:'.$validated['phone']) >= ($fraud->allow_per_hour ?? 3)
            || cacheMemo()->get('fraud:daily:'.request()->ip()) >= ($fraud->allow_per_day ?? 7)
            || cacheMemo()->get('fraud:daily:'.$validated['phone']) >= ($fraud->allow_per_day ?? 7)
        ) {
            return response()->json([
                'message' => 'প্রিয় গ্রাহক, আরও অর্ডার করতে চাইলে আমাদের হেল্প লাইন '.setting('company')->phone.' নাম্বারে কল দিয়ে সরাসরি কথা বলুন।',
            ], 422);
        }

        $requestedLandingProductIds = collect($validated['items'])
            ->pluck('landing_product_id')
            ->map(fn ($id): int => (int) $id)
            ->values();

        $invalidLandingProductIds = $requestedLandingProductIds
            ->reject(fn ($id): bool => $landingItems->has((int) $id))
            ->values();

        if ($invalidLandingProductIds->isNotEmpty()) {
            return response()->json([
                'message' => 'Some selected products are not available for this landing page.',
                'invalid_product_ids' => $invalidLandingProductIds,
            ], 422);
        }

        $requestedProductIds = collect($validated['items'])
            ->pluck('product_id')
            ->map(fn ($id): int => (int) $id)
            ->values();

        $products = Product::query()
            ->whereIn('id', $requestedProductIds->all())
            ->get()
            ->keyBy('id');

        $productsPayload = [];

        foreach ($validated['items'] as $item) {
            $landingProductId = (int) $item['landing_product_id'];
            $productId = (int) $item['product_id'];
            $requestedQuantity = (int) $item['quantity'];
            $product = $products->get($productId);

            if (! $product) {
                continue;
            }

            $belongsToLandingProduct = $product->id === $landingProductId || (int) $product->parent_id === $landingProductId;
            if (! $belongsToLandingProduct || ! $landingItems->has($landingProductId)) {
                continue;
            }

            $fraudQuantity = setting('fraud')->max_qty_per_product ?? 3;
            $maxQuantity = $product->should_track ? min($product->stock_count, $fraudQuantity) : $fraudQuantity;
            $quantity = min((int) $requestedQuantity, $maxQuantity);

            if ($quantity <= 0) {
                continue;
            }

            $productData = (new ProductResource($product))->toCartItem($quantity);
            $productData['landing_free_delivery'] = (bool) data_get($landingItems->get($landingProductId), 'free_delivery', false);

            $productsPayload[$product->id] = $productData;
        }

        if (empty($productsPayload)) {
            return response()->json([
                'message' => 'No available products were selected for checkout.',
            ], 422);
        }

        $order = DB::transaction(function () use ($validated, $shippingArea, $productsPayload) {

            $user = $this->getOrCreateUser($validated);
            $oldOrders = Order::select(['id', 'admin_id', 'status'])->where('phone', $validated['phone'])->get();
            $adminIds = $oldOrders->pluck('admin_id')->unique()->toArray();

            if (config('app.round_robin_order_receiving')) {
                $adminQuery = Admin::orderByRaw('CASE WHEN is_active = 1 THEN 0 ELSE 1 END, role_id desc, last_order_received_at asc');
                $admin = count($adminIds) > 0 ? $adminQuery->whereIn('id', $adminIds)->first() ?? $adminQuery->first() : $adminQuery->first();
            } else {
                $adminQuery = Admin::where('role_id', Admin::SALESMAN)->where('is_active', true)->inRandomOrder();
                $admin = count($adminIds) > 0
                    ? $adminQuery->whereIn('id', $adminIds)->first() ?? $adminQuery->first() ?? Admin::where('is_active', true)->inRandomOrder()->first()
                    : $adminQuery->first() ?? Admin::where('is_active', true)->inRandomOrder()->first();
            }

            $orderModel = new Order;
            $subtotal = (float) collect($productsPayload)->sum(
                fn (array $item): int|float => (($item['price'] ?? 0) * ($item['quantity'] ?? 0))
            );
            $shippingCost = $orderModel->getShippingCost($productsPayload, $subtotal, $shippingArea);

            $order = Order::create([
                'source_id' => config('app.instant_order_forwarding') ? 0 : null,
                'admin_id' => $admin->id ?? Admin::query()->inRandomOrder()->first()->id,
                'user_id' => $user->id,
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'note' => '',
                'products' => $productsPayload,
                'status' => data_get(config('app.orders', []), 0, 'PENDING'),
                'status_at' => now()->toDateTimeString(),
                'data' => [
                    'courier' => 'Other',
                    'is_fraud' => $oldOrders->whereIn('status', ['CANCELLED', 'RETURNED', 'PAID_RETURN'])->count() > 0,
                    'is_repeat' => $oldOrders->count() > 0,
                    'shipping_area' => $shippingArea,
                    'shipping_cost' => $shippingCost,
                    'retail_delivery_fee' => $shippingCost,
                    'advanced' => 0,
                    'retail_discount' => 0,
                    'coupon_discount' => 0,
                    'subtotal' => $subtotal,
                    'purchase_cost' => collect($productsPayload)->sum(
                        fn (array $item): int|float => (($item['purchase_price'] ?? $item['price'] ?? 0) * ($item['quantity'] ?? 0))
                    ),
                ],
            ]);

            defer(function () use ($admin, $user, $order): void {
                $admin?->update(['last_order_received_at' => now()]);
                $user->notify(new OrderPlaced($order));

                Cache::add('fraud:hourly:'.request()->ip(), 0, now()->addHour());
                Cache::add('fraud:daily:'.request()->ip(), 0, now()->addDay());

                Cache::increment('fraud:hourly:'.request()->ip());
                Cache::increment('fraud:daily:'.request()->ip());

                Cache::add('fraud:hourly:'.$order->phone, 0, now()->addHour());
                Cache::add('fraud:daily:'.$order->phone, 0, now()->addDay());

                Cache::increment('fraud:hourly:'.$order->phone);
                Cache::increment('fraud:daily:'.$order->phone);
            });

            return $order;
        });

        if (! $order instanceof Order) {
            return response()->json([
                'message' => 'All selected products are unavailable.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'redirect_url' => route('thank-you', ['order' => $order->id]),
        ]);
    }

    private function getOrCreateUser(array $data): User
    {
        if ($user = auth('user')->user()) {
            return $user;
        }

        return User::query()->firstOrCreate(
            ['phone_number' => $data['phone']],
            array_merge(Arr::except($data, ['phone', 'items', 'delivery_area']), [
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'remember_token' => Str::random(10),
            ])
        );
    }

    private function hydrateSectionMedia(array $sections): array
    {
        $singleImageSections = ['hero', 'features', 'final_cta', 'footer'];
        $singleImageIds = collect($singleImageSections)
            ->map(fn (string $key): int => (int) data_get($sections, $key.'.image_id'))
            ->filter(fn (int $id): bool => $id > 0);

        $galleryIds = collect(data_get($sections, 'gallery.images', []))
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->values();

        $allImageIds = $singleImageIds->merge($galleryIds)->unique()->values();

        if ($allImageIds->isEmpty()) {
            return $sections;
        }

        $images = Image::query()->whereIn('id', $allImageIds)->get()->keyBy('id');

        foreach ($singleImageSections as $key) {
            $id = (int) data_get($sections, $key.'.image_id');
            $image = $images->get($id);

            if ($image) {
                data_set($sections, $key.'.image_src', $image->src);
            }
        }

        data_set(
            $sections,
            'gallery.image_urls',
            $galleryIds
                ->map(fn (int $id): ?string => $images->get($id)?->src)
                ->filter()
                ->values()
                ->all(),
        );

        return $sections;
    }
}
