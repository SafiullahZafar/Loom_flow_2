<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f6f4] text-[#131e18]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LoomFlow Factory Workspace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f6f4;
        }
        .loom-pattern {
            background-image: radial-gradient(#133023 0.75px, transparent 0.75px);
            background-size: 24px 24px;
            background-color: #f4f6f4;
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 loom-pattern">

    <div class="max-w-md w-full">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-[#133e2b] text-[#c6f16d] items-center justify-center shadow-md mb-4 transform hover:scale-105 transition-transform border border-[#1b4835]">
                <span class="text-2xl font-black tracking-tight">LF</span>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-[#133e2b]">loomflow.</h1>
            <p class="text-[10px] uppercase tracking-widest font-extrabold text-[#15803d] mt-1">FACTORY WORKSPACE</p>
            <p class="text-xs text-stone-500 mt-2">Sign in to control your loom production, bags &amp; stock</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white border border-[#e2e8e0] rounded-3xl p-6 sm:p-8 shadow-sm">
            
            @if(session('error'))
                <div role="alert" class="mb-5 flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-800">
                    <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div role="alert" class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-800">
                    <p class="font-bold">We couldn’t sign you in.</p>
                    <ul class="mt-1 list-inside list-disc space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-[#133e2b] text-xs">
                    {{ session('info') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                        </div>
                        <input type="email" name="email" id="email" required value="{{ old('email', 'admin@loomflow.com') }}" class="w-full bg-[#f4f6f4] border border-[#e2e8e0] rounded-xl pl-10 pr-4 py-2.5 text-xs text-stone-900 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-[#133e2b]/20 focus:border-[#133e2b] transition-all" placeholder="user@loomflow.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input type="password" name="password" id="password" required value="password" class="w-full bg-[#f4f6f4] border border-[#e2e8e0] rounded-xl pl-10 pr-4 py-2.5 text-xs text-stone-900 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-[#133e2b]/20 focus:border-[#133e2b] transition-all" placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-stone-600 hover:text-stone-900">
                        <input type="checkbox" name="remember" checked class="rounded border-[#e2e8e0] bg-white text-[#133e2b] focus:ring-[#133e2b]">
                        <span>Remember credentials</span>
                    </label>
                    <span class="text-stone-400 font-mono text-[11px]">Pass: password</span>
                </div>

                <button type="submit" class="w-full mt-2 py-3 px-4 rounded-xl bg-[#133e2b] hover:bg-[#1b5239] text-white font-bold text-xs shadow-2xs transition-all transform active:scale-[0.99] flex items-center justify-center gap-2">
                    <span>Sign In to Workspace</span>
                    <span class="text-sm">&nearr;</span>
                </button>
            </form>

            <!-- 1-Click Quick Login Demo Section -->
            <div class="mt-8 pt-6 border-t border-stone-100">
                <p class="text-[10px] font-extrabold uppercase tracking-widest text-[#15803d] mb-3 text-center">&bull; Instant 1-Click Demo Access &bull;</p>
                <div class="grid grid-cols-3 gap-2">
                    <form @if(!app()->environment('local') || !config('app.debug')) style="display:none" @endif action="{{ route('quick-login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="admin@loomflow.com">
                        <button type="submit" class="w-full p-2.5 rounded-xl bg-[#133e2b] hover:bg-[#1b5239] text-center transition-all group">
                            <span class="block text-xs font-bold text-[#c6f16d]">Admin</span>
                            <span class="block text-[9px] text-emerald-300/80">Full Access</span>
                        </button>
                    </form>

                    <form @if(!app()->environment('local') || !config('app.debug')) style="display:none" @endif action="{{ route('quick-login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="store@loomflow.com">
                        <button type="submit" class="w-full p-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 border border-stone-200 text-center transition-all group">
                            <span class="block text-xs font-bold text-stone-800">Store</span>
                            <span class="block text-[9px] text-stone-500">Bags &amp; Parts</span>
                        </button>
                    </form>

                    <form @if(!app()->environment('local') || !config('app.debug')) style="display:none" @endif action="{{ route('quick-login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="master@loomflow.com">
                        <button type="submit" class="w-full p-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 border border-stone-200 text-center transition-all group">
                            <span class="block text-xs font-bold text-stone-800">Master</span>
                            <span class="block text-[9px] text-stone-500">Looms &amp; Shift</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <div class="text-center mt-6 text-xs text-stone-400 font-mono">
            loomflow &bull; Single-Operator Weaving Engine
        </div>
    </div>

</body>
</html>
