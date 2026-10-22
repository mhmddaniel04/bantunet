<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Npo\ApplicationController as NpoApplicationController;
use App\Http\Controllers\Npo\DashboardController;
use App\Http\Controllers\Npo\EventController as NpoEventController;
use App\Http\Controllers\Npo\EventSuggestionController;
use App\Http\Controllers\Npo\ProfileController;
use App\Http\Controllers\Npo\ReportController;
use App\Http\Controllers\Npo\RosterController;
use App\Http\Controllers\Public\ApplicationController as PublicApplicationController;
use App\Http\Controllers\Public\EventController as PublicEventController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\MatchController;
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

// Smart AI Match
Route::get('/match', [MatchController::class, 'index'])->name('match.index');
Route::get('/match/results', [MatchController::class, 'results'])
    ->middleware('throttle:15,1')
    ->name('match.results');

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
            // Profile & settings
            Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
                Route::get('/', 'edit')->name('edit');
                Route::put('/', 'update')->name('update');
                Route::put('email', 'updateEmail')->middleware('throttle:6,1')->name('email');
                Route::put('password', 'updatePassword')->middleware('throttle:6,1')->name('password');
                Route::delete('/', 'destroy')->middleware('throttle:3,1')->name('destroy');
            });

            // AI: suggest category & skills from a description (JSON)
            Route::post('events/suggest-tags', EventSuggestionController::class)
                ->middleware('throttle:20,1')
                ->name('events.suggest');

            // My initiatives
            Route::resource('events', NpoEventController::class)->except('show');

            // Volunteer list per initiative (roster, attendance, exports)
            Route::controller(RosterController::class)
                ->prefix('events/{event}/volunteers')
                ->whereNumber('event')
                ->group(function () {
                    Route::get('/', 'show')->name('events.roster');
                    Route::put('attendance', 'attendance')->name('events.attendance');
                    Route::get('export', 'export')->name('events.roster.export');
                    Route::get('print', 'print')->name('events.roster.print');
                });

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

            // Engagement report
            Route::controller(ReportController::class)->prefix('reports')->group(function () {
                Route::get('/', 'index')->name('reports.index');
                Route::get('export', 'export')->name('reports.export');
                Route::get('print', 'print')->name('reports.print');
            });
        });
    });
});