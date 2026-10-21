@extends('layouts.print')

@section('title', 'Engagement report')
@section('back_url', route('npo.reports.index', request()->query()))

@section('content')
    <header class="print-header">
        <div>
            <p class="eyebrow">Volunteer engagement report</p>
            <h1>{{ $organization->org_name }}</h1>
            <p class="text-muted">ROS {{ $organization->ros_number }}</p>
        </div>
        <div class="print-meta">
            {{ $report['from']->format('j M Y') }} – {{ $report['to']->format('j M Y') }}<br>
            {{ $report['category']?->name ?? 'All categories' }}
        </div>
    </header>

    @include('npo.reports._body', ['report' => $report])
@endsection