<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::get('/', fn() => response()->json(['version' => config('app.api.version'), 'status' => 'OK']));

    Route::middleware(\App\Http\Middleware\Auth\EnsureJWTAuthentication::class)->group(function () {
        Route::get('/me', fn(Request $request) => $request->user());
    });
});


