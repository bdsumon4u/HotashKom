<?php

namespace App\Console\Commands;

use App\Jobs\CopyProductToResellers;
use App\Jobs\CopyResourceToResellers;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Option;
use App\Models\Product;
use Illuminate\Console\Command;

class SyncResellerResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:reseller-resources {--only= : Comma-separated list of resources to sync (products,brands,categories,attributes,options,images)} {--delay=1 : Base delay in seconds between job dispatches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy all products and related resources to resellers if not exists.';

    /**
     * Table-specific delay multipliers based on table size
     */
    protected array $delayMultipliers = [
        'products' => 5,    // Large table - 5x base delay
        'images' => 3,      // Large table - 3x base delay
        'brands' => 1,      // Small table - 1x base delay
        'categories' => 1,  // Small table - 1x base delay
        'attributes' => 1,  // Small table - 1x base delay
        'options' => 1,     // Small table - 1x base delay
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $only = $this->option('only') ? array_map('trim', explode(',', $this->option('only'))) : [];
        $baseDelay = (int) $this->option('delay');

        if (empty($only) || in_array('brands', $only)) {
            $this->info('Syncing brands...');
            $this->dispatchJobsWithDelay(Brand::class, $baseDelay);
        }

        if (empty($only) || in_array('categories', $only)) {
            $this->info('Syncing categories...');
            $this->dispatchJobsWithDelay(Category::class, $baseDelay);
        }

        if (empty($only) || in_array('attributes', $only)) {
            $this->info('Syncing attributes...');
            $this->dispatchJobsWithDelay(Attribute::class, $baseDelay);
        }

        if (empty($only) || in_array('options', $only)) {
            $this->info('Syncing options...');
            $this->dispatchJobsWithDelay(Option::class, $baseDelay);
        }

        if (empty($only) || in_array('images', $only)) {
            $this->info('Syncing images...');
            $this->dispatchJobsWithDelay(Image::class, $baseDelay);
        }

        if (empty($only) || in_array('products', $only)) {
            $this->info('Syncing products...');
            $this->dispatchJobsWithDelay(Product::class, $baseDelay);
        }

        $this->info('Syncing complete!');
    }

    /**
     * Dispatch jobs with progressive delay to prevent connection refused errors
     */
    protected function dispatchJobsWithDelay(string $modelClass, int $baseDelay): void
    {
        $tableName = $this->getTableNameFromModel($modelClass);
        $isProduct = $modelClass === Product::class;
        $currentDelay = 0;

        // Get the delay multiplier for this table
        $delayMultiplier = $this->delayMultipliers[$tableName] ?? 1;
        $actualDelay = $baseDelay * $delayMultiplier;

        $this->info("Using delay: {$actualDelay}s for {$tableName} table (base: {$baseDelay}s × multiplier: {$delayMultiplier})");

        $modelClass::chunk($isProduct ? 50 : 100, function ($items) use ($actualDelay, $isProduct, &$currentDelay) {
            foreach ($items as $item) {
                if ($isProduct) {
                    dispatch(new CopyProductToResellers($item))->delay(now()->addSeconds($currentDelay));
                } else {
                    dispatch(new CopyResourceToResellers($item))->delay(now()->addSeconds($currentDelay));
                }

                $currentDelay += $actualDelay;
            }
        });
    }

    /**
     * Get table name from model class
     */
    protected function getTableNameFromModel(string $modelClass): string
    {
        $model = new $modelClass;

        return $model->getTable();
    }
}
