@extends('admin.layouts.app')

@section('title', 'Quotes')
@section('subtitle', 'Manage all quotes')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        {{-- Search --}}
        <form method="GET" action="{{ route('admin.quotes.index') }}" class="flex items-center gap-2">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search quotes..."
                       class="pl-9 pr-4 py-2 text-sm rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-amber-400/30 w-56">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <select name="category" class="py-2 px-3 text-sm rounded-xl bg-white/5 border border-white/10 text-white/70 focus:outline-none focus:ring-2 focus:ring-amber-400/30">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="status" class="py-2 px-3 text-sm rounded-xl bg-white/5 border border-white/10 text-white/70 focus:outline-none focus:ring-2 focus:ring-amber-400/30">
                <option value="">All Status</option>
                <option value="publish" {{ request('status') === 'publish' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
            <button type="submit" class="px-4 py-2 text-sm rounded-xl bg-white/5 border border-white/10 text-white/60 hover:bg-white/10 transition-colors">Filter</button>
            @if(request()->hasAny(['search','category','status']))
                <a href="{{ route('admin.quotes.index') }}" class="px-3 py-2 text-sm text-white/40 hover:text-white/70 transition-colors">Clear</a>
            @endif
        </form>
    </div>

    <a href="{{ route('admin.quotes.create') }}"
       class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-black transition-all"
       style="background: linear-gradient(135deg, #c9a84c, #f0d07a); box-shadow: 0 4px 15px rgba(201,168,76,0.3)">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Quote
    </a>
</div>

{{-- Quotes grid --}}
@if($quotes->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
        @foreach($quotes as $quote)
            <div class="glass-card rounded-2xl overflow-hidden group" x-data="{ menuOpen: false }">
                {{-- Image --}}
                <div class="relative h-40 bg-gradient-to-br from-slate-800 to-slate-900">
                    @if($quote->image)
                        <img src="{{ $quote->image_url }}" alt="{{ $quote->title }}" class="w-full h-full object-cover opacity-80">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-indigo-900 to-purple-900"></div>
                    @endif
                    <div class="absolute inset-0" style="background-color: {{ $quote->overlay_color }}; opacity: {{ $quote->overlay_opacity / 100 }}"></div>
                    <div class="absolute inset-0 flex {{ $quote->text_position_class }} p-3">
                        <p class="text-xs font-serif text-white line-clamp-3 leading-snug" style="color: {{ $quote->text_color }}; text-shadow: 0 1px 4px rgba(0,0,0,0.5)">
                            "{{ Str::limit($quote->quote_text, 80) }}"
                        </p>
                    </div>

                    {{-- Status badge --}}
                    <div class="absolute top-2 left-2">
                        <button onclick="toggleStatus({{ $quote->id }}, this)"
                                data-status="{{ $quote->status }}"
                                class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $quote->status === 'publish' ? 'bg-green-500/20 text-green-300 border border-green-500/30' : 'bg-orange-500/20 text-orange-300 border border-orange-500/30' }}">
                            {{ $quote->status }}
                        </button>
                    </div>

                    {{-- Actions --}}
                    <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('admin.quotes.preview', $quote) }}" target="_blank"
                           class="w-7 h-7 rounded-lg bg-black/40 backdrop-blur flex items-center justify-center hover:bg-black/60 transition-colors">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="{{ route('admin.quotes.edit', $quote) }}"
                           class="w-7 h-7 rounded-lg bg-black/40 backdrop-blur flex items-center justify-center hover:bg-black/60 transition-colors">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Card footer --}}
                <div class="p-3">
                    <p class="text-white text-sm font-medium truncate">{{ $quote->title }}</p>
                    <div class="flex items-center justify-between mt-1.5">
                        <span class="text-xs text-white/30" style="color: {{ $quote->category->color ?? '#fff' }}60">{{ $quote->category->icon }} {{ $quote->category->name }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-white/25 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                {{ number_format($quote->views) }}
                            </span>
                            <form action="{{ route('admin.quotes.destroy', $quote) }}" method="POST"
                                  onsubmit="return confirm('Delete this quote?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-6 h-6 rounded flex items-center justify-center hover:text-red-400 text-white/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center">
        {{ $quotes->links('partials.pagination') }}
    </div>
@else
    <div class="text-center py-24">
        <div class="text-5xl mb-4">💬</div>
        <h3 class="text-white font-semibold text-xl mb-2">No quotes found</h3>
        <p class="text-white/40 text-sm mb-6">Create your first quote to get started.</p>
        <a href="{{ route('admin.quotes.create') }}"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-black"
           style="background: linear-gradient(135deg, #c9a84c, #f0d07a)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Quote
        </a>
    </div>
@endif

@endsection

@section('scripts')
<script>
function toggleStatus(id, btn) {
    fetch(`/admin/quotes/${id}/toggle`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        btn.dataset.status = data.status;
        btn.textContent = data.status;
        if (data.status === 'publish') {
            btn.className = 'px-2 py-0.5 rounded-full text-xs font-semibold bg-green-500/20 text-green-300 border border-green-500/30';
        } else {
            btn.className = 'px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-500/20 text-orange-300 border border-orange-500/30';
        }
    });
}
</script>
@endsection
