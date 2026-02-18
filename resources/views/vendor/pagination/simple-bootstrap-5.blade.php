@if ($paginator->hasPages())
<div class="d-flex flex-stack flex-wrap pt-5">
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item previous disabled">
                <span class="page-link"><i class="previous"></i></span>
            </li>
        @else
            <li class="page-item previous">
                <a href="{{ $paginator->previousPageUrl() }}" class="page-link"><i class="previous"></i></a>
            </li>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item next">
                <a href="{{ $paginator->nextPageUrl() }}" class="page-link"><i class="next"></i></a>
            </li>
        @else
            <li class="page-item next disabled">
                <span class="page-link"><i class="next"></i></span>
            </li>
        @endif
    </ul>
</div>
@endif
