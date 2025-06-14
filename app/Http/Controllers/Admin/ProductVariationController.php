<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Jobs\RemoveProductVariationsFromResellers;

class ProductVariationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product): void
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product)
    {
        abort_if($request->user()->is('salesman'), 403, 'You don\'t have permission.');
        $attributes = collect($request->get('attributes'));
        $options = Option::find($attributes->flatten());

        DB::transaction(function () use ($attributes, $product, $options): void {
            try {
                // Delete all existing variations first
                $product->variations()->delete();

                // Delete variations from reseller databases
                RemoveProductVariationsFromResellers::dispatch($product->id);

                $variations = collect($attributes->first())->crossJoin(...$attributes->splice(1));
                $newVariations = collect();

                $variations->each(function ($items, $i) use ($product, $options, $newVariations): void {
                    $name = $options->filter(fn ($item): bool => in_array($item->id, $items))->pluck('name')->join('-');
                    $sku = $product->sku.'('.implode('-', $items).')';
                    $slug = $product->slug.'('.implode('-', $items).')';

                    // Create new variation
                    $variation = $product->replicate();
                    $variation->forceFill([
                        'name' => $name,
                        'sku' => $sku,
                        'slug' => $slug,
                        'parent_id' => $product->id,
                    ]);
                    $variation->save();

                    // Sync options
                    $variation->options()->sync($items);
                    $newVariations->push($variation);
                });

            } catch (\Exception $e) {
                throw $e;
            }
        });

        return back()->withSuccess('Check your variations.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product, Product $variation): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product, Product $variation): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product, Product $variation)
    {
        abort_if($request->user()->is('salesman'), 403, 'You don\'t have permission.');
        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'suggested_price' => 'nullable',
            'wholesale.quantity' => 'sometimes|array',
            'wholesale.price' => 'sometimes|array',
            'wholesale.quantity.*' => 'required|integer|gt:1',
            'wholesale.price.*' => 'required|integer|min:1',
            'should_track' => 'required|boolean',
            'sku' => 'required|unique:products,sku,'.$variation->id,
        ]);

        $validator->sometimes('stock_count', 'required|numeric', fn ($input): bool => $input->should_track == 1);

        $variation->update($validator->validate());

        // $query = "UPDATE products SET ";
        // foreach ($request->variations as $name => $variation) {
        //     $query .= "$name = CASE id ";
        //     foreach ($variation as $id => $value) {
        //         $query .= "WHEN $id THEN '{$value}' ";
        //     }
        //     $query .= "ELSE $name END, ";
        // }
        // $query = rtrim($query, ', ');

        // DB::statement($query);

        return back()->withSuccess('Variations updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, Product $variation): void
    {
        //
    }
}
