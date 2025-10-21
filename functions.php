<?php

use App\Http\Controllers\PageController;
use App\Http\Middleware\ShortKodeMiddleware;
use App\Models\Brand;
use App\Models\Category;
use App\Models\HomeSection;
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
use Illuminate\Support\Str;

if (! function_exists('slides')) {
    function slides()
    {
        return cache()->memo()->rememberForever('slides', function () {
            return Slide::whereIsActive(1)->get([
                'title', 'text', 'mobile_src', 'desktop_src', 'btn_name', 'btn_href',
            ]);
        });
    }
}

if (! function_exists('sections')) {
    function sections()
    {
        return cache()->memo()->rememberForever('homesections', function () {
            return HomeSection::orderBy('order', 'asc')->get();
        });
    }
}

if (! function_exists('categories')) {
    function categories()
    {
        // Load categories with images only
        $categoriesWithImages = Category::with('image')
            ->where('is_enabled', true)
            ->whereHas('image') // Only categories that have images
            ->inRandomOrder()
            ->get();

        // Load categories without images and eager load their product images
        $categoriesWithoutImages = Category::with('products.images')
            ->where('is_enabled', true)
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
            $category->image_src = cdn($image->src ?? 'https://placehold.co/600x600?text=No+Product');

            return $category;
        });
    }
}

if (! function_exists('brands')) {
    function brands()
    {
        // Load brands with images only
        $brandsWithImages = Brand::with('image')
            ->where('is_enabled', true)
            ->whereHas('image') // Only brands that have images
            ->inRandomOrder()
            ->get();

        // Load brands without images and eager load their product images
        $brandsWithoutImages = Brand::with('products.images')
            ->where('is_enabled', true)
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
            $brand->image_src = cdn($image->src ?? 'https://placehold.co/600x600?text=No+Product');

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
            // throw $th;
        }
    }
}

if (! function_exists('setting')) {
    function setting($name, $default = null)
    {
        return cache()->memo()->rememberForever('settings:'.$name, function () use ($name, $default) {
            return optional(Setting::whereName($name)->first())->value ?? $default;
        });
    }
}

if (! function_exists('theMoney')) {
    function theMoney($amount, $decimals = null, $currency = 'TK')
    {
        // Ensure amount is numeric to prevent number_format errors
        if (! is_numeric($amount)) {
            $amount = (float) ($amount ?? 0);
        }

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

function couriers()
{
    return array_filter([
        'Pathao',
        config('redx.enabled') ? 'Redx' : null,
        'SteadFast',
        'Other',
    ]);
}

function cdn(?string $url, int $w = 150, int $h = 150)
{
    if (! $url) {
        return asset('https://placehold.co/600x600?text=No+Image');
    }

    if (parse_url($url, PHP_URL_HOST) == 'placehold.co') {
        return $url;
    }

    if ($username = config('services.gumlet.username')) {
        return str_replace(request()->getHost(), $username.'.gumlet.io', $url).'?fit=resize&w='.$w.'&h='.$h;
    }

    if ($username = config('services.cloudinary.username')) {
        return 'https://res.cloudinary.com/'.$username.'/image/fetch/w_'.$w.',h_'.$h.',c_thumb/'.asset($url);
    }

    if ($username = config('services.imagekit.username')) {
        return str_replace(request()->getHost(), 'ik.imagekit.io/'.$username, $url).'??tr=w-'.$w.',h-'.$h;
    }

    return asset($url);
}

function longCookie($field, $value)
{
    if ($value) {
        Cookie::queue(Cookie::make($field, $value, 10 * 365 * 24 * 60)); // 10 years
    }
}

function cart($id = null): CartInstance|CartItem|null
{
    $cart = Cart::instance(session('kart', 'default'));

    if (! $id) {
        return $cart;
    }

    return $cart->content()->first(fn ($item) => $item->id == $id);
}

function storeOrUpdateCart($phone = null, $name = '')
{
    if (! $phone = $phone ?? Cookie::get('phone', '')) {
        return;
    }

    if (Str::startsWith($phone, '01')) {
        $phone = '+88'.$phone;
    } elseif (Str::startsWith($phone, '1')) {
        $phone = '+880'.$phone;
    }

    if (strlen($phone) != 14) {
        return;
    }

    $content = cart()->content()->mapWithKeys(fn ($item) => [$item->options->parent_id => $item]);

    if ($content->isEmpty()) {
        return;
    }

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
    $phone = Cookie::get('phone', '');
    $content = cart()->content()->mapWithKeys(fn ($item) => [$item->options->parent_id => $item]);

    if (Str::startsWith($phone, '01')) {
        $phone = '+88'.$phone;
    } elseif (Str::startsWith($phone, '1')) {
        $phone = '+880'.$phone;
    }

    if (strlen($phone) != 14) {
        return;
    }

    $identifier = session()->getId();
    $cart = $cart = DB::table('shopping_cart')
        ->where('phone', $phone)
        ->first();
    if ($cart) {
        $identifier = $cart->identifier;
    }

    $cart = DB::table('shopping_cart')
        ->where('identifier', $identifier)
        ->first();

    if ($cart) {
        $content = unserialize($cart->content)->diffKeys($content);
        if ($content->isEmpty()) {
            return DB::table('shopping_cart')
                ->where('identifier', $identifier)
                ->delete();
        }
        DB::table('shopping_cart')
            ->where('identifier', $identifier)
            ->update([
                'name' => Cookie::get('name'),
                'phone' => $phone,
                'content' => serialize($content),
                'identifier' => session()->getId(),
                'updated_at' => now(),
            ]);

        return;
    }

    DB::table('shopping_cart')
        ->insert([
            'name' => Cookie::get('name'),
            'phone' => $phone,
            'instance' => 'default',
            'identifier' => session()->getId(),
            'content' => serialize($content),
            'updated_at' => now(),
        ]);
}

function isOninda(): bool
{
    return config('app.oninda');
}

function isReseller(): bool
{
    if (config('app.reseller') === false) {
        return false;
    }

    static $reseller = null;
    if ($reseller === null) {
        $reseller = (bool) config('app.oninda_url');
    }

    return $reseller;
}
