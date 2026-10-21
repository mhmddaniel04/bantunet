@extends('layouts.public')

@section('title', 'Apply: '.$event->title)

@section('content')
    <section class="section-block section-block--tight">
        <div class="container">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('events.show', $event) }}">← Back to initiative</a>
            </nav>

            <div class="apply-layout">

                {{-- Form --}}
                <div>
                    <p class="eyebrow" style="margin-top: 16px;">Volunteer application</p>
                    <h1 class="page-title">Apply to {{ $event->title }}</h1>
                    <p class="lead">No account needed. You will receive a tracking code to follow your application.</p>

                    @error('event')
                        <p class="alert alert--error">{{ $message }}</p>
                    @enderror

                    @if ($source === \App\Models\Submission::SOURCE_SMART_MATCH)
                        <p class="alert alert--success">✨ Recommended for you by Smart AI Match ({{ number_format($matchScore) }}% match)</p>
                    @endif

                    <form method="POST" action="{{ route('events.apply.store', $event) }}"
                          enctype="multipart/form-data" class="card form-card">
                        @csrf
                        <input type="hidden" name="source" value="{{ old('source', $source) }}">
                        <input type="hidden" name="match_score" value="{{ old('match_score', $matchScore) }}">

                        <section class="form-section">
                            <h2 class="form-section__title">About you</h2>
                            <div class="form">
                                <x-form.input name="volunteer_name" label="Full name" autocomplete="name" required autofocus />

                                <div class="form-row form-row--2">
                                    <x-form.input name="volunteer_email" label="Email" type="email" autocomplete="email"
                                                  hint="Your tracking code will be sent here." required />
                                    <x-form.input name="volunteer_phone" label="Phone" type="tel" autocomplete="tel"
                                                  placeholder="012-345 6789" required />
                                </div>

                                <div class="form-row form-row--2">
                                    <x-form.input name="age" label="Age" type="number" min="13" max="100" required />
                                    <x-form.select name="gender" label="Gender" :options="$genders"
                                                   placeholder="Select" required />
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <h2 class="form-section__title">Availability & logistics</h2>
                            <div class="form">
                                <div class="form-row form-row--2">
                                    <x-form.select name="availability" label="When are you usually free?" :options="$availability"
                                                   placeholder="Select" required />
                                    <x-form.select name="tshirt_size" label="T-shirt size" :options="$tshirtSizes"
                                                   placeholder="Select size" required />
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <h2 class="form-section__title">Emergency contact</h2>
                            <p class="form-section__text">Only used if something happens during the activity.</p>
                            <div class="form">
                                <div class="form-row form-row--2">
                                    <x-form.input name="emergency_name" label="Name & relationship"
                                                  placeholder="e.g. Aminah binti Ali (Mother)" required />
                                    <x-form.input name="emergency_phone" label="Phone" type="tel"
                                                  placeholder="013-456 7890" required />
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <h2 class="form-section__title">Experience</h2>
                            <div class="form">
                                <x-form.textarea name="skills_summary" label="Relevant skills or experience (optional)" rows="4"
                                                 placeholder="e.g. I tutor primary school students and have basic first aid training." />

                                <x-form.file name="resume" label="Résumé (optional)" accept=".pdf,.doc,.docx"
                                             hint="PDF or Word, up to 2 MB. Only this organisation can see it." />
                            </div>
                        </section>

                        <section class="form-section">
                            <x-form.checkbox name="consent" boxed>
                                I agree that {{ $event->organization->org_name }} may use my details to process this application
                                and contact me about this initiative, in line with the Personal Data Protection Act 2010.
                            </x-form.checkbox>
                        </section>

                        <div class="form-actions">
                            <a href="{{ route('events.show', $event) }}" class="btn btn--ghost">Cancel</a>
                            <button type="submit" class="btn btn--primary">Submit application</button>
                        </div>
                    </form>
                </div>

                {{-- Summary of the initiative --}}
                <aside class="apply-layout__aside">
                    <div class="card summary-card">
                        <span class="chip">{{ $event->category->name }}</span>
                        <h2 class="summary-card__title">{{ $event->title }}</h2>
                        <p class="text-muted">by {{ $event->organization->org_name }}</p>

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
                                <dt>Apply by</dt>
                                <dd>{{ $event->closingDate()->format('j F Y') }}</dd>
                            </div>
                            <div>
                                <dt>Volunteers</dt>
                                <dd><x-slots :event="$event" /></dd>
                            </div>
                        </dl>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection