@extends('layouts.app')

@section('title', 'QuoteVerse')

@section('content')

{{-- Hero Section --}}
<section class="relative min-h-[92vh] flex items-center overflow-hidden">
    {{-- Decorative background --}}
    <div class="absolute inset-0 bg-gradient-to-br from-cream dark:from-ink via-cream-dark/40 dark:via-ink/80 to-amber-50/60 dark:to-indigo-950/40"></div>
    <div class="absolute top-20 right-0 w-96 h-96 bg-gold/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-72 h-72 bg-pink-300/10 dark:bg-purple-900/20 rounded-full blur-3xl"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-gold/5 rounded-full blur-3xl"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 grid lg:grid-cols-2 gap-12 items-center">
        {{-- Text --}}
        <div class="space-y-8 animate-fade-up">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gold/10 border border-gold/20 text-gold text-sm font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-gold animate-pulse"></span>
                Words that inspire millions
            </div>

            <h1 class="font-serif text-5xl sm:text-6xl lg:text-7xl font-bold leading-tight">
                Find Words<br>
                <span class="italic text-gradient">That Move</span><br>
                Your Soul
            </h1>

            <p class="text-lg text-ink/60 dark:text-cream/60 max-w-md leading-relaxed">
                Discover beautifully crafted quotes from the world's greatest thinkers, writers, and leaders. Let their wisdom guide your journey.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('quotes.index') }}" class="btn-primary px-8 py-3.5 rounded-full text-sm inline-flex items-center gap-2 justify-center">
                    Explore Quotes
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="{{ route('quotes.index') }}" class="px-8 py-3.5 rounded-full text-sm border border-ink/20 dark:border-cream/20 hover:border-gold/50 transition-colors inline-flex items-center gap-2 justify-center font-medium">
                    Browse Categories
                </a>
            </div>

            {{-- Stats --}}
            <div class="flex items-center gap-8 pt-4">
                <div>
                    <div class="font-serif text-3xl font-bold text-gradient">{{ \App\Models\Quote::where('status','publish')->count() }}+</div>
                    <div class="text-xs text-ink/50 dark:text-cream/50 mt-1">Quotes</div>
                </div>
                <div class="w-px h-10 bg-ink/10 dark:bg-cream/10"></div>
                <div>
                    <div class="font-serif text-3xl font-bold text-gradient">{{ \App\Models\Category::count() }}</div>
                    <div class="text-xs text-ink/50 dark:text-cream/50 mt-1">Categories</div>
                </div>
                <div class="w-px h-10 bg-ink/10 dark:bg-cream/10"></div>
                <div>
                    <div class="font-serif text-3xl font-bold text-gradient">∞</div>
                    <div class="text-xs text-ink/50 dark:text-cream/50 mt-1">Inspiration</div>
                </div>
            </div>
        </div>

        {{-- Floating quote cards preview --}}
        <div class="relative hidden lg:block h-[580px]">
            @foreach($featuredQuotes->take(3) as $i => $quote)
                @php
                    $transforms = [
                        'rotate-2 top-0 right-0 w-64',
                        '-rotate-3 top-32 left-0 w-56',
                        'rotate-1 bottom-0 right-12 w-60',
                    ];
                    $delays = ['animation-delay: 0s', 'animation-delay: 0.3s', 'animation-delay: 0.6s'];
                @endphp
                <div class="absolute {{ $transforms[$i] }} rounded-2xl overflow-hidden shadow-2xl animate-float" style="{{ $delays[$i] }}">
                    <a href="{{ route('quotes.show', $quote->slug) }}" class="block">
                        <div class="relative h-40 bg-gradient-to-br from-slate-700 to-slate-900">
                            @if($quote->image)
                                <img src="{{ $quote->image_url }}" alt="{{ $quote->title }}" class="w-full h-full object-cover opacity-70">
                            @endif
                            <div class="absolute inset-0 flex {{ $quote->text_position_class }} p-4 text-left">
                                <p class="font-serif text-white text-sm leading-snug line-clamp-3">"{{ $quote->quote_text }}"</p>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-ink/80 px-3 py-2">
                            <p class="text-xs font-medium text-ink/70 dark:text-cream/70 truncate">{{ $quote->title }}</p>
                            <p class="text-xs text-ink/40 dark:text-cream/40">{{ $quote->category->name }}</p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 animate-bounce">
        <span class="text-xs text-ink/30 dark:text-cream/30">scroll</span>
        <svg class="w-4 h-4 text-ink/30 dark:text-cream/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </div>
</section>

{{-- Categories Strip --}}
<section class="py-12 border-y border-cream-dark/20 dark:border-white/10 bg-white/50 dark:bg-ink/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 overflow-x-auto pb-2 scrollbar-hide">
            <a href="{{ route('quotes.index') }}"
               class="flex-shrink-0 flex items-center gap-2 px-5 py-2.5 rounded-full {{ !request('category') && request()->routeIs('quotes.index') ? 'bg-ink dark:bg-cream text-cream dark:text-ink' : 'bg-transparent border border-ink/15 dark:border-cream/15 hover:border-gold/50 text-ink/70 dark:text-cream/70' }} text-sm font-medium transition-all">
                ✨ All Quotes
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('category.show', $cat->slug) }}"
                   class="flex-shrink-0 flex items-center gap-2 px-5 py-2.5 rounded-full border border-ink/15 dark:border-cream/15 hover:border-gold/50 text-ink/70 dark:text-cream/70 text-sm font-medium transition-all hover:bg-white/80 dark:hover:bg-white/5">
                    <span>{{ $cat->icon }}</span>
                    {{ $cat->name }}
                    <span class="text-xs text-ink/30 dark:text-cream/30">({{ $cat->published_count }})</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Featured Quotes --}}
<section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-10">
        <div>
            <p class="text-xs font-semibold tracking-widest text-gold uppercase mb-2">Featured</p>
            <h2 class="font-serif text-4xl font-bold">Today's Inspiration</h2>
        </div>
        <a href="{{ route('quotes.index') }}" class="hidden sm:flex items-center gap-2 text-sm text-ink/50 dark:text-cream/50 hover:text-gold transition-colors">
            View all <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($featuredQuotes as $quote)
            @include('partials.quote-card', ['quote' => $quote])
        @endforeach
    </div>
</section>

{{-- Latest Quotes --}}
<section class="py-20 bg-white/40 dark:bg-ink/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <p class="text-xs font-semibold tracking-widest text-gold uppercase mb-2">Fresh</p>
                <h2 class="font-serif text-4xl font-bold">Latest Quotes</h2>
            </div>
        </div>
        <div class="masonry">
            @foreach($latestQuotes as $quote)
                @include('partials.quote-card-mini', ['quote' => $quote])
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
    <div class="relative rounded-3xl overflow-hidden p-12 md:p-20" style="background: linear-gradient(135deg, #1a1625 0%, #2d1f4e 50%, #1a1625 100%);">
        <div class="absolute inset-0 opacity-10" style="background: radial-gradient(ellipse at center, #c9a84c 0%, transparent 70%)"></div>
        <div class="relative">
            <p class="font-serif text-5xl md:text-6xl text-white mb-4 italic">"The only way to do</p>
            <p class="font-serif text-5xl md:text-6xl text-gradient mb-8 italic">great work is to love what you do."</p>
            <p class="text-white/50 mb-8">— Steve Jobs</p>
            <a href="{{ route('quotes.index') }}" class="btn-primary px-10 py-4 rounded-full text-sm inline-flex items-center gap-2">
                Discover More Quotes
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

@endsection
