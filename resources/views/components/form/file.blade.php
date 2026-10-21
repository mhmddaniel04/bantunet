{{-- Usage: <x-form.file name="resume" label="Résumé" accept=".pdf,.doc,.docx" hint="PDF or Word, max 2 MB" /> --}}
@props([
    'name',
    'label',
    'accept' => null,
    'hint' => null,
])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
@endphp

<div class="field">
    <label for="{{ $id }}" class="field__label">{{ $label }}</label>

    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="file"
        @if ($accept) accept="{{ $accept }}" @endif
        @if ($hasError) aria-invalid="true" @endif
        {{ $attributes->except('id')->class(['input', 'is-invalid' => $hasError]) }}
    >

    @if ($hasError)
        <p class="field__error">{{ $errors->first($name) }}</p>
    @elseif ($hint)
        <p class="field__hint">{{ $hint }}</p>
    @endif
</div>