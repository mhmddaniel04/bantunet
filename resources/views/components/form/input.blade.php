{{--
    Text / email / tel / password field with label, error and hint.
    Usage: <x-form.input name="org_email" label="Email" type="email" required />
    Password fields automatically get a Show/Hide button.
--}}
@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'hint' => null,
])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
@endphp

<div class="field">
    @if ($type === 'password')
        <div class="field__label-row">
            <label for="{{ $id }}" class="field__label">{{ $label }}</label>
            <button type="button" class="field__toggle" data-password-toggle="{{ $id }}">Show</button>
        </div>
    @else
        <label for="{{ $id }}" class="field__label">{{ $label }}</label>
    @endif

    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
        @if ($hasError) aria-invalid="true" @endif
        {{ $attributes->except('id')->class(['input', 'is-invalid' => $hasError]) }}
    >

    @if ($hasError)
        <p class="field__error">{{ $errors->first($name) }}</p>
    @elseif ($hint)
        <p class="field__hint">{{ $hint }}</p>
    @endif
</div>