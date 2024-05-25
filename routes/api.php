<?php

use App\Http\Controllers\API as APIControllers;
use App\Http\Middleware\Auth as AuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::get('/', fn() => response()->json(['version' => config('app.api.version'), 'status' => 'OK']));

    Route::middleware(AuthMiddleWare\EnsureJWTAuthentication::class)->group(function () {
        Route::get('/me', fn(Request $request) => $request->user());
    });

    Route::prefix('product')->group(function () {
        Route::get('/', APIControllers\Product\ListProductController::class)->name('api_product_list');
        Route::get('/{product}', APIControllers\Product\ReadProductController::class)->name('api_product_read');

        Route::middleware([AuthMiddleWare\EnsureJWTAuthentication::class])->group(function () {
            Route::post('/', APIControllers\Product\StoreProductController::class)->name('api_product_store');
            Route::put('/{product}', APIControllers\Product\UpdateProductController::class)->name('api_product_update');
        });
    });
});


