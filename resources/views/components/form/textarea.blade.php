{{-- Usage: <x-form.textarea name="description" label="Description" rows="6" hint="..." /> --}}
@props([
    'name',
    'label',
    'value' => null,
    'hint' => null,
    'rows' => 4,
])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
@endphp

<div class="field">
    <label for="{{ $id }}" class="field__label">{{ $label }}</label>

    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if ($hasError) aria-invalid="true" @endif
        {{ $attributes->except('id')->class(['input', 'is-invalid' => $hasError]) }}
    >{{ old($name, $value) }}</textarea>

    @if ($hasError)
        <p class="field__error">{{ $errors->first($name) }}</p>
    @elseif ($hint)
        <p class="field__hint">{{ $hint }}</p>
    @endif
</div>