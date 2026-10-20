{{-- Usage: <x-stat-card label="Applications" :value="12" hint="All time" /> --}}
@props([
    'label',
    'value',
    'hint' => null,
    'alert' => false,
])

<div {{ $attributes->class(['card', 'stat']) }}>
    <p class="stat__label">{{ $label }}</p>
    <p @class(['stat__value', 'stat__value--alert' => $alert])>{{ $value }}</p>
    @if ($hint)
        <p class="stat__hint">{{ $hint }}</p>
    @endif
</div>