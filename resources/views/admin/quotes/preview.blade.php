<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: {{ $quote->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { serif: ['Playfair Display', 'serif'], sans: ['DM Sans', 'sans-serif'] } } } }</script>
    <style>body { font-family: 'DM Sans', sans-serif; background: #0a0814; }</style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-8">

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('admin.quotes.edit', $quote) }}" class="text-white/50 hover:text-white text-sm transition-colors">← Edit Quote</a>
        <span class="px-3 py-1 rounded-full text-xs {{ $quote->status === 'publish' ? 'bg-green-500/15 text-green-400' : 'bg-orange-500/15 text-orange-400' }}">{{ $quote->status }}</span>
    </div>

    <div class="relative rounded-3xl overflow-hidden shadow-2xl w-full max-w-2xl" style="aspect-ratio: 4/3;">
        @if($quote->image)
            <img src="{{ $quote->image_url }}" alt="{{ $quote->title }}" class="absolute inset-0 w-full h-full object-cover">
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900"></div>
        @endif

        <div class="absolute inset-0"
             style="background-color: {{ $quote->overlay_color }}; opacity: {{ $quote->overlay_opacity / 100 }}"></div>

        <div class="absolute inset-0 flex {{ $quote->text_position_class }} p-12">
            <div class="max-w-xl">
                <div class="font-serif text-6xl leading-none mb-4 opacity-30" style="color: {{ $quote->text_color }}">"</div>
                <p class="{{ $quote->font_size_class }} {{ $quote->font_style_class }} font-bold leading-relaxed"
                   style="color: {{ $quote->text_color }}; text-shadow: 0 2px 16px rgba(0,0,0,0.5)">
                    {{ $quote->quote_text }}
                </p>
                @if($quote->author)
                    <p class="mt-4 text-base {{ $quote->font_style_class }} opacity-75" style="color: {{ $quote->text_color }}">
                        — {{ $quote->author }}
                    </p>
                @endif
            </div>
        </div>

        <div class="absolute bottom-5 right-5">
            <div class="px-3 py-1 rounded-full text-xs text-white" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(8px)">
                QuoteVerse
            </div>
        </div>
    </div>

    <div class="mt-6 text-center">
        <h2 class="text-white font-semibold text-lg">{{ $quote->title }}</h2>
        <p class="text-white/30 text-sm mt-1">{{ $quote->category->icon }} {{ $quote->category->name }} · {{ $quote->created_at->format('M d, Y') }}</p>
    </div>

</body>
</html>
