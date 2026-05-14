@extends('admin.layouts.app')

@section('title', isset($quote) ? 'Edit Quote' : 'New Quote')
@section('subtitle', isset($quote) ? 'Update quote details' : 'Create a beautiful quote')

@section('head')
<style>
    .form-input {
        width: 100%;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 0.75rem;
        padding: 0.625rem 0.875rem;
        color: white;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    .form-input:focus { outline: none; border-color: rgba(201,168,76,0.5); box-shadow: 0 0 0 3px rgba(201,168,76,0.1); }
    .form-input::placeholder { color: rgba(255,255,255,0.2); }
    .form-label { display: block; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: rgba(255,255,255,0.4); margin-bottom: 0.375rem; }
    .pos-btn { display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.03); cursor: pointer; transition: all 0.15s; font-size: 0.6rem; color: rgba(255,255,255,0.4); }
    .pos-btn:hover, .pos-btn.active { background: rgba(201,168,76,0.2); border-color: rgba(201,168,76,0.5); color: #f0d07a; }
</style>
@endsection

@section('content')

<form action="{{ isset($quote) ? route('admin.quotes.update', $quote) : route('admin.quotes.store') }}"
      method="POST" enctype="multipart/form-data"
      x-data="quoteForm()" x-init="init()">
    @csrf
    @if(isset($quote)) @method('PUT') @endif

    <div class="grid xl:grid-cols-3 gap-6">

        {{-- Main form --}}
        <div class="xl:col-span-2 space-y-5">

            {{-- Title + Author --}}
            <div class="glass-card rounded-2xl p-6 space-y-4">
                <h3 class="text-white font-semibold text-sm mb-4 border-b border-white/5 pb-3">Quote Content</h3>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Quote Title *</label>
                        <input type="text" name="title" value="{{ old('title', $quote->title ?? '') }}" required
                               placeholder="e.g. The Power Within"
                               class="form-input" x-model="title">
                        @error('title')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Author</label>
                        <input type="text" name="author" value="{{ old('author', $quote->author ?? '') }}"
                               placeholder="e.g. Winston Churchill"
                               class="form-input" x-model="author">
                    </div>
                </div>

                <div>
                    <label class="form-label">Quote Text *</label>
                    <textarea name="quote_text" required rows="4"
                              placeholder="Enter the quote text here..."
                              class="form-input resize-none" x-model="quoteText">{{ old('quote_text', $quote->quote_text ?? '') }}</textarea>
                    @error('quote_text')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Category *</label>
                        <select name="category_id" required class="form-input">
                            <option value="">Select category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $quote->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->icon }} {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input">
                            <option value="draft" {{ old('status', $quote->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="publish" {{ old('status', $quote->status ?? '') === 'publish' ? 'selected' : '' }}>Publish</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Background Image --}}
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-white font-semibold text-sm mb-4 border-b border-white/5 pb-3">Background Image</h3>
                <div class="flex items-center gap-4">
                    <div class="w-28 h-20 rounded-xl overflow-hidden bg-white/5 border border-white/10 flex items-center justify-center flex-shrink-0">
                        @if(isset($quote) && $quote->image)
                            <img src="{{ $quote->image_url }}" alt="Current" class="w-full h-full object-cover" id="imgPreview">
                        @else
                            <img id="imgPreview" class="w-full h-full object-cover hidden">
                            <svg class="w-6 h-6 text-white/20" id="imgPlaceholder" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="image" accept="image/*" id="imageInput"
                               class="form-input" onchange="previewImage(this)">
                        <p class="text-white/30 text-xs mt-1">JPG, PNG or WebP. Max 5MB. {{ isset($quote) ? 'Leave empty to keep current.' : '' }}</p>
                        @error('image')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Text Styling --}}
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-white font-semibold text-sm mb-5 border-b border-white/5 pb-3">Text Styling</h3>

                <div class="grid sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="form-label">Text Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="text_color" value="{{ old('text_color', $quote->text_color ?? '#ffffff') }}"
                                   class="w-10 h-10 rounded-lg border border-white/10 cursor-pointer bg-transparent"
                                   x-model="textColor">
                            <input type="text" x-bind:value="textColor" @input="textColor = $event.target.value"
                                   class="form-input flex-1" placeholder="#ffffff">
                            <input type="hidden" name="text_color" x-bind:value="textColor">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Font Size</label>
                        <select name="font_size" class="form-input" x-model="fontSize">
                            @foreach(['sm','base','lg','xl','2xl','3xl','4xl','5xl'] as $size)
                                <option value="{{ $size }}" {{ old('font_size', $quote->font_size ?? '2xl') === $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Font Style</label>
                        <select name="font_style" class="form-input" x-model="fontStyle">
                            <option value="serif" {{ old('font_style', $quote->font_style ?? 'serif') === 'serif' ? 'selected' : '' }}>Serif (Elegant)</option>
                            <option value="sans"  {{ old('font_style', $quote->font_style ?? '') === 'sans' ? 'selected' : '' }}>Sans Serif (Modern)</option>
                        </select>
                    </div>
                </div>

                {{-- Text position grid --}}
                <div>
                    <label class="form-label mb-3">Text Position</label>
                    @php
                        $positions = [
                            ['top-left','↖'],['top-center','↑'],['top-right','↗'],
                            ['center-left','←'],['center','·'],['center-right','→'],
                            ['bottom-left','↙'],['bottom-center','↓'],['bottom-right','↘'],
                        ];
                    @endphp
                    <input type="hidden" name="text_position" x-bind:value="textPosition">
                    <div class="grid grid-cols-3 gap-1.5 w-36">
                        @foreach($positions as [$pos, $arrow])
                            <button type="button"
                                    class="pos-btn"
                                    :class="textPosition === '{{ $pos }}' ? 'active' : ''"
                                    @click="textPosition = '{{ $pos }}'">
                                {{ $arrow }}
                            </button>
                        @endforeach
                    </div>
                    <p class="text-white/20 text-xs mt-2">Current: <span x-text="textPosition"></span></p>
                </div>
            </div>

            {{-- Overlay --}}
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-white font-semibold text-sm mb-4 border-b border-white/5 pb-3">Image Overlay</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Overlay Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" value="{{ old('overlay_color', $quote->overlay_color ?? '#000000') }}"
                                   class="w-10 h-10 rounded-lg border border-white/10 cursor-pointer bg-transparent"
                                   x-model="overlayColor">
                            <input type="hidden" name="overlay_color" x-bind:value="overlayColor">
                            <span class="text-white/40 text-sm" x-text="overlayColor"></span>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Overlay Opacity: <span x-text="overlayOpacity + '%'"></span></label>
                        <input type="range" name="overlay_opacity" min="0" max="100" step="5"
                               value="{{ old('overlay_opacity', $quote->overlay_opacity ?? 40) }}"
                               x-model="overlayOpacity"
                               class="w-full accent-amber-400 mt-2">
                    </div>
                </div>
            </div>
        </div>

        {{-- Live Preview Sidebar --}}
        <div class="xl:col-span-1">
            <div class="sticky top-20">
                <div class="glass-card rounded-2xl p-4">
                    <h3 class="text-white font-semibold text-sm mb-3 border-b border-white/5 pb-3">Live Preview</h3>

                    {{-- Preview card --}}
                    <div class="relative rounded-xl overflow-hidden" style="min-height: 300px;"
                         :style="{ background: 'linear-gradient(135deg, #374151, #1f2937)' }">

                        @if(isset($quote) && $quote->image)
                            <img src="{{ $quote->image_url }}" alt="bg" class="absolute inset-0 w-full h-full object-cover" id="previewBg">
                        @else
                            <img id="previewBg" class="absolute inset-0 w-full h-full object-cover hidden">
                        @endif

                        <div class="absolute inset-0 transition-all"
                             :style="{ backgroundColor: overlayColor, opacity: overlayOpacity / 100 }"></div>

                        <div class="absolute inset-0 flex p-5" style="min-height: 300px;"
                             :class="positionClass">
                            <div>
                                <p :class="fontSizeClass + ' ' + fontStyleClass + ' font-bold leading-snug'"
                                   :style="{ color: textColor, textShadow: '0 2px 8px rgba(0,0,0,0.6)' }"
                                   x-text="quoteText || 'Your quote text will appear here...'">
                                </p>
                                <p x-show="author" :style="{ color: textColor }" class="text-xs mt-2 opacity-70" x-text="'— ' + author"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Save buttons --}}
                    <div class="mt-4 space-y-2">
                        <button type="submit" name="status" value="publish"
                                class="w-full py-3 rounded-xl text-sm font-semibold text-black transition-all"
                                style="background: linear-gradient(135deg, #c9a84c, #f0d07a)">
                            💾 Save & Publish
                        </button>
                        <button type="submit" name="status_override" value="draft"
                                onclick="document.querySelector('[name=status]').value='draft'"
                                class="w-full py-3 rounded-xl text-sm font-medium text-white/60 border border-white/10 hover:bg-white/5 transition-colors">
                            Save as Draft
                        </button>
                        <a href="{{ route('admin.quotes.index') }}"
                           class="w-full py-3 rounded-xl text-sm font-medium text-white/40 flex items-center justify-center hover:text-white/60 transition-colors">
                            ← Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@section('scripts')
<script>
function quoteForm() {
    return {
        title: '{{ addslashes(old("title", $quote->title ?? "")) }}',
        quoteText: `{{ addslashes(old("quote_text", $quote->quote_text ?? "")) }}`,
        author: '{{ addslashes(old("author", $quote->author ?? "")) }}',
        textColor: '{{ old("text_color", $quote->text_color ?? "#ffffff") }}',
        fontSize: '{{ old("font_size", $quote->font_size ?? "2xl") }}',
        fontStyle: '{{ old("font_style", $quote->font_style ?? "serif") }}',
        textPosition: '{{ old("text_position", $quote->text_position ?? "center") }}',
        overlayColor: '{{ old("overlay_color", $quote->overlay_color ?? "#000000") }}',
        overlayOpacity: {{ old("overlay_opacity", $quote->overlay_opacity ?? 40) }},

        init() {
            // Watch file input for preview
            const fileInput = document.getElementById('imageInput');
            if (fileInput) {
                fileInput.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        const url = URL.createObjectURL(file);
                        const bg = document.getElementById('previewBg');
                        bg.src = url;
                        bg.classList.remove('hidden');
                    }
                });
            }
        },

        get positionClass() {
            const map = {
                'top-left': 'items-start justify-start',
                'top-center': 'items-start justify-center text-center',
                'top-right': 'items-start justify-end text-right',
                'center-left': 'items-center justify-start',
                'center': 'items-center justify-center text-center',
                'center-right': 'items-center justify-end text-right',
                'bottom-left': 'items-end justify-start',
                'bottom-center': 'items-end justify-center text-center',
                'bottom-right': 'items-end justify-end text-right',
            };
            return map[this.textPosition] || 'items-center justify-center text-center';
        },

        get fontSizeClass() {
            const map = { sm:'text-sm', base:'text-base', lg:'text-lg', xl:'text-xl', '2xl':'text-2xl', '3xl':'text-3xl', '4xl':'text-4xl', '5xl':'text-5xl' };
            return map[this.fontSize] || 'text-2xl';
        },

        get fontStyleClass() {
            return this.fontStyle === 'serif' ? 'font-serif' : 'font-sans';
        }
    }
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('imgPreview');
            const placeholder = document.getElementById('imgPlaceholder');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');

            const bgPreview = document.getElementById('previewBg');
            bgPreview.src = e.target.result;
            bgPreview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
