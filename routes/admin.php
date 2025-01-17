<?php

use App\Http\Controllers\Admin\ApiController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeOptionController;
use App\Http\Controllers\Admin\Auth\ChangePasswordController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategoryMenuController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\HomeSectionController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\StaffController;
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
        //...
        //...
    });

    // Route::post('resend-otp', 'Auth\LoginController@resendOTP')->name('resend-otp');

    Route::permanentRedirect('/admin', '/admin/dashboard'); // Permanent Redirect
    Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin']], function (): void {
        // Admin Level Namespace & 'admin' Prefix
        Route::get('/dashboard', [HomeController::class, 'index'])->name('home');
        Route::match(['get', 'post'], '/profile', ChangePasswordController::class)
            ->name('password.change');
        Route::any('settings', SettingController::class)->name('settings');
        Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
        Route::get('/reports/filter', [OrderController::class, 'filter'])->name('orders.filter');
        Route::get('/reports/customer', [ReportController::class, 'customer'])->name('reports.customer');
        Route::get('/orders/pathao-csv', [OrderController::class, 'csv'])->name('orders.pathao-csv');
        Route::get('/orders/invoices', [OrderController::class, 'invoices'])->name('orders.invoices');
        Route::get('/orders/booking', [OrderController::class, 'booking'])->name('orders.booking');
        Route::post('/orders/change-courier', [OrderController::class, 'courier'])->name('orders.courier');
        Route::post('/orders/change-status', [OrderController::class, 'status'])->name('orders.status');
        Route::post('/orders/change-staff', [OrderController::class, 'staff'])->name('orders.staff');
        Route::patch('/orders/{order}/update-quantity', [OrderController::class, 'updateQuantity'])->name('orders.update-quantity');
        Route::post('/logout-others/{admin}', [ApiController::class, 'logoutOthers'])->name('logout-others');
        Route::get('/customers', CustomerController::class)->name('customers');
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
        ]);
    });
});

// Controller Level Namespace & No Prefix
//...
//...
