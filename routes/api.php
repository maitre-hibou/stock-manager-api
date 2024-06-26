<?php

use App\Http\Controllers\API as APIControllers;
use App\Http\Middleware\Authentication as AuthenticationMiddleware;
use App\Http\Middleware\Authorization as AuthorizationMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::get('/', fn() => response()->json(['version' => config('app.api.version'), 'status' => 'OK']));

    Route::middleware(AuthenticationMiddleware\EnsureJWTAuthentication::class)->group(function () {
        Route::get('/me', fn(Request $request) => $request->user());
    });

    Route::prefix('product')->group(function () {
        Route::get('/', APIControllers\Product\ListProductController::class)->name('api_product_list');
        Route::get('/{product}', APIControllers\Product\ReadProductController::class)->name('api_product_read');

        Route::middleware([AuthenticationMiddleware\EnsureJWTAuthentication::class])->group(function () {
            Route::post('/', APIControllers\Product\StoreProductController::class)->name('api_product_store');
            Route::put('/{product}', APIControllers\Product\UpdateProductController::class)->name('api_product_update');
            Route::delete('/{product}', APIControllers\Product\DeleteProductController::class)->name('api_product_delete');

            Route::get('/{product}/stock_movements', APIControllers\Product\StockMovement\ListProductStockMovementController::class)->name('api_product_stock_movements_list');
            Route::post('/{product}/stock_movements', APIControllers\Product\StockMovement\StoreProductStockMovementController::class)->name('api_product_stock_movements_store');
        });
    });

    Route::prefix('stock_movement')
        ->middleware([
            AuthenticationMiddleware\EnsureJWTAuthentication::class,
            AuthorizationMiddleware\EnsureIsAdmin::class
        ])
        ->group(function () {
            Route::get('/', APIControllers\StockMovement\ListStockMovementController::class)->name('api_stock_movement_list');
            Route::post('/', APIControllers\StockMovement\StoreStockMovementController::class)->name('api_stock_movement_store');
        });
});


