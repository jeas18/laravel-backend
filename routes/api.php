<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CurrencyController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProductPriceController;
use Illuminate\Support\Facades\Route;

/*
| All routes are served under the "api/v1" prefix (see bootstrap/app.php).
*/

Route::prefix('auth')->name('auth.')->group(function () {
    Route::middleware('throttle:auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');
    });

    Route::post('logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('logout');
});

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('currencies', [CurrencyController::class, 'index'])->name('currencies.index');

    Route::apiResource('products', ProductController::class);

    Route::prefix('products/{product}/prices')->name('products.prices.')->group(function () {
        Route::get('/', [ProductPriceController::class, 'index'])->name('index');
        Route::post('/', [ProductPriceController::class, 'store'])->name('store');
    });
});
