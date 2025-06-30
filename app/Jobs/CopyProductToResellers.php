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

        if (! $data) {
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
        if (! empty($foreignKeys)) {
            $relatedIds = [];
            foreach ($foreignKeys as $key => $value) {
                // Get the table name from the foreign key
                $relatedTable = str_replace('_id', 's', $key);

                if (! isset($relatedIds[$relatedTable])) {
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

        // Use Product model for insertion to handle encoding properly
        if ($table === 'products') {
            $newModel = Product::on('reseller')->create($insertData);
            $newId = $newModel->id;
        } else {
            // Use DB facade for other tables
            $newId = DB::connection('reseller')
                ->table($table)
                ->insertGetId($insertData);
        }

        // Store the ID mapping
        $this->idMap[$table][$sourceId] = $newId;

        return $newId;
    }

    /**
     * Generate a unique value for a column in the reseller's products table
     */
    protected function getUniqueValue(string $column, string $value): string
    {
        $baseValue = $value;
        $suffix = '-oninda';
        $i = 1;
        $newValue = $baseValue;
        while (DB::connection('reseller')->table('products')->where($column, $newValue)->exists()) {
            $newValue = $baseValue.$suffix.($i > 1 ? "-$i" : '');
            $i++;
        }

        return $newValue;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all active resellers with database configuration
        $resellers = User::where('is_active', true)
            ->whereNotNull('db_password')
            ->where('db_password', '!=', '')
            ->inRandomOrder()
            ->get();

        foreach ($resellers as $reseller) {
            try {
                $this->idMap = [];
                // Connect to reseller's database using their config
                config(['database.connections.reseller' => $reseller->getDatabaseConfig()]);

                // Purge and reconnect to ensure fresh connection
                DB::purge('reseller');
                DB::reconnect('reseller');

                // Copy brand if exists
                if (! empty($this->product->brand_id)) {
                    $brand = DB::table('brands')->where('id', $this->product->brand_id)->first();
                    if ($brand) {
                        $newBrandId = $this->getOrCreateResource('brands', $this->product->brand_id, 'slug', $brand->slug);
                        $this->product->brand_id = $newBrandId;
                    } else {
                        $this->product->brand_id = null;
                    }
                }

                // Copy categories
                $categoryIds = [];
                $categories = DB::table('category_product')
                    ->where('product_id', $this->product->id)
                    ->get(['category_id']);

                foreach ($categories as $category) {
                    $cat = DB::table('categories')->where('id', $category->category_id)->first();
                    if ($cat) {
                        $newCategoryId = $this->getOrCreateResource('categories', $category->category_id, 'slug', $cat->slug);
                        $categoryIds[] = $newCategoryId;
                    }
                }

                // Copy images
                $imageIds = [];
                $images = DB::table('image_product')
                    ->where('product_id', $this->product->id)
                    ->get(['image_id', 'img_type', 'order']);

                foreach ($images as $image) {
                    $img = DB::table('images')->where('id', $image->image_id)->first();
                    if ($img) {
                        $newImageId = $this->getOrCreateResource('images', $image->image_id, 'filename', $img->filename);
                        $imageIds[] = [
                            'id' => $newImageId,
                            'img_type' => $image->img_type,
                            'order' => $image->order,
                        ];
                    }
                }

                // Copy attributes and options
                $optionIds = [];
                $options = DB::table('option_product')
                    ->where('product_id', $this->product->id)
                    ->get(['option_id']);

                foreach ($options as $option) {
                    $opt = DB::table('options')->where('id', $option->option_id)->first();
                    if ($opt) {
                        $attr = DB::table('attributes')->where('id', $opt->attribute_id)->first();
                        if ($attr) {
                            $newAttrId = $this->getOrCreateResource('attributes', $attr->id, 'name', $attr->name);
                            $newOptId = $this->getOrCreateResource('options', $opt->id, 'name', $opt->name);
                            $optionIds[] = $newOptId;
                        }
                    }
                }

                // Copy main product
                $productData = $this->product->getAttributes();
                $productData['source_id'] = $productData['id'];
                unset($productData['id']);

                // Check if product already exists by source_id first
                $existingBySourceId = DB::connection('reseller')
                    ->table('products')
                    ->where('source_id', $this->product->id)
                    ->first();

                if ($existingBySourceId) {
                    // Product already exists with this source_id, use existing ID
                    $newProductId = $existingBySourceId->id;
                    $this->idMap['products'][$this->product->id] = $newProductId;
                } else {
                    // Check if product with same slug already exists
                    $existingBySlug = DB::connection('reseller')
                        ->table('products')
                        ->where('slug', $productData['slug'])
                        ->first();

                    if ($existingBySlug) {
                        // Update existing product's source_id to link it to this source product
                        DB::connection('reseller')
                            ->table('products')
                            ->where('id', $existingBySlug->id)
                            ->update(['source_id' => $this->product->id]);
                        $newProductId = $existingBySlug->id;
                        $this->idMap['products'][$this->product->id] = $newProductId;
                    } else {
                        // Generate unique SKU only (no slug modification needed)
                        $productData['sku'] = $this->getUniqueValue('sku', $productData['sku']);

                        // Use Product model for insertion to handle encoding properly
                        info('Creating product', $productData);
                        $newProduct = Product::on('reseller')->create($productData);
                        $newProductId = $newProduct->id;
                        $this->idMap['products'][$this->product->id] = $newProductId;
                    }
                }

                // Copy variations if any
                $variations = DB::table('products')
                    ->where('parent_id', $this->product->id)
                    ->get();

                foreach ($variations as $variation) {
                    $varData = (array) $variation;
                    $varData['parent_id'] = $newProductId;
                    $varData['source_id'] = $varData['id'];
                    unset($varData['id']);

                    // Check if variation already exists by source_id first
                    $existingVarBySourceId = DB::connection('reseller')
                        ->table('products')
                        ->where('source_id', $variation->id)
                        ->first();

                    if ($existingVarBySourceId) {
                        // Variation already exists with this source_id, skip
                        continue;
                    }

                    // Check if variation with same slug already exists
                    $existingVarBySlug = DB::connection('reseller')
                        ->table('products')
                        ->where('slug', $varData['slug'])
                        ->first();

                    if ($existingVarBySlug) {
                        // Update existing variation's source_id
                        DB::connection('reseller')
                            ->table('products')
                            ->where('id', $existingVarBySlug->id)
                            ->update(['source_id' => $variation->id]);
                    } else {
                        // Generate unique SKU only for new variations
                        $varData['sku'] = $this->getUniqueValue('sku', $varData['sku']);

                        // Use Product model for insertion to handle encoding properly
                        Product::on('reseller')->create($varData);
                    }
                }

                // Insert category relationships
                foreach ($categoryIds as $categoryId) {
                    DB::connection('reseller')
                        ->table('category_product')
                        ->insert([
                            'product_id' => $newProductId,
                            'category_id' => $categoryId,
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
                            'order' => $image['order'],
                        ]);
                }

                // Insert option relationships
                foreach ($optionIds as $optionId) {
                    DB::connection('reseller')
                        ->table('option_product')
                        ->insert([
                            'product_id' => $newProductId,
                            'option_id' => $optionId,
                        ]);
                }

                Log::info("Successfully copied product {$this->product->id} to reseller {$reseller->id} [".DB::connection('reseller')->getDatabaseName().']');

            } catch (\Exception $e) {
                Log::error("Failed to copy product {$this->product->id} to reseller {$reseller->id}: ".$e->getMessage().' at line '.$e->getLine().' in '.$e->getFile());

                continue;
            }
        }
    }
}
