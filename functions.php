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
use Azmolla\Shoppingcart\Cart as CartInstance;
use Azmolla\Shoppingcart\CartItem;
use Azmolla\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
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

function longCookie($field, $value)
{
    if ($value) {
        Cookie::queue(Cookie::make($field, $value, 10 * 365 * 24 * 60)); // 10 years
    }
}

function cart($id = null): CartInstance|CartItem
{
    $cart = Cart::instance(session('kart', 'default'));

    if (! $id) {
        return $cart;
    }

    return $cart->first(fn ($item) => $item->id == $id);
}

function storeOrUpdateCart($phone = null, $name = '')
{
    info('amiparina');
    if (! $phone = $phone ?? Cookie::get('phone', '')) {
        return;
    }

    info($phone, cart()->content()->toArray());
    if (strlen($phone) < 11) {
        return;
    }

    $content = cart()->content()->mapWithKeys(fn ($item) => [$item->options->parent_id => $item]);

    $identifier = session()->getId();
    if ($cart = DB::table('shopping_cart')->where('phone', $phone)->first()) {
        $identifier = $cart->identifier;
    }

    $cart = DB::table('shopping_cart')
        ->where('identifier', $identifier)
        ->first();

    if ($cart) {
        DB::table('shopping_cart')
            ->where('identifier', $identifier)
            ->update([
                'identifier' => session()->getId(),
                'name' => Cookie::get('name', $name),
                'phone' => $phone,
                'content' => serialize($content->union(unserialize($cart->content))),
                'updated_at' => now(),
            ]);

        return;
    }

    DB::table('shopping_cart')
        ->insert([
            'name' => Cookie::get('name', $name),
            'phone' => $phone,
            'instance' => 'default',
            'identifier' => session()->getId(),
            'content' => serialize($content),
            'updated_at' => now(),
        ]);
}

function deleteOrUpdateCart()
{
    $content = cart()->content()->mapWithKeys(fn ($item) => [$item->parent_id => $item]);

    $cart = DB::table('shopping_cart')
        ->where('identifier', session()->getId())
        ->first();

    if ($cart) {
        $content = unserialize($cart->content)->diffKeys($content);
        if ($content->isEmpty()) {
            return DB::table('shopping_cart')
                ->where('identifier', session()->getId())
                ->delete();
        }
        DB::table('shopping_cart')
            ->where('identifier', session()->getId())
            ->update([
                'name' => Cookie::get('name'),
                'phone' => Cookie::get('phone'),
                'content' => serialize($content),
                'updated_at' => now(),
            ]);
    }

    DB::table('shopping_cart')
        ->insert([
            'name' => Cookie::get('name'),
            'phone' => Cookie::get('phone'),
            'instance' => 'default',
            'identifier' => session()->getId(),
            'content' => serialize($content),
            'updated_at' => now(),
        ]);
}
