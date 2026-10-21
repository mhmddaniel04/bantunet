@extends('layouts.npo')

@section('title', 'Volunteers · '.$event->title)

@section('content')
@php
    $attendedCount = $volunteers->where('attended', true)->count();
    $hoursTotal = (float) $volunteers->where('attended', true)->sum('hours_volunteered');
@endphp

    <a href="{{ route('npo.events.index') }}" class="back-link">← Back to my initiatives</a>

    <x-page-header eyebrow="Volunteer list" :title="$event->title"
                   :lead="$event->event_date->format('l, j F Y').' · '.$event->timeRange().' · '.$event->location">
        <a href="{{ route('npo.events.roster.export', $event) }}" class="btn btn--secondary btn--sm">⬇ Excel (CSV)</a>
        <a href="{{ route('npo.events.roster.print', $event) }}" class="btn btn--secondary btn--sm" target="_blank">🖨 Sign-in sheet / PDF</a>
    </x-page-header>

    <div class="stats">
        <x-stat-card label="Approved volunteers" :value="$volunteers->count()"
                     :hint="$event->capacity ? 'of '.$event->capacity.' slots' : 'No slot limit'" />
        <x-stat-card label="Attended" :value="$attendedCount" hint="Marked after the event" />
        <x-stat-card label="Volunteer hours" :value="rtrim(rtrim(number_format($hoursTotal, 1), '0'), '.')" hint="Total contributed" />
        <x-stat-card label="Default hours" :value="$defaultHours ?? '—'" hint="From the event start/end time" />
    </div>

    @if ($sizes)
        <p class="size-chips">
            <strong>T-shirts to prepare:</strong>
            @foreach ($sizes as $size => $count)
                <span class="chip">{{ $size }} × {{ $count }}</span>
            @endforeach
        </p>
    @endif

    @if ($volunteers->isEmpty())
        <x-empty-state class="section" title="No approved volunteers yet"
                       :action="route('npo.applications.index', ['event' => $event->event_id, 'status' => 'Pending'])"
                       action-label="Review pending applications">
            Approved volunteers appear here, ready for exporting and attendance.
        </x-empty-state>
    @else
        @unless ($canMark)
            <p class="alert alert--info section">You can mark attendance from the event day ({{ $event->event_date->format('j M Y') }}) onwards.</p>
        @endunless

        @error('attendance')
            <p class="alert alert--error section">{{ $message }}</p>
        @enderror

        <form method="POST" action="{{ route('npo.events.attendance', $event) }}">
            @csrf
            @method('PUT')

            <div class="card table-card">
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Volunteer</th>
                                <th>Phone</th>
                                <th>Emergency contact</th>
                                <th>T-shirt</th>
                                <th>Attended</th>
                                <th>Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($volunteers as $volunteer)
                                @php
                                    $id = $volunteer->submission_id;
                                    $key = "attendance.{$id}";
                                @endphp
                                <tr>
                                    <td>
                                        <p class="table__title">
                                            <a href="{{ route('npo.applications.show', $volunteer) }}">{{ $volunteer->volunteer_name }}</a>
                                        </p>
                                        <p class="table__sub">{{ $volunteer->volunteer_email }}</p>
                                    </td>
                                    <td>{{ $volunteer->volunteer_phone }}</td>
                                    <td>
                                        {{ $volunteer->emergency_name }}
                                        <p class="table__sub">{{ $volunteer->emergency_phone }}</p>
                                    </td>
                                    <td>{{ $volunteer->tshirt_size }}</td>
                                    <td>
                                        <input type="hidden" name="attendance[{{ $id }}][attended]" value="0">
                                        <label class="check">
                                            <input type="checkbox" name="attendance[{{ $id }}][attended]" value="1"
                                                   @checked(old("{$key}.attended", $volunteer->attended))
                                                   @disabled(! $canMark)>
                                            <span class="visually-hidden">Attended</span>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="number" name="attendance[{{ $id }}][hours]" class="input input--hours"
                                               min="0" max="24" step="0.5"
                                               value="{{ old("{$key}.hours", $volunteer->hours_volunteered ?? $defaultHours) }}"
                                               @disabled(! $canMark)>
                                        @error("{$key}.hours")
                                            <p class="field__error">{{ $message }}</p>
                                        @enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($canMark)
                <div class="form-actions">
                    <p class="field__hint" style="margin-right: auto;">Hours are only saved for volunteers ticked as attended.</p>
                    <button type="submit" class="btn btn--primary">Save attendance</button>
                </div>
            @endif
        </form>
    @endif
@endsection