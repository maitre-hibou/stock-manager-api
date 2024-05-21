<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::get('/', fn() => response()->json(['version' => config('app.api.version'), 'status' => 'OK']));
});
