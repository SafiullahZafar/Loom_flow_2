<!DOCTYPE html>
<html lang="en" class="h-full bg-zinc-50 text-zinc-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LoomFlow Textile ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f4f5;
        }
        .loom-pattern {
            background-image: radial-gradient(rgba(0,0,0,0.06) 1px, transparent 1px);
            background-size: 28px 28px;
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 loom-pattern">

    <div class="max-w-md w-full">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-zinc-900 items-center justify-center shadow-lg mb-4 transform hover:scale-105 transition-transform">
                <svg class="w-8 h-8 text-zinc-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-zinc-900">LoomFlow</h1>
            <p class="text-xs uppercase tracking-widest font-semibold text-zinc-500 mt-1">Textile Weaving & Mill Management</p>
            <p class="text-sm text-zinc-400 mt-2">Sign in to control your loom production, bags & stock</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white border border-zinc-200 rounded-3xl p-6 sm:p-8 shadow-sm">
            
            @if(session('error'))
                <div role="alert" class="mb-5 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-800">
                    <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-800">
                    <p class="font-bold">We couldn’t sign you in.</p>
                    <ul class="mt-1 list-inside list-disc space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 p-3 rounded-xl bg-zinc-50 border border-zinc-200 text-zinc-600 text-xs">
                    {{ session('info') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-zinc-600 uppercase tracking-wider mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                        </div>
                        <input type="email" name="email" id="email" required value="{{ old('email', 'admin@loomflow.com') }}" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl pl-10 pr-4 py-2.5 text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:border-transparent transition-all" placeholder="user@loomflow.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-zinc-600 uppercase tracking-wider mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input type="password" name="password" id="password" required value="password" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl pl-10 pr-4 py-2.5 text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:border-transparent transition-all" placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-zinc-500 hover:text-zinc-800">
                        <input type="checkbox" name="remember" checked class="rounded border-zinc-300 bg-zinc-50 text-zinc-700 focus:ring-zinc-400/50">
                        <span>Remember credentials</span>
                    </label>
                    <span class="text-zinc-400 font-mono text-[11px]">Pass: password</span>
                </div>

                <button type="submit" class="w-full mt-2 py-3 px-4 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 font-bold text-sm shadow-sm transition-all transform active:scale-[0.99] flex items-center justify-center gap-2">
                    <span>Sign In to Dashboard</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

            <!-- 1-Click Quick Login Demo Section -->
            <div class="mt-8 pt-6 border-t border-zinc-100">
                <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 mb-3 text-center">⚡ Instant 1-Click Demo Login</p>
                <div class="grid grid-cols-3 gap-2">
                    <form @if(!app()->environment('local') || !config('app.debug')) style="display:none" @endif action="{{ route('quick-login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="admin@loomflow.com">
                        <button type="submit" class="w-full p-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-700 border border-zinc-800 text-center transition-all group">
                            <span class="block text-xs font-bold text-zinc-50">Admin</span>
                            <span class="block text-[9px] text-zinc-400">Full Access</span>
                        </button>
                    </form>

                    <form @if(!app()->environment('local') || !config('app.debug')) style="display:none" @endif action="{{ route('quick-login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="store@loomflow.com">
                        <button type="submit" class="w-full p-2.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 border border-zinc-200 text-center transition-all group">
                            <span class="block text-xs font-bold text-zinc-800">Store</span>
                            <span class="block text-[9px] text-zinc-500">Bags & Parts</span>
                        </button>
                    </form>

                    <form @if(!app()->environment('local') || !config('app.debug')) style="display:none" @endif action="{{ route('quick-login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="master@loomflow.com">
                        <button type="submit" class="w-full p-2.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 border border-zinc-200 text-center transition-all group">
                            <span class="block text-xs font-bold text-zinc-800">Master</span>
                            <span class="block text-[9px] text-zinc-500">Looms & Shift</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <div class="text-center mt-6 text-xs text-zinc-400 font-mono">
            SQLite: <strong class="text-zinc-600">looming</strong> &bull; Engine: Active
        </div>
    </div>

</body>
</html>
