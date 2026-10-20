@extends('layouts.npo')

@section('title', 'Overview')

@section('content')
    <header>
        <p class="eyebrow">Dashboard</p>
        <h1 class="page-title">Hello, {{ $organization->org_name }}</h1>
        <p class="lead">Here is what is happening with your initiatives today, {{ now()->format('l, j F Y') }}.</p>
    </header>

    <div class="stats">
        <x-stat-card label="Open initiatives"  :value="$stats['open_events']"  hint="Accepting volunteers now" />
        <x-stat-card label="Total initiatives" :value="$stats['total_events']" hint="All time" />
        <x-stat-card label="Applications"      :value="$stats['applications']" hint="All time" />
        <x-stat-card label="Pending review"    :value="$stats['pending']"      hint="Need your decision"
                     :alert="$stats['pending'] > 0" />
    </div>

    <section class="card section">
        <h2 class="card__title">Getting started</h2>

        <ul class="checklist">
            @foreach ($checklist as $step)
                <li @class(['checklist__item', 'is-done' => $step['done']])>
                    <span class="checklist__dot">{{ $step['done'] ? '✓' : $loop->iteration }}</span>
                    {{ $step['label'] }}
                </li>
            @endforeach
        </ul>
    </section>
@endsection