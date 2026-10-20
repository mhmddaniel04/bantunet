{{--
    Usage:
    <x-empty-state title="No initiatives found" :action="route('events.index')" action-label="Clear filters">
        Try another category or state.
    </x-empty-state>
--}}
@props([
    'title',
    'action' => null,
    'actionLabel' => null,
])

<div {{ $attributes->class('empty-state') }}>
    <h3 class="empty-state__title">{{ $title }}</h3>

    @if ($slot->isNotEmpty())
        <p class="empty-state__text">{{ $slot }}</p>
    @endif

    @if ($action)
        <a href="{{ $action }}" class="btn btn--secondary">{{ $actionLabel }}</a>
    @endif
</div>