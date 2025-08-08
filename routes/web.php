<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\BrandProductController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HomeSectionProductController;
use App\Http\Controllers\OrderTrackController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ResellerController;
use App\Http\Middleware\EnsureResellerIsVerified;
use App\Http\Middleware\GoogleTagManagerMiddleware;
use Hotash\FacebookPixel\MetaPixelMiddleware;
use Hotash\LaravelMultiUi\Facades\MultiUi;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

// Language Change
// Route::get('lang/{locale}', function ($locale) {
//     if (!in_array($locale, ['en', 'de', 'es', 'fr', 'pt', 'cn', 'ae'])) {
//         abort(400);
//     }
//     Session::put('locale', $locale);
//     return redirect()->back();
// })->name('lang');

Route::middleware([GoogleTagManagerMiddleware::class, MetaPixelMiddleware::class])->group(function (): void {
    Route::group(['as' => 'user.'], function (): void {

        Route::namespace('App\\Http\\Controllers\\User')->group(function (): void {
            // Admin Level Namespace & No Prefix
            MultiUi::routes([
                'register' => true,
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
            // ...
            // ...
            Route::post('resend-otp', 'Auth\LoginController@resendOTP')->name('resend-otp');

            // Password reset routes
            Route::post('password/resend-otp', 'Auth\ForgotPasswordController@resendOtp')->name('password.resend-otp');

            // User profile and orders routes
            Route::middleware('auth:user')->group(function () {
                Route::match(['get', 'post'], 'profile', 'ProfileController')->name('profile');
                Route::get('orders', 'OrderController')->name('orders');

                // Payment routes for account verification
                Route::get('payment/verification', 'PaymentController@showPaymentForm')->name('payment.verification');
                Route::post('payment/apply-coupon', 'PaymentController@applyCoupon')->name('payment.apply-coupon');
                Route::post('payment/create', 'PaymentController@createPayment')->name('payment.create');

                // Transaction routes
                Route::get('transactions', 'TransactionController@index')->name('transactions');
                Route::post('withdraw-request', 'TransactionController@withdrawRequest')->name('withdraw.request');
            });

            // bKash callback route (no auth required)
            Route::get('bkash/callback', 'BkashCallbackController@callback')->name('bkash.callback');
        });

    });

    // Reseller Panel Routes (outside user group but with auth middleware)
    Route::middleware('auth:user')->group(function () {
        Route::prefix('reseller')->name('reseller.')->group(function () {
            Route::get('dashboard', [ResellerController::class, 'dashboard'])->name('dashboard');
            Route::get('orders', [ResellerController::class, 'orders'])->name('orders');
            Route::get('orders/{order}', [ResellerController::class, 'showOrder'])->name('orders.show');
            Route::get('orders/{order}/edit', [ResellerController::class, 'editOrder'])->name('orders.edit');
            Route::post('orders/{order}/cancel', [ResellerController::class, 'cancelOrder'])->name('orders.cancel');
            Route::get('transactions', [ResellerController::class, 'transactions'])->name('transactions');
            Route::get('profile', [ResellerController::class, 'profile'])->name('profile');
            Route::post('profile', [ResellerController::class, 'updateProfile'])->name('profile');
        });
    });

    Route::get('/categories', [ApiController::class, 'categories'])->name('categories');
    Route::get('/brands', [ApiController::class, 'brands'])->name('brands');
    Route::post('save-checkout-progress', [ApiController::class, 'saveCheckoutProgress']);

    Route::get('/', HomeController::class)->name('/');
    Route::get('/sections/{section}/products', HomeSectionProductController::class)->name('home-sections.products');
    Route::get('/shop', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/categories/{category:slug}/products', CategoryProductController::class)->name('categories.products');
    Route::get('/brands/{brand:slug}/products', BrandProductController::class)->name('brands.products');

    Route::view('/cart', 'cart')->name('cart');
    Route::match(['get', 'post'], '/checkout', CheckoutController::class)->name('checkout')->middleware(EnsureResellerIsVerified::class);
    Route::get('/thank-you', OrderTrackController::class)->name('thank-you');
    Route::match(['get', 'post'], 'track-order', OrderTrackController::class)->name('track-order');

    pageRoutes();
});

Route::get('/storage-link', [ApiController::class, 'storageLink']);
Route::get('/scout-flush', [ApiController::class, 'scoutFlush']);
Route::get('/scout-import', [ApiController::class, 'scoutImport']);
Route::get('/link-optimize', [ApiController::class, 'linkOptimize']);
Route::get('/cache-clear', [ApiController::class, 'clearCache'])->name('clear.cache');
