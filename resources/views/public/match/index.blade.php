@extends('layouts.public')

@section('title', 'Smart Match')

@section('content')
@php
    $examples = [
        'Student teacher' => "I'm a university student studying education. I love teaching children to read and do maths, and I'm free on weekends.",
        'Outdoor lover'   => "I enjoy hiking and being in nature. I'm physically fit and happy to do outdoor work like planting trees or cleaning rivers.",
        'Caring listener' => "I'm patient and a good listener. I'd like to spend time with elderly people and make them feel less lonely.",
        'Has a car'       => "I have a car and a driving licence. I can help deliver food or supplies to people in need, especially during floods.",
    ];
@endphp

    <section class="page-hero">
        <div class="container">
            <span class="ai-badge">✨ Smart AI Match</span>
            <h1 class="page-title">Find initiatives that fit you</h1>
            <p class="lead">
                Tell us a little about yourself. Our AI reads your answers, compares them with every open
                initiative and ranks the best matches in a few seconds. No account needed.
            </p>
        </div>
    </section>

    <section class="section-block section-block--tight">
        <div class="container match-layout">

            <form method="GET" action="{{ route('match.results') }}" class="card">

                <section class="form-section">
                    <h2 class="form-section__title">1. Tell us about yourself</h2>
                    <p class="form-section__text">Write freely: what you enjoy, what you're good at, who you'd like to help.</p>

                    <div class="form">
                        <x-form.textarea name="about" label="About you" rows="5" maxlength="600" required
                                         :value="$values['about'] ?? null"
                                         placeholder="e.g. I'm a university student who enjoys teaching kids and I'm free on weekends." />

                        <div>
                            <p class="field__hint">Need ideas? Try an example:</p>
                            <div class="examples">
                                @foreach ($examples as $label => $text)
                                    <button type="button" class="chip chip--button" data-fill="#about" data-text="{{ $text }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>

                <section class="form-section">
                    <h2 class="form-section__title">2. Your skills <span class="text-muted">(optional)</span></h2>
                    <p class="form-section__text">Pick up to 6.</p>

                    <div class="form">
                        <x-form.checkbox-group name="skills" label="Skills" :options="$skills"
                                               :selected="$values['skills'] ?? []" />
                    </div>
                </section>

                <section class="form-section">
                    <h2 class="form-section__title">3. Preferences</h2>

                    <div class="form">
                        <div class="form-row form-row--2">
                            <x-form.select name="availability" label="When are you free?" :options="$availability"
                                           :value="$values['availability'] ?? 'Any'" required />
                            <x-form.select name="category" label="Cause you care about" :options="$categories"
                                           :value="$values['category'] ?? null" placeholder="Any cause" />
                        </div>

                        <x-form.select name="state" label="Where? (optional)" :options="$states"
                                       :value="$values['state'] ?? null" placeholder="Anywhere in Malaysia" />
                    </div>
                </section>

                <div class="form-actions">
                    <button type="submit" class="btn btn--primary" data-loading-text="Finding your matches…">✨ Find my matches</button>
                </div>
            </form>

            <aside class="card match-layout__aside">
                <h2 class="card__title">How it works</h2>
                <ol class="how-list">
                    <li><strong>1.</strong> <span>Google Gemini turns your answers into a list of numbers that captures their <strong>meaning</strong>.</span></li>
                    <li><strong>2.</strong> <span>Every initiative was converted the same way when it was published.</span></li>
                    <li><strong>3.</strong> <span>We measure how close they are (<strong>cosine similarity</strong>) and add your skills, cause, schedule and location.</span></li>
                    <li><strong>4.</strong> <span>You get a ranked list with the reasons for each match.</span></li>
                </ol>
                <p class="field__hint" style="margin-top: 20px;">
                    Your answers are not linked to your name or email. They are only stored anonymously to improve the matching.
                </p>
            </aside>
        </div>
    </section>
@endsection