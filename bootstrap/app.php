<?php

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Not logged in and opens an NPO page -> go to login
        $middleware->redirectGuestsTo(fn () => route('login'));

        // Already logged in and opens login/register -> go to dashboard
        $middleware->redirectUsersTo(fn () => route('npo.dashboard'));

        // Browser security headers on every response
        $middleware->append(SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();