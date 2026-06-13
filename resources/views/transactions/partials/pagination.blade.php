@if ($transactions->hasPages() || $transactions->total() > 0)
    <div id="transactions-pagination" class="flex flex-col gap-4 border-t border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
        <p id="transactions-pagination-info" class="text-sm text-gray-500">
            {{ $transactions->firstItem() ?? 0 }}-{{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }}
        </p>

        <div class="flex items-center gap-1">
            @if ($transactions->onFirstPage())
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-300">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <button type="button" data-page="{{ $transactions->currentPage() - 1 }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-600 transition hover:bg-gray-100">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            @endif

            @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                @if ($page == $transactions->currentPage())
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white">{{ $page }}</span>
                @else
                    <button type="button" data-page="{{ $page }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-sm text-gray-600 transition hover:bg-gray-100">{{ $page }}</button>
                @endif
            @endforeach

            @if ($transactions->hasMorePages())
                <button type="button" data-page="{{ $transactions->currentPage() + 1 }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-600 transition hover:bg-gray-100">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @else
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-300">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
    </div>
@else
    <div id="transactions-pagination"></div>
@endif
