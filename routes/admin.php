<?php

use App\Http\Controllers\Admin\ApiController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeOptionController;
use App\Http\Controllers\Admin\Auth\ChangePasswordController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CartController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategoryMenuController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\HomeSectionController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\MoneyRequestController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariationController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ResellerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShipmentReportController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\TransactionController;
use Hotash\LaravelMultiUi\Facades\MultiUi;
use Illuminate\Support\Facades\Route;

// Controller Level Namespace
Route::group(['as' => 'admin.'], function (): void {

    Route::namespace('App\\Http\\Controllers\\Admin')->group(function (): void {
        // Admin Level Namespace & No Prefix
        MultiUi::routes([
            'register' => false,
            'URLs' => [
                'login' => 'getpass',
                'register' => 'create-admin-account',
                'reset/password' => 'reset-pass',
                'logout' => 'getout',
            ],
            'prefix' => [
                'URL' => 'admin-',
                'except' => ['login', 'register'],
            ],
        ]);
        // ...
        // ...
    });

    // Route::post('resend-otp', 'Auth\LoginController@resendOTP')->name('resend-otp');

    Route::permanentRedirect('/admin', '/admin/dashboard'); // Permanent Redirect
    Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin']], function (): void {
        // Admin Level Namespace & 'admin' Prefix
        Route::get('carts', [CartController::class, 'index'])->name('carts.index');
        Route::delete('carts/{identifier}', [CartController::class, 'destroy'])->name('carts.destroy');
        Route::get('/dashboard', [HomeController::class, 'index'])->name('home');
        Route::match(['get', 'post'], '/profile', ChangePasswordController::class)
            ->name('password.change');
        Route::any('settings', SettingController::class)->name('settings');
        Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
        Route::get('/reports/shipment', [ShipmentReportController::class, 'index'])->name('reports.shipment');
        Route::get('/reports/filter', [OrderController::class, 'filter'])->name('orders.filter');
        Route::get('/reports/customer', [ReportController::class, 'customer'])->name('reports.customer');
        Route::get('/orders/pathao-csv', [OrderController::class, 'csv'])->name('orders.pathao-csv');
        Route::get('/orders/invoices', [OrderController::class, 'invoices'])->name('orders.invoices');
        Route::get('/orders/stickers', [OrderController::class, 'stickers'])->name('orders.stickers');
        Route::get('/orders/booking', [OrderController::class, 'booking'])->name('orders.booking');
        Route::post('orders/forward-to-oninda', [OrderController::class, 'forwardToOninda'])->name('orders.forward-to-oninda');
        Route::post('/orders/change-courier', [OrderController::class, 'courier'])->name('orders.courier');
        Route::post('/orders/change-status', [OrderController::class, 'status'])->name('orders.status');
        Route::post('/orders/change-staff', [OrderController::class, 'staff'])->name('orders.staff');
        Route::patch('/orders/{order}/update-quantity', [OrderController::class, 'updateQuantity'])->name('orders.update-quantity');
        Route::post('/logout-others/{admin}', [ApiController::class, 'logoutOthers'])->name('logout-others');
        Route::get('/customers', CustomerController::class)->name('customers');

        Route::get('resellers', [ResellerController::class, 'index'])->name('resellers.index');
        Route::get('resellers/{reseller}/edit', [ResellerController::class, 'edit'])->name('resellers.edit');
        Route::put('resellers/{reseller}', [ResellerController::class, 'update'])->name('resellers.update');

        // Money Requests Routes
        Route::get('money-requests', [MoneyRequestController::class, 'index'])->name('money-requests.index');
        Route::get('money-requests/data', [MoneyRequestController::class, 'data'])->name('money-requests.data');
        Route::post('money-requests/confirm', [MoneyRequestController::class, 'confirm'])->name('money-requests.confirm');
        Route::post('money-requests/delete', [MoneyRequestController::class, 'deleteRequest'])->name('money-requests.delete');
        Route::get('money-requests/summary', [MoneyRequestController::class, 'summary'])->name('money-requests.summary');

        Route::get('transactions/{user}', [TransactionController::class, 'index'])->name('transactions.index');
        Route::post('transactions/{user}/withdraw', [TransactionController::class, 'withdraw'])->name('transactions.withdraw');
        Route::post('transactions/{user}/confirm-withdraw', [TransactionController::class, 'confirmWithdraw'])->name('transactions.confirm-withdraw');
        Route::delete('transactions/{user}/delete-withdraw', [TransactionController::class, 'deleteWithdraw'])->name('transactions.delete-withdraw');

        // Coupon additional routes
        Route::get('coupons/generate-code', [CouponController::class, 'generateCode'])->name('coupons.generate-code');
        Route::post('coupons/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('coupons.toggle-status');

        Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
        Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');

        // Bulk variations update route
        Route::patch('products/{product}/variations/bulk-update', [ProductVariationController::class, 'bulkUpdate'])->name('products.variations.bulk-update');

        Route::resources([
            'staffs' => StaffController::class,
            'slides' => SlideController::class,
            'categories' => CategoryController::class,
            'brands' => BrandController::class,
            'attributes.options' => AttributeOptionController::class,
            'attributes' => AttributeController::class,
            'products.variations' => ProductVariationController::class,
            'products' => ProductController::class,
            'images' => ImageController::class,
            'orders' => OrderController::class,
            'reports' => ReportController::class,
            'home-sections' => HomeSectionController::class,
            'pages' => PageController::class,
            'menus' => MenuController::class,
            'menu-items' => MenuItemController::class,
            'category-menus' => CategoryMenuController::class,
            'purchases' => PurchaseController::class,
            'coupons' => CouponController::class,
        ]);

    });
});

// Controller Level Namespace & No Prefix
// ...
// ...
