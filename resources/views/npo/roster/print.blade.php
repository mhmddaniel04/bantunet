@extends('layouts.print')

@section('title', 'Sign-in sheet · '.$event->title)
@section('back_url', route('npo.events.roster', $event))

@section('content')
    <header class="print-header">
        <div>
            <p class="eyebrow">Volunteer sign-in sheet</p>
            <h1>{{ $event->title }}</h1>
            <p class="text-muted">{{ $event->organization->org_name }}</p>
        </div>
        <div class="print-meta">
            {{ $event->event_date->format('l, j F Y') }}<br>
            {{ $event->timeRange() }}<br>
            {{ $event->location }}, {{ $event->state }}
        </div>
    </header>

    @if ($sizes)
        <p style="margin-bottom: 16px; font-size: 13px;">
            <strong>T-shirts:</strong>
            @foreach ($sizes as $size => $count)
                {{ $size }} × {{ $count }}@unless ($loop->last), @endunless
            @endforeach
            · <strong>Total volunteers:</strong> {{ $volunteers->count() }}
        </p>
    @endif

    <table class="print-table">
        <thead>
            <tr>
                <th style="width: 32px;">#</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Emergency contact</th>
                <th style="width: 60px;">T-shirt</th>
                <th class="signature">Signature / time in</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($volunteers as $volunteer)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $volunteer->volunteer_name }}</strong></td>
                    <td>{{ $volunteer->volunteer_phone }}</td>
                    <td>{{ $volunteer->emergency_name }}<br>{{ $volunteer->emergency_phone }}</td>
                    <td>{{ $volunteer->tshirt_size }}</td>
                    <td class="signature"></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No approved volunteers yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="print-footer">Contains personal data. Handle according to the Personal Data Protection Act 2010 and dispose of securely after use.</p>
@endsection