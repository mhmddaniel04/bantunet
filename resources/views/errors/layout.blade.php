{{-- Standalone on purpose: must still work when something else is broken. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') · @yield('title') · {{ config('app.name', 'BantuNet') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700;800&display=swap">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/errors.css') }}">
</head>
<body class="error-page">
    <main class="error">
        <a href="{{ url('/') }}" class="logo">
            <span class="logo__mark">B</span>
            <span class="logo__text">BantuNet</span>
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