@if ($paginator->hasPages())
<nav class="flex items-center gap-1" aria-label="Pagination">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span class="w-9 h-9 flex items-center justify-center rounded-full text-ink/20 dark:text-cream/20 cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-full border border-ink/15 dark:border-cream/15 hover:border-gold/50 text-ink/60 dark:text-cream/60 hover:text-gold transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="w-9 h-9 flex items-center justify-center text-xs text-ink/30 dark:text-cream/30">…</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="w-9 h-9 flex items-center justify-center rounded-full bg-ink dark:bg-cream text-cream dark:text-ink text-xs font-semibold">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center rounded-full border border-ink/15 dark:border-cream/15 hover:border-gold/50 text-ink/60 dark:text-cream/60 hover:text-gold text-xs font-medium transition-all">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-full border border-ink/15 dark:border-cream/15 hover:border-gold/50 text-ink/60 dark:text-cream/60 hover:text-gold transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    @else
        <span class="w-9 h-9 flex items-center justify-center rounded-full text-ink/20 dark:text-cream/20 cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </span>
    @endif
</nav>
@endif
