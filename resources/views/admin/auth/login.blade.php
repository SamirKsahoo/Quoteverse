<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login – QuoteVerse</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
        body { font-family: 'DM Sans', sans-serif; }
        .text-gradient { background: linear-gradient(135deg, #c9a84c, #f0d07a); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    </style>
</head>
<body class="bg-[#0a0814] min-h-screen flex items-center justify-center p-4">

    {{-- Background decoration --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-purple-900/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-gold/10 rounded-full blur-3xl" style="--tw-bg-opacity:0.1; background-color: rgba(201,168,76,0.08)"></div>
    </div>

    <div class="relative w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-yellow-500 to-amber-400 flex items-center justify-center text-black font-bold text-2xl mx-auto mb-4 shadow-lg shadow-amber-500/25">Q</div>
            <h1 class="font-serif text-3xl font-bold text-white">Quote<span class="text-gradient">Verse</span></h1>
            <p class="text-white/30 text-sm mt-1">Admin Dashboard</p>
        </div>

        {{-- Card --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl">
            <h2 class="text-white font-semibold text-xl mb-1">Welcome back</h2>
            <p class="text-white/40 text-sm mb-8">Sign in to manage your quotes</p>

            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 text-red-300 px-4 py-3 rounded-xl text-sm mb-6">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-500/10 border border-red-500/20 text-red-300 px-4 py-3 rounded-xl text-sm mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-white/60 text-xs font-medium mb-2 uppercase tracking-wider">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="admin@quoteverse.com"
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400/40 transition-all">
                </div>

                <div>
                    <label class="block text-white/60 text-xs font-medium mb-2 uppercase tracking-wider">Password</label>
                    <input type="password" name="password" required
                           placeholder="••••••••"
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400/40 transition-all">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded accent-amber-400">
                    <label for="remember" class="text-white/50 text-sm">Remember me</label>
                </div>

                <button type="submit"
                        class="w-full py-3.5 rounded-xl font-semibold text-sm transition-all"
                        style="background: linear-gradient(135deg, #c9a84c, #f0d07a); color: #0a0814; box-shadow: 0 8px 25px rgba(201,168,76,0.3)">
                    Sign In to Dashboard
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-white/5 text-center">
                <a href="{{ route('home') }}" class="text-white/30 text-xs hover:text-white/50 transition-colors">
                    ← Back to QuoteVerse
                </a>
            </div>
        </div>

        <p class="text-center text-white/20 text-xs mt-6">
            Demo: admin@quoteverse.com / password
        </p>
    </div>

</body>
</html>
