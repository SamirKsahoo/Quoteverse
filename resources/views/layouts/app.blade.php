<!DOCTYPE html>
<html lang="en" class="scroll-smooth" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', mobileMenu: false }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'QuoteVerse – Beautiful quotes to inspire your day')">
    <title>@yield('title', 'QuoteVerse') – Beautiful Quotes</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            fontFamily: {
              serif: ['Playfair Display', 'Georgia', 'serif'],
              sans:  ['DM Sans', 'system-ui', 'sans-serif'],
            },
            colors: {
              ink:   { DEFAULT: '#0f0d1a', 50: '#f5f3ff', 100: '#ede9fe' },
              cream: { DEFAULT: '#faf8f5', dark: '#f0ece6' },
              gold:  { DEFAULT: '#c9a84c', light: '#f0d07a' },
            },
            animation: {
              'fade-up':    'fadeUp 0.6s ease forwards',
              'fade-in':    'fadeIn 0.4s ease forwards',
              'float':      'float 6s ease-in-out infinite',
              'shimmer':    'shimmer 2s linear infinite',
            },
            keyframes: {
              fadeUp:  { '0%': { opacity: 0, transform: 'translateY(20px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
              fadeIn:  { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
              float:   { '0%,100%': { transform: 'translateY(0px)' }, '50%': { transform: 'translateY(-12px)' } },
              shimmer: { '0%': { backgroundPosition: '-200% 0' }, '100%': { backgroundPosition: '200% 0' } },
            }
          }
        }
      }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
      body { font-family: 'DM Sans', sans-serif; }
      .font-serif { font-family: 'Playfair Display', serif; }
      .quote-card { transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1); }
      .quote-card:hover { transform: translateY(-6px); }
      .glass { backdrop-filter: blur(12px); background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); }
      .glass-dark { backdrop-filter: blur(12px); background: rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.1); }
      .text-gradient { background: linear-gradient(135deg, #c9a84c, #f0d07a, #c9a84c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
      .btn-primary { background: linear-gradient(135deg, #c9a84c, #f0d07a); color: #0f0d1a; font-weight: 600; transition: all 0.2s; }
      .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 25px rgba(201,168,76,0.4); }
      .noise-bg::before { content: ''; position: fixed; inset: 0; background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.03'/%3E%3C/svg%3E"); pointer-events: none; z-index: 0; opacity: 0.4; }
      .masonry { columns: 1; gap: 1.5rem; }
      @media (min-width: 640px) { .masonry { columns: 2; } }
      @media (min-width: 1024px) { .masonry { columns: 3; } }
      @media (min-width: 1280px) { .masonry { columns: 4; } }
      .masonry > * { break-inside: avoid; margin-bottom: 1.5rem; }
      .lazy { opacity: 0; transition: opacity 0.4s ease; }
      .lazy.loaded { opacity: 1; }
    </style>

    @yield('head')
</head>
<body class="bg-cream dark:bg-ink text-ink dark:text-cream transition-colors duration-300 noise-bg">

<!-- Navbar -->
<nav class="sticky top-0 z-50 glass dark:glass-dark border-b border-cream-dark/20 dark:border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gold to-gold-light flex items-center justify-center text-ink font-bold text-sm">Q</div>
                <span class="font-serif text-xl font-bold">Quote<span class="text-gradient">Verse</span></span>
            </a>

            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-sm font-medium hover:text-gold transition-colors {{ request()->routeIs('home') ? 'text-gold' : 'text-ink/70 dark:text-cream/70' }}">Home</a>
                <a href="{{ route('quotes.index') }}" class="text-sm font-medium hover:text-gold transition-colors {{ request()->routeIs('quotes.*') ? 'text-gold' : 'text-ink/70 dark:text-cream/70' }}">Quotes</a>
                @foreach(\App\Models\Category::take(4)->get() as $cat)
                    <a href="{{ route('category.show', $cat->slug) }}" class="text-sm font-medium hover:text-gold transition-colors text-ink/70 dark:text-cream/70">{{ $cat->name }}</a>
                @endforeach
            </div>

            <!-- Right actions -->
            <div class="flex items-center gap-3">
                <!-- Search -->
                <form action="{{ route('quotes.index') }}" method="GET" class="hidden md:flex items-center">
                    <div class="relative">
                        <input type="text" name="search" placeholder="Search quotes..." value="{{ request('search') }}"
                            class="pl-9 pr-4 py-1.5 text-sm rounded-full bg-white/50 dark:bg-white/10 border border-cream-dark/20 dark:border-white/10 focus:outline-none focus:ring-2 focus:ring-gold/40 w-40 focus:w-56 transition-all placeholder-ink/40 dark:placeholder-cream/40">
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-ink/40 dark:text-cream/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </form>

                <!-- Dark mode toggle -->
                <button @click="darkMode = !darkMode" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-black/5 dark:hover:bg-white/10 transition-colors">
                    <svg x-show="!darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>

                <!-- Mobile menu btn -->
                <button @click="mobileMenu = !mobileMenu" class="md:hidden w-8 h-8 rounded-full flex items-center justify-center hover:bg-black/5 dark:hover:bg-white/10 transition-colors">
                    <svg x-show="!mobileMenu" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileMenu" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="mobileMenu" x-transition class="md:hidden pb-4 space-y-2">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-black/5 dark:hover:bg-white/5">Home</a>
            <a href="{{ route('quotes.index') }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-black/5 dark:hover:bg-white/5">All Quotes</a>
            @foreach(\App\Models\Category::all() as $cat)
                <a href="{{ route('category.show', $cat->slug) }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-black/5 dark:hover:bg-white/5">{{ $cat->icon }} {{ $cat->name }}</a>
            @endforeach
            <form action="{{ route('quotes.index') }}" method="GET" class="px-3">
                <input type="text" name="search" placeholder="Search quotes..." class="w-full px-4 py-2 text-sm rounded-full bg-white/50 dark:bg-white/10 border border-cream-dark/20 dark:border-white/10 focus:outline-none focus:ring-2 focus:ring-gold/40">
            </form>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="relative z-10">
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 pt-4">
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif
    @yield('content')
</main>

<!-- Footer -->
<footer class="relative z-10 mt-24 border-t border-cream-dark/20 dark:border-white/10 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="text-center md:text-left">
                <a href="{{ route('home') }}" class="font-serif text-2xl font-bold">Quote<span class="text-gradient">Verse</span></a>
                <p class="text-sm text-ink/50 dark:text-cream/50 mt-1">Words that move the world.</p>
            </div>
            <div class="flex items-center gap-6">
                @foreach(\App\Models\Category::take(5)->get() as $cat)
                    <a href="{{ route('category.show', $cat->slug) }}" class="text-sm text-ink/50 dark:text-cream/50 hover:text-gold transition-colors">{{ $cat->name }}</a>
                @endforeach
            </div>
            <p class="text-xs text-ink/30 dark:text-cream/30">© {{ date('Y') }} QuoteVerse</p>
        </div>
    </div>
</footer>

<script>
// Lazy load images
const images = document.querySelectorAll('img[data-src]');
const imageObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            img.classList.add('loaded');
            imageObserver.unobserve(img);
        }
    });
});
images.forEach(img => imageObserver.observe(img));
</script>
@yield('scripts')
</body>
</html>
