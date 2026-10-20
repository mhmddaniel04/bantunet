@php
    // Track application & Smart Match links are added in Phases 6–7
    $links = [
        ['label' => 'Home',               'route' => 'home',         'active' => 'home'],
        ['label' => 'Browse initiatives', 'route' => 'events.index', 'active' => 'events.*'],
    ];
@endphp

<header class="site-header">
    <div class="container">
        <div class="site-header__bar">
            <x-logo />

            <nav class="site-nav" aria-label="Main">
                @foreach ($links as $link)
                    <a href="{{ route($link['route']) }}"
                       @class(['site-nav__link', 'is-active' => request()->routeIs($link['active'])])>
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="site-header__actions">
                @auth
                    <a href="{{ route('npo.dashboard') }}" class="btn btn--secondary btn--sm">NPO dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn--ghost btn--sm">NPO sign in</a>
                    <a href="{{ route('register') }}" class="btn btn--primary btn--sm">Register NPO</a>
                @endauth
            </div>

            <button type="button" class="btn btn--ghost site-header__toggle"
                    data-toggle="#mobile-nav" aria-controls="mobile-nav" aria-expanded="false">
                ☰ Menu
            </button>
        </div>

        {{-- Mobile menu (opened by the ☰ button) --}}
        <nav id="mobile-nav" class="mobile-nav" aria-label="Mobile">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}"
                   @class(['mobile-nav__link', 'is-active' => request()->routeIs($link['active'])])>
                    {{ $link['label'] }}
                </a>
            @endforeach

            @auth
                <a href="{{ route('npo.dashboard') }}" class="btn btn--secondary btn--block">NPO dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn--secondary btn--block">NPO sign in</a>
                <a href="{{ route('register') }}" class="btn btn--primary btn--block">Register NPO</a>
            @endauth
        </nav>
    </div>
</header>