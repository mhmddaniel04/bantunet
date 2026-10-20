@extends('layouts.npo')

@section('title', 'My initiatives')

@section('content')
@php
    $tabs = [[
        'label'  => 'All',
        'url'    => route('npo.events.index'),
        'active' => $status === null,
        'count'  => array_sum($statusCounts),
    ]];

    foreach (config('bantunet.event_statuses') as $value => $label) {
        $tabs[] = [
            'label'  => $label,
            'url'    => route('npo.events.index', ['status' => $value]),
            'active' => $status === $value,
            'count'  => $statusCounts[$value] ?? 0,
        ];
    }
@endphp

    <x-page-header eyebrow="Manage" title="My initiatives"
                   lead="Create, update and track every volunteer initiative your organisation runs.">
        <a href="{{ route('npo.events.create') }}" class="btn btn--primary">+ New initiative</a>
    </x-page-header>

    <x-tabs :items="$tabs" />

    @if ($events->isEmpty())
        <x-empty-state class="section" title="No initiatives here yet"
                       :action="route('npo.events.create')" action-label="Create an initiative">
            Published initiatives appear on the public website, where volunteers can find and apply for them.
        </x-empty-state>
    @else
        <div class="card table-card">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Initiative</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Applications</th>
                            <th>Volunteers</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($events as $event)
                            <tr>
                                <td>
                                    <p class="table__title">
                                        <a href="{{ route('npo.events.edit', $event) }}">{{ $event->title }}</a>
                                    </p>
                                    <p class="table__sub">{{ $event->category->name }} · {{ $event->state }}</p>
                                </td>
                                <td>
                                    {{ $event->event_date->format('j M Y') }}
                                    <p class="table__sub">{{ $event->timeRange() }}</p>
                                </td>
                                <td><x-event-status :event="$event" /></td>
                                <td>
                                    <a href="{{ route('npo.applications.index', ['event' => $event->event_id]) }}" class="link">
                                        {{ $event->submissions_count }} total
                                    </a>
                                    @if ($event->pending_count)
                                        <p class="table__sub">{{ $event->pending_count }} pending</p>
                                    @endif
                                </td>
                                <td><x-slots :event="$event" /></td>
                                <td>
                                    <div class="table__actions">
                                        @if ($event->status !== \App\Models\Event::STATUS_DRAFT)
                                            <a href="{{ route('events.show', $event) }}" class="btn btn--ghost btn--sm" target="_blank">View</a>
                                        @endif

                                        <a href="{{ route('npo.events.edit', $event) }}" class="btn btn--secondary btn--sm">Edit</a>

                                        <form method="POST" action="{{ route('npo.events.destroy', $event) }}" class="inline-form"
                                              data-confirm="Delete “{{ $event->title }}”? This cannot be undone.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn--danger btn--sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{ $events->links() }}
    @endif
@endsection