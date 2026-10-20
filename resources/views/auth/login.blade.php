@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
    <p class="eyebrow">NPO Portal</p>
    <h2 class="page-title">Welcome back</h2>
    <p class="lead">Sign in to manage your initiatives and volunteers.</p>

    <form method="POST" action="{{ route('login.store') }}" class="form">
        @csrf

        <x-form.input name="org_email" label="Organisation email" type="email"
                      placeholder="contact@organisation.org" autocomplete="email" required autofocus />

        <x-form.input name="password" label="Password" type="password"
                      autocomplete="current-password" required />

        <x-form.checkbox name="remember">Keep me signed in</x-form.checkbox>

        <button type="submit" class="btn btn--primary btn--block">Sign in</button>
    </form>

    <p class="auth__switch">
        New to BantuNet?
        <a href="{{ route('register') }}" class="link">Register your NPO</a>
    </p>
@endsection