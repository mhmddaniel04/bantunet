{{-- Page numbers for $paginator->links(). Styled by .pagination in app.css --}}
@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Pagination">

        @if ($paginator->onFirstPage())
            <span class="pagination__link is-disabled">‹ Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pagination__link" rel="prev">‹ Prev</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pagination__link is-disabled">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pagination__link is-current" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pagination__link">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="pagination__link" rel="next">Next ›</a>
        @else
            <span class="pagination__link is-disabled">Next ›</span>
        @endif

    </nav>
@endif