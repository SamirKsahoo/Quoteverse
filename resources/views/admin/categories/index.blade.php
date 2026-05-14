@extends('admin.layouts.app')

@section('title', 'Categories')
@section('subtitle', 'Manage quote categories')

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-white/40 text-sm">{{ $categories->total() }} categories</p>
    <a href="{{ route('admin.categories.create') }}"
       class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-black transition-all"
       style="background: linear-gradient(135deg, #c9a84c, #f0d07a); box-shadow: 0 4px 15px rgba(201,168,76,0.3)">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Category
    </a>
</div>

<div class="glass-card rounded-2xl overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-white/5">
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/30">Category</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/30">Slug</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/30">Quotes</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/30">Created</th>
                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-white/30">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse($categories as $cat)
                <tr class="hover:bg-white/3 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg flex-shrink-0"
                                 style="background: {{ $cat->color }}20; border: 1px solid {{ $cat->color }}40">
                                {{ $cat->icon }}
                            </div>
                            <div>
                                <p class="text-white font-medium text-sm">{{ $cat->name }}</p>
                                <div class="w-16 h-1 rounded-full mt-1" style="background: {{ $cat->color }}"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <code class="text-xs text-white/40 bg-white/5 px-2 py-0.5 rounded">{{ $cat->slug }}</code>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-white/70">{{ $cat->quotes_count }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs text-white/30">{{ $cat->created_at->format('M d, Y') }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('admin.categories.edit', $cat) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-medium bg-white/5 hover:bg-white/10 text-white/60 hover:text-white transition-all">
                                Edit
                            </a>
                            <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST"
                                  onsubmit="return confirm('Delete {{ $cat->name }}? All quotes will be deleted too.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-red-500/10 hover:bg-red-500/20 text-red-400 transition-all">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <div class="text-4xl mb-3">🏷️</div>
                        <p class="text-white/40 text-sm">No categories yet.</p>
                        <a href="{{ route('admin.categories.create') }}" class="text-amber-400 text-sm hover:text-amber-300 mt-2 inline-block">Create one →</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($categories->hasPages())
    <div class="mt-6 flex justify-center">
        {{ $categories->links('partials.pagination') }}
    </div>
@endif

@endsection
