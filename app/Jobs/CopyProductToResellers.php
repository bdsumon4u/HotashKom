<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyProductToResellers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;
    protected $idMap = [];

    /**
     * Create a new job instance.
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Get or create a resource in reseller's database
     */
    protected function getOrCreateResource(string $table, int $sourceId, string $uniqueColumn, $uniqueValue): int
    {
        // If we already have the ID mapping, return it
        if (isset($this->idMap[$table][$sourceId])) {
            return $this->idMap[$table][$sourceId];
        }

        // First check if the resource's ID exists in source_id column
        $existingBySourceId = DB::connection('reseller')
            ->table($table)
            ->where('source_id', $sourceId)
            ->first();

        if ($existingBySourceId) {
            // Resource already exists with this source_id, store mapping and return
            $this->idMap[$table][$sourceId] = $existingBySourceId->id;
            return $existingBySourceId->id;
        }

        // If not found by source_id, check if unique column exists
        $existingByUnique = DB::connection('reseller')
            ->table($table)
            ->where($uniqueColumn, $uniqueValue)
            ->first();

        if ($existingByUnique) {
            // Update source_id in reseller's database to match original resource's ID
            DB::connection('reseller')
                ->table($table)
                ->where($uniqueColumn, $uniqueValue)
                ->update(['source_id' => $sourceId]);

            // Store the ID mapping
            $this->idMap[$table][$sourceId] = $existingByUnique->id;
            return $existingByUnique->id;
        }

        // Get the original data
        $data = DB::table($table)
            ->where('id', $sourceId)
            ->first();

        if (!$data) {
            throw new \Exception("Resource not found in source database: {$table} {$sourceId}");
        }

        // Convert to array and handle foreign keys
        $insertData = (array) $data;
        $foreignKeys = [];
        foreach ($insertData as $key => $value) {
            if (str_ends_with($key, '_id') && $value) {
                $foreignKeys[$key] = $value;
            }
        }

        // If we have foreign keys, get all related IDs in one query per table
        if (!empty($foreignKeys)) {
            $relatedIds = [];
            foreach ($foreignKeys as $key => $value) {
                // Get the table name from the foreign key
                $relatedTable = str_replace('_id', 's', $key);

                if (!isset($relatedIds[$relatedTable])) {
                    $relatedIds[$relatedTable] = [];
                }
                $relatedIds[$relatedTable][] = $value;
            }

            // Get all related IDs in one query per table
            foreach ($relatedIds as $table => $ids) {
                $existingRelated = DB::connection('reseller')
                    ->table($table)
                    ->whereIn('source_id', $ids)
                    ->get(['id', 'source_id']);

                foreach ($existingRelated as $related) {
                    if (isset($this->idMap[$table][$related->source_id])) {
                        continue;
                    }
                    $this->idMap[$table][$related->source_id] = $related->id;
                }
            }

            // Update foreign keys with new IDs
            foreach ($foreignKeys as $key => $value) {
                // Get the table name from the foreign key
                $relatedTable = str_replace('_id', 's', $key);

                if (isset($this->idMap[$relatedTable][$value])) {
                    $insertData[$key] = $this->idMap[$relatedTable][$value];
                }
            }
        }

        // Set source_id to original ID and remove id from data
        $insertData['source_id'] = $insertData['id'];
        unset($insertData['id']);

        // Insert the data and get the new auto-generated ID
        $newId = DB::connection('reseller')
            ->table($table)
            ->insertGetId($insertData);

        // Store the ID mapping
        $this->idMap[$table][$sourceId] = $newId;

        return $newId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all active resellers
        $resellers = User::where('is_active', true)->get();

        foreach ($resellers as $reseller) {
            try {
                // Connect to reseller's database using their config
                config(['database.connections.reseller' => $reseller->getDatabaseConfig()]);

                // Get the product data
                $productData = $this->product->getRawOriginal();

                // Copy brand if exists
                if ($productData['brand_id']) {
                    $brand = DB::table('brands')->where('id', $productData['brand_id'])->first();
                    $newBrandId = $this->getOrCreateResource('brands', $productData['brand_id'], 'slug', $brand->slug);
                    $productData['brand_id'] = $newBrandId;
                }

                // Copy categories
                $categoryIds = [];
                $categories = DB::table('category_product')
                    ->where('product_id', $this->product->id)
                    ->get(['category_id']);

                foreach ($categories as $category) {
                    $cat = DB::table('categories')->where('id', $category->category_id)->first();
                    $newCategoryId = $this->getOrCreateResource('categories', $category->category_id, 'slug', $cat->slug);
                    $categoryIds[] = $newCategoryId;
                }

                // Copy images
                $imageIds = [];
                $images = DB::table('image_product')
                    ->where('product_id', $this->product->id)
                    ->get(['image_id', 'img_type', 'order']);

                foreach ($images as $image) {
                    $img = DB::table('images')->where('id', $image->image_id)->first();
                    $newImageId = $this->getOrCreateResource('images', $image->image_id, 'filename', $img->filename);
                    $imageIds[] = [
                        'id' => $newImageId,
                        'img_type' => $image->img_type,
                        'order' => $image->order
                    ];
                }

                // Copy attributes and options
                $optionIds = [];
                $options = DB::table('option_product')
                    ->where('product_id', $this->product->id)
                    ->get(['option_id']);

                foreach ($options as $option) {
                    $opt = DB::table('options')->where('id', $option->option_id)->first();
                    $attr = DB::table('attributes')->where('id', $opt->attribute_id)->first();

                    $newAttrId = $this->getOrCreateResource('attributes', $attr->id, 'name', $attr->name);
                    $newOptId = $this->getOrCreateResource('options', $opt->id, 'name', $opt->name);

                    $optionIds[] = $newOptId;
                }

                // Copy main product
                $insertData = $productData;
                $insertData['source_id'] = $insertData['id'];
                unset($insertData['id']);

                $newProductId = DB::connection('reseller')
                    ->table('products')
                    ->insertGetId($insertData);

                // Copy variations if any
                $variations = DB::table('products')
                    ->where('parent_id', $this->product->id)
                    ->get();

                foreach ($variations as $variation) {
                    $varData = (array) $variation;
                    $varData['parent_id'] = $newProductId;
                    $varData['source_id'] = $varData['id'];
                    unset($varData['id']);

                    DB::connection('reseller')
                        ->table('products')
                        ->insert($varData);
                }

                // Insert category relationships
                foreach ($categoryIds as $categoryId) {
                    DB::connection('reseller')
                        ->table('category_product')
                        ->insert([
                            'product_id' => $newProductId,
                            'category_id' => $categoryId
                        ]);
                }

                // Insert image relationships
                foreach ($imageIds as $image) {
                    DB::connection('reseller')
                        ->table('image_product')
                        ->insert([
                            'product_id' => $newProductId,
                            'image_id' => $image['id'],
                            'img_type' => $image['img_type'],
                            'order' => $image['order']
                        ]);
                }

                // Insert option relationships
                foreach ($optionIds as $optionId) {
                    DB::connection('reseller')
                        ->table('option_product')
                        ->insert([
                            'product_id' => $newProductId,
                            'option_id' => $optionId
                        ]);
                }

                Log::info("Successfully copied product {$this->product->id} to reseller {$reseller->id}");

            } catch (\Exception $e) {
                Log::error("Failed to copy product {$this->product->id} to reseller {$reseller->id}: " . $e->getMessage());
                continue;
            }
        }
    }
}
