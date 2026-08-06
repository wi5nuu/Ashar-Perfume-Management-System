<?php

use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\PosController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SecurityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Authenticated user info
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public health check — no auth required
Route::get('/health', HealthController::class)->name('api.health');

// ── General authenticated endpoints ─────────────────────────────────────
Route::middleware(['auth:sanctum', 'throttle:60,1'])
    ->prefix('v1')
    ->name('api.')
    ->group(function () {

        // Products
        Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

        // POS
        Route::post('/pos/validate-cart', [PosController::class, 'validateCart'])->name('pos.validate-cart');
        Route::post('/pos/calculate-change', [PosController::class, 'calculateChange'])->name('pos.calculate-change');
        Route::get('/pos/check-stock/{product}', [PosController::class, 'checkStock'])->name('pos.check-stock');

        // Inventory
        Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
    });

// ── Admin-only endpoints ─────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'throttle:120,1', 'role:owner,admin'])
    ->prefix('v1/admin')
    ->name('api.admin.')
    ->group(function () {

        Route::post('/security/force-unlock/{user}', [SecurityController::class, 'forceUnlock'])->name('security.unlock');
        Route::get('/security/active-sessions', [SecurityController::class, 'activeSessions'])->name('security.active-sessions');
    });
