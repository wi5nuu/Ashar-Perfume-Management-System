<?php

use App\Http\Controllers\Api\PosController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('v1')->name('api.')->group(function () {
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    Route::post('/pos/validate-cart', [PosController::class, 'validateCart'])->name('pos.validate-cart');
    Route::post('/pos/calculate-change', [PosController::class, 'calculateChange'])->name('pos.calculate-change');
    Route::get('/pos/check-stock/{product}', [PosController::class, 'checkStock'])->name('pos.check-stock');

    Route::get('/inventory/low-stock', function () {
        return \App\Models\Inventory::with('product')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('branch_id', auth()->user()->branch_id)
            ->limit(20)
            ->get();
    })->name('inventory.low-stock');
});

Route::middleware(['auth:sanctum', 'throttle:120,1'])->prefix('v1/admin')->name('api.admin.')->group(function () {
    Route::post('/security/force-unlock/{user}', function (\App\Models\User $user) {
        if (!in_array(request()->user()->role, ['owner', 'admin'])) {
            abort(403, 'Unauthorized');
        }
        $user->unlock();
        return response()->json(['message' => 'User unlocked successfully']);
    })->name('security.unlock');

    Route::get('/security/active-sessions', function () {
        if (!in_array(request()->user()->role, ['owner', 'admin'])) {
            abort(403, 'Unauthorized');
        }
        return \App\Models\LoginActivity::with('user')
            ->whereDate('created_at', today())
            ->distinct('user_id')
            ->count();
    })->name('security.active-sessions');
});
