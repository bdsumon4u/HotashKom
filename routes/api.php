<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\LivewireCheckoutController;
use App\Http\Controllers\Api\MenuItemSortController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['as' => 'api.', 'middleware' => HandleCors::class], function (): void {
    Route::get('products', ProductController::class)->name('products');
    Route::get('images', [ImageController::class, 'index'])->name('images.index');
    Route::get('images/single', [ImageController::class, 'single'])->name('images.single');
    Route::get('images/multiple', [ImageController::class, 'multiple'])->name('images.multiple');
    Route::post('menu/{menu}/sort-items', [MenuItemSortController::class])->name('menu-items.sort');
    Route::get('orders', OrderController::class)->name('orders');
    Route::get('purchases', \App\Http\Controllers\Api\PurchaseController::class)->name('purchases');
    Route::get('purchases/products', [\App\Http\Controllers\Api\PurchaseController::class, 'getProducts'])->name('purchases.products');
    Route::get('purchases/suppliers', [\App\Http\Controllers\Api\PurchaseController::class, 'getSuppliers'])->name('purchases.suppliers');

    Route::get('shop', [\App\Http\Controllers\ProductController::class, 'index']);
    Route::get('menus', [ApiController::class, 'menus']);
    Route::get('search/suggestions.json', [ApiController::class, 'searchSuggestions']);
    Route::get('page/{page:slug}', [ApiController::class, 'page']);
    Route::get('slides', [ApiController::class, 'slides']);
    Route::get('services', [ApiController::class, 'services']);
    Route::get('sections', [ApiController::class, 'sections']);
    Route::get('sections/{section}/products', [ApiController::class, 'sectionProducts']);
    Route::get('products/{slug}.json', [ApiController::class, 'product'])->name('product');
    Route::get('products/{slug}/related.json', [ApiController::class, 'relatedProducts']);
    Route::get('areas/{city_id}', [ApiController::class, 'areas']);
    Route::get('categories', [ApiController::class, 'categories']);
    Route::get('categories/{slug}.json', [ApiController::class, 'category']);
    Route::get('products/{search}', [ApiController::class, 'products']);
    Route::get('settings', [ApiController::class, 'settings']);
    Route::get('pending-count/{admin}', [ApiController::class, 'pendingCount']);
    Route::post('pathao-webhook', [ApiController::class, 'pathaoWebhook']);
    Route::post('checkout', LivewireCheckoutController::class);
    Route::get('orders/{order}', [ApiController::class, 'order']);
    Route::get('resellers', App\Http\Controllers\Api\ResellerController::class)->name('resellers');
    Route::put('resellers/{id}', [App\Http\Controllers\Api\ResellerController::class, 'update'])->name('resellers.update');
    Route::delete('resellers/{id}', [App\Http\Controllers\Api\ResellerController::class, 'destroy'])->name('resellers.destroy');
    Route::post('resellers/{id}/toggle-verify', [App\Http\Controllers\Api\ResellerController::class, 'toggleVerify'])->name('resellers.toggle-verify');
    Route::post('reseller/orders/place', [App\Http\Controllers\Api\ResellerOrderController::class, 'placeOrder'])
        ->name('api.reseller.orders.place');
});
