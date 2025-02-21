<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\BrandProductController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HomeSectionProductController;
use App\Http\Controllers\OrderTrackController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\GoogleTagManagerMiddleware;
use Combindma\FacebookPixel\MetaPixelMiddleware;
use Hotash\LaravelMultiUi\Facades\MultiUi;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

//Language Change
// Route::get('lang/{locale}', function ($locale) {
//     if (!in_array($locale, ['en', 'de', 'es', 'fr', 'pt', 'cn', 'ae'])) {
//         abort(400);
//     }
//     Session::put('locale', $locale);
//     return redirect()->back();
// })->name('lang');

Route::middleware([GoogleTagManagerMiddleware::class, MetaPixelMiddleware::class])->group(function (): void {
    Route::get('auth', 'App\\Http\\Controllers\\User\\Auth\\LoginController@showLoginForm')->middleware('guest:user')->name('auth');

    Route::group(['as' => 'user.'], function (): void {

        Route::namespace('App\\Http\\Controllers\\User')->group(function (): void {
            // Admin Level Namespace & No Prefix
            MultiUi::routes([
                'register' => false,
                'URLs' => [
                    'login' => 'login',
                    'register' => 'register',
                    'reset/password' => 'reset-pass',
                    'logout' => 'logout',
                ],
                'prefix' => [
                    'URL' => 'user-',
                    'except' => ['login', 'register'],
                ],
            ]);
            //...
            //...
            Route::post('resend-otp', 'Auth\LoginController@resendOTP')->name('resend-otp');
        });

    });

    Route::get('/categories', [ApiController::class, 'categories'])->name('categories');
    Route::get('/brands', [ApiController::class, 'brands'])->name('brands');

    Route::get('/', HomeController::class)->name('/');
    Route::get('/sections/{section}/products', HomeSectionProductController::class)->name('home-sections.products');
    Route::get('/shop', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/categories/{category:slug}/products', CategoryProductController::class)->name('categories.products');
    Route::get('/brands/{brand:slug}/products', BrandProductController::class)->name('brands.products');

    Route::view('/cart', 'cart')->name('cart');
    Route::match(['get', 'post'], '/checkout', CheckoutController::class)->name('checkout');
    Route::get('/thank-you', OrderTrackController::class)->name('thank-you');
    Route::match(['get', 'post'], 'track-order', OrderTrackController::class)->name('track-order');

    pageRoutes();
});

Route::get('/storage-link', [ApiController::class, 'storageLink']);
Route::get('/scout-flush', [ApiController::class, 'scoutFlush']);
Route::get('/scout-import', [ApiController::class, 'scoutImport']);
Route::get('/link-optimize', [ApiController::class, 'linkOptimize']);
Route::get('/cache-clear', [ApiController::class, 'clearCache'])->name('clear.cache');
