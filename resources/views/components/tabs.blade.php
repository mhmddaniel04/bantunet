{{-- Usage: <x-tabs :items="[['label' => 'All', 'url' => '...', 'active' => true, 'count' => 5]]" /> --}}
@props(['items' => []])

<nav {{ $attributes->class('tabs') }} aria-label="Filter">
    @foreach ($items as $item)
        <a href="{{ $item['url'] }}" @class(['tab', 'is-active' => $item['active']])
           @if ($item['active']) aria-current="page" @endif>
            {{ $item['label'] }}
            @isset($item['count'])
                <span class="tab__count">{{ $item['count'] }}</span>
            @endisset
        </a>
    @endforeach
</nav>