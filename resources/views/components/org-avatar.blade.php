{{--
    Organisation logo, or its initials when there is no logo.
    Usage: <x-org-avatar :organization="$org" />   size: sm | md | lg   dark: initials on dark circle
--}}
@props([
    'organization',
    'size' => 'md',
    'dark' => false,
])

@php
    $classes = ['org-avatar', "org-avatar--{$size}" => $size !== 'md', 'org-avatar--dark' => $dark];
@endphp

@if ($organization->logoUrl())
    <img src="{{ $organization->logoUrl() }}" alt="{{ $organization->org_name }} logo" {{ $attributes->class($classes) }}>
@else
    <span {{ $attributes->class($classes) }} aria-hidden="true">{{ $organization->initials() }}</span>
@endif