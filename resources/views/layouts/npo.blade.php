<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head', ['css' => ['dashboard']])
</head>
<body>

<div class="npo">
    @include('layouts.partials.npo-sidebar')

    <div class="npo__main">
        {{-- Mobile top bar --}}
        <header class="topbar">
            <x-logo size="sm" :href="route('npo.dashboard')" />
            <button type="button" class="btn btn--ghost" data-toggle="#sidebar" aria-controls="sidebar" aria-expanded="false">
                ☰ Menu
            </button>
        </header>

        <main class="npo__content">
            @yield('content')
        </main>
    </div>
</div>

<x-flash />
</body>
</html>