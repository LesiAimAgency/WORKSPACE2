<?php

use App\Http\Controllers\Api\AgencyAuthController;
use App\Http\Controllers\Api\ShopApiController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\V1\ProjectApiController;
use App\Http\Controllers\Api\WidgetController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.token'])->group(function () {
    Route::post('/sync/widgets', [SyncController::class, 'syncWidgets']);
    Route::post('/sync/settings', [SyncController::class, 'syncSettings']);
});

Route::middleware('agency.verify')->group(function () {
    Route::post('/agency/auth', [AgencyAuthController::class, 'requestMagicLink']);
});

Route::post('/widgets/sync', [WidgetController::class, 'sync']);
Route::get('/widgets/render/{code}', [WidgetController::class, 'renderPartial']);

// Viettinmart Headless Shop API
Route::prefix('v1/shop')->group(function () {
    Route::get('/products', [ShopApiController::class, 'products']);
    Route::get('/products/{slug}', [ShopApiController::class, 'product']);
    Route::get('/categories', [ShopApiController::class, 'categories']);
    Route::get('/cart', [ShopApiController::class, 'cart']);
    Route::post('/cart/add', [ShopApiController::class, 'addToCart']);
    Route::post('/cart/update', [ShopApiController::class, 'updateCart']);
    Route::post('/cart/remove', [ShopApiController::class, 'removeFromCart']);
    Route::post('/checkout', [ShopApiController::class, 'checkout']);
});

// VGT Core Remote Management API (v1) - Control Plane <-> Data Plane
Route::prefix('v1/projects')->name('api.v1.projects.')->group(function () {
    Route::post('/register', [ProjectApiController::class, 'register'])->name('register');
    Route::post('/heartbeat', [ProjectApiController::class, 'heartbeat'])->name('heartbeat');
    Route::get('/status', [ProjectApiController::class, 'status'])->name('status');
    Route::post('/report', [ProjectApiController::class, 'report'])->name('report');
    Route::post('/token/rotate', [ProjectApiController::class, 'rotateToken'])->name('token.rotate');
});
