{{--
    Checkbox with its text as the slot.
    Usage: <x-form.checkbox name="remember">Keep me signed in</x-form.checkbox>
    Add "boxed" for a bordered box, and :checked="true" to tick it when editing.
--}}
@props([
    'name',
    'value' => '1',
    'boxed' => false,
    'checked' => false,
])

@php
    $hasError = $errors->has($name);

    // After a failed submit, show what the user chose; otherwise the saved value.
    $isChecked = $errors->any() ? (bool) old($name) : (bool) old($name, $checked);
@endphp

<div class="field">
    <div @class(['check-box' => $boxed, 'is-invalid' => $boxed && $hasError])>
        <label class="check">
            <input type="checkbox" name="{{ $name }}" value="{{ $value }}" @checked($isChecked) {{ $attributes }}>
            <span>{{ $slot }}</span>
        </label>
    </div>

    @if ($hasError)
        <p class="field__error">{{ $errors->first($name) }}</p>
    @endif
</div>