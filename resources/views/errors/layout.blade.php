{{-- Standalone on purpose: must still work when something else is broken. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') · @yield('title') · {{ config('app.name', 'BantuNet') }}</title>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/errors.css') }}">
</head>
<body class="error-page">
    <main class="error">
        <a href="{{ url('/') }}" class="logo" aria-label="BantuNet home">
            <svg class="logo__mark" viewBox="0 8 64 48" aria-hidden="true" focusable="false">
                <path class="logo__half--left" d="M29 53 C 11 41, 2 31, 7 20 C 11 11, 24 10, 29 20 Z"/>
                <path class="logo__half--right" d="M35 20 C 40 10, 53 11, 57 20 C 62 31, 53 41, 35 53 Z"/>
            </svg>
            <span class="logo__text">
                <span class="logo__word"><span class="logo__bantu">bantu</span><span class="logo__net">net</span></span>
                <span class="logo__tagline">Your perfect volunteer match</span>
            </span>
        </a>

        <p class="error__code">@yield('code')</p>
        <h1 class="error__title">@yield('title')</h1>
        <p class="error__message">@yield('message')</p>

        <div class="error__actions">
            <a href="{{ url('/') }}" class="btn btn--primary">Go to home page</a>
            <button type="button" class="btn btn--secondary" onclick="history.back()">Go back</button>
        </div>
    </main>
</body>
</html>