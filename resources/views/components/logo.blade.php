{{-- Usage: <x-logo />  or  <x-logo size="sm" :href="route('npo.dashboard')" /> --}}
@props([
    'href' => route('home'),
    'size' => null,
])

<a href="{{ $href }}" {{ $attributes->class(['logo', 'logo--sm' => $size === 'sm']) }}>
    <span class="logo__mark">B</span>
    <span class="logo__text">BantuNet</span>
</a>