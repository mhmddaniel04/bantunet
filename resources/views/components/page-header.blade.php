{{--
    Usage:
    <x-page-header eyebrow="Manage" title="My initiatives" lead="...">
        <a class="btn btn--primary" href="...">+ New</a>   ← optional buttons
    </x-page-header>
--}}
@props([
    'title',
    'eyebrow' => null,
    'lead' => null,
])

<header {{ $attributes->class('page-header') }}>
    <div>
        @if ($eyebrow)
            <p class="eyebrow">{{ $eyebrow }}</p>
        @endif
        <h1 class="page-title">{{ $title }}</h1>
        @if ($lead)
            <p class="lead">{{ $lead }}</p>
        @endif
    </div>

    @if ($slot->isNotEmpty())
        <div class="page-header__actions">{{ $slot }}</div>
    @endif
</header>