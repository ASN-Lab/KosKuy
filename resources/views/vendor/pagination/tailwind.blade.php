@if ($paginator->hasPages())
    <nav class="mt-8 flex flex-wrap items-center justify-center gap-2" aria-label="Navigasi halaman">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="btn btn-sm btn-outline cursor-not-allowed opacity-50" aria-disabled="true">
                <x-ui.icon name="chevron-left" class="size-3.5" />
                <span>Sebelumnya</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-sm btn-outline">
                <x-ui.icon name="chevron-left" class="size-3.5" />
                <span>Sebelumnya</span>
            </a>
        @endif

        {{-- Page numbers (the summary below covers small screens) --}}
        <span class="hidden items-center gap-2 sm:flex">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-1.5 text-sm text-ink-400" aria-disabled="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="badge bg-brand-600 text-white" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="badge bg-ink-100 text-ink-600 transition-colors hover:bg-ink-200 hover:text-ink-800" aria-label="Ke halaman {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </span>

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-sm btn-outline">
                <span>Berikutnya</span>
                <x-ui.icon name="chevron-right" class="size-3.5" />
            </a>
        @else
            <span class="btn btn-sm btn-outline cursor-not-allowed opacity-50" aria-disabled="true">
                <span>Berikutnya</span>
                <x-ui.icon name="chevron-right" class="size-3.5" />
            </span>
        @endif

        <p class="w-full text-center text-xs text-ink-400">
            Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }} — total {{ $paginator->total() }} item
        </p>
    </nav>
@endif