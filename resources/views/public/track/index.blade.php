@extends('layouts.public')

@section('title', 'Track your application')

@section('content')
    <section class="section-block">
        <div class="container container--narrow">
            <p class="eyebrow">Track</p>
            <h1 class="page-title">Track your application</h1>
            <p class="lead">
                Enter the tracking code from your confirmation email (for example <strong>BN-7K2QX9</strong>)
                and the email address you applied with.
            </p>

            <form method="POST" action="{{ route('track.lookup') }}" class="card form">
                @csrf

                <x-form.input name="tracking_code" label="Tracking code" :value="$code"
                              class="input--upper" placeholder="BN-XXXXXX" maxlength="12"
                              autocomplete="off" required :autofocus="$code === ''" />

                <x-form.input name="volunteer_email" label="Email" type="email"
                              autocomplete="email" required :autofocus="$code !== ''" />

                <button type="submit" class="btn btn--primary btn--block">Check status</button>
            </form>

            <p class="help-text">
                Lost your code? Search your inbox (and spam folder) for “Application received”.
            </p>
        </div>
    </section>
@endsection