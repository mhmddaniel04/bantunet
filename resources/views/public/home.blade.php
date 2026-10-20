@extends('layouts.public')

@section('title', 'Volunteer where your skills matter')

@section('content')

    {{-- Hero --}}
    <section class="hero">
        <div class="container hero__grid">
            <div>
                <p class="eyebrow">Volunteer matching for Malaysia</p>
                <h1 class="hero__title">Find where your skills <em>matter most.</em></h1>
                <p class="hero__lead">
                    BantuNet connects you with registered non-profits across Malaysia.
                    Browse initiatives near you, or let Smart AI Match recommend the ones
                    that fit your skills and interests.
                </p>
                <div class="hero__actions">
                    <a href="{{ route('events.index') }}" class="btn btn--primary">Browse initiatives</a>
                    <a href="{{ route('register') }}" class="btn btn--secondary">I represent an NPO</a>
                </div>
            </div>

            <div class="card hero__stats">
                @foreach ([
                    [$stats['open_events'], 'initiatives open for volunteers'],
                    [$stats['organizations'], 'non-profit organisations on BantuNet'],
                    [$stats['volunteers_placed'], 'volunteers placed so far'],
                ] as [$value, $label])
                    <div class="hero-stat">
                        <p class="hero-stat__value">{{ number_format($value) }}</p>
                        <p class="hero-stat__label">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Featured initiatives --}}
    <section class="section-block">
        <div class="container">
            <div class="section-head">
                <div>
                    <p class="eyebrow">Happening soon</p>
                    <h2 class="section-title">Featured initiatives</h2>
                </div>
                <a href="{{ route('events.index') }}" class="link">See all initiatives →</a>
            </div>

            @if ($featured->isEmpty())
                <x-empty-state title="No initiatives yet">
                    New volunteer opportunities will appear here as soon as organisations publish them.
                </x-empty-state>
            @else
                <div class="event-grid">
                    @foreach ($featured as $event)
                        <x-event-card :event="$event" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- How it works --}}
    <section class="section-block section-block--dark">
        <div class="container">
            <p class="eyebrow">How it works</p>
            <h2 class="section-title">From good intentions to real impact</h2>

            <div class="steps">
                @foreach ([
                    ['Tell us about you', 'Share your skills, causes you care about and when you are free.'],
                    ['Get matched', 'Our AI compares your profile with every open initiative and ranks the best fits.'],
                    ['Apply & track', 'Apply in minutes — no account needed — and follow your status with a tracking code.'],
                ] as [$title, $text])
                    <div class="step">
                        <p class="step__number">0{{ $loop->iteration }}</p>
                        <h3 class="step__title">{{ $title }}</h3>
                        <p class="step__text">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Categories --}}
    <section class="section-block">
        <div class="container">
            <div class="section-head">
                <div>
                    <p class="eyebrow">Causes</p>
                    <h2 class="section-title">Browse by category</h2>
                </div>
            </div>

            <div class="category-grid">
                @foreach ($categories as $category)
                    <a href="{{ route('events.index', ['category' => $category->slug]) }}" class="category-tile">
                        <span class="category-tile__name">{{ $category->name }}</span>
                        <span @class(['category-tile__count', 'has-events' => $category->open_events_count > 0])>
                            {{ $category->open_events_count }} open {{ Str::plural('initiative', $category->open_events_count) }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- NPO call to action --}}
    <section class="section-block" style="padding-top: 0;">
        <div class="container">
            <div class="cta-band">
                <div>
                    <h2 class="cta-band__title">Running a non-profit?</h2>
                    <p class="cta-band__text">
                        Publish initiatives, receive matched volunteers and export reports for your funders —
                        free for registered Malaysian NPOs.
                    </p>
                </div>
                <a href="{{ route('register') }}" class="btn">Register your NPO</a>
            </div>
        </div>
    </section>

@endsection