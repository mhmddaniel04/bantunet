<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Npo\ApplicationController as NpoApplicationController;
use App\Http\Controllers\Npo\DashboardController;
use App\Http\Controllers\Npo\EventController as NpoEventController;
use App\Http\Controllers\Public\ApplicationController as PublicApplicationController;
use App\Http\Controllers\Public\EventController as PublicEventController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\TrackingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website (volunteers)
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');

// Browse & detail
Route::get('/events', [PublicEventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [PublicEventController::class, 'show'])
    ->whereNumber('event')
    ->name('events.show');

// Apply (no account needed)
Route::get('/events/{event}/apply', [PublicApplicationController::class, 'create'])
    ->whereNumber('event')
    ->name('events.apply');
Route::post('/events/{event}/apply', [PublicApplicationController::class, 'store'])
    ->whereNumber('event')
    ->middleware('throttle:5,1')
    ->name('events.apply.store');

// Track application (tracking code + email)
Route::get('/track', [TrackingController::class, 'index'])->name('track.index');
Route::post('/track', [TrackingController::class, 'lookup'])
    ->middleware('throttle:10,1')
    ->name('track.lookup');
Route::get('/track/{submission:tracking_code}', [TrackingController::class, 'show'])
    ->name('track.show');
Route::post('/track/{submission:tracking_code}/withdraw', [TrackingController::class, 'withdraw'])
    ->name('track.withdraw');

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
            // My initiatives
            Route::resource('events', NpoEventController::class)->except('show');

            // Applications
            Route::get('applications', [NpoApplicationController::class, 'index'])
                ->name('applications.index');
            Route::get('applications/{submission}', [NpoApplicationController::class, 'show'])
                ->whereNumber('submission')
                ->name('applications.show');
            Route::patch('applications/{submission}/review', [NpoApplicationController::class, 'review'])
                ->whereNumber('submission')
                ->name('applications.review');
            Route::get('applications/{submission}/resume', [NpoApplicationController::class, 'resume'])
                ->whereNumber('submission')
                ->name('applications.resume');
        });
    });
});