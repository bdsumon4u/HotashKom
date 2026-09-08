<?php

namespace App\Actions\Tenant;

use App\Models\Attribute;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\LandingPagePro;
use App\Models\LandingPageProItem;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Option;
use App\Models\Page;
use App\Models\Product;
use App\Models\Slide;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class CloneTenantResources
{
    /**
     * Clone baseline resources to the newly created tenant according to selected options.
     *
     * @param  array{
     *     clone_products?: bool,
     *     clone_categories?: bool,
     *     clone_brands?: bool,
     *     clone_landing_pages?: bool,
     *     clone_sliders?: bool,
     *     clone_home_sections?: bool,
     *     clone_menus?: bool,
     *     clone_pages?: bool,
     *     clone_blogs?: bool,
     * }  $options
     */
    public function execute(Tenant $tenant, array $options = []): void
    {
        // Enforce dependencies
        $cloneLandingPages = ! empty($options['clone_landing_pages']);
        $cloneProducts = ! empty($options['clone_products']) || $cloneLandingPages;
        $cloneCategories = ! empty($options['clone_categories']);
        $cloneBrands = ! empty($options['clone_brands']);
        $cloneSliders = ! empty($options['clone_sliders']);
        $cloneHomeSections = ! empty($options['clone_home_sections']);
        $cloneMenus = ! empty($options['clone_menus']);
        $clonePages = ! empty($options['clone_pages']);
        $cloneBlogs = ! empty($options['clone_blogs']);

        // Determine source tenant (first existing tenant or central platform baseline)
        $referenceTenant = Tenant::query()
            ->where('id', '!=', $tenant->id)
            ->orderBy('created_at', 'asc')
            ->first();

        $sourceTenantId = $referenceTenant?->id;

        // Execute bulk cloning with model events muted for maximum speed & efficiency
        Product::withoutEvents(function () use (
            $tenant, $sourceTenantId, $cloneBrands, $cloneCategories, $cloneProducts,
            $cloneLandingPages, $cloneSliders, $cloneHomeSections, $cloneMenus, $clonePages, $cloneBlogs
        ): void {
            Category::withoutEvents(function () use (
                $tenant, $sourceTenantId, $cloneBrands, $cloneCategories, $cloneProducts,
                $cloneLandingPages, $cloneSliders, $cloneHomeSections, $cloneMenus, $clonePages, $cloneBlogs
            ): void {
                Brand::withoutEvents(function () use (
                    $tenant, $sourceTenantId, $cloneBrands, $cloneCategories, $cloneProducts,
                    $cloneLandingPages, $cloneSliders, $cloneHomeSections, $cloneMenus, $clonePages, $cloneBlogs
                ): void {
                    $brandMap = [];
                    $categoryMap = [];
                    $attributeMap = [];
                    $optionMap = [];
                    $productMap = [];

                    // 1. Clone Brands
                    if ($cloneBrands) {
                        $this->cloneBrands($tenant, $sourceTenantId, $brandMap);
                    }

                    // 2. Clone Categories
                    if ($cloneCategories) {
                        $this->cloneCategories($tenant, $sourceTenantId, $categoryMap);
                    }

                    // 3. Clone Attributes & Options (required if Products are cloned)
                    if ($cloneProducts) {
                        $this->cloneAttributesAndOptions($tenant, $sourceTenantId, $attributeMap, $optionMap);
                        $this->cloneProducts($tenant, $sourceTenantId, $cloneBrands, $cloneCategories, $brandMap, $categoryMap, $optionMap, $productMap);
                    }

                    // 4. Clone Landing Pages
                    if ($cloneLandingPages) {
                        $this->cloneLandingPages($tenant, $sourceTenantId, $productMap);
                    }

                    // 5. Clone Sliders
                    if ($cloneSliders) {
                        $this->cloneSliders($tenant, $sourceTenantId);
                    }

                    // 6. Clone Home Sections
                    if ($cloneHomeSections) {
                        $this->cloneHomeSections($tenant, $sourceTenantId, $cloneProducts, $cloneCategories, $productMap, $categoryMap);
                    }

                    // 7. Clone Menus
                    if ($cloneMenus) {
                        $this->cloneMenus($tenant, $sourceTenantId);
                    }

                    // 8. Clone Pages
                    if ($clonePages) {
                        $this->clonePages($tenant, $sourceTenantId);
                    }

                    // 9. Clone Blogs
                    if ($cloneBlogs) {
                        $this->cloneBlogs($tenant, $sourceTenantId);
                    }
                });
            });
        });
    }

    /**
     * Scope query to the source tenant or central baseline.
     * If the reference tenant has no records for this resource, fallback to central platform baseline (tenant_id IS NULL).
     */
    protected function sourceQuery(string $modelClass, ?string $sourceTenantId)
    {
        $query = $modelClass::withoutTenancy();

        if ($sourceTenantId !== null) {
            $hasRecords = (clone $query)->where('tenant_id', $sourceTenantId)->exists();
            if ($hasRecords) {
                return $query->where('tenant_id', $sourceTenantId);
            }
        }

        return $query->whereNull('tenant_id');
    }

    /**
     * Clone Brands.
     */
    protected function cloneBrands(Tenant $tenant, ?string $sourceTenantId, array &$brandMap): void
    {
        $sourceBrands = $this->sourceQuery(Brand::class, $sourceTenantId)->get();

        foreach ($sourceBrands as $sourceBrand) {
            $newBrand = new Brand;
            $newBrand->forceFill([
                'tenant_id' => $tenant->id,
                'name' => $sourceBrand->name,
                'slug' => $sourceBrand->slug,
                'image_id' => $sourceBrand->image_id,
                'is_enabled' => $sourceBrand->is_enabled,
                'content' => $sourceBrand->content,
            ]);
            $newBrand->save();

            $brandMap[$sourceBrand->id] = $newBrand->id;
        }
    }

    /**
     * Clone Categories preserving parent/child hierarchy.
     */
    protected function cloneCategories(Tenant $tenant, ?string $sourceTenantId, array &$categoryMap): void
    {
        // 1. First clone root categories (parent_id IS NULL)
        $rootCategories = $this->sourceQuery(Category::class, $sourceTenantId)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        foreach ($rootCategories as $rootCat) {
            $newCat = new Category;
            $newCat->forceFill([
                'tenant_id' => $tenant->id,
                'parent_id' => null,
                'name' => $rootCat->name,
                'slug' => $rootCat->slug,
                'image_id' => $rootCat->image_id,
                'order' => $rootCat->order,
                'is_enabled' => $rootCat->is_enabled,
                'content' => $rootCat->content,
            ]);
            $newCat->save();

            $categoryMap[$rootCat->id] = $newCat->id;
        }

        // 2. Clone child categories (parent_id IS NOT NULL)
        $childCategories = $this->sourceQuery(Category::class, $sourceTenantId)
            ->whereNotNull('parent_id')
            ->orderBy('order')
            ->get();

        foreach ($childCategories as $childCat) {
            $newCat = new Category;
            $newCat->forceFill([
                'tenant_id' => $tenant->id,
                'parent_id' => $categoryMap[$childCat->parent_id] ?? null,
                'name' => $childCat->name,
                'slug' => $childCat->slug,
                'image_id' => $childCat->image_id,
                'order' => $childCat->order,
                'is_enabled' => $childCat->is_enabled,
                'content' => $childCat->content,
            ]);
            $newCat->save();

            $categoryMap[$childCat->id] = $newCat->id;
        }
    }

    /**
     * Clone Attributes and their Options.
     */
    protected function cloneAttributesAndOptions(Tenant $tenant, ?string $sourceTenantId, array &$attributeMap, array &$optionMap): void
    {
        $sourceAttributes = $this->sourceQuery(Attribute::class, $sourceTenantId)->with('options')->get();

        foreach ($sourceAttributes as $sourceAttr) {
            $newAttr = new Attribute;
            $newAttr->forceFill([
                'tenant_id' => $tenant->id,
                'name' => $sourceAttr->name,
            ]);
            $newAttr->save();

            $attributeMap[$sourceAttr->id] = $newAttr->id;

            $sourceOptions = $this->sourceQuery(Option::class, $sourceTenantId)
                ->where('attribute_id', $sourceAttr->id)
                ->get();

            foreach ($sourceOptions as $sourceOption) {
                $newOption = new Option;
                $newOption->forceFill([
                    'tenant_id' => $tenant->id,
                    'attribute_id' => $newAttr->id,
                    'name' => $sourceOption->name,
                    'value' => $sourceOption->value ?? $sourceOption->name,
                ]);
                $newOption->save();

                $optionMap[$sourceOption->id] = $newOption->id;
            }
        }
    }

    /**
     * Clone Products (Roots & Variations), Pivots, Images reuse without duplicating image records.
     * High-performance batch implementation with pre-fetched pivots and bulk inserts.
     */
    protected function cloneProducts(
        Tenant $tenant,
        ?string $sourceTenantId,
        bool $cloneBrands,
        bool $cloneCategories,
        array $brandMap,
        array $categoryMap,
        array $optionMap,
        array &$productMap
    ): void {
        $rootProducts = $this->sourceQuery(Product::class, $sourceTenantId)
            ->withoutGlobalScopes()
            ->whereNull('parent_id')
            ->get();

        $variationProducts = $this->sourceQuery(Product::class, $sourceTenantId)
            ->withoutGlobalScopes()
            ->whereNotNull('parent_id')
            ->get();

        $allSourceProductIds = $rootProducts->pluck('id')->merge($variationProducts->pluck('id'))->all();

        // 1. Pre-fetch all pivot relations in bulk (3 single queries instead of thousands)
        $allImagePivots = ! empty($allSourceProductIds)
            ? DB::table('image_product')->whereIn('product_id', $allSourceProductIds)->get()->groupBy('product_id')
            : collect();

        $allCategoryPivots = ($cloneCategories && ! empty($allSourceProductIds))
            ? DB::table('category_product')->whereIn('product_id', $allSourceProductIds)->get()->groupBy('product_id')
            : collect();

        $allOptionPivots = ! empty($allSourceProductIds)
            ? DB::table('option_product')->whereIn('product_id', $allSourceProductIds)->get()->groupBy('product_id')
            : collect();

        $newImagePivotRows = [];
        $newCategoryPivotRows = [];
        $newOptionPivotRows = [];
        $now = now();

        // 2. Clone root products
        foreach ($rootProducts as $sourceProduct) {
            $newProduct = new Product;
            $newProduct->forceFill([
                'tenant_id' => $tenant->id,
                'parent_id' => null,
                'brand_id' => ($cloneBrands && $sourceProduct->brand_id) ? ($brandMap[$sourceProduct->brand_id] ?? null) : null,
                'name' => $sourceProduct->name,
                'slug' => $sourceProduct->slug,
                'description' => $sourceProduct->description,
                'short_description' => $sourceProduct->short_description,
                'price' => $sourceProduct->price,
                'average_purchase_price' => $sourceProduct->average_purchase_price,
                'selling_price' => $sourceProduct->selling_price,
                'suggested_price' => $sourceProduct->suggested_price,
                'wholesale' => $sourceProduct->getRawOriginal('wholesale'),
                'sku' => $sourceProduct->sku,
                'should_track' => $sourceProduct->should_track,
                'stock_count' => $sourceProduct->stock_count,
                'desc_img' => $sourceProduct->desc_img,
                'desc_img_pos' => $sourceProduct->desc_img_pos,
                'is_active' => $sourceProduct->is_active,
                'hot_sale' => $sourceProduct->hot_sale,
                'new_arrival' => $sourceProduct->new_arrival,
                'shipping_inside' => $sourceProduct->getRawOriginal('shipping_inside'),
                'shipping_outside' => $sourceProduct->getRawOriginal('shipping_outside'),
                'delivery_charges' => $sourceProduct->delivery_charges,
                'delivery_text' => $sourceProduct->delivery_text,
                'packaging_charge' => $sourceProduct->packaging_charge,
            ]);
            $newProduct->save();

            $productMap[$sourceProduct->id] = $newProduct->id;

            // Prepare image pivots
            if (isset($allImagePivots[$sourceProduct->id])) {
                foreach ($allImagePivots[$sourceProduct->id] as $img) {
                    $newImagePivotRows[] = [
                        'product_id' => $newProduct->id,
                        'image_id' => $img->image_id,
                        'img_type' => $img->img_type,
                        'order' => $img->order ?? 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            // Prepare category pivots
            if ($cloneCategories && isset($allCategoryPivots[$sourceProduct->id])) {
                foreach ($allCategoryPivots[$sourceProduct->id] as $cat) {
                    if (isset($categoryMap[$cat->category_id])) {
                        $newCategoryPivotRows[] = [
                            'product_id' => $newProduct->id,
                            'category_id' => $categoryMap[$cat->category_id],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }

            // Prepare option pivots
            if (isset($allOptionPivots[$sourceProduct->id])) {
                foreach ($allOptionPivots[$sourceProduct->id] as $opt) {
                    if (isset($optionMap[$opt->option_id])) {
                        $newOptionPivotRows[] = [
                            'product_id' => $newProduct->id,
                            'option_id' => $optionMap[$opt->option_id],
                        ];
                    }
                }
            }
        }

        // 3. Clone variation products
        foreach ($variationProducts as $sourceVar) {
            $newParentId = $productMap[$sourceVar->parent_id] ?? null;
            if (! $newParentId) {
                continue;
            }

            $newVar = new Product;
            $newVar->forceFill([
                'tenant_id' => $tenant->id,
                'parent_id' => $newParentId,
                'brand_id' => ($cloneBrands && $sourceVar->brand_id) ? ($brandMap[$sourceVar->brand_id] ?? null) : null,
                'name' => $sourceVar->name,
                'slug' => $sourceVar->slug,
                'description' => $sourceVar->description,
                'short_description' => $sourceVar->short_description,
                'price' => $sourceVar->price,
                'average_purchase_price' => $sourceVar->average_purchase_price,
                'selling_price' => $sourceVar->selling_price,
                'suggested_price' => $sourceVar->suggested_price,
                'wholesale' => $sourceVar->getRawOriginal('wholesale'),
                'sku' => $sourceVar->sku,
                'should_track' => $sourceVar->should_track,
                'stock_count' => $sourceVar->stock_count,
                'desc_img' => $sourceVar->desc_img,
                'desc_img_pos' => $sourceVar->desc_img_pos,
                'is_active' => $sourceVar->is_active,
                'hot_sale' => $sourceVar->hot_sale,
                'new_arrival' => $sourceVar->new_arrival,
                'shipping_inside' => $sourceVar->getRawOriginal('shipping_inside'),
                'shipping_outside' => $sourceVar->getRawOriginal('shipping_outside'),
                'delivery_charges' => $sourceVar->delivery_charges,
                'delivery_text' => $sourceVar->delivery_text,
                'packaging_charge' => $sourceVar->packaging_charge,
            ]);
            $newVar->save();

            $productMap[$sourceVar->id] = $newVar->id;

            // Prepare image pivots
            if (isset($allImagePivots[$sourceVar->id])) {
                foreach ($allImagePivots[$sourceVar->id] as $img) {
                    $newImagePivotRows[] = [
                        'product_id' => $newVar->id,
                        'image_id' => $img->image_id,
                        'img_type' => $img->img_type,
                        'order' => $img->order ?? 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            // Prepare option pivots for variation
            if (isset($allOptionPivots[$sourceVar->id])) {
                foreach ($allOptionPivots[$sourceVar->id] as $opt) {
                    if (isset($optionMap[$opt->option_id])) {
                        $newOptionPivotRows[] = [
                            'product_id' => $newVar->id,
                            'option_id' => $optionMap[$opt->option_id],
                        ];
                    }
                }
            }
        }

        // 4. Bulk chunked insertion of all pivots
        if (! empty($newImagePivotRows)) {
            foreach (array_chunk($newImagePivotRows, 500) as $chunk) {
                DB::table('image_product')->insert($chunk);
            }
        }

        if (! empty($newCategoryPivotRows)) {
            foreach (array_chunk($newCategoryPivotRows, 500) as $chunk) {
                DB::table('category_product')->insert($chunk);
            }
        }

        if (! empty($newOptionPivotRows)) {
            foreach (array_chunk($newOptionPivotRows, 500) as $chunk) {
                DB::table('option_product')->insert($chunk);
            }
        }
    }

    /**
     * Clone Landing Pages and items in bulk.
     */
    protected function cloneLandingPages(Tenant $tenant, ?string $sourceTenantId, array $productMap): void
    {
        $sourceLandingPages = $this->sourceQuery(LandingPagePro::class, $sourceTenantId)->with('items')->get();
        if ($sourceLandingPages->isEmpty()) {
            return;
        }

        $allSourceLpIds = $sourceLandingPages->pluck('id')->all();
        $allItems = $this->sourceQuery(LandingPageProItem::class, $sourceTenantId)
            ->whereIn('landing_page_pro_id', $allSourceLpIds)
            ->get()
            ->groupBy('landing_page_pro_id');

        $newItemRows = [];
        $now = now();

        foreach ($sourceLandingPages as $sourceLp) {
            $newLp = new LandingPagePro;
            $newLp->forceFill([
                'tenant_id' => $tenant->id,
                'title' => $sourceLp->title,
                'slug' => $sourceLp->slug,
                'template_key' => $sourceLp->template_key,
                'is_published' => $sourceLp->is_published,
                'seo' => $sourceLp->seo,
                'section_settings' => $sourceLp->section_settings,
                'published_at' => $sourceLp->published_at,
            ]);
            $newLp->save();

            if (isset($allItems[$sourceLp->id])) {
                foreach ($allItems[$sourceLp->id] as $sourceItem) {
                    $newProductId = $productMap[$sourceItem->product_id] ?? null;
                    if (! $newProductId) {
                        continue;
                    }

                    $newItemRows[] = [
                        'tenant_id' => $tenant->id,
                        'landing_page_pro_id' => $newLp->id,
                        'product_id' => $newProductId,
                        'free_delivery' => $sourceItem->free_delivery ? 1 : 0,
                        'is_active' => $sourceItem->is_active ? 1 : 0,
                        'sort_order' => $sourceItem->sort_order ?? 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (! empty($newItemRows)) {
            foreach (array_chunk($newItemRows, 500) as $chunk) {
                DB::table('landing_page_pro_items')->insert($chunk);
            }
        }
    }

    /**
     * Clone Sliders / Hero Banners.
     */
    protected function cloneSliders(Tenant $tenant, ?string $sourceTenantId): void
    {
        $sourceSlides = $this->sourceQuery(Slide::class, $sourceTenantId)->get();

        foreach ($sourceSlides as $sourceSlide) {
            $newSlide = new Slide;
            $newSlide->forceFill([
                'tenant_id' => $tenant->id,
                'mobile_src' => $sourceSlide->mobile_src,
                'desktop_src' => $sourceSlide->desktop_src,
                'title' => $sourceSlide->title,
                'text' => $sourceSlide->text,
                'btn_name' => $sourceSlide->btn_name,
                'btn_href' => $sourceSlide->btn_href,
                'is_active' => $sourceSlide->is_active,
                'object_fit' => $sourceSlide->object_fit,
            ]);
            $newSlide->save();
        }
    }

    /**
     * Clone Home Sections.
     */
    protected function cloneHomeSections(
        Tenant $tenant,
        ?string $sourceTenantId,
        bool $cloneProducts,
        bool $cloneCategories,
        array $productMap,
        array $categoryMap
    ): void {
        $sourceSections = $this->sourceQuery(HomeSection::class, $sourceTenantId)->get();
        if ($sourceSections->isEmpty()) {
            return;
        }

        $allSecIds = $sourceSections->pluck('id')->all();
        $allCatHomeSections = $cloneCategories
            ? DB::table('category_home_section')->whereIn('home_section_id', $allSecIds)->get()->groupBy('home_section_id')
            : collect();

        $newCatSectionRows = [];
        $now = now();

        foreach ($sourceSections as $sourceSec) {
            $newItems = [];
            if ($cloneProducts && is_array($sourceSec->items)) {
                $newItems = array_values(array_filter(array_map(
                    fn ($pid) => $productMap[$pid] ?? null,
                    $sourceSec->items
                )));
            }

            $newSec = new HomeSection;
            $newSec->forceFill([
                'tenant_id' => $tenant->id,
                'title' => $sourceSec->title,
                'type' => $sourceSec->type,
                'items' => $newItems,
                'order' => $sourceSec->order,
                'data' => $sourceSec->data,
                'content' => $sourceSec->content,
            ]);
            $newSec->save();

            if ($cloneCategories && isset($allCatHomeSections[$sourceSec->id])) {
                foreach ($allCatHomeSections[$sourceSec->id] as $catSection) {
                    if (isset($categoryMap[$catSection->category_id])) {
                        $newCatSectionRows[] = [
                            'home_section_id' => $newSec->id,
                            'category_id' => $categoryMap[$catSection->category_id],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
        }

        if (! empty($newCatSectionRows)) {
            foreach (array_chunk($newCatSectionRows, 500) as $chunk) {
                DB::table('category_home_section')->insert($chunk);
            }
        }
    }

    /**
     * Clone Menus and Menu Items in bulk.
     */
    protected function cloneMenus(Tenant $tenant, ?string $sourceTenantId): void
    {
        $sourceMenus = $this->sourceQuery(Menu::class, $sourceTenantId)->with('menuItems')->get();
        if ($sourceMenus->isEmpty()) {
            return;
        }

        $allMenuIds = $sourceMenus->pluck('id')->all();
        $allItems = $this->sourceQuery(MenuItem::class, $sourceTenantId)
            ->whereIn('menu_id', $allMenuIds)
            ->get()
            ->groupBy('menu_id');

        $newMenuItemRows = [];
        $now = now();

        foreach ($sourceMenus as $sourceMenu) {
            $newMenu = new Menu;
            $newMenu->forceFill([
                'tenant_id' => $tenant->id,
                'name' => $sourceMenu->name,
                'slug' => $sourceMenu->slug,
            ]);
            $newMenu->save();

            if (isset($allItems[$sourceMenu->id])) {
                foreach ($allItems[$sourceMenu->id] as $sourceItem) {
                    $newMenuItemRows[] = [
                        'tenant_id' => $tenant->id,
                        'menu_id' => $newMenu->id,
                        'name' => $sourceItem->name,
                        'href' => $sourceItem->href,
                        'order' => $sourceItem->order ?? 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (! empty($newMenuItemRows)) {
            foreach (array_chunk($newMenuItemRows, 500) as $chunk) {
                DB::table('menu_items')->insert($chunk);
            }
        }
    }

    /**
     * Clone Custom Pages.
     */
    protected function clonePages(Tenant $tenant, ?string $sourceTenantId): void
    {
        $sourcePages = $this->sourceQuery(Page::class, $sourceTenantId)->get();

        foreach ($sourcePages as $sourcePage) {
            $newPage = new Page;
            $newPage->forceFill([
                'tenant_id' => $tenant->id,
                'title' => $sourcePage->title,
                'slug' => $sourcePage->slug,
                'content' => $sourcePage->content,
            ]);
            $newPage->save();
        }
    }

    /**
     * Clone Blogs & Articles.
     */
    protected function cloneBlogs(Tenant $tenant, ?string $sourceTenantId): void
    {
        $sourceBlogs = $this->sourceQuery(Blog::class, $sourceTenantId)->get();

        foreach ($sourceBlogs as $sourceBlog) {
            $newBlog = new Blog;
            $newBlog->forceFill([
                'tenant_id' => $tenant->id,
                'title' => $sourceBlog->title,
                'slug' => $sourceBlog->slug,
                'content' => $sourceBlog->content,
                'image' => $sourceBlog->image,
            ]);
            $newBlog->save();
        }
    }
}
