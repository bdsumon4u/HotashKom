<?php

namespace App\Http\Controllers\Admin;

use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->view();
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
        event(new ProductCreated($product = Product::create($data), $data));

        return redirect()->action([self::class, 'edit'], $product)->with('success', 'Product Has Been Created.');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
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
        $product->update($data);

        // if ($product->getChanges()) {
        //     session()->flash('success', 'Product Updated');
        // } else {
        //     session()->flash('success', 'No Field Was Changed');
        // }

        event(new ProductUpdated($product, $data));

        return redirect()->action([self::class, 'index'])->with('success', 'Product Has Been Updated. Check <a href="'.route('products.show', $product).'" target="_blank">Product</a>');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');
        $product->delete();

        return request()->ajax()
            ? true
            : back()->with('success', 'Product Has Been Deleted.');
    }
}
