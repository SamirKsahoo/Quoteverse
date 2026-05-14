<div class="quote-card rounded-2xl overflow-hidden shadow-sm hover:shadow-lg group cursor-pointer">
    <a href="{{ route('quotes.show', $quote->slug) }}" class="block">
        <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #374151, #1f2937)">
            @if($quote->image)
                <img data-src="{{ $quote->image_url }}"
                     src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 3'%3E%3C/svg%3E"
                     alt="{{ $quote->title }}"
                     class="lazy w-full object-cover group-hover:scale-105 transition-transform duration-700"
                     style="min-height: 120px; max-height: 320px">
            @else
                <div class="w-full bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900" style="min-height: 160px"></div>
            @endif

            <div class="absolute inset-0"
                 style="background-color: {{ $quote->overlay_color }}; opacity: {{ $quote->overlay_opacity / 100 }}"></div>

            <div class="absolute inset-0 flex {{ $quote->text_position_class }} p-4">
                <div>
                    <p class="{{ $quote->font_size_class }} {{ $quote->font_style_class }} font-bold leading-snug"
                       style="color: {{ $quote->text_color }}; text-shadow: 0 2px 8px rgba(0,0,0,0.5); font-size: clamp(0.8rem, 2vw, 1.25rem)">
                        "{{ Str::limit($quote->quote_text, 120) }}"
                    </p>
                    @if($quote->author)
                        <p class="text-xs mt-1 opacity-75" style="color: {{ $quote->text_color }}">— {{ $quote->author }}</p>
                    @endif
                </div>
            </div>

            <div class="absolute top-2 left-2">
                <span class="px-2 py-0.5 rounded-full text-xs font-medium text-white glass-dark">{{ $quote->category->icon ?? '✨' }} {{ $quote->category->name }}</span>
            </div>
        </div>

        <div class="px-3 py-2 bg-white dark:bg-ink/60 border border-cream-dark/20 dark:border-white/5 border-t-0 rounded-b-2xl">
            <p class="text-xs font-medium text-ink/60 dark:text-cream/50 truncate group-hover:text-gold transition-colors">{{ $quote->title }}</p>
        </div>
    </a>
</div>
