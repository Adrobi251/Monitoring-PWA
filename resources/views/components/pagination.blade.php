@props(['paginator'])

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-muted-foreground">
            Menampilkan <span class="font-medium text-foreground">{{ $paginator->firstItem() }}</span>
            sampai <span class="font-medium text-foreground">{{ $paginator->lastItem() }}</span>
            dari <span class="font-medium text-foreground">{{ $paginator->total() }}</span> data
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-border text-muted-foreground opacity-50">
                    <x-icon name="chevron-left" class="h-4 w-4" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex h-9 w-9 items-center justify-center rounded-lg border border-border text-muted-foreground transition-colors hover:bg-muted hover:text-foreground" aria-label="Halaman sebelumnya">
                    <x-icon name="chevron-left" class="h-4 w-4" />
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="flex h-9 w-9 items-center justify-center text-sm text-muted-foreground">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="flex h-9 min-w-9 items-center justify-center rounded-lg bg-primary px-2 text-sm font-medium text-white" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-border px-2 text-sm text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex h-9 w-9 items-center justify-center rounded-lg border border-border text-muted-foreground transition-colors hover:bg-muted hover:text-foreground" aria-label="Halaman berikutnya">
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </a>
            @else
                <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-border text-muted-foreground opacity-50">
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </span>
            @endif
        </div>
    </nav>
@endif
