{{-- Status badge. A published initiative whose date has passed shows "Ended". --}}
@props(['event'])

@php
    $ended = $event->status === \App\Models\Event::STATUS_PUBLISHED && $event->event_date->lt(today());
    $key = $ended ? 'ended' : $event->status;
    $label = $ended ? 'Ended' : (config('bantunet.event_statuses')[$event->status] ?? $event->status);
@endphp

<span {{ $attributes->class(['badge', "badge--{$key}"]) }}>{{ $label }}</span>