<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));

Route::prefix('auth')->group(function () {
    Route::post('/login', LoginController::class)->middleware('guest')->name('auth_login');
})->name('auth');
