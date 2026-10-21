@extends('layouts.npo')

@section('title', 'Engagement report')

@section('content')
    <x-page-header eyebrow="Reports" title="Volunteer engagement report"
                   lead="Impact numbers for your board, funders and annual report.">
        <a href="{{ route('npo.reports.export', $filters) }}" class="btn btn--secondary btn--sm">⬇ Excel (CSV)</a>
        <a href="{{ route('npo.reports.print', $filters) }}" class="btn btn--secondary btn--sm" target="_blank">🖨 Print / PDF</a>
    </x-page-header>

    {{-- Period & category --}}
    <form method="GET" action="{{ route('npo.reports.index') }}" class="report-filters">
        <x-form.input name="from" label="From" type="date" :value="$filters['from']" required />
        <x-form.input name="to" label="To" type="date" :value="$filters['to']" required />
        <x-form.select name="category" label="Category" :options="$categories"
                       :value="$filters['category'] ?? null" placeholder="All categories" />
        <div class="field">
            <button type="submit" class="btn btn--primary">Update report</button>
        </div>
    </form>

    <div class="preset-links">
        @foreach ($presets as $label => [$from, $to])
            <a href="{{ route('npo.reports.index', array_filter(['from' => $from->toDateString(), 'to' => $to->toDateString(), 'category' => $filters['category'] ?? null])) }}"
               @class(['tab', 'is-active' => $filters['from'] === $from->toDateString() && $filters['to'] === $to->toDateString()])>
                {{ $label }}
            </a>
        @endforeach
    </div>

    <p class="report-period">
        Showing initiatives held from <strong>{{ $report['from']->format('j M Y') }}</strong>
        to <strong>{{ $report['to']->format('j M Y') }}</strong>
        @if ($report['category'])
            in <strong>{{ $report['category']->name }}</strong>
        @endif
    </p>

    @include('npo.reports._body', ['report' => $report])
@endsection