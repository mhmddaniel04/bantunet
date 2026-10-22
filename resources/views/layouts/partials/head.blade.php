{{--
    Shared <head>. Pass extra page CSS files by name:
    @include('layouts.partials.head', ['css' => ['auth']])
    The ?v= number changes whenever a file is saved, so browsers never use old CSS.
--}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#1A1A1A">

<title>@hasSection('title')@yield('title') · @endif{{ config('app.name') }}</title>

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,700;0,800;1,700&display=swap">

@foreach (array_merge(['app'], $css ?? []) as $file)
    <link rel="stylesheet" href="{{ asset("css/{$file}.css") }}?v={{ filemtime(public_path("css/{$file}.css")) }}">
@endforeach

@stack('styles')

<script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>