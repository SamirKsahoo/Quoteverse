@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Overview & statistics')

@section('content')

{{-- Stats grid --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    @php
        $statCards = [
            ['label' => 'Total Quotes',     'value' => $stats['total_quotes'],     'icon' => '💬', 'color' => '#8b5cf6'],
            ['label' => 'Published',         'value' => $stats['published_quotes'], 'icon' => '✅', 'color' => '#22c55e'],
            ['label' => 'Drafts',            'value' => $stats['draft_quotes'],     'icon' => '✏️', 'color' => '#f97316'],
            ['label' => 'Categories',        'value' => $stats['total_categories'], 'icon' => '🏷️', 'color' => '#06b6d4'],
            ['label' => 'Total Views',       'value' => number_format($stats['total_views']), 'icon' => '👁️', 'color' => '#ec4899'],
        ];
    @endphp

    @foreach($statCards as $card)
        <div class="glass-card rounded-2xl p-5">
            <div class="text-2xl mb-3">{{ $card['icon'] }}</div>
            <div class="text-2xl font-bold text-white mb-1">{{ $card['value'] }}</div>
            <div class="text-xs text-white/40">{{ $card['label'] }}</div>
        </div>
    @endforeach
</div>

<div class="grid lg:grid-cols-3 gap-6">

    {{-- Recent quotes --}}
    <div class="lg:col-span-2 glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-white font-semibold">Recent Quotes</h2>
            <a href="{{ route('admin.quotes.index') }}" class="text-xs text-amber-400 hover:text-amber-300 transition-colors">View all →</a>
        </div>

        <div class="space-y-3">
            @forelse($recentQuotes as $quote)
                <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/5 transition-colors group">
                    <div class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0 bg-gradient-to-br from-slate-700 to-slate-900">
                        @if($quote->image)
                            <img src="{{ $quote->image_url }}" alt="{{ $quote->title }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-medium truncate">{{ $quote->title }}</p>
                        <p class="text-white/30 text-xs truncate mt-0.5">{{ Str::limit($quote->quote_text, 50) }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $quote->status === 'publish' ? 'bg-green-500/15 text-green-400' : 'bg-orange-500/15 text-orange-400' }}">
                            {{ $quote->status }}
                        </span>
                        <a href="{{ route('admin.quotes.edit', $quote) }}" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-white/10 transition-colors opacity-0 group-hover:opacity-100">
                            <svg class="w-3.5 h-3.5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-white/30 text-sm">No quotes yet. <a href="{{ route('admin.quotes.create') }}" class="text-amber-400 hover:text-amber-300">Create one →</a></div>
            @endforelse
        </div>
    </div>

    {{-- Top categories --}}
    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-white font-semibold">Categories</h2>
            <a href="{{ route('admin.categories.index') }}" class="text-xs text-amber-400 hover:text-amber-300 transition-colors">Manage →</a>
        </div>

        <div class="space-y-3">
            @forelse($topCategories as $cat)
                <div class="flex items-center gap-3">
                    <span class="text-xl w-8 text-center">{{ $cat->icon }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-white truncate">{{ $cat->name }}</span>
                            <span class="text-xs text-white/40">{{ $cat->published_quotes_count }}</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-white/5 overflow-hidden">
                            @php $max = $topCategories->max('published_quotes_count') ?: 1; @endphp
                            <div class="h-full rounded-full transition-all"
                                 style="width: {{ ($cat->published_quotes_count / $max) * 100 }}%; background: {{ $cat->color }}"></div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-white/30 text-sm text-center py-4">No categories yet.</p>
            @endforelse
        </div>

        <a href="{{ route('admin.categories.create') }}" class="mt-6 w-full flex items-center justify-center gap-2 py-2.5 rounded-xl border border-amber-400/20 text-amber-400 text-sm hover:bg-amber-400/10 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Category
        </a>
    </div>
</div>

{{-- Quick actions --}}
<div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
    <a href="{{ route('admin.quotes.create') }}"
       class="flex flex-col items-center gap-3 p-5 glass-card rounded-2xl hover:border-amber-400/30 transition-all group">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(201,168,76,0.15)">
            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </div>
        <span class="text-xs text-white/60 group-hover:text-white transition-colors font-medium">New Quote</span>
    </a>
    <a href="{{ route('admin.categories.create') }}"
       class="flex flex-col items-center gap-3 p-5 glass-card rounded-2xl hover:border-amber-400/30 transition-all group">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(6,182,212,0.15)">
            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        </div>
        <span class="text-xs text-white/60 group-hover:text-white transition-colors font-medium">New Category</span>
    </a>
    <a href="{{ route('admin.quotes.index', ['status' => 'draft']) }}"
       class="flex flex-col items-center gap-3 p-5 glass-card rounded-2xl hover:border-amber-400/30 transition-all group">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(249,115,22,0.15)">
            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </div>
        <span class="text-xs text-white/60 group-hover:text-white transition-colors font-medium">View Drafts</span>
    </a>
    <a href="{{ route('home') }}" target="_blank"
       class="flex flex-col items-center gap-3 p-5 glass-card rounded-2xl hover:border-amber-400/30 transition-all group">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(139,92,246,0.15)">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        </div>
        <span class="text-xs text-white/60 group-hover:text-white transition-colors font-medium">View Site</span>
    </a>
</div>

@endsection
