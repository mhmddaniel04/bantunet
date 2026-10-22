{{--
    Usage: <x-event-card :event="$event" />
    Optional: :similarity="0.87" shows "🧠 87% similar" (used by "You may also like").
--}}
@props(['event', 'similarity' => null])

@php
    $url = route('events.show', $event);
    $extraSkills = $event->skills->count() - 3;
@endphp

<article {{ $attributes->class('event-card') }}>
    <a href="{{ $url }}" class="event-card__media" tabindex="-1" aria-hidden="true">
        @if ($event->imageUrl())
            <img src="{{ $event->imageUrl() }}" alt="" loading="lazy">
        @else
            <div class="event-placeholder">{{ $event->category->name }}</div>
        @endif

        <span class="event-card__tag">{{ $event->category->name }}</span>

        @if ($event->is_featured)
            <span class="event-card__tag event-card__tag--featured">★ Featured</span>
        @endif
    </a>

    <div class="event-card__body">
        <p class="event-card__meta">{{ $event->event_date->format('D, j M Y') }} · {{ $event->state }}</p>
        <h3 class="event-card__title"><a href="{{ $url }}">{{ $event->title }}</a></h3>
        <p class="event-card__org">by {{ $event->organization->org_name }}</p>

        @if ($similarity !== null)
            <p class="event-card__similar">🧠 {{ round((float) $similarity * 100) }}% similar</p>
        @endif

        <p class="event-card__excerpt">{{ $event->description }}</p>

        @if ($event->skills->isNotEmpty())
            <div class="chips">
                @foreach ($event->skills->take(3) as $skill)
                    <span class="chip">{{ $skill->name }}</span>
                @endforeach
                @if ($extraSkills > 0)
                    <span class="chip">+{{ $extraSkills }}</span>
                @endif
            </div>
        @endif
    </div>

    <div class="event-card__footer">
        <x-slots :event="$event" />
        <a href="{{ $url }}" class="btn btn--primary btn--sm">View details</a>
    </div>
</article>