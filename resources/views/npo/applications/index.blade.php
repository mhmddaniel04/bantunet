@extends('layouts.npo')

@section('title', 'Applications')

@section('content')
@php
    $tabs = [[
        'label'  => 'All',
        'url'    => route('npo.applications.index', array_filter(['event' => $eventId, 'sort' => $sort === 'newest' ? null : $sort])),
        'active' => $status === null,
        'count'  => array_sum($counts),
    ]];

    foreach (config('bantunet.review_statuses') as $value) {
        $tabs[] = [
            'label'  => $value,
            'url'    => route('npo.applications.index', array_filter(['event' => $eventId, 'status' => $value, 'sort' => $sort === 'newest' ? null : $sort])),
            'active' => $status === $value,
            'count'  => $counts[$value] ?? 0,
        ];
    }
@endphp

    <x-page-header eyebrow="Volunteers" title="Applications"
                   lead="Review volunteers who applied to your initiatives." />

    {{-- Filters (submit automatically when changed) --}}
    <form method="GET" action="{{ route('npo.applications.index') }}" class="filter-inline">
        @if ($status)
            <input type="hidden" name="status" value="{{ $status }}">
        @endif

        <x-form.select name="event" label="Initiative" :options="$eventOptions"
                       :value="$eventId" placeholder="All initiatives" data-autosubmit />

        <x-form.select name="sort" label="Sort by" :options="$sortOptions"
                       :value="$sort" data-autosubmit />

        <noscript><button type="submit" class="btn btn--secondary">Filter</button></noscript>
    </form>

    <x-tabs :items="$tabs" />

    @if ($applications->isEmpty())
        <x-empty-state class="section" title="No applications yet">
            When volunteers apply to your initiatives, they will appear here.
        </x-empty-state>
    @else
        <div class="card table-card">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Volunteer</th>
                            <th>Initiative</th>
                            <th>🧠 AI fit</th>
                            <th>Applied</th>
                            <th>Found via</th>
                            <th>Status</th>
                            <th class="text-right"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $application)
                            <tr>
                                <td>
                                    <p class="table__title">
                                        <a href="{{ route('npo.applications.show', $application) }}">{{ $application->volunteer_name }}</a>
                                    </p>
                                    <p class="table__sub">{{ $application->volunteer_email }}</p>
                                </td>
                                <td>
                                    {{ Str::limit($application->event->title, 40) }}
                                    <p class="table__sub">{{ $application->event->event_date->format('j M Y') }}</p>
                                </td>
                                <td>
                                    @if ($application->fitPercent() !== null)
                                        <span class="fit fit--{{ $application->fitLevel() }}">{{ $application->fitPercent() }}%</span>
                                        <p class="table__sub">{{ ucfirst($application->fitLevel()) }} fit</p>
                                    @else
                                        <span class="text-muted">—</span>
                                        <p class="table__sub">No experience given</p>
                                    @endif
                                </td>
                                <td>
                                    {{ $application->created_at->diffForHumans() }}
                                    <p class="table__sub">{{ $application->tracking_code }}</p>
                                </td>
                                <td>
                                    @if ($application->source === \App\Models\Submission::SOURCE_SMART_MATCH)
                                        Smart Match
                                        <p class="table__sub">{{ number_format($application->match_score) }}% match</p>
                                    @else
                                        Browse
                                    @endif
                                </td>
                                <td><span class="{{ $application->statusBadgeClass() }}">{{ $application->review_status }}</span></td>
                                <td>
                                    <div class="table__actions">
                                        <a href="{{ route('npo.applications.show', $application) }}" class="btn btn--secondary btn--sm">
                                            {{ $application->isPending() ? 'Review' : 'View' }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <p class="ai-note">
            🧠 <strong>AI fit</strong> compares each applicant’s skills & experience with the initiative using Gemini embeddings
            (cosine similarity). It is a guide to help you review faster — not a decision. It never uses name, age or gender.
        </p>

        {{ $applications->links() }}
    @endif
@endsection