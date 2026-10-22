@extends('layouts.public')

@section('title', $event->title)

@section('content')
@php
    use App\Models\Event;

    // Why volunteers can't apply (null = applications are open)
    $closedMessage = match (true) {
        $event->status === Event::STATUS_CANCELLED => 'This initiative has been cancelled.',
        $event->status === Event::STATUS_COMPLETED,
        $event->event_date->lt(today())            => 'This initiative has already taken place.',
        $event->isFull()                           => 'All volunteer slots have been filled.',
        ! $event->isOpen()                         => 'Applications closed on '.$event->closingDate()->format('j F Y').'.',
        default                                    => null,
    };

    $organization = $event->organization;
    $mapUrl = 'https://www.google.com/maps/search/?api=1&query='.urlencode($event->location.', '.$event->state);
@endphp

    <section class="section-block section-block--tight">
        <div class="container">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('events.index') }}">← All initiatives</a>
            </nav>

            <div class="event-detail">

                {{-- Main content --}}
                <article>
                    <div class="event-detail__media">
                        @if ($event->imageUrl())
                            <img src="{{ $event->imageUrl() }}" alt="{{ $event->title }}">
                        @else
                            <div class="event-placeholder">{{ $event->category->name }}</div>
                        @endif
                    </div>

                    <div class="chips event-detail__tags">
                        <a href="{{ route('events.index', ['category' => $event->category->slug]) }}" class="chip">
                            {{ $event->category->name }}
                        </a>
                        @if ($event->is_featured)
                            <span class="chip">★ Featured</span>
                        @endif
                    </div>

                    <h1 class="event-detail__title">{{ $event->title }}</h1>

                    <p class="event-detail__org">
                        by <strong>{{ $organization->org_name }}</strong>
                        @if ($organization->is_verified)
                            <span class="badge badge--approved">✓ ROS verified</span>
                        @endif
                    </p>

                    <section class="content-block">
                        <h2 class="content-block__title">About this initiative</h2>
                        <div class="prose">{!! nl2br(e($event->description)) !!}</div>
                    </section>

                    @if ($event->requirements)
                        <section class="content-block">
                            <h2 class="content-block__title">Before you join</h2>
                            <div class="prose">{!! nl2br(e($event->requirements)) !!}</div>
                        </section>
                    @endif

                    @if ($event->skills->isNotEmpty())
                        <section class="content-block">
                            <h2 class="content-block__title">Skills that help</h2>
                            <div class="chips">
                                @foreach ($event->skills as $skill)
                                    <a href="{{ route('events.index', ['skill' => $skill->slug]) }}" class="chip">{{ $skill->name }}</a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </article>

                {{-- Side panel --}}
                <aside class="event-detail__aside">
                    <div class="card">
                        <dl class="detail-list">
                            <div>
                                <dt>Date</dt>
                                <dd>{{ $event->event_date->format('l, j F Y') }}</dd>
                            </div>
                            <div>
                                <dt>Time</dt>
                                <dd>{{ $event->timeRange() }}</dd>
                            </div>
                            <div>
                                <dt>Location</dt>
                                <dd>
                                    {{ $event->location }}, {{ $event->state }}<br>
                                    <a href="{{ $mapUrl }}" class="link" target="_blank" rel="noopener">Open in Google Maps ↗</a>
                                </dd>
                            </div>
                            <div>
                                <dt>Apply by</dt>
                                <dd>{{ $event->closingDate()->format('j F Y') }}</dd>
                            </div>
                            <div>
                                <dt>Volunteers</dt>
                                <dd><x-slots :event="$event" /></dd>
                            </div>
                        </dl>

                        <div class="detail-card__action">
                            @if ($closedMessage)
                                <p class="status-banner">{{ $closedMessage }}</p>
                            @else
                                <a href="{{ route('events.apply', $event) }}" class="btn btn--primary btn--block">Apply now</a>
                                <p class="detail-card__note">No account needed · takes about 3 minutes</p>
                            @endif
                        </div>
                    </div>

                    <div class="card org-card">
                        <div class="org-card__row">
                            <span class="org-card__avatar">{{ $organization->initials() }}</span>
                            <div>
                                <p class="org-card__name">{{ $organization->org_name }}</p>
                                <p class="text-muted">{{ $organization->state }}</p>
                            </div>
                        </div>

                        @if ($organization->description)
                            <p class="org-card__text">{{ Str::limit($organization->description, 180) }}</p>
                        @endif

                        @if ($organization->website)
                            <a href="{{ $organization->website }}" class="link" target="_blank" rel="noopener nofollow">Visit website ↗</a>
                        @endif
                    </div>
                </aside>
            </div>

            {{-- AI: similar initiatives (cosine similarity between initiative vectors) --}}
            @if ($similarEvents->isNotEmpty())
                <div class="section-head" style="margin-top: 64px;">
                    <div>
                        <p class="eyebrow">🧠 Recommended by AI</p>
                        <h2 class="section-title">You may also like</h2>
                    </div>
                    <a href="{{ route('match.index') }}" class="link">Get personal matches →</a>
                </div>

                <div class="event-grid">
                    @foreach ($similarEvents as $similar)
                        <x-event-card :event="$similar" :similarity="$similar->similarity" />
                    @endforeach
                </div>
            @endif

            {{-- More from the same NPO --}}
            @if ($moreEvents->isNotEmpty())
                <div class="section-head" style="margin-top: 64px;">
                    <div>
                        <p class="eyebrow">Same organisation</p>
                        <h2 class="section-title">More from {{ $organization->org_name }}</h2>
                    </div>
                </div>

                <div class="event-grid">
                    @foreach ($moreEvents as $more)
                        <x-event-card :event="$more" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

@endsection