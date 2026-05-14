@extends('layouts.app')

@section('title', $quote->title)
@section('meta_description', Str::limit($quote->quote_text, 160))

@section('content')

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-ink/40 dark:text-cream/40 mb-8">
        <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('quotes.index') }}" class="hover:text-gold transition-colors">Quotes</a>
        <span>/</span>
        <a href="{{ route('category.show', $quote->category->slug) }}" class="hover:text-gold transition-colors">{{ $quote->category->name }}</a>
        <span>/</span>
        <span class="text-ink/70 dark:text-cream/70 truncate max-w-48">{{ $quote->title }}</span>
    </nav>

    <div class="grid lg:grid-cols-5 gap-12">

        {{-- Quote display (main) --}}
        <div class="lg:col-span-3 space-y-6">

            {{-- The big quote card --}}
            <div id="quote-card" class="relative rounded-3xl overflow-hidden shadow-2xl" style="min-height: 480px;">
                @if($quote->image)
                    <img src="{{ $quote->image_url }}" alt="{{ $quote->title }}" class="absolute inset-0 w-full h-full object-cover">
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900"></div>
                @endif

                {{-- Overlay --}}
                <div class="absolute inset-0"
                     style="background-color: {{ $quote->overlay_color }}; opacity: {{ $quote->overlay_opacity / 100 }}"></div>

                {{-- Quote text --}}
                <div class="relative flex {{ $quote->text_position_class }} p-10" style="min-height: 480px;">
                    <div class="max-w-lg">
                        <div class="font-serif text-5xl mb-4 leading-none opacity-40" style="color: {{ $quote->text_color }}">"</div>
                        <p class="{{ $quote->font_size_class }} {{ $quote->font_style_class }} font-bold leading-relaxed"
                           style="color: {{ $quote->text_color }}; text-shadow: 0 2px 12px rgba(0,0,0,0.5)">
                            {{ $quote->quote_text }}
                        </p>
                        @if($quote->author)
                            <p class="mt-4 text-sm {{ $quote->font_style_class }} opacity-80" style="color: {{ $quote->text_color }}">
                                — {{ $quote->author }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Category badge --}}
                <div class="absolute top-5 left-5">
                    <span class="px-4 py-1.5 rounded-full text-sm font-semibold text-white glass-dark">
                        {{ $quote->category->icon ?? '✨' }} {{ $quote->category->name }}
                    </span>
                </div>

                {{-- Views --}}
                <div class="absolute top-5 right-5">
                    <span class="px-3 py-1.5 rounded-full text-xs text-white glass-dark flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        {{ number_format($quote->views) }}
                    </span>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="flex flex-wrap gap-3">

                {{-- Copy quote --}}
                <button onclick="copyQuote()" class="flex items-center gap-2 px-5 py-2.5 rounded-full border border-ink/20 dark:border-cream/20 hover:border-gold/50 text-sm font-medium transition-all hover:bg-white/50 dark:hover:bg-white/5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Copy Quote
                </button>

                {{-- Share Twitter --}}
                <a href="https://twitter.com/intent/tweet?text={{ urlencode('"'.$quote->quote_text.'"'.($quote->author ? ' — '.$quote->author : '')."\n\nvia QuoteVerse") }}&url={{ urlencode(route('quotes.show', $quote->slug)) }}"
                   target="_blank"
                   class="flex items-center gap-2 px-5 py-2.5 rounded-full border border-ink/20 dark:border-cream/20 hover:border-sky-400/50 text-sm font-medium transition-all hover:bg-white/50 dark:hover:bg-white/5">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.74l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    Share on X
                </a>

                {{-- Share Facebook --}}
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('quotes.show', $quote->slug)) }}"
                   target="_blank"
                   class="flex items-center gap-2 px-5 py-2.5 rounded-full border border-ink/20 dark:border-cream/20 hover:border-blue-500/50 text-sm font-medium transition-all hover:bg-white/50 dark:hover:bg-white/5">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    Facebook
                </a>

                {{-- Download --}}
                <button onclick="downloadQuote()" class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-gold/10 border border-gold/20 hover:bg-gold/20 text-gold text-sm font-medium transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download
                </button>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Quote info --}}
            <div class="rounded-2xl border border-ink/10 dark:border-cream/10 bg-white/60 dark:bg-ink/40 p-6">
                <h2 class="font-serif text-2xl font-bold mb-4">{{ $quote->title }}</h2>

                @if($quote->author)
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gold/30 to-gold/10 flex items-center justify-center text-gold font-bold">
                            {{ strtoupper(substr($quote->author, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-sm">{{ $quote->author }}</p>
                            <p class="text-xs text-ink/40 dark:text-cream/40">Author</p>
                        </div>
                    </div>
                @endif

                <blockquote class="font-serif text-lg italic text-ink/80 dark:text-cream/80 border-l-2 border-gold pl-4 mb-4 leading-relaxed">
                    "{{ $quote->quote_text }}"
                </blockquote>

                <div class="flex flex-wrap gap-2 mt-4">
                    <a href="{{ route('category.show', $quote->category->slug) }}"
                       class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold"
                       style="background-color: {{ $quote->category->color }}20; color: {{ $quote->category->color }}; border: 1px solid {{ $quote->category->color }}40">
                        {{ $quote->category->icon }} {{ $quote->category->name }}
                    </a>
                    <span class="text-xs text-ink/40 dark:text-cream/40 flex items-center">{{ $quote->created_at->format('M d, Y') }}</span>
                </div>
            </div>

            {{-- Related quotes --}}
            @if($related->count())
                <div>
                    <h3 class="font-serif text-xl font-bold mb-4">More from {{ $quote->category->name }}</h3>
                    <div class="space-y-3">
                        @foreach($related as $r)
                            <a href="{{ route('quotes.show', $r->slug) }}"
                               class="flex gap-3 p-3 rounded-xl hover:bg-white/60 dark:hover:bg-white/5 transition-colors group">
                                <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0 bg-gradient-to-br from-slate-700 to-slate-900">
                                    @if($r->image)
                                        <img src="{{ $r->image_url }}" alt="{{ $r->title }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate group-hover:text-gold transition-colors">{{ $r->title }}</p>
                                    <p class="text-xs text-ink/50 dark:text-cream/50 mt-1 line-clamp-2">{{ $r->quote_text }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
function copyQuote() {
    const text = `"{{ addslashes($quote->quote_text) }}"{{ $quote->author ? ' — '.$quote->author : '' }}`;
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.currentTarget;
        const orig = btn.innerHTML;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Copied!';
        btn.classList.add('text-green-500');
        setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('text-green-500'); }, 2000);
    });
}

function downloadQuote() {
    const card = document.getElementById('quote-card');
    html2canvas(card, { scale: 2, useCORS: true }).then(canvas => {
        const link = document.createElement('a');
        link.download = '{{ Str::slug($quote->title) }}.png';
        link.href = canvas.toDataURL();
        link.click();
    });
}
</script>
@endsection
