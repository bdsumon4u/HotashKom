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
    protected $signature = 'sync:reseller-resources {--only= : Comma-separated list of resources to sync (products,brands,categories,attributes,options,images)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy all products and related resources to resellers if not exists.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $only = $this->option('only') ? array_map('trim', explode(',', $this->option('only'))) : [];

        if (empty($only) || in_array('brands', $only)) {
            $this->info('Syncing brands...');
            Brand::chunk(100, function ($brands) {
                foreach ($brands as $brand) {
                    dispatch(new CopyResourceToResellers($brand));
                }
            });
        }

        if (empty($only) || in_array('categories', $only)) {
            $this->info('Syncing categories...');
            Category::chunk(100, function ($categories) {
                foreach ($categories as $category) {
                    dispatch(new CopyResourceToResellers($category));
                }
            });
        }

        if (empty($only) || in_array('attributes', $only)) {
            $this->info('Syncing attributes...');
            Attribute::chunk(100, function ($attributes) {
                foreach ($attributes as $attribute) {
                    dispatch(new CopyResourceToResellers($attribute));
                }
            });
        }

        if (empty($only) || in_array('options', $only)) {
            $this->info('Syncing options...');
            Option::chunk(100, function ($options) {
                foreach ($options as $option) {
                    dispatch(new CopyResourceToResellers($option));
                }
            });
        }

        if (empty($only) || in_array('images', $only)) {
            $this->info('Syncing images...');
            Image::chunk(100, function ($images) {
                foreach ($images as $image) {
                    dispatch(new CopyResourceToResellers($image));
                }
            });
        }

        if (empty($only) || in_array('products', $only)) {
            $this->info('Syncing products...');
            Product::chunk(50, function ($products) {
                foreach ($products as $product) {
                    dispatch(new CopyProductToResellers($product));
                }
            });
        }

        $this->info('Syncing complete!');
    }
}
