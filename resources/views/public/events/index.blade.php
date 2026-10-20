@extends('layouts.public')

@section('title', 'Browse initiatives')

@section('content')

    <section class="page-hero">
        <div class="container">
            <p class="eyebrow">Browse</p>
            <h1 class="page-title">Volunteer initiatives</h1>
            <p class="lead">Search open opportunities from registered non-profits across Malaysia.</p>
        </div>
    </section>

    <section class="section-block section-block--tight">
        <div class="container">

            {{-- Filters (GET form, so results can be bookmarked or shared) --}}
            <form method="GET" action="{{ route('events.index') }}" class="card filters">
                <div class="filters__search">
                    <label for="q" class="field__label">Search</label>
                    <input id="q" name="q" type="search" class="input"
                           value="{{ $filters['q'] ?? '' }}"
                           placeholder="Title, organisation or place, e.g. tutoring, Penang">
                </div>

                <x-form.select name="category" label="Category" :options="$options['categories']"
                               :value="$filters['category'] ?? null" placeholder="All categories" />

                <x-form.select name="state" label="State" :options="$options['states']"
                               :value="$filters['state'] ?? null" placeholder="All states" />

                <x-form.select name="skill" label="Skill" :options="$options['skills']"
                               :value="$filters['skill'] ?? null" placeholder="Any skill" />

                <x-form.select name="when" label="When" :options="$options['dates']"
                               :value="$filters['when'] ?? null" placeholder="Any date" />

                <x-form.select name="sort" label="Sort by" :options="$options['sort']"
                               :value="$filters['sort'] ?? 'soonest'" />

                <div class="filters__actions">
                    <button type="submit" class="btn btn--primary">Search</button>
                    @if ($filters)
                        <a href="{{ route('events.index') }}" class="btn btn--ghost">Clear</a>
                    @endif
                </div>
            </form>

            {{-- Results --}}
            <div class="results-bar">
                <p>
                    <strong>{{ $events->total() }}</strong>
                    {{ Str::plural('initiative', $events->total()) }} found
                </p>
                @if ($events->hasPages())
                    <p>Page {{ $events->currentPage() }} of {{ $events->lastPage() }}</p>
                @endif
            </div>

            @if ($events->isEmpty())
                <x-empty-state title="No initiatives found"
                               :action="route('events.index')" action-label="Clear all filters">
                    Try a different keyword, category or state.
                </x-empty-state>
            @else
                <div class="event-grid">
                    @foreach ($events as $event)
                        <x-event-card :event="$event" />
                    @endforeach
                </div>

                {{ $events->links() }}
            @endif
        </div>
    </section>

@endsection