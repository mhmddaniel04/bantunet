<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Extra browser protections added to every response.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = $response->headers;

        // Don't let browsers guess file types (stops some upload attacks).
        $headers->set('X-Content-Type-Options', 'nosniff');

        // Don't allow other websites to show BantuNet inside a frame (clickjacking).
        $headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Only send our domain (not full URLs with tracking codes) to other sites.
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // BantuNet never needs camera, microphone, location or payments.
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Pages with personal data must never be stored by the browser or a proxy.
        if ($request->routeIs('npo.*', 'track.*')) {
            $headers->set('Cache-Control', 'no-store, private');
        }

        return $response;
    }
}