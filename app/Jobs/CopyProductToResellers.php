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

    public $tries = 3;

    public $backoff = [10, 30, 60]; // Retry delays in seconds

    public $timeout = 600;

    protected $idMap = [];

    /**
     * Create a new job instance.
     */
    public function __construct(protected \App\Models\Product $product) {}

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Product copy job failed for product {$this->product->id}: ".$exception->getMessage());
    }

    /**
     * Retry the job if it fails due to connection issues
     */
    public function backoff(\Throwable $exception): int
    {
        if ($this->attempts() >= $this->tries) {
            return 0; // Don't retry anymore
        }

        // Return the backoff delay for this attempt
        return $this->backoff[$this->attempts() - 1] ?? 60;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting CopyProductToResellers job', [
            'productId' => $this->product->id,
            'productName' => $this->product->name,
        ]);

        // ===== ONINDA DATABASE OPERATIONS (DEFAULT CONNECTION) =====

        // Get all active resellers with database configuration
        $resellers = User::where('is_active', true)
            ->whereNotNull('db_name')
            ->where('db_name', '!=', '')
            ->whereNotNull('db_username')
            ->where('db_username', '!=', '')
            ->inRandomOrder()
            ->get();

        foreach ($resellers as $reseller) {
            try {
                $this->idMap = [];

                // Configure reseller database connection
                $config = $reseller->getDatabaseConfig();
                config(['database.connections.reseller' => $config]);

                // Purge and reconnect to ensure fresh connection
                DB::purge('reseller');
                DB::reconnect('reseller');

                // Test connection before proceeding
                try {
                    DB::connection('reseller')->getPdo();
                } catch (\Exception $e) {
                    Log::error('Failed to connect to reseller database', [
                        'resellerId' => $reseller->id,
                        'domain' => $reseller->domain,
                        'error' => $e->getMessage(),
                    ]);

                    continue;
                }

                // Copy product to this reseller
                $this->copyProductToReseller($reseller);

                Log::info("Successfully copied product {$this->product->id} to reseller {$reseller->id} [".DB::connection('reseller')->getDatabaseName().']');

            } catch (\PDOException $e) {
                Log::error("Database connection failed for reseller {$reseller->id}: ".$e->getMessage());

                continue;
            } catch (\Exception $e) {
                Log::error("Failed to copy product {$this->product->id} to reseller {$reseller->id}: ".$e->getMessage().' at line '.$e->getLine().' in '.$e->getFile());

                continue;
            } finally {
                // Always purge the connection to free up resources
                DB::purge('reseller');
            }
        }
    }

    /**
     * Copy product to a specific reseller
     */
    private function copyProductToReseller(User $reseller): void
    {
        // ===== ONINDA DATABASE OPERATIONS (DEFAULT CONNECTION) =====

        // Copy brand if exists
        if (! empty($this->product->brand_id)) {
            $brand = DB::table('brands')->where('id', $this->product->brand_id)->first();
            if ($brand) {
                // Use 'slug' as unique column for brands
                $newBrandId = $this->getOrCreateResource('brands', $this->product->brand_id, 'slug', $brand->slug);
                $this->product->brand_id = $newBrandId;
                Log::info('Brand copied successfully', [
                    'originalBrandId' => $this->product->brand_id,
                    'newBrandId' => $newBrandId,
                    'brandSlug' => $brand->slug,
                ]);
            } else {
                $this->product->brand_id = null;
                Log::warning('Brand not found in source database', ['brandId' => $this->product->brand_id]);
            }
        }

        // Copy categories
        $categoryIds = [];
        $categories = DB::table('category_product')
            ->where('product_id', $this->product->id)
            ->get(['category_id']);

        Log::info('Found categories for product', [
            'productId' => $this->product->id,
            'categoryCount' => $categories->count(),
            'categoryIds' => $categories->pluck('category_id')->toArray(),
        ]);

        foreach ($categories as $category) {
            $cat = DB::table('categories')->where('id', $category->category_id)->first();
            if ($cat) {
                // Use 'slug' as unique column for categories
                $newCategoryId = $this->getOrCreateResource('categories', $category->category_id, 'slug', $cat->slug);
                $categoryIds[] = $newCategoryId;
                Log::info('Category copied successfully', [
                    'originalCategoryId' => $category->category_id,
                    'newCategoryId' => $newCategoryId,
                    'categorySlug' => $cat->slug,
                ]);
            } else {
                Log::warning('Category not found in source database', ['categoryId' => $category->category_id]);
            }
        }

        // Copy images
        $imageIds = [];
        $images = DB::table('image_product')
            ->where('product_id', $this->product->id)
            ->get(['image_id', 'img_type', 'order']);

        Log::info('Found images for product', [
            'productId' => $this->product->id,
            'imageCount' => $images->count(),
            'imageIds' => $images->pluck('image_id')->toArray(),
        ]);

        foreach ($images as $image) {
            $img = DB::table('images')->where('id', $image->image_id)->first();
            if ($img) {
                $newImageId = $this->getOrCreateResource('images', $image->image_id, 'path', $img->path);
                $imageIds[] = [
                    'id' => $newImageId,
                    'img_type' => $image->img_type,
                    'order' => $image->order,
                ];
                Log::info('Image copied successfully', [
                    'originalImageId' => $image->image_id,
                    'newImageId' => $newImageId,
                    'imagePath' => $img->path,
                    'imgType' => $image->img_type,
                    'order' => $image->order,
                ]);
            } else {
                Log::warning('Image not found in source database', ['imageId' => $image->image_id]);
            }
        }

        // Copy attributes and options
        $optionIds = [];
        $options = DB::table('option_product')
            ->where('product_id', $this->product->id)
            ->get(['option_id']);

        Log::info('Found options for product', [
            'productId' => $this->product->id,
            'optionCount' => $options->count(),
            'optionIds' => $options->pluck('option_id')->toArray(),
        ]);

        foreach ($options as $option) {
            $opt = DB::table('options')->where('id', $option->option_id)->first();
            if ($opt) {
                $attr = DB::table('attributes')->where('id', $opt->attribute_id)->first();
                if ($attr) {
                    $newAttrId = $this->getOrCreateResource('attributes', $attr->id, 'name', $attr->name);
                    $newOptId = $this->getOrCreateResource('options', $opt->id, 'name', $opt->name);
                    $optionIds[] = $newOptId;
                    Log::info('Option copied successfully', [
                        'originalOptionId' => $option->option_id,
                        'newOptionId' => $newOptId,
                        'optionName' => $opt->name,
                        'attributeName' => $attr->name,
                    ]);
                } else {
                    Log::warning('Attribute not found in source database', ['attributeId' => $opt->attribute_id]);
                }
            } else {
                Log::warning('Option not found in source database', ['optionId' => $option->option_id]);
            }
        }

        // Copy main product
        $productData = $this->product->getAttributes();
        $productData['source_id'] = $productData['id'];
        unset($productData['id']);

        if (config('app.resell')) {
            // Ensure reseller selling price equals retail price
            $productData['selling_price'] = $this->product->retailPrice();
        }

        // ===== RESELLER DATABASE OPERATIONS =====

        // Check if product already exists by source_id first
        $existingBySourceId = DB::connection('reseller')
            ->table('products')
            ->where('source_id', $this->product->id)
            ->first();

        if ($existingBySourceId) {
            // Product already exists with this source_id, use existing ID
            $newProductId = $existingBySourceId->id;
            $this->idMap['products'][$this->product->id] = $newProductId;

            // Clean up any existing duplicate relationships before inserting new ones
            $this->cleanupDuplicateRelationships($newProductId);
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

                // Clean up any existing duplicate relationships before inserting new ones
                $this->cleanupDuplicateRelationships($newProductId);
            } else {
                // Generate unique SKU only (no slug modification needed)
                $productData['sku'] = $this->getUniqueValue('sku', $productData['sku']);

                // Use DB facade for insertion to avoid triggering model events
                Log::info('Creating product in reseller database', [
                    'productId' => $this->product->id,
                    'resellerId' => $reseller->id,
                    'sourceId' => $productData['source_id'],
                ]);
                $newProductId = DB::connection('reseller')
                    ->table('products')
                    ->insertGetId($productData);
                $this->idMap['products'][$this->product->id] = $newProductId;

                // Verify source_id was set correctly
                $createdProduct = DB::connection('reseller')
                    ->table('products')
                    ->where('id', $newProductId)
                    ->first(['id', 'source_id']);

                Log::info('Product created successfully', [
                    'newProductId' => $newProductId,
                    'actualSourceId' => $createdProduct->source_id,
                    'expectedSourceId' => $this->product->id,
                ]);
            }
        }

        // Copy variations if any
        $this->copyProductVariations($newProductId);

        // Insert relationships
        $this->insertProductRelationships($newProductId, $categoryIds, $imageIds, $optionIds);

        Log::info('Product copy to reseller completed', [
            'originalProductId' => $this->product->id,
            'newProductId' => $newProductId,
            'categoryCount' => count($categoryIds),
            'imageCount' => count($imageIds),
            'optionCount' => count($optionIds),
            'resellerId' => $reseller->id,
        ]);
    }

    /**
     * Copy product variations to reseller database
     */
    private function copyProductVariations(int $newProductId): void
    {
        // ===== ONINDA DATABASE OPERATIONS (DEFAULT CONNECTION) =====

        $variations = DB::table('products')
            ->where('parent_id', $this->product->id)
            ->get();

        foreach ($variations as $variation) {
            $varData = (array) $variation;
            $varData['parent_id'] = $newProductId;
            $varData['source_id'] = $varData['id'];
            unset($varData['id']);

            if (config('app.resell')) {
                // Ensure reseller variation selling price equals retail price
                $varData['selling_price'] = $this->calculateRetailPrice($variation->suggested_price, $variation->selling_price);
            }

            // ===== RESELLER DATABASE OPERATIONS =====

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

                // Use DB facade for insertion to avoid triggering model events
                DB::connection('reseller')
                    ->table('products')
                    ->insert($varData);
            }
        }
    }

    /**
     * Calculate retail price based on suggested_price or fallback to 1.4x selling_price
     */
    private function calculateRetailPrice($suggestedPrice, $sellingPrice): int
    {
        $price = $suggestedPrice;

        if (is_string($price)) {
            $price = trim($price);
            if ($price !== '' && preg_match('/^\s*(\d+(?:\.\d+)?)\s*-\s*(\d+(?:\.\d+)?)\s*$/', $price, $matches)) {
                $low = (float) $matches[1];
                $high = (float) $matches[2];

                return (int) round(($low + $high) / 2);
            }
        }

        if (is_numeric($price) && $price > 0) {
            return (int) round((float) $price);
        }

        return (int) round(((float) $sellingPrice) * 1.4);
    }

    /**
     * Insert product relationships in reseller database
     */
    private function insertProductRelationships(int $newProductId, array $categoryIds, array $imageIds, array $optionIds): void
    {
        // ===== RESELLER DATABASE OPERATIONS =====

        Log::info('Inserting product relationships', [
            'newProductId' => $newProductId,
            'categoryCount' => count($categoryIds),
            'imageCount' => count($imageIds),
            'optionCount' => count($optionIds),
            'categoryIds' => $categoryIds,
            'imageIds' => $imageIds,
            'optionIds' => $optionIds,
        ]);

        // Insert category relationships with duplicate prevention
        foreach ($categoryIds as $categoryId) {
            try {
                // Use insertOrIgnore to handle unique constraints
                $inserted = DB::connection('reseller')
                    ->table('category_product')
                    ->insertOrIgnore([
                        'product_id' => $newProductId,
                        'category_id' => $categoryId,
                    ]);

                if ($inserted) {
                    Log::info('Category relationship inserted', [
                        'productId' => $newProductId,
                        'categoryId' => $categoryId,
                    ]);
                } else {
                    Log::info('Category relationship already exists, skipped', [
                        'productId' => $newProductId,
                        'categoryId' => $categoryId,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to insert category relationship', [
                    'productId' => $newProductId,
                    'categoryId' => $categoryId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Insert image relationships with duplicate prevention
        foreach ($imageIds as $image) {
            try {
                // Use insertOrIgnore to handle unique constraints
                $inserted = DB::connection('reseller')
                    ->table('image_product')
                    ->insertOrIgnore([
                        'product_id' => $newProductId,
                        'image_id' => $image['id'],
                        'img_type' => $image['img_type'],
                        'order' => $image['order'],
                    ]);

                if ($inserted) {
                    Log::info('Image relationship inserted', [
                        'productId' => $newProductId,
                        'imageId' => $image['id'],
                        'imgType' => $image['img_type'],
                        'order' => $image['order'],
                    ]);
                } else {
                    Log::info('Image relationship already exists, skipped', [
                        'productId' => $newProductId,
                        'imageId' => $image['id'],
                        'imgType' => $image['img_type'],
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to insert image relationship', [
                    'productId' => $newProductId,
                    'imageId' => $image['id'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Insert option relationships with duplicate prevention
        foreach ($optionIds as $optionId) {
            try {
                // Use insertOrIgnore to handle unique constraints
                $inserted = DB::connection('reseller')
                    ->table('option_product')
                    ->insertOrIgnore([
                        'product_id' => $newProductId,
                        'option_id' => $optionId,
                    ]);

                if ($inserted) {
                    Log::info('Option relationship inserted', [
                        'productId' => $newProductId,
                        'optionId' => $optionId,
                    ]);
                } else {
                    Log::info('Option relationship already exists, skipped', [
                        'productId' => $newProductId,
                        'optionId' => $optionId,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to insert option relationship', [
                    'productId' => $newProductId,
                    'optionId' => $optionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Product relationships insertion completed', [
            'newProductId' => $newProductId,
        ]);
    }

    /**
     * Clean up duplicate relationships in the reseller's database for a given product.
     */
    private function cleanupDuplicateRelationships(int $newProductId): void
    {
        try {
            // Clean up duplicate category relationships
            $duplicateCategories = DB::connection('reseller')
                ->table('category_product')
                ->select('category_id')
                ->where('product_id', $newProductId)
                ->groupBy('category_id')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            foreach ($duplicateCategories as $duplicate) {
                // Keep the first record, delete the rest
                $recordsToDelete = DB::connection('reseller')
                    ->table('category_product')
                    ->where('product_id', $newProductId)
                    ->where('category_id', $duplicate->category_id)
                    ->orderBy('id')
                    ->skip(1)
                    ->get(['id']);

                if ($recordsToDelete->isNotEmpty()) {
                    DB::connection('reseller')
                        ->table('category_product')
                        ->whereIn('id', $recordsToDelete->pluck('id'))
                        ->delete();

                    Log::info('Deleted duplicate category relationships', [
                        'productId' => $newProductId,
                        'categoryId' => $duplicate->category_id,
                        'deletedCount' => $recordsToDelete->count(),
                    ]);
                }
            }

            // Clean up duplicate image relationships
            $duplicateImages = DB::connection('reseller')
                ->table('image_product')
                ->select('image_id', 'img_type')
                ->where('product_id', $newProductId)
                ->groupBy('image_id', 'img_type')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            foreach ($duplicateImages as $duplicate) {
                // Keep the first record, delete the rest
                $recordsToDelete = DB::connection('reseller')
                    ->table('image_product')
                    ->where('product_id', $newProductId)
                    ->where('image_id', $duplicate->image_id)
                    ->where('img_type', $duplicate->img_type)
                    ->orderBy('id')
                    ->skip(1)
                    ->get(['id']);

                if ($recordsToDelete->isNotEmpty()) {
                    DB::connection('reseller')
                        ->table('image_product')
                        ->whereIn('id', $recordsToDelete->pluck('id'))
                        ->delete();

                    Log::info('Deleted duplicate image relationships', [
                        'productId' => $newProductId,
                        'imageId' => $duplicate->image_id,
                        'imgType' => $duplicate->img_type,
                        'deletedCount' => $recordsToDelete->count(),
                    ]);
                }
            }

            // Clean up duplicate option relationships
            $duplicateOptions = DB::connection('reseller')
                ->table('option_product')
                ->select('option_id')
                ->where('product_id', $newProductId)
                ->groupBy('option_id')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            foreach ($duplicateOptions as $duplicate) {
                // Keep the first record, delete the rest
                $recordsToDelete = DB::connection('reseller')
                    ->table('option_product')
                    ->where('product_id', $newProductId)
                    ->where('option_id', $duplicate->option_id)
                    ->orderBy('id')
                    ->skip(1)
                    ->get(['id']);

                if ($recordsToDelete->isNotEmpty()) {
                    DB::connection('reseller')
                        ->table('option_product')
                        ->whereIn('id', $recordsToDelete->pluck('id'))
                        ->delete();

                    Log::info('Deleted duplicate option relationships', [
                        'productId' => $newProductId,
                        'optionId' => $duplicate->option_id,
                        'deletedCount' => $recordsToDelete->count(),
                    ]);
                }
            }

            Log::info('Duplicate relationships cleaned up for product', [
                'productId' => $newProductId,
                'duplicateCategories' => $duplicateCategories->count(),
                'duplicateImages' => $duplicateImages->count(),
                'duplicateOptions' => $duplicateOptions->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cleanup duplicate relationships', [
                'productId' => $newProductId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get or create a resource in reseller's database
     */
    protected function getOrCreateResource(string $table, int $sourceId, string $uniqueColumn, $uniqueValue): int
    {
        // If we already have the ID mapping, return it
        if (isset($this->idMap[$table][$sourceId])) {
            Log::info("Using cached ID mapping for {$table}", [
                'sourceId' => $sourceId,
                'cachedId' => $this->idMap[$table][$sourceId],
            ]);

            return $this->idMap[$table][$sourceId];
        }

        // ===== RESELLER DATABASE OPERATIONS =====

        // First check if the resource's ID exists in source_id column
        $existingBySourceId = DB::connection('reseller')
            ->table($table)
            ->where('source_id', $sourceId)
            ->first();

        if ($existingBySourceId) {
            // Resource already exists with this source_id, store mapping and return
            $this->idMap[$table][$sourceId] = $existingBySourceId->id;
            Log::info("Found existing resource by source_id in {$table}", [
                'sourceId' => $sourceId,
                'existingId' => $existingBySourceId->id,
            ]);

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
            Log::info("Found existing resource by unique column in {$table}", [
                'sourceId' => $sourceId,
                'existingId' => $existingByUnique->id,
                'uniqueColumn' => $uniqueColumn,
                'uniqueValue' => $uniqueValue,
            ]);

            return $existingByUnique->id;
        }

        // ===== ONINDA DATABASE OPERATIONS (DEFAULT CONNECTION) =====

        // Get the original data
        $data = DB::table($table)
            ->where('id', $sourceId)
            ->first();

        if (! $data) {
            Log::error('Resource not found in source database', [
                'table' => $table,
                'sourceId' => $sourceId,
            ]);
            throw new \Exception("Resource not found in source database: {$table} {$sourceId}");
        }

        Log::info("Found source data for {$table}", [
            'sourceId' => $sourceId,
            'dataKeys' => array_keys((array) $data),
        ]);

        // Convert to array and handle foreign keys
        $insertData = (array) $data;

        $foreignKeys = [];
        foreach ($insertData as $key => $value) {
            if (str_ends_with((string) $key, '_id') && $value) {
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

        // ===== RESELLER DATABASE OPERATIONS =====

        // Use DB facade for all tables to avoid triggering model events
        $newId = DB::connection('reseller')
            ->table($table)
            ->insertGetId($insertData);

        // Store the ID mapping
        $this->idMap[$table][$sourceId] = $newId;

        // Verify source_id was set correctly for products
        if ($table === 'products') {
            $createdRecord = DB::connection('reseller')
                ->table($table)
                ->where('id', $newId)
                ->first(['id', 'source_id']);

            Log::info("Resource created successfully in {$table}", [
                'newId' => $newId,
                'actualSourceId' => $createdRecord->source_id,
                'expectedSourceId' => $sourceId,
            ]);
        }

        return $newId;
    }

    /**
     * Generate a unique value for a column in the reseller's products table
     */
    protected function getUniqueValue(string $column, string $value): string
    {
        $baseValue = $value;
        $suffix = '-wholesaler';
        $i = 1;
        $newValue = $baseValue;

        while (DB::connection('reseller')->table('products')->where($column, $newValue)->exists()) {
            $newValue = $baseValue.$suffix.($i > 1 ? "-$i" : '');
            $i++;
        }

        return $newValue;
    }
}
