@extends('layouts.npo')

@section('title', $submission->volunteer_name)

@section('content')
@php
    use App\Models\Submission;

    $event = $submission->event;

    // WhatsApp needs the number in international format: 012-345 6789 -> 60123456789
    $digits = preg_replace('/\D/', '', $submission->volunteer_phone);
    $whatsApp = 'https://wa.me/'.(str_starts_with($digits, '0') ? '6'.$digits : $digits);
@endphp

    <a href="{{ route('npo.applications.index', ['event' => $event->event_id]) }}" class="back-link">← Back to applications</a>

    <x-page-header :eyebrow="'Application '.$submission->tracking_code" :title="$submission->volunteer_name">
        <span class="{{ $submission->statusBadgeClass() }}">{{ $submission->review_status }}</span>
    </x-page-header>

    <div class="detail-grid">

        {{-- Left: volunteer information --}}
        <div class="stack">
            <section class="card">
                <h2 class="card__title">Volunteer details</h2>

                <dl class="info-list">
                    <div>
                        <dt>Email</dt>
                        <dd><a href="mailto:{{ $submission->volunteer_email }}" class="link">{{ $submission->volunteer_email }}</a></dd>
                    </div>
                    <div>
                        <dt>Phone</dt>
                        <dd>
                            {{ $submission->volunteer_phone }}
                            · <a href="{{ $whatsApp }}" class="link" target="_blank" rel="noopener">WhatsApp ↗</a>
                        </dd>
                    </div>
                    <div>
                        <dt>Age</dt>
                        <dd>{{ $submission->age }}</dd>
                    </div>
                    <div>
                        <dt>Gender</dt>
                        <dd>{{ $submission->gender }}</dd>
                    </div>
                    <div>
                        <dt>Availability</dt>
                        <dd>{{ config('bantunet.availability')[$submission->availability] ?? $submission->availability }}</dd>
                    </div>
                    <div>
                        <dt>T-shirt size</dt>
                        <dd>{{ $submission->tshirt_size }}</dd>
                    </div>
                    <div>
                        <dt>Emergency contact</dt>
                        <dd>{{ $submission->emergency_name }} · {{ $submission->emergency_phone }}</dd>
                    </div>
                    <div>
                        <dt>Applied</dt>
                        <dd>
                            {{ $submission->created_at->format('j M Y, g:i A') }}
                            @if ($submission->source === Submission::SOURCE_SMART_MATCH)
                                <br><span class="text-muted">via Smart Match ({{ number_format($submission->match_score) }}% match)</span>
                            @endif
                        </dd>
                    </div>

                    <div class="info-list__full">
                        <dt>Skills & experience</dt>
                        <dd><div class="note-box">{{ $submission->skills_summary ?: 'Not provided.' }}</div></dd>
                    </div>

                    <div class="info-list__full">
                        <dt>Résumé</dt>
                        <dd>
                            @if ($submission->resume_path)
                                <a href="{{ route('npo.applications.resume', $submission) }}" class="btn btn--secondary btn--sm">
                                    ⬇ Download {{ $submission->resume_original_name }}
                                </a>
                            @else
                                <span class="text-muted">No résumé uploaded.</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="card">
                <h2 class="card__title">Initiative</h2>
                <dl class="info-list">
                    <div class="info-list__full">
                        <dt>Title</dt>
                        <dd><a href="{{ route('npo.events.edit', $event) }}" class="link">{{ $event->title }}</a></dd>
                    </div>
                    <div>
                        <dt>Date</dt>
                        <dd>{{ $event->event_date->format('l, j F Y') }} · {{ $event->timeRange() }}</dd>
                    </div>
                    <div>
                        <dt>Volunteers</dt>
                        <dd><x-slots :event="$event" /></dd>
                    </div>
                </dl>
            </section>
        </div>

        {{-- Right: decision --}}
        <aside class="card">
            <h2 class="card__title">Decision</h2>

            @if ($submission->review_status === Submission::STATUS_WITHDRAWN)
                <p class="alert alert--info" style="margin-top: 16px;">The volunteer withdrew this application.</p>
            @else
                @if ($submission->reviewed_at)
                    <p class="lead">
                        Marked <strong>{{ $submission->review_status }}</strong>
                        on {{ $submission->reviewed_at->format('j M Y, g:i A') }}. You can change it below.
                    </p>
                @endif

                @if ($event->isFull() && $submission->review_status !== Submission::STATUS_APPROVED)
                    <p class="alert alert--warning" style="margin-top: 16px;">All slots are filled. Increase the capacity to approve more volunteers.</p>
                @endif

                <form method="POST" action="{{ route('npo.applications.review', $submission) }}" class="form">
                    @csrf
                    @method('PATCH')

                    <x-form.radio-cards name="decision" label="Decision"
                                        :value="$submission->isPending() ? null : $submission->review_status"
                                        :options="[Submission::STATUS_APPROVED => '✓ Approve', Submission::STATUS_REJECTED => '✕ Reject']" />

                    <x-form.textarea name="reviewer_note" label="Note to volunteer (optional)" rows="4"
                                     :value="$submission->reviewer_note"
                                     hint="Shown to the volunteer on the tracking page." />

                    <button type="submit" class="btn btn--primary btn--block">Save decision</button>
                </form>
            @endif
        </aside>
    </div>
@endsection