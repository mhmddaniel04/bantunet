{{--
    Vertical bar chart in plain HTML/CSS (prints well, no JavaScript library).
    Usage: <x-bar-chart :items="[['label' => 'Jan', 'value' => 5], ...]" />
--}}
@props([
    'items' => [],
    'empty' => 'No data for this period yet.',
    'dark'  => false,
])

@php
    $max = max(1, (float) collect($items)->max('value'));
    $hasData = collect($items)->sum('value') > 0;
@endphp

@if (! $hasData)
    <p class="field__hint" style="margin-top: 20px;">{{ $empty }}</p>
@else
    <div @class(['bar-chart', 'bar-chart--dark' => $dark]) role="img"
         aria-label="{{ collect($items)->map(fn ($i) => $i['label'].': '.$i['value'])->implode(', ') }}">
        @foreach ($items as $item)
            <div class="bar-chart__col" title="{{ $item['label'] }}: {{ $item['value'] }}">
                <span class="bar-chart__value">{{ $item['value'] ?: '' }}</span>
                <div class="bar-chart__track">
                    <span class="bar-chart__bar" style="height: {{ round($item['value'] / $max * 100) }}%"></span>
                </div>
                <span class="bar-chart__label">{{ $item['label'] }}</span>
            </div>
        @endforeach
    </div>
@endif