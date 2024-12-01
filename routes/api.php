<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\MenuItemSortController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
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

Route::group(['as' => 'api.'], function () {
    Route::get('products', ProductController::class)->name('products');
    Route::get('images', [ImageController::class, 'index'])->name('images.index');
    Route::get('images/single', [ImageController::class, 'single'])->name('images.single');
    Route::get('images/multiple', [ImageController::class, 'multiple'])->name('images.multiple');
    Route::post('menu/{menu}/sort-items', [MenuItemSortController::class])->name('menu-items.sort');
    Route::get('orders', OrderController::class)->name('orders');

    Route::get('slides', [ApiController::class, 'slides']);
    Route::get('sections', [ApiController::class, 'sections']);
    Route::get('sections/{section}/products', [ApiController::class, 'sectionProducts']);
    Route::get('products/{product:slug}.json', [ApiController::class, 'product']);
    Route::get('products/{product:slug}/related.json', [ApiController::class, 'relatedProducts']);
    Route::get('areas/{city_id}', [ApiController::class, 'areas']);
    Route::get('categories', [ApiController::class, 'categories']);
    Route::get('products/{search}', [ApiController::class, 'products']);
    Route::get('pending-count/{admin}', [ApiController::class, 'pendingCount']);
    Route::post('pathao-webhook', [ApiController::class, 'pathaoWebhook']);
});
