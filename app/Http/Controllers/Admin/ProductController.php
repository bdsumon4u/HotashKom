<?php

namespace App\Http\Controllers\Admin;

use App\Events\ProductUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Traits\PreventsSourcedResourceDeletion;

class ProductController extends Controller
{
    use PreventsSourcedResourceDeletion;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stats = \App\Models\Product::stockStatistics();

        return $this->view($stats);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        abort_if($request->user()->is('salesman'), 403, 'You don\'t have permission.');
        $data = $request->validationData();

        // Create the product
        $product = Product::create($data);

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
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        $product->load(['variations' => fn ($query) => $query->with('parent', 'options')]);

        return $this->view(compact('product'), '', [
            'categories' => Category::nested(),
            'brands' => Brand::cached(),
            'attributes' => Attribute::with('options')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        abort_if($request->user()->is('salesman'), 403, 'You don\'t have permission.');
        $data = $request->validationData();

        // Check if price fields are being updated before saving
        $priceFields = ['price', 'selling_price', 'average_purchase_price', 'suggested_price'];
        $priceFieldsChanged = false;
        $fieldsToUpdate = [];

        foreach ($priceFields as $field) {
            if (isset($data[$field]) && $data[$field] != $product->$field) {
                $priceFieldsChanged = true;
                $fieldsToUpdate[$field] = $data[$field];
            }
        }

        $product->update($data);

        // Sync price changes to variations if price fields were updated
        if ($priceFieldsChanged && ! $product->parent_id && $product->variations()->count() > 0) {
            $this->syncPriceChangesToVariations($product, $fieldsToUpdate);
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
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
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
            $product->categories()->sync($data['categories']);
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
        \App\Jobs\CopyProductToResellers::dispatch($product);
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
}
