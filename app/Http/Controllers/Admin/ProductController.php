<?php

namespace App\Http\Controllers\Admin;

use App\Events\ProductUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductSeoRequest;
use App\Jobs\CopyProductToResellers;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Traits\PreventsSourcedResourceDeletion;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    use PreventsSourcedResourceDeletion;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $stats = Product::stockStatistics();

        return $this->view($stats);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        return $this->view([
            'categories' => Category::nested(),
            'brands' => Brand::all(),
            'product' => new Product,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(ProductRequest $request)
    {
        abort_if($request->user()->is('salesman'), 403, 'You don\'t have permission.');
        $data = $request->validationData();

        // Create the product
        try {
            $product = Product::create($data);
        } catch (QueryException $e) {
            $this->handleDatabaseStringException($e);
        }

        // Handle relationships and dispatch copy job
        $this->handleProductRelationships($product, $data);

        return redirect()->action([static::class, 'edit'], $product)->with('success', 'Product Has Been Created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Product $product)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        $product->load(['images', 'variations' => fn ($query) => $query->with('parent', 'options')]);

        return $this->view(compact('product'), '', [
            'categories' => Category::nested(),
            'brands' => Brand::cached(),
            'attributes' => cacheMemo()->remember('attributes_with_options', now()->addHours(24), fn () => Attribute::with('options')->get()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        abort_if($request->user()->is('salesman'), 403, 'You don\'t have permission.');
        $data = $request->validationData();

        if (! isset($data['wholesale'])) {
            $data['wholesale'] = [];
        }

        // Check if price fields are being updated before saving
        $priceFields = ['price', 'selling_price', 'average_purchase_price', 'suggested_price', 'wholesale'];
        $priceFieldsChanged = false;
        $fieldsToUpdate = [];

        foreach ($priceFields as $field) {
            if (isset($data[$field]) && $data[$field] != $product->$field) {
                $priceFieldsChanged = true;
                $fieldsToUpdate[$field] = $data[$field];
            }
        }

        try {
            $product->update($data);
        } catch (QueryException $e) {
            $this->handleDatabaseStringException($e);
        }

        // Sync price changes to variations if price fields were updated
        if ($priceFieldsChanged && ! $product->parent_id && $product->variations()->count() > 0) {
            $this->syncPriceChangesToVariations($product, $fieldsToUpdate);
        }

        // Sync stock_group to variations if parent product has variations
        if (! $product->parent_id && $product->variations()->count() > 0) {
            $product->variations()->update([
                'stock_group' => $product->stock_group,
            ]);
        }

        // Handle relationships and dispatch copy job
        $this->handleProductRelationships($product, $data);

        // if ($product->getChanges()) {
        //     session()->flash('success', 'Product Updated');
        // } else {
        //     session()->flash('success', 'No Field Was Changed');
        // }

        event(new ProductUpdated($product, $data));

        return redirect()
            ->action([static::class, 'index'])
            ->with('success', 'Product Has Been Updated. <a href="'.route('products.show', $product).'" target="_blank">View the Product</a> or <a href="'.route('admin.products.edit', $product).'">Edit the Product</a> again.');
    }

    /**
     * Update the SEO settings for a product.
     *
     * @return Response
     */
    public function updateSeo(ProductSeoRequest $request, Product $product)
    {
        abort_if($request->user()->is('salesman'), 403, 'You don\'t have permission.');

        $seoData = $request->input('seo', []);

        // Remove empty values
        $seoData = array_filter($seoData, fn ($value): bool => ! empty($value));

        if (! empty($seoData)) {
            $product->seo()->updateOrCreate([], $seoData);
        } else {
            // If all SEO fields are empty, delete SEO data
            $product->seo?->delete();
        }

        return back()->with('success', 'SEO Settings Have Been Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Product $product)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');

        if (($result = $this->preventSourcedResourceDeletion($product)) !== true) {
            return $result;
        }

        $product->delete();

        return request()->ajax()
            ? true
            : back()->with('success', 'Product Has Been Deleted.');
    }

    /**
     * Handle product relationships and dispatch copy job
     */
    private function handleProductRelationships(Product $product, array $data): void
    {
        // Handle categories
        if (isset($data['categories'])) {
            // Get old categories before sync to clear their specific caches
            $oldCategoryIds = $product->categories->pluck('id')->toArray();
            $product->categories()->sync($data['categories']);

            // Clear category-specific filter data for both old and new categories
            $allCategoryIds = array_unique(array_merge($oldCategoryIds, $data['categories']));
            foreach ($allCategoryIds as $categoryId) {
                cacheMemo()->forget('product_filter_data:category:'.$categoryId);
            }
        }

        // Handle images
        if (isset($data['base_image'])) {
            $order = 0;
            $images = [$data['base_image'] => ['img_type' => 'base']];

            if (isset($data['additional_images'])) {
                foreach ($data['additional_images'] as $additional_image) {
                    if ($additional_image != $data['base_image']) {
                        $images[$additional_image] = ['img_type' => 'additional', 'order' => ++$order];
                    }
                }
            }

            $product->images()->sync($images);
        }

        // Dispatch copy job after all relationships are established
        dispatch(new CopyProductToResellers($product));
    }

    /**
     * Sync price changes from parent product to all its variations
     */
    private function syncPriceChangesToVariations(Product $product, array $fieldsToUpdate): void
    {
        if ($product->parent_id || empty($fieldsToUpdate)) {
            return;
        }

        // Update all variations with the changed price fields
        $product->variations()->update($fieldsToUpdate);
    }

    /**
     * Handle database string-related exceptions (e.g. charset/emoji or data too long) as validation errors.
     *
     * @throws ValidationException
     */
    private function handleDatabaseStringException(QueryException $e): void
    {
        $message = $e->getMessage();
        $code = (int) $e->getCode();

        // 1366: Incorrect string value (e.g. 4-byte UTF-8 emojis on a utf8mb3/latin1 table)
        if ($code === 22007 || str_contains($message, '1366 Incorrect string value')) {
            $field = 'short_description';
            if (preg_match('/for column [`\']?(?:[^`\']*\.)*([a-zA-Z0-9_]+)[`\']? at row/i', $message, $matches)) {
                $field = $matches[1];
            }

            throw ValidationException::withMessages([
                $field => 'The '.str_replace('_', ' ', $field).' contains emojis or special characters not supported by your database table character set. Please update the table charset to utf8mb4 or remove unsupported emojis.',
            ]);
        }

        // 1406: Data too long for column
        if ($code === 22001 || str_contains($message, '1406 Data too long')) {
            $field = 'short_description';
            if (preg_match('/for column [`\']?(?:[^`\']*\.)*([a-zA-Z0-9_]+)[`\']? at row/i', $message, $matches)) {
                $field = $matches[1];
            }

            throw ValidationException::withMessages([
                $field => 'The '.str_replace('_', ' ', $field).' is too long for the database.',
            ]);
        }

        throw $e;
    }
}
