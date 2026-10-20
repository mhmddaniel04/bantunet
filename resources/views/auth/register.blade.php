@extends('layouts.auth')

@section('title', 'Register your NPO')
@section('box_class', 'auth__box--wide')

@section('content')
    <p class="eyebrow">NPO Portal</p>
    <h2 class="page-title">Register your organisation</h2>
    <p class="lead">Free for registered Malaysian non-profits. Takes about a minute.</p>

    <form method="POST" action="{{ route('register.store') }}" class="form">
        @csrf

        <x-form.input name="org_name" label="Organisation name"
                      placeholder="e.g. Persatuan Hijau Selangor" required autofocus />

        <div class="form-row form-row--2">
            <x-form.input name="ros_number" label="ROS number" class="input--upper"
                          placeholder="PPM-012-10-15122019" required />

            <x-form.select name="state" label="State" :options="$states"
                           placeholder="Select state" required />
        </div>

        <div class="form-row form-row--2">
            <x-form.input name="org_email" label="Official email" type="email"
                          placeholder="contact@organisation.org" autocomplete="email" required />

            <x-form.input name="org_phone" label="Phone" type="tel"
                          placeholder="03-1234 5678" required />
        </div>

        <div class="form-row form-row--2">
            <x-form.input name="password" label="Password" type="password"
                          autocomplete="new-password" required />

            <x-form.input name="password_confirmation" label="Confirm password" type="password"
                          hint="Min. 8 characters, letters and numbers."
                          autocomplete="new-password" required />
        </div>

        <x-form.checkbox name="declaration" boxed>
            I confirm this organisation is registered with the Registry of Societies Malaysia (ROS)
            and the information above is accurate.
        </x-form.checkbox>

        <button type="submit" class="btn btn--primary btn--block">Create NPO account</button>
    </form>

    <p class="auth__switch">
        Already registered?
        <a href="{{ route('login') }}" class="link">Sign in</a>
    </p>
@endsection