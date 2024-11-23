<?php

use App\Http\Controllers\PageController;
use App\Http\Middleware\ShortKodeMiddleware;
use App\Models\Brand;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Image;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Slide;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

if (! function_exists('slides')) {
    function slides()
    {
        return cache()->rememberForever('slides', function () {
            return Slide::whereIsActive(1)->get([
                'title', 'text', 'mobile_src', 'desktop_src', 'btn_name', 'btn_href',
            ]);
        });
    }
}

if (! function_exists('sections')) {
    function sections()
    {
        return cache()->rememberForever('homesections', function () {
            return HomeSection::orderBy('order', 'asc')->get();
        });
    }
}

if (! function_exists('categories')) {
    function categories()
    {
        // Load categories with images only
        $categoriesWithImages = Category::with('image')
            ->whereHas('image') // Only categories that have images
            ->inRandomOrder()
            ->get();

        // Load categories without images and eager load their product images
        $categoriesWithoutImages = Category::with('products.images')
            ->whereDoesntHave('image') // Only categories without images
            ->inRandomOrder()
            ->get();

        // Merge the two collections and map for final processing
        $categories = $categoriesWithImages->merge($categoriesWithoutImages);

        return $categories->map(function ($category) {
            if ($category->relationLoaded('image')) {
                $image = $category->image;
            } else {
                $images = $category->products->pluck('images')->filter();
                $image = $images->isEmpty() ? null : $images->random()->first();
            }

            // Set the image_src property with a fallback placeholder
            $category->image_src = asset($image->path ?? 'https://placehold.co/600x600?text=No+Product');

            return $category;
        });
    }
}

if (! function_exists('brands')) {
    function brands()
    {
        return collect(collect());
        // Load brands with images only
        $brandsWithImages = Brand::with('image')
            ->whereHas('image') // Only brands that have images
            ->inRandomOrder()
            ->get();

        // Load brands without images and eager load their product images
        $brandsWithoutImages = Brand::with('products.images')
            ->whereDoesntHave('image') // Only brands without images
            ->inRandomOrder()
            ->get();

        // Merge the two collections and map for final processing
        $brands = $brandsWithImages->merge($brandsWithoutImages);

        return $brands->map(function ($brand) {
            if ($brand->relationLoaded('image')) {
                $image = $brand->image;
            } else {
                $images = $brand->products->pluck('images')->filter();
                $image = $images->isEmpty() ? null : $images->random()->first();
            }

            // Set the image_src property with a fallback placeholder
            $brand->image_src = asset($image->path ?? 'https://placehold.co/600x600?text=No+Product');

            return $brand;
        });
    }
}

if (! function_exists('pageRoutes')) {
    function pageRoutes()
    {
        try {
            Schema::hasTable((new Page)->getTable())
                && Route::get('{page:slug}', PageController::class)
                    ->where('page', 'test-page|'.implode(
                        '|', Page::get('slug')
                            ->map->slug
                            ->toArray()
                    ))
                    ->middleware(ShortKodeMiddleware::class)
                    ->name('page');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}

if (! function_exists('setting')) {
    function setting($name, $default = null)
    {
        return cache()->rememberForever('settings:'.$name, function () use ($name, $default) {
            return optional(Setting::whereName($name)->first())->value ?? $default;
        });
    }
}

if (! function_exists('theMoney')) {
    function theMoney($amount, $decimals = null, $currency = 'TK')
    {
        return $currency.'&nbsp;<span>'.number_format($amount, $decimals).'</span>';
    }
}

function bytesToHuman($bytes)
{
    $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, 2).' '.$units[$i];
}

function hasErr($errors, $params)
{
    foreach (explode('|', $params) as $param) {
        if ($errors->has($param)) {
            return true;
        }
    }

    return false;
}

function genSKU($repeat = 5, $length = null)
{
    $sku = null;
    $length = $length ?: mt_rand(6, 10);
    $charset = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $multiplier = ceil($length / strlen($charset));
    // Generate SKU
    if (--$repeat) {
        $sku = substr(str_shuffle(str_repeat($charset, $multiplier)), 1, $length);
        Product::where('sku', $sku)->count() && genSKU($repeat);
    }

    return $sku;
}
