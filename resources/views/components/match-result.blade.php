{{-- One ranked recommendation. Usage: <x-match-result :result="$result" :rank="1" /> --}}
@props(['result', 'rank'])

@php
    $event = $result->event;
    $percent = $result->percent();
    $detailUrl = route('events.show', $event);
    $applyUrl = route('events.apply', ['event' => $event, 'via' => 'smart_match', 'score' => $percent]);
@endphp

<article class="match-card">
    <a href="{{ $detailUrl }}" class="match-card__media" tabindex="-1" aria-hidden="true">
        @if ($event->imageUrl())
            <img src="{{ $event->imageUrl() }}" alt="" loading="lazy">
        @else
            <div class="event-placeholder">{{ $event->category->name }}</div>
        @endif
        <span class="match-card__rank">#{{ $rank }}</span>
    </a>

    <div>
        <p class="event-card__meta">{{ $event->event_date->format('D, j M Y') }} · {{ $event->state }} · {{ $event->category->name }}</p>
        <h3 class="match-card__title"><a href="{{ $detailUrl }}">{{ $event->title }}</a></h3>
        <p class="event-card__org">by {{ $event->organization->org_name }} · <x-slots :event="$event" /></p>

        @if ($result->reasons)
            <ul class="reasons">
                @foreach ($result->reasons as $reason)
                    <li>{{ $reason }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="match-card__score">
        <p class="match-score">{{ $percent }}%</p>
        <p class="match-score__label">match</p>
        <div class="meter" role="img" aria-label="{{ $percent }} percent match">
            <span style="width: {{ $percent }}%"></span>
        </div>
        <a href="{{ $applyUrl }}" class="btn btn--primary btn--sm btn--block">Apply</a>
        <a href="{{ $detailUrl }}" class="btn btn--ghost btn--sm btn--block">Details</a>
    </div>
</article>