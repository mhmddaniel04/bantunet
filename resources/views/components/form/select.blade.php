{{--
    Dropdown with label and error.
    :options can be a list ['A', 'B'] or value => label ['W' => 'Weekdays only'].
    Usage: <x-form.select name="state" label="State" :options="$states" placeholder="Select state" />
--}}
@props([
    'name',
    'label',
    'options' => [],
    'value' => null,
    'placeholder' => null,
])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
    $selected = (string) old($name, $value);
    $isList = array_is_list($options);
@endphp

<div class="field">
    <label for="{{ $id }}" class="field__label">{{ $label }}</label>

    <select
        id="{{ $id }}"
        name="{{ $name }}"
        @if ($hasError) aria-invalid="true" @endif
        {{ $attributes->except('id')->class(['input', 'is-invalid' => $hasError]) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $key => $text)
            @php $optionValue = (string) ($isList ? $text : $key); @endphp
            <option value="{{ $optionValue }}" @selected($selected === $optionValue)>{{ $text }}</option>
        @endforeach
    </select>

    @if ($hasError)
        <p class="field__error">{{ $errors->first($name) }}</p>
    @endif
</div>