@extends('layouts.app')

@section('title', $category->name . ' Quotes')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Category hero --}}
    <div class="relative rounded-3xl overflow-hidden mb-12 p-12 md:p-20 text-center"
         style="background: linear-gradient(135deg, {{ $category->color }}30, {{ $category->color }}10);">
        <div class="absolute inset-0 rounded-3xl border border-current opacity-10" style="border-color: {{ $category->color }}"></div>
        <div class="relative">
            <div class="text-6xl mb-4">{{ $category->icon }}</div>
            <h1 class="font-serif text-5xl md:text-6xl font-bold mb-3">{{ $category->name }}</h1>
            <p class="text-ink/50 dark:text-cream/50">{{ $quotes->total() }} quotes in this collection</p>
        </div>
    </div>

    {{-- Category filter --}}
    <div class="flex items-center gap-2 overflow-x-auto mb-10 pb-1">
        <a href="{{ route('quotes.index') }}"
           class="flex-shrink-0 px-4 py-2 rounded-full text-xs font-semibold border border-ink/15 dark:border-cream/15 hover:border-gold/40 text-ink/60 dark:text-cream/60 transition-all">
            ← All Quotes
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('category.show', $cat->slug) }}"
               class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ $cat->slug === $category->slug ? 'bg-ink dark:bg-cream text-cream dark:text-ink' : 'border border-ink/15 dark:border-cream/15 hover:border-gold/40 text-ink/60 dark:text-cream/60' }}">
                {{ $cat->icon }} {{ $cat->name }}
                <span class="opacity-50">({{ $cat->published_count }})</span>
            </a>
        @endforeach
    </div>

    {{-- Quotes --}}
    @if($quotes->count())
        <div class="masonry">
            @foreach($quotes as $quote)
                @include('partials.quote-card-mini', ['quote' => $quote])
            @endforeach
        </div>
        <div class="mt-12 flex justify-center">
            {{ $quotes->links('partials.pagination') }}
        </div>
    @else
        <div class="text-center py-24">
            <div class="text-6xl mb-4">{{ $category->icon }}</div>
            <h3 class="font-serif text-2xl font-bold mb-2">No quotes yet</h3>
            <p class="text-ink/50 dark:text-cream/50">Check back soon for {{ $category->name }} quotes.</p>
        </div>
    @endif
</div>

@endsection
