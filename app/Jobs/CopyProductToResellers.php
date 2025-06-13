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
use Illuminate\Database\Eloquent\Model;

class CopyProductToResellers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;

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
    protected function getOrCreateResource(string $table, Model $model, string $uniqueColumn): int
    {
        $data = $model->getRawOriginal();

        $existing = DB::connection('reseller')
            ->table($table)
            ->where($uniqueColumn, $data[$uniqueColumn])
            ->first();

        if ($existing) {
            return $existing->id;
        }

        $insertData = $data;
        $insertData['source_id'] = $insertData['id'];
        unset($insertData['id']);

        return DB::connection('reseller')
            ->table($table)
            ->insertGetId($insertData);
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

                // Copy brand if exists
                $brandId = null;
                if ($this->product->brand_id) {
                    $brand = $this->product->brand()->first();
                    $brandId = $this->getOrCreateResource('brands', $brand, 'slug');
                }

                // Copy categories
                $categoryIds = [];
                $categories = $this->product->categories()->get();
                foreach ($categories as $category) {
                    $categoryIds[] = $this->getOrCreateResource('categories', $category, 'slug');
                }

                // Copy images
                $imageIds = [];
                $images = $this->product->images()->get();
                foreach ($images as $image) {
                    $imageIds[] = $this->getOrCreateResource('images', $image, 'filename');
                }

                // Copy attributes and options
                $optionIds = [];
                $options = $this->product->options()->with('attribute')->get();
                foreach ($options as $option) {
                    $attributeId = $this->getOrCreateResource('attributes', $option->attribute, 'name');
                    $optionIds[] = $this->getOrCreateResource('options', $option, 'name');
                }

                // Copy main product
                $productData = $this->product->getRawOriginal();
                unset($productData['id']);
                $productData['source_id'] = $this->product->id;
                $productData['brand_id'] = $brandId;

                $productId = DB::connection('reseller')->table('products')->insertGetId($productData);

                // Create category relationships
                foreach ($categoryIds as $categoryId) {
                    DB::connection('reseller')->table('category_product')->insert([
                        'category_id' => $categoryId,
                        'product_id' => $productId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Create image relationships
                foreach ($images as $index => $image) {
                    DB::connection('reseller')->table('image_product')->insert([
                        'image_id' => $imageIds[$index],
                        'product_id' => $productId,
                        'img_type' => $image->pivot->img_type,
                        'order' => $image->pivot->order,
                        'created_at' => $image->pivot->created_at,
                        'updated_at' => $image->pivot->updated_at,
                    ]);
                }

                // Create option relationships
                foreach ($optionIds as $optionId) {
                    DB::connection('reseller')->table('option_product')->insert([
                        'option_id' => $optionId,
                        'product_id' => $productId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Copy variations if any
                $variations = $this->product->variations()->get();
                foreach ($variations as $variation) {
                    $variationData = $variation->getRawOriginal();
                    unset($variationData['id']);
                    $variationData['source_id'] = $variation->id;
                    $variationData['parent_id'] = $productId;
                    $variationData['brand_id'] = $brandId;

                    $variationId = DB::connection('reseller')->table('products')->insertGetId($variationData);

                    // Copy variation images
                    $variationImages = $variation->images()->get();
                    foreach ($variationImages as $index => $image) {
                        DB::connection('reseller')->table('image_product')->insert([
                            'image_id' => $imageIds[$index],
                            'product_id' => $variationId,
                            'img_type' => $image->pivot->img_type,
                            'order' => $image->pivot->order,
                            'created_at' => $image->pivot->created_at,
                            'updated_at' => $image->pivot->updated_at,
                        ]);
                    }
                }

                Log::info("Successfully copied product {$this->product->id} to reseller {$reseller->id}");

            } catch (\Exception $e) {
                Log::error("Failed to copy product {$this->product->id} to reseller {$reseller->id}: " . $e->getMessage());
                continue;
            }
        }
    }
}
