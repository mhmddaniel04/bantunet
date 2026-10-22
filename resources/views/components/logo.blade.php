{{--
    BantuNet logo: two heart halves (a volunteer and a cause) + "bantunet".
    Usage:
      <x-logo />                              standard, for light backgrounds
      <x-logo size="sm" />                    smaller (mobile top bar)
      <x-logo variant="light" />              for dark backgrounds
      <x-logo variant="reversed" />           for a red background
      <x-logo tagline />                      adds "YOUR PERFECT VOLUNTEER MATCH"
--}}
@props([
    'href' => route('home'),
    'size' => null,
    'variant' => 'default',
    'tagline' => false,
])

<a href="{{ $href }}" aria-label="BantuNet home"
   {{ $attributes->class(['logo', 'logo--sm' => $size === 'sm', "logo--{$variant}" => $variant !== 'default']) }}>
    <svg class="logo__mark" viewBox="0 8 64 48" aria-hidden="true" focusable="false">
        <path class="logo__half--left" d="M29 53 C 11 41, 2 31, 7 20 C 11 11, 24 10, 29 20 Z"/>
        <path class="logo__half--right" d="M35 20 C 40 10, 53 11, 57 20 C 62 31, 53 41, 35 53 Z"/>
    </svg>

    <span class="logo__text">
        <span class="logo__word"><span class="logo__bantu">bantu</span><span class="logo__net">net</span></span>
        @if ($tagline)
            <span class="logo__tagline">Your perfect volunteer match</span>
        @endif
    </span>
</a>