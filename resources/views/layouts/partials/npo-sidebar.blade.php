@php
    $organization = auth()->user();

    $navigation = [
        ['label' => 'Overview',       'route' => 'npo.dashboard',          'active' => 'npo.dashboard'],
        ['label' => 'My Initiatives', 'route' => 'npo.events.index',       'active' => 'npo.events.*'],
        ['label' => 'Applications',   'route' => 'npo.applications.index', 'active' => 'npo.applications.*'],
        ['label' => 'Reports',        'route' => 'npo.reports.index',      'active' => 'npo.reports.*'],
    ];
@endphp

<div id="sidebar" class="sidebar">
    <div class="sidebar__brand">
        <x-logo :href="route('npo.dashboard')" />
    </div>

    <div class="sidebar__org">
        <div class="sidebar__org-row">
            <span class="avatar">{{ $organization->initials() }}</span>
            <div class="sidebar__org-text">
                <p class="sidebar__org-name">{{ $organization->org_name }}</p>
                <p class="sidebar__org-email">{{ $organization->org_email }}</p>
            </div>
        </div>

        @if ($organization->is_verified)
            <span class="verified">✓ ROS verified</span>
        @endif
    </div>

    <nav class="sidebar__nav">
        @foreach ($navigation as $item)
            <a href="{{ route($item['route']) }}"
               @class(['nav-link', 'is-active' => request()->routeIs($item['active'])])>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="sidebar__footer">
        <a href="{{ route('home') }}" class="nav-link">← View public site</a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link nav-link--danger">Sign out</button>
        </form>
    </div>
</div>