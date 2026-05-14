@extends('admin.layouts.app')

@section('title', isset($category) ? 'Edit Category' : 'New Category')
@section('subtitle', isset($category) ? 'Update category details' : 'Create a new category')

@section('content')

<div class="max-w-xl">
    <form action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
          method="POST" x-data="{ name: '{{ old('name', $category->name ?? '') }}', icon: '{{ old('icon', $category->icon ?? '✨') }}', color: '{{ old('color', $category->color ?? '#6366f1') }}' }">
        @csrf
        @if(isset($category)) @method('PUT') @endif

        <div class="glass-card rounded-2xl p-6 space-y-5">

            {{-- Preview --}}
            <div class="flex items-center gap-4 p-4 rounded-xl" :style="{ background: color + '15', border: '1px solid ' + color + '30' }">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl flex-shrink-0"
                     :style="{ background: color + '20', border: '1px solid ' + color + '40' }"
                     x-text="icon"></div>
                <div>
                    <p class="font-semibold text-white" x-text="name || 'Category Name'"></p>
                    <div class="w-24 h-1 rounded-full mt-1.5 transition-all" :style="{ background: color }"></div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-white/40 mb-2">Name *</label>
                <input type="text" name="name" required
                       placeholder="e.g. Motivation"
                       x-model="name"
                       value="{{ old('name', $category->name ?? '') }}"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-amber-400/30 transition-all">
                @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-white/40 mb-2">Icon (emoji)</label>
                    <input type="text" name="icon"
                           placeholder="🔥"
                           x-model="icon"
                           value="{{ old('icon', $category->icon ?? '') }}"
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-2xl focus:outline-none focus:ring-2 focus:ring-amber-400/30 transition-all text-center">
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach(['🔥','❤️','🌿','⭐','😊','🦉','💪','🌟','🎯','✨','🌈','💡'] as $e)
                            <button type="button" @click="icon = '{{ $e }}'"
                                    class="text-lg hover:scale-125 transition-transform">{{ $e }}</button>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-white/40 mb-2">Color</label>
                    <input type="color" name="color"
                           x-model="color"
                           value="{{ old('color', $category->color ?? '#6366f1') }}"
                           class="w-full h-12 rounded-xl border border-white/10 cursor-pointer bg-transparent">
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach(['#f97316','#ec4899','#22c55e','#eab308','#06b6d4','#8b5cf6','#ef4444','#3b82f6'] as $c)
                            <button type="button" @click="color = '{{ $c }}'"
                                    class="w-6 h-6 rounded-full hover:scale-125 transition-transform border-2 border-white/20"
                                    style="background: {{ $c }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 py-3 rounded-xl text-sm font-semibold text-black transition-all"
                        style="background: linear-gradient(135deg, #c9a84c, #f0d07a)">
                    {{ isset($category) ? 'Update Category' : 'Create Category' }}
                </button>
                <a href="{{ route('admin.categories.index') }}"
                   class="px-5 py-3 rounded-xl text-sm font-medium text-white/50 border border-white/10 hover:bg-white/5 transition-colors">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

@endsection
