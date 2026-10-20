<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head', ['css' => ['public']])
</head>
<body>

@include('layouts.partials.public-header')

<main>
    @yield('content')
</main>

@include('layouts.partials.public-footer')

<x-flash />
</body>
</html>