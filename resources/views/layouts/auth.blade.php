<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head', ['css' => ['auth']])
</head>
<body>

<div class="auth">

    {{-- Left: brand panel (desktop only) --}}
    <div class="auth__panel">
        <span class="auth__glow auth__glow--top"></span>
        <span class="auth__glow auth__glow--bottom"></span>

        <div class="auth__panel-inner">
            <x-logo variant="light" tagline />

            <div>
                <p class="eyebrow">For non-profit organisations</p>
                <h1 class="auth__headline">
                    Less time on spreadsheets.
                    <em>More time on your mission.</em>
                </h1>

                <ul class="auth__features">
                    @foreach ([
                        'Publish volunteer initiatives in minutes.',
                        'Smart AI matching brings you volunteers with the right skills.',
                        'Review applications and export reports for funders.',
                    ] as $feature)
                        <li>
                            <span class="auth__step">{{ $loop->iteration }}</span>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <p class="auth__copyright">&copy; {{ now()->year }} BantuNet · Intelligence Volunteer Matching</p>
        </div>
    </div>

    {{-- Right: the form --}}
    <main class="auth__main">
        <div class="auth__box @yield('box_class')">
            <x-logo class="auth__mobile-logo" />

            @yield('content')
        </div>
    </main>
</div>

<x-flash />
</body>
</html>