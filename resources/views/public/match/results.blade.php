@extends('layouts.public')

@section('title', 'Your matches')

@section('content')
@php
    $pct = fn (?float $value) => $value === null ? '—' : round($value * 100).'%';
    $labels = [
        'semantic'     => 'Meaning (AI)',
        'skills'       => 'Skills',
        'category'     => 'Cause',
        'availability' => 'Availability',
        'state'        => 'Location',
    ];

    $answerChips = array_filter([
        ...collect($answers['skills'] ?? [])->map(fn ($id) => $skills[$id] ?? null)->all(),
        isset($answers['category']) ? $categories[$answers['category']] ?? null : null,
        $availability[$answers['availability']] ?? null,
        $answers['state'] ?? null,
    ]);
@endphp

    <section class="page-hero">
        <div class="container">
            <div class="results-head">
                <span class="ai-badge">
                    ✨ {{ $report->usedAi ? 'Gemini embeddings + pgvector' : 'Rule-based matching' }}
                    · {{ number_format($report->responseMs) }} ms
                </span>
                <a href="{{ route('match.index', request()->query()) }}" class="btn btn--secondary btn--sm">✎ Edit my answers</a>
            </div>

            <h1 class="page-title" style="margin-top: 16px;">
                @if ($report->isEmpty())
                    No matches yet
                @else
                    Your top {{ count($report->results) }} {{ Str::plural('match', count($report->results)) }}
                @endif
            </h1>
            <p class="lead">“{{ Str::limit($answers['about'], 160) }}”</p>

            @if ($answerChips)
                <div class="chips answer-chips">
                    @foreach ($answerChips as $chip)
                        <span class="chip">{{ $chip }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="section-block section-block--tight">
        <div class="container">

            @unless ($report->usedAi)
                <p class="alert alert--warning">
                    Smart AI is temporarily unavailable, so these results use your skills, cause, availability and location only.
                </p>
            @endunless

            @if ($report->isEmpty())
                <x-empty-state title="No open initiatives to match yet"
                               :action="route('events.index')" action-label="Browse all initiatives">
                    Check back soon: new initiatives are published regularly.
                </x-empty-state>
            @else
                <div class="match-results">
                    @foreach ($report->results as $result)
                        <x-match-result :result="$result" :rank="$loop->iteration" />
                    @endforeach
                </div>

                {{-- Transparency panel (also handy for your viva demo) --}}
                <details class="card how-panel">
                    <summary>How were these matches calculated?</summary>

                    <h3>1. Your answers, as sent to Google Gemini</h3>
                    <pre>{{ $report->queryText }}</pre>

                    <h3>2. Weights</h3>
                    <div class="chips" style="margin-top: 10px;">
                        @foreach ($report->weights as $part => $weight)
                            <span class="chip">{{ $labels[$part] }}: {{ round($weight * 100) }}%</span>
                        @endforeach
                    </div>
                    <p class="field__hint">
                        Final score = weighted average of the parts you answered (“—” = no preference, so it is left out).
                        “Meaning” is the cosine similarity between your answer and the initiative, calculated in PostgreSQL with pgvector.
                    </p>

                    <h3>3. Score breakdown</h3>
                    <div class="table-scroll">
                        <table class="score-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Initiative</th>
                                    @foreach ($labels as $label)
                                        <th class="num">{{ $label }}</th>
                                    @endforeach
                                    <th class="num">Final</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($report->results as $result)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ Str::limit($result->event->title, 40) }}</td>
                                        @foreach (array_keys($labels) as $part)
                                            <td class="num">{{ $pct($result->breakdown[$part]) }}</td>
                                        @endforeach
                                        <td class="num"><strong>{{ $result->percent() }}%</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            @endif
        </div>
    </section>
@endsection