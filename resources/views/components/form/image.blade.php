{{--
    Photo upload with live preview. Shows a "Remove current photo" option when editing.
    Usage: <x-form.image name="image" label="Cover photo" :current-url="$event->imageUrl()" />
    The <form> must have enctype="multipart/form-data".
--}}
@props([
    'name',
    'label',
    'currentUrl' => null,
    'hint' => null,
])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
@endphp

<div class="field image-field">
    <label for="{{ $id }}" class="field__label">{{ $label }}</label>

    <img src="{{ $currentUrl }}" alt="" class="image-field__preview"
         data-image-preview-target="{{ $id }}" @unless ($currentUrl) hidden @endunless>

    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="file"
        accept="image/jpeg,image/png,image/webp"
        data-image-preview="{{ $id }}"
        @if ($hasError) aria-invalid="true" @endif
        {{ $attributes->except('id')->class(['input', 'is-invalid' => $hasError]) }}
    >

    @if ($currentUrl)
        <x-form.checkbox name="remove_{{ $name }}">Remove current photo</x-form.checkbox>
    @endif

    @if ($hasError)
        <p class="field__error">{{ $errors->first($name) }}</p>
    @elseif ($hint)
        <p class="field__hint">{{ $hint }}</p>
    @endif
</div>