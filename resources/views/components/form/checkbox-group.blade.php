{{--
    Several checkboxes sent as an array, e.g. skills[].
    Usage: <x-form.checkbox-group name="skills" label="Skills" :options="[1 => 'Teaching']" :selected="[1]" />
--}}
@props([
    'name',
    'label',
    'options' => [],
    'selected' => [],
    'hint' => null,
])

@php
    $chosen = collect(old($name, $selected))->map(fn ($v) => (string) $v)->all();
    $message = $errors->first($name) ?: $errors->first($name.'.*');
@endphp

<fieldset class="field">
    <legend class="field__label">{{ $label }}</legend>

    <div class="check-grid">
        @foreach ($options as $value => $text)
            <label class="check">
                <input type="checkbox" name="{{ $name }}[]" value="{{ $value }}"
                       @checked(in_array((string) $value, $chosen, true))>
                <span>{{ $text }}</span>
            </label>
        @endforeach
    </div>

    @if ($message)
        <p class="field__error">{{ $message }}</p>
    @elseif ($hint)
        <p class="field__hint">{{ $hint }}</p>
    @endif
</fieldset>