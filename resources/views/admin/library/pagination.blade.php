@if ($pages->hasPages())
    <nav class="page-header__actions" aria-label="Pagination">
        @if ($pages->previousPageUrl()) <a class="btn btn--secondary btn--sm" href="{{ $pages->previousPageUrl() }}">Previous</a> @endif
        <span>Page {{ $pages->currentPage() }} of {{ $pages->lastPage() }}</span>
        @if ($pages->nextPageUrl()) <a class="btn btn--secondary btn--sm" href="{{ $pages->nextPageUrl() }}">Next</a> @endif
    </nav>
@endif
