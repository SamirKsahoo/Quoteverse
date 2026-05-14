<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') – QuoteVerse Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['Playfair Display', 'serif'],
                        sans: ['DM Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'DM Sans', sans-serif; background: #0a0814; }
        .text-gradient { background: linear-gradient(135deg, #c9a84c, #f0d07a); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .sidebar-link { transition: all 0.2s; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(201,168,76,0.1); color: #f0d07a; border-left-color: #c9a84c; }
        .glass-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(12px); }
    </style>
    @yield('head')
</head>
<body class="text-white min-h-screen" x-data="{ sidebarOpen: true }">

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 flex flex-col transition-all duration-300"
           :class="sidebarOpen ? 'w-64' : 'w-16'"
           style="background: linear-gradient(180deg, #0f0820 0%, #130a24 100%); border-right: 1px solid rgba(255,255,255,0.06)">

        {{-- Logo --}}
        <div class="flex items-center gap-3 p-5 border-b border-white/5">
            <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center font-bold text-black text-lg" style="background: linear-gradient(135deg, #c9a84c, #f0d07a)">Q</div>
            <span class="font-serif text-lg font-bold truncate transition-opacity" :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">
                Quote<span class="text-gradient">Verse</span>
            </span>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
            @php
                $navItems = [
                    ['route' => 'admin.dashboard',        'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',  'label' => 'Dashboard'],
                    ['route' => 'admin.quotes.index',     'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'label' => 'Quotes'],
                    ['route' => 'admin.categories.index', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'label' => 'Categories'],
                ];
            @endphp

            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl border-l-2 border-transparent text-white/50 text-sm font-medium {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span class="truncate transition-opacity" :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- Bottom actions --}}
        <div class="p-3 border-t border-white/5 space-y-1">
            <a href="{{ route('home') }}" target="_blank"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/40 hover:text-white/70 text-sm transition-colors">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span class="truncate transition-opacity" :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">View Site</span>
            </a>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/40 hover:text-red-400 text-sm transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span class="truncate transition-opacity" :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-16'">

        {{-- Topbar --}}
        <header class="sticky top-0 z-30 flex items-center justify-between px-6 py-4" style="background: rgba(10,8,20,0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.06)">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-white font-semibold text-sm">@yield('title', 'Dashboard')</h1>
                    <p class="text-white/30 text-xs">@yield('subtitle', 'QuoteVerse Admin')</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full glass-card text-xs text-white/50">
                    <div class="w-1.5 h-1.5 rounded-full bg-green-400"></div>
                    {{ auth()->user()->name }}
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-500/10 border border-green-500/20 text-green-300 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-4 bg-red-500/10 border border-red-500/20 text-red-300 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Page content --}}
        <main class="p-6">
            @yield('content')
        </main>
    </div>
</div>

@yield('scripts')
</body>
</html>
