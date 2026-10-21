{{--
    Horizontal bars with labels.
    Usage: <x-bar-list :items="[['label' => 'Male', 'value' => 12]]" suffix="%" :max="100" />
--}}
@props([
    'items'  => [],
    'suffix' => '',
    'max'    => null,
    'empty'  => 'No data for this period yet.',
])

@php
    $values = collect($items)->pluck('value')->filter(fn ($v) => $v !== null);
    $scale = $max ?? max(1, (float) $values->max());
@endphp

@if ($values->sum() == 0)
    <p class="field__hint" style="margin-top: 20px;">{{ $empty }}</p>
@else
    <ul class="bar-list">
        @foreach ($items as $item)
            <li class="bar-list__row">
                <span class="bar-list__label" title="{{ $item['label'] }}">{{ $item['label'] }}</span>
                <span class="bar-list__track">
                    <span class="bar-list__bar" style="width: {{ round(($item['value'] ?? 0) / $scale * 100) }}%"></span>
                </span>
                <span class="bar-list__value">{{ $item['value'] === null ? '—' : $item['value'].$suffix }}</span>
            </li>
        @endforeach
    </ul>
@endif