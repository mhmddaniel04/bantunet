{{--
    Checkbox with its text as the slot.
    Usage: <x-form.checkbox name="remember">Keep me signed in</x-form.checkbox>
    Add "boxed" to put it inside a bordered box.
--}}
@props([
    'name',
    'value' => '1',
    'boxed' => false,
])

@php
    $hasError = $errors->has($name);
@endphp

<div class="field">
    <div @class(['check-box' => $boxed, 'is-invalid' => $boxed && $hasError])>
        <label class="check">
            <input type="checkbox" name="{{ $name }}" value="{{ $value }}" @checked(old($name)) {{ $attributes }}>
            <span>{{ $slot }}</span>
        </label>
    </div>

    @if ($hasError)
        <p class="field__error">{{ $errors->first($name) }}</p>
    @endif
</div>