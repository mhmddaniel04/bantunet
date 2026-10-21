@extends('layouts.public')

@section('title', 'Application '.$submission->tracking_code)

@section('content')
@php
    use App\Models\Submission;

    $event = $submission->event;
    $organization = $event->organization;
    $status = $submission->review_status;
    $isPending = $status === Submission::STATUS_PENDING;

    [$decisionTitle, $decisionText] = match ($status) {
        Submission::STATUS_APPROVED  => ['Approved — see you there!', 'The organisation will contact you with any final details.'],
        Submission::STATUS_REJECTED  => ['Not selected this time', 'Thank you for applying. Other initiatives may suit you.'],
        Submission::STATUS_WITHDRAWN => ['Withdrawn', 'You withdrew this application.'],
        default                      => ['Decision', 'You will receive an email when a decision is made.'],
    };

    $steps = [
        [
            'title' => 'Application submitted',
            'text'  => $submission->created_at->format('j M Y, g:i A'),
            'state' => 'done',
            'icon'  => '✓',
        ],
        [
            'title' => 'Review by '.$organization->org_name,
            'text'  => $submission->reviewed_at
                ? 'Reviewed on '.$submission->reviewed_at->format('j M Y')
                : ($isPending ? 'The organisation is reviewing applications.' : 'Not reviewed.'),
            'state' => $isPending ? 'current' : 'done',
            'icon'  => $isPending ? '' : '✓',
        ],
        [
            'title' => $decisionTitle,
            'text'  => $decisionText,
            'state' => $isPending ? 'upcoming' : strtolower($status),
            'icon'  => match ($status) {
                Submission::STATUS_APPROVED  => '✓',
                Submission::STATUS_REJECTED  => '✕',
                Submission::STATUS_WITHDRAWN => '–',
                default                      => '',
            },
        ],
    ];
@endphp

    <section class="section-block section-block--tight">
        <div class="container">

            @if (session('just_applied'))
                <div class="success-banner">
                    <span class="success-banner__icon">✓</span>
                    <div>
                        <h2 class="success-banner__title">Application sent!</h2>
                        <p>
                            Your tracking code is <span class="tracking-code">{{ $submission->tracking_code }}</span>.
                            We have emailed it to <strong>{{ $submission->volunteer_email }}</strong> — keep it to check your status later.
                        </p>
                    </div>
                </div>
            @endif

            <x-page-header :eyebrow="'Tracking code '.$submission->tracking_code"
                           :title="'Hi, '.Str::before($submission->volunteer_name, ' ')">
                <span class="{{ $submission->statusBadgeClass() }}">{{ $status }}</span>
            </x-page-header>

            <div class="track-grid">

                {{-- Progress --}}
                <section class="card">
                    <h2 class="card__title">Progress</h2>

                    <ol class="timeline">
                        @foreach ($steps as $step)
                            <li class="timeline__item is-{{ $step['state'] }}">
                                <span class="timeline__dot">{{ $step['icon'] }}</span>
                                <div>
                                    <p class="timeline__title">{{ $step['title'] }}</p>
                                    <p class="timeline__text">{{ $step['text'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>

                    @if ($submission->reviewer_note && ! $isPending)
                        <div class="track-section">
                            <h3>Note from {{ $organization->org_name }}</h3>
                            <div class="note-box">{{ $submission->reviewer_note }}</div>
                        </div>
                    @endif

                    @if ($status === Submission::STATUS_APPROVED)
                        <div class="track-section alert alert--success">
                            <strong>What’s next?</strong> Arrive 15 minutes early on
                            {{ $event->event_date->format('j F') }}.
                            @if ($event->requirements)
                                Remember: {{ $event->requirements }}
                            @endif
                        </div>
                    @endif

                    @if ($canWithdraw)
                        <form method="POST" action="{{ route('track.withdraw', $submission) }}" class="withdraw-form"
                              data-confirm="Withdraw your application? The organisation will no longer expect you.">
                            @csrf
                            <p class="text-muted" style="margin-bottom: 12px;">Can’t make it anymore? Let the organisation know by withdrawing.</p>
                            <button type="submit" class="btn btn--danger btn--sm">Withdraw application</button>
                            @error('withdraw') <p class="field__error">{{ $message }}</p> @enderror
                        </form>
                    @endif
                </section>

                {{-- Initiative summary --}}
                <aside class="card summary-card">
                    <span class="chip">{{ $event->category->name }}</span>
                    <h2 class="summary-card__title">{{ $event->title }}</h2>
                    <p class="text-muted">by {{ $organization->org_name }}</p>

                    <dl class="detail-list">
                        <div>
                            <dt>Date</dt>
                            <dd>{{ $event->event_date->format('l, j F Y') }}</dd>
                        </div>
                        <div>
                            <dt>Time</dt>
                            <dd>{{ $event->timeRange() }}</dd>
                        </div>
                        <div>
                            <dt>Location</dt>
                            <dd>{{ $event->location }}, {{ $event->state }}</dd>
                        </div>
                        <div>
                            <dt>Organisation contact</dt>
                            <dd><a href="mailto:{{ $organization->org_email }}" class="link">{{ $organization->org_email }}</a></dd>
                        </div>
                    </dl>

                    <a href="{{ route('events.show', $event) }}" class="btn btn--secondary btn--block" style="margin-top: 24px;">View initiative</a>
                </aside>
            </div>
        </div>
    </section>
@endsection