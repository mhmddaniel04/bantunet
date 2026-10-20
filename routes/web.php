<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Npo\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website (volunteers) — Phase 4
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome')->name('home');

/*
|--------------------------------------------------------------------------
| NPO portal  →  /npo/...
|--------------------------------------------------------------------------
*/

Route::prefix('npo')->group(function () {

    // Visitors who are NOT logged in
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->name('login.store');

        Route::get('register', [RegisterController::class, 'create'])->name('register');
        Route::post('register', [RegisterController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('register.store');
    });

    // Logged-in organisations
    Route::middleware('auth')->group(function () {
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

        Route::get('dashboard', DashboardController::class)->name('npo.dashboard');
    });
});