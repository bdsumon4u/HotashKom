<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BrandProductController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HomeSectionProductController;
use App\Http\Controllers\LandingPageProController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MaintenancePaymentController;
use App\Http\Controllers\OrderTrackController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ResellerController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\EnsureResellerIsVerified;
use App\Http\Middleware\GoogleTagManagerMiddleware;
use Hotash\FacebookPixel\MetaPixelMiddleware;
use Hotash\LaravelMultiUi\Facades\MultiUi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('land', fn () => view('land'))->name('land');

// Language Change
// Route::get('lang/{locale}', function ($locale) {
//     if (!in_array($locale, ['en', 'de', 'es', 'fr', 'pt', 'cn', 'ae'])) {
//         abort(400);
//     }
//     Session::put('locale', $locale);
//     return redirect()->back();
// })->name('lang');

Route::get('maintenance-payment', MaintenancePaymentController::class)->name('maintenance.payment');
Route::post('maintenance-payment/pay', [MaintenancePaymentController::class, 'pay'])->name('maintenance.payment.pay');
Route::post('maintenance-payment/defer', [MaintenancePaymentController::class, 'defer'])->name('maintenance.payment.defer');

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
            Route::middleware('auth:user')->group(function (): void {
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
    Route::middleware('auth:user')->group(function (): void {
        Route::prefix('reseller')->name('reseller.')->group(function (): void {
            Route::get('dashboard', [ResellerController::class, 'dashboard'])->name('dashboard');
            Route::get('profile', [ResellerController::class, 'profile'])->name('profile');
            Route::post('profile', [ResellerController::class, 'updateProfile'])->name('profile.update');

            // Routes that require verification
            Route::middleware(EnsureResellerIsVerified::class)->group(function (): void {
                Route::get('products', [ResellerController::class, 'products'])->name('products');
                Route::get('orders', [ResellerController::class, 'orders'])->name('orders');
                Route::get('orders/{order}', [ResellerController::class, 'showOrder'])->name('orders.show');
                Route::get('orders/{order}/edit', [ResellerController::class, 'editOrder'])->name('orders.edit');
                Route::post('orders/{order}/cancel', [ResellerController::class, 'cancelOrder'])->name('orders.cancel');
                Route::get('transactions', [ResellerController::class, 'transactions'])->name('transactions');
                Route::match(['GET', 'POST'], 'checkout', [ResellerController::class, 'checkout'])
                    ->name('checkout')
                    ->middleware('doNotCacheResponse');
                Route::get('thank-you', [ResellerController::class, 'thankYou'])
                    ->name('thank-you')
                    ->middleware('doNotCacheResponse');
            });
        });
    });

    Route::post('save-checkout-progress', [ApiController::class, 'saveCheckoutProgress']);

    Route::get('/', HomeController::class)->name('/');
    Route::get('/sections/{section}/products', HomeSectionProductController::class)->name('home-sections.products');
    Route::get('/shop', [ProductController::class, 'index'])->name('products.index');
    Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
    Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
    Route::post('/products/{product:slug}/reviews', [ReviewController::class, 'store'])->name('products.reviews.store');
    Route::get('/products/{product:slug}/reviews', [ReviewController::class, 'index'])->name('products.reviews.index');
    Route::get('/category/{category:slug}', CategoryProductController::class)->name('category.show');
    Route::get('/categories/{category:slug}/products', CategoryProductController::class)->name('categories.products');
    Route::get('/brand/{brand:slug}', BrandProductController::class)->name('brand.show');
    Route::get('/brands/{brand:slug}/products', BrandProductController::class)->name('brands.products');
    Route::get('/lp/{landingPagePro:slug}', [LandingPageProController::class, 'show'])->name('landing-pro.show');
    Route::post('/lp/{landingPagePro:slug}/checkout', [LandingPageProController::class, 'checkout'])->name('landing-pro.checkout');
    Route::view('/lead-form', 'leads.form')->name('leads.form');
    Route::post('/leads', [LeadController::class, 'store'])
        ->middleware('throttle:1,10')
        ->name('leads.store');
    Route::middleware('response.cache')->group(function (): void {
        Route::get('/categories', [ApiController::class, 'categories'])->name('categories');
        Route::get('/brands', [ApiController::class, 'brands'])->name('brands');

        Route::get('/', HomeController::class)->name('/');
        Route::get('/sections/{section}/products', HomeSectionProductController::class)->name('home-sections.products');
        Route::get('/shop', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/categories/{category:slug}/products', CategoryProductController::class)->name('categories.products');
        Route::get('/brands/{brand:slug}/products', BrandProductController::class)->name('brands.products');

        Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
        Route::get('/blogs/{blog:slug}', [BlogController::class, 'show'])->name('blogs.show');

        pageRoutes();
    });

    Route::view('/cart', 'cart')
        ->name('cart')
        ->middleware('doNotCacheResponse');

    Route::match(['get', 'post'], '/checkout', CheckoutController::class)
        ->name('checkout')
        ->middleware(['doNotCacheResponse', EnsureResellerIsVerified::class]);

    Route::get('/thank-you', OrderTrackController::class)
        ->name('thank-you')
        ->middleware('doNotCacheResponse');

    Route::match(['get', 'post'], 'track-order', OrderTrackController::class)
        ->name('track-order')
        ->middleware('doNotCacheResponse');

    // Cart API routes (moved from api.php to use same session)
    Route::post('cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('cart', [CartController::class, 'get'])->name('cart.get');

});

Route::get('/storage-link', [ApiController::class, 'storageLink']);
Route::get('/scout-flush', [ApiController::class, 'scoutFlush']);
Route::get('/scout-import', [ApiController::class, 'scoutImport']);
Route::get('/link-optimize', [ApiController::class, 'linkOptimize']);
Route::get('/cache-clear', [ApiController::class, 'clearCache'])->name('clear.cache');

// Feed routes
Route::get('/feed/catalog', [FeedController::class, 'catalog'])->name('feed.catalog');
Route::get('/feed/catalog-simple', [FeedController::class, 'catalogSimple'])->name('feed.catalog.simple');

// Secure MySQL connection diagnostics — access via /db-status?key=YOUR_DEBUG_KEY
Route::get('/db-status', function () {
    $secret = config('app.debug_key');
    if (! $secret || request('key') !== $secret) {
        abort(403);
    }

    $status = collect(DB::select('SHOW GLOBAL STATUS'))
        ->keyBy('Variable_name');

    $variables = collect(DB::select('SHOW VARIABLES'))
        ->keyBy('Variable_name');

    $processes = DB::select('SHOW PROCESSLIST');

    return response()->json([
        'connections' => [
            'current' => $status->get('Threads_connected')?->Value,
            'max_ever' => $status->get('Max_used_connections')?->Value,
            'limit' => $variables->get('max_connections')?->Value,
            'aborted_connects' => $status->get('Aborted_connects')?->Value,  // refused: max_connections hit
            'aborted_clients' => $status->get('Aborted_clients')?->Value,   // dropped: client died mid-connection
        ],
        'timeouts' => [
            'wait_timeout' => $variables->get('wait_timeout')?->Value,
            'interactive_timeout' => $variables->get('interactive_timeout')?->Value,
            'connect_timeout' => $variables->get('connect_timeout')?->Value,
        ],
        'traffic' => [
            'total_connections_ever' => $status->get('Connections')?->Value,
            'global_queries' => $status->get('Queries')?->Value,
            'slow_queries' => $status->get('Slow_queries')?->Value,
            'threads_running' => $status->get('Threads_running')?->Value,   // actively executing (not sleeping)
            'threads_cached' => $status->get('Threads_cached')?->Value,    // waiting to be reused
        ],
        // Only shows YOUR own connections if you lack PROCESS privilege on shared hosting.
        // On cPanel shared hosting this will typically only show 1 row (this request itself).
        'your_process_list' => $processes,
        'checked_at' => now()->toDateTimeString(),
    ], 200, [], JSON_PRETTY_PRINT);
});
