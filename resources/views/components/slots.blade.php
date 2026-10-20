{{-- "12 slots left" / "Only 3 slots left" / "Fully booked" / "Open to all" --}}
@props(['event'])

@php
    $left = $event->slotsLeft();

    [$text, $modifier] = match (true) {
        $left === null => ['Open to all', null],
        $left === 0    => ['Fully booked', 'full'],
        $left <= 5     => ["Only {$left} ".Str::plural('slot', $left).' left', 'low'],
        default        => ["{$left} slots left", null],
    };
@endphp

<span {{ $attributes->class(['slots', "slots--{$modifier}" => $modifier]) }}>{{ $text }}</span>