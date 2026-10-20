{{-- Usage: <x-form.radio-cards name="decision" label="Decision" :options="['Approved' => 'Approve']" /> --}}
@props([
    'name',
    'label',
    'options' => [],
    'value' => null,
])

@php
    $current = (string) old($name, $value);
@endphp

<fieldset class="field">
    <legend class="field__label">{{ $label }}</legend>

    <div class="radio-cards">
        @foreach ($options as $optionValue => $text)
            <label class="radio-card">
                <input type="radio" name="{{ $name }}" value="{{ $optionValue }}"
                       @checked($current === (string) $optionValue) required>
                <span>{{ $text }}</span>
            </label>
        @endforeach
    </div>

    @if ($errors->has($name))
        <p class="field__error">{{ $errors->first($name) }}</p>
    @endif
</fieldset>