<div class="quote-card rounded-2xl overflow-hidden shadow-md hover:shadow-xl group">
    <a href="{{ route('quotes.show', $quote->slug) }}" class="block">
        {{-- Image with overlay --}}
        <div class="relative h-64 overflow-hidden"
             style="background: linear-gradient(135deg, #374151, #1f2937)">
            @if($quote->image)
                <img data-src="{{ $quote->image_url }}"
                     src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E"
                     alt="{{ $quote->title }}"
                     class="lazy w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            @else
                <div class="w-full h-full bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900"></div>
            @endif

            {{-- Overlay --}}
            <div class="absolute inset-0 transition-opacity duration-300"
                 style="background-color: {{ $quote->overlay_color }}; opacity: {{ $quote->overlay_opacity / 100 }}"></div>

            {{-- Quote text overlay --}}
            <div class="absolute inset-0 flex {{ $quote->text_position_class }} p-6">
                <div class="max-w-full">
                    <p class="{{ $quote->font_size_class }} {{ $quote->font_style_class }} font-bold leading-snug line-clamp-4"
                       style="color: {{ $quote->text_color }}; text-shadow: 0 2px 8px rgba(0,0,0,0.5)">
                        "{{ $quote->quote_text }}"
                    </p>
                    @if($quote->author)
                        <p class="text-xs mt-2 opacity-80 {{ $quote->font_style_class }}" style="color: {{ $quote->text_color }}">— {{ $quote->author }}</p>
                    @endif
                </div>
            </div>

            {{-- Category badge --}}
            <div class="absolute top-3 left-3">
                <span class="px-3 py-1 rounded-full text-xs font-semibold text-white glass-dark">
                    {{ $quote->category->icon ?? '✨' }} {{ $quote->category->name }}
                </span>
            </div>
        </div>

        {{-- Card footer --}}
        <div class="p-4 bg-white dark:bg-ink/60 border border-cream-dark/20 dark:border-white/5 border-t-0 rounded-b-2xl">
            <h3 class="font-semibold text-sm text-ink dark:text-cream truncate group-hover:text-gold transition-colors">{{ $quote->title }}</h3>
            <div class="flex items-center justify-between mt-2">
                <span class="text-xs text-ink/40 dark:text-cream/40">{{ $quote->created_at->diffForHumans() }}</span>
                <div class="flex items-center gap-1 text-xs text-ink/40 dark:text-cream/40">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    {{ number_format($quote->views) }}
                </div>
            </div>
        </div>
    </a>
</div>
