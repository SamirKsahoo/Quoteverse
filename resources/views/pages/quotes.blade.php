@extends('layouts.app')

@section('title', 'Browse Quotes')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Page header --}}
    <div class="mb-10">
        <p class="text-xs font-semibold tracking-widest text-gold uppercase mb-2">Explore</p>
        <h1 class="font-serif text-5xl font-bold mb-4">
            @if(request('search'))
                Results for "<span class="italic text-gradient">{{ request('search') }}</span>"
            @else
                All Quotes
            @endif
        </h1>
        <p class="text-ink/50 dark:text-cream/50">{{ $quotes->total() }} quotes found</p>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col md:flex-row gap-4 mb-10">
        {{-- Category filters --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 flex-1">
            <a href="{{ route('quotes.index', array_merge(request()->except('category', 'page'), [])) }}"
               class="flex-shrink-0 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ !request('category') ? 'bg-ink dark:bg-cream text-cream dark:text-ink' : 'border border-ink/20 dark:border-cream/20 hover:border-gold/40 text-ink/60 dark:text-cream/60' }}">
                All
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('quotes.index', array_merge(request()->except('category','page'), ['category' => $cat->slug])) }}"
                   class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request('category') === $cat->slug ? 'bg-ink dark:bg-cream text-cream dark:text-ink' : 'border border-ink/20 dark:border-cream/20 hover:border-gold/40 text-ink/60 dark:text-cream/60' }}">
                    {{ $cat->icon }} {{ $cat->name }}
                    <span class="opacity-50">({{ $cat->published_count }})</span>
                </a>
            @endforeach
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('quotes.index') }}" class="flex-shrink-0">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search quotes…"
                       class="pl-10 pr-4 py-2.5 text-sm rounded-full bg-white/60 dark:bg-white/10 border border-ink/15 dark:border-cream/15 focus:outline-none focus:ring-2 focus:ring-gold/40 w-64 placeholder-ink/40 dark:placeholder-cream/40">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink/40 dark:text-cream/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </form>
    </div>

    {{-- Quotes grid --}}
    @if($quotes->count())
        <div class="masonry">
            @foreach($quotes as $quote)
                @include('partials.quote-card-mini', ['quote' => $quote])
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-12 flex justify-center">
            {{ $quotes->links('partials.pagination') }}
        </div>
    @else
        <div class="text-center py-24">
            <div class="text-6xl mb-4">🔍</div>
            <h3 class="font-serif text-2xl font-bold mb-2">No quotes found</h3>
            <p class="text-ink/50 dark:text-cream/50 mb-6">Try a different search or browse all categories.</p>
            <a href="{{ route('quotes.index') }}" class="btn-primary px-6 py-3 rounded-full text-sm">Clear filters</a>
        </div>
    @endif
</div>

@endsection
