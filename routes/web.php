<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Npo\ApplicationController;
use App\Http\Controllers\Npo\DashboardController;
use App\Http\Controllers\Npo\EventController as NpoEventController;
use App\Http\Controllers\Public\EventController as PublicEventController;
use App\Http\Controllers\Public\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website (volunteers)
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');

Route::get('/events', [PublicEventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [PublicEventController::class, 'show'])
    ->whereNumber('event')
    ->name('events.show');

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

        Route::name('npo.')->group(function () {
            // My initiatives: /npo/events, /npo/events/create, /npo/events/{event}/edit ...
            Route::resource('events', NpoEventController::class)->except('show');

            // Applications
            Route::get('applications', [ApplicationController::class, 'index'])
                ->name('applications.index');
            Route::get('applications/{submission}', [ApplicationController::class, 'show'])
                ->whereNumber('submission')
                ->name('applications.show');
            Route::patch('applications/{submission}/review', [ApplicationController::class, 'review'])
                ->whereNumber('submission')
                ->name('applications.review');
            Route::get('applications/{submission}/resume', [ApplicationController::class, 'resume'])
                ->whereNumber('submission')
                ->name('applications.resume');
        });
    });
});