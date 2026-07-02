@if ($paginator->hasPages())
    <nav class="app-pagination" role="navigation" aria-label="Navigasi halaman">
        <p class="app-pagination-summary">
            Menampilkan {{ $paginator->firstItem() }} sampai {{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
        </p>

        <div class="app-pagination-links">
            @if ($paginator->onFirstPage())
                <span class="app-pagination-btn is-disabled" aria-disabled="true">Sebelumnya</span>
            @else
                <a class="app-pagination-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">Sebelumnya</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="app-pagination-btn is-disabled" aria-disabled="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="app-pagination-btn is-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="app-pagination-btn" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="app-pagination-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya</a>
            @else
                <span class="app-pagination-btn is-disabled" aria-disabled="true">Berikutnya</span>
            @endif
        </div>
    </nav>
@endif
