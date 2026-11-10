<!DOCTYPE html>
<html lang="en" class="h-full bg-zinc-50 text-zinc-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LoomFlow') - Textile Weaving & Mill ERP</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
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
            background-color: #fafafa;
            min-height: 100vh;
        }
        .card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 1rem;
        }
        .card-hover {
            transition: all 0.2s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px -6px rgba(0,0,0,0.10);
            border-color: #a1a1aa;
        }
        /* Keep glass-card as alias for backward compat with child views */
        .glass-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
        }
        .glass-card-hover {
            transition: all 0.2s ease;
        }
        .glass-card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px -6px rgba(0,0,0,0.10);
            border-color: #a1a1aa;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f4f4f5; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 9999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a1a1aa; }
    </style>
</head>
<body class="h-full antialiased custom-scrollbar bg-zinc-50 text-zinc-900">
    <div class="min-h-screen flex flex-col">
        
        <!-- Top Navigation Bar -->
        <header class="sticky top-0 z-40 bg-white border-b border-zinc-200 shadow-sm">
            <div class="w-full px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    
                    <!-- Logo & Brand -->
                    <div class="flex items-center gap-4">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                            <div class="w-10 h-10 rounded-xl bg-zinc-900 flex items-center justify-center shadow-sm group-hover:bg-zinc-700 transition-colors">
                                <svg class="w-5 h-5 text-zinc-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-xl font-extrabold tracking-tight text-zinc-900">LoomFlow</span>
                                <span class="block text-[10px] uppercase tracking-wider font-semibold text-zinc-500">Textile ERP</span>
                            </div>
                        </a>

                        <!-- MODULE SWITCHER DROPDOWN -->
                        <div class="relative ml-4" id="moduleSwitcherContainer">
                            @php
                                $currentRoute = Route::currentRouteName();
                                $activeModule = 'Dashboard';
                                $activeIcon = 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z';
                                $activeColor = 'text-zinc-700 border-zinc-300 bg-zinc-100';

                                if (str_starts_with($currentRoute, 'factory')) {
                                    $activeModule = 'Factory Operations';
                                    $activeColor = 'text-zinc-800 border-zinc-400 bg-zinc-100';
                                } elseif (str_starts_with($currentRoute, 'bags')) {
                                    $activeModule = 'Bags (Yarn)';
                                    $activeColor = 'text-zinc-800 border-zinc-400 bg-zinc-100';
                                    $activeIcon = 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4';
                                } elseif (str_starts_with($currentRoute, 'sizing')) {
                                    $activeModule = 'Sizing & Beams';
                                    $activeColor = 'text-zinc-800 border-zinc-400 bg-zinc-100';
                                    $activeIcon = 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15';
                                } elseif (str_starts_with($currentRoute, 'parts')) {
                                    $activeModule = 'Parts Store';
                                    $activeColor = 'text-zinc-800 border-zinc-400 bg-zinc-100';
                                    $activeIcon = 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z';
                                } elseif (str_starts_with($currentRoute, 'fabric')) {
                                    $activeModule = 'Fabric Stock';
                                    $activeColor = 'text-zinc-800 border-zinc-400 bg-zinc-100';
                                    $activeIcon = 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10';
                                } elseif (str_starts_with($currentRoute, 'sales')) {
                                    $activeModule = 'Sales & Slips';
                                    $activeColor = 'text-zinc-800 border-zinc-400 bg-zinc-100';
                                    $activeIcon = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                                } elseif (str_starts_with($currentRoute, 'invoices')) {
                                    $activeModule = 'Invoices & Accounts';
                                    $activeColor = 'text-zinc-800 border-zinc-400 bg-zinc-100';
                                    $activeIcon = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a2 2 0 01.707.293l5.414 5.414A2 2 0 0119 9v10a2 2 0 01-2 2z';
                                } elseif (str_starts_with($currentRoute, 'production')) {
                                    $activeModule = 'Production';
                                    $activeColor = 'text-zinc-800 border-zinc-400 bg-zinc-100';
                                    $activeIcon = 'M13 10V3L4 14h7v7l9-11h-7z';
                                } elseif (str_starts_with($currentRoute, 'ledgers')) {
                                    $activeModule = 'Ledgers';
                                    $activeColor = 'text-zinc-800 border-zinc-400 bg-zinc-100';
                                    $activeIcon = 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                                }
                            @endphp

                            <button type="button" onclick="toggleModuleSwitcher()" class="flex items-center gap-2.5 px-3.5 py-1.5 rounded-xl border {{ $activeColor }} font-semibold text-xs tracking-wide transition-all hover:bg-zinc-200 focus:outline-none">
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-zinc-400 opacity-60"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-zinc-700"></span>
                                </span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $activeIcon }}" />
                                </svg>
                                <span>Active: <strong class="underline decoration-dotted ml-0.5">{{ $activeModule }}</strong></span>
                                <svg class="w-3.5 h-3.5 opacity-60 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="moduleSwitcherDropdown" class="hidden absolute left-0 mt-2 w-72 rounded-2xl bg-white border border-zinc-200 shadow-xl shadow-zinc-200/80 py-2 z-50">
                                <div class="px-3.5 py-2 border-b border-zinc-100 text-[11px] font-bold text-zinc-500 uppercase tracking-wider flex items-center justify-between">
                                    <span>Jump Directly to Module</span>
                                    <span class="text-zinc-700 font-mono text-[10px]">LoomFlow Hub</span>
                                </div>
                                <div class="p-1.5 space-y-0.5">
                                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium {{ $currentRoute == 'dashboard' ? 'bg-zinc-900 text-zinc-50' : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900' }}">
                                        <div class="w-7 h-7 rounded-lg {{ $currentRoute == 'dashboard' ? 'bg-zinc-700 text-zinc-200' : 'bg-zinc-100 text-zinc-600' }} flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold">Main Hub / Boxes</div>
                                            <div class="text-[10px] text-zinc-400">6 Core Cards Overview</div>
                                        </div>
                                    </a>

                                    <a href="{{ route('factory.orders') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl text-xs font-bold bg-zinc-900 text-white hover:bg-zinc-700">Factory Orders & Parta</a>
                                    <a href="{{ route('factory.checking') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl text-xs font-bold bg-zinc-100 text-zinc-800 hover:bg-zinc-200">Checking / Thaan Stock</a>
                                    <a href="{{ route('factory.parchis') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl text-xs font-bold bg-zinc-100 text-zinc-800 hover:bg-zinc-200">Kachi / Pakki / Returns</a>
                                    <a href="{{ route('factory.reports') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl text-xs font-bold bg-zinc-100 text-zinc-800 hover:bg-zinc-200">Factory Reports & Wages</a>
                                    <a href="{{ route('bags.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium {{ str_starts_with($currentRoute, 'bags') ? 'bg-zinc-900 text-zinc-50' : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900' }}">
                                        <div class="w-7 h-7 rounded-lg {{ str_starts_with($currentRoute, 'bags') ? 'bg-zinc-700 text-zinc-200' : 'bg-zinc-100 text-zinc-600' }} flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold">1. Bags (Yarn)</div>
                                            <div class="text-[10px] text-zinc-400">Purchase Split & Store Stock</div>
                                        </div>
                                    </a>

                                    <a href="{{ route('sizing.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium {{ str_starts_with($currentRoute, 'sizing') ? 'bg-zinc-900 text-zinc-50' : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900' }}">
                                        <div class="w-7 h-7 rounded-lg {{ str_starts_with($currentRoute, 'sizing') ? 'bg-zinc-700 text-zinc-200' : 'bg-zinc-100 text-zinc-600' }} flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold">2. Sizing & Beams</div>
                                            <div class="text-[10px] text-zinc-400">Slips, Beams & Unused Bags</div>
                                        </div>
                                    </a>

                                    <a href="{{ route('parts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium {{ str_starts_with($currentRoute, 'parts') ? 'bg-zinc-900 text-zinc-50' : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900' }}">
                                        <div class="w-7 h-7 rounded-lg {{ str_starts_with($currentRoute, 'parts') ? 'bg-zinc-700 text-zinc-200' : 'bg-zinc-100 text-zinc-600' }} flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold">3. Parts Store</div>
                                            <div class="text-[10px] text-zinc-400">Grippers & Factory Issuance</div>
                                        </div>
                                    </a>

                                    <a href="{{ route('fabric.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium {{ str_starts_with($currentRoute, 'fabric') ? 'bg-zinc-900 text-zinc-50' : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900' }}">
                                        <div class="w-7 h-7 rounded-lg {{ str_starts_with($currentRoute, 'fabric') ? 'bg-zinc-700 text-zinc-200' : 'bg-zinc-100 text-zinc-600' }} flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold">4. Fabric Stock</div>
                                            <div class="text-[10px] text-zinc-400">Open Sale vs Contract Stock</div>
                                        </div>
                                    </a>

                                    <a href="{{ route('sales.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium {{ str_starts_with($currentRoute, 'sales') ? 'bg-zinc-900 text-zinc-50' : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900' }}">
                                        <div class="w-7 h-7 rounded-lg {{ str_starts_with($currentRoute, 'sales') ? 'bg-zinc-700 text-zinc-200' : 'bg-zinc-100 text-zinc-600' }} flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold">5. Sales & Slips</div>
                                            <div class="text-[10px] text-zinc-400">Open Slips & Multi-Contracts</div>
                                        </div>
                                    </a>

                                    <a href="{{ route('invoices.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium {{ str_starts_with($currentRoute, 'invoices') ? 'bg-zinc-900 text-zinc-50' : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900' }}">
                                        <div class="w-7 h-7 rounded-lg {{ str_starts_with($currentRoute, 'invoices') ? 'bg-zinc-700 text-zinc-200' : 'bg-zinc-100 text-zinc-600' }} flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a2 2 0 01.707.293l5.414 5.414A2 2 0 0119 9v10a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div><div class="font-semibold">7. Invoices & Accounts</div><div class="text-[10px] text-zinc-400">Customers, receipts & bank balances</div></div>
                                    </a>

                                    <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium {{ str_starts_with($currentRoute, 'expenses') ? 'bg-zinc-900 text-zinc-50' : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900' }}">
                                        <div class="w-7 h-7 rounded-lg {{ str_starts_with($currentRoute, 'expenses') ? 'bg-zinc-700 text-zinc-200' : 'bg-zinc-100 text-zinc-600' }} flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div><div class="font-semibold">8. Expenses</div><div class="text-[10px] text-zinc-400">Fixed schedules & variable costs</div></div>
                                    </a>

                                    <a href="{{ route('production.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium {{ str_starts_with($currentRoute, 'production') ? 'bg-zinc-900 text-zinc-50' : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900' }}">
                                        <div class="w-7 h-7 rounded-lg {{ str_starts_with($currentRoute, 'production') ? 'bg-zinc-700 text-zinc-200' : 'bg-zinc-100 text-zinc-600' }} flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold">6. Production</div>
                                            <div class="text-[10px] text-zinc-400">Loom-wise & Leakage Loss</div>
                                        </div>
                                    </a>

                                    <div class="border-t border-zinc-100 my-1"></div>

                                    <a href="{{ route('ledgers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium {{ str_starts_with($currentRoute, 'ledgers') ? 'bg-zinc-900 text-zinc-50' : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900' }}">
                                        <div class="w-7 h-7 rounded-lg {{ str_starts_with($currentRoute, 'ledgers') ? 'bg-zinc-700 text-zinc-200' : 'bg-zinc-100 text-zinc-600' }} flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold">Party Ledgers</div>
                                            <div class="text-[10px] text-zinc-400">Audit & Statement History</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile & Action Bar -->
                    <div class="flex items-center gap-4">
                        @auth
                            <div class="hidden md:flex flex-col text-right">
                                <span class="text-xs font-bold text-zinc-800">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</span>
                            </div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="p-2 rounded-xl bg-zinc-100 text-zinc-500 hover:text-zinc-900 hover:bg-zinc-200 border border-zinc-200 transition-colors" title="Logout">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-xs font-bold transition-colors">Login</a>
                        @endauth
                    </div>

                </div>
            </div>
        </header>

        <!-- Flash Messages Banner -->
        <div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-zinc-100 border border-zinc-300 text-zinc-800 flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-zinc-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-zinc-100 border border-zinc-400 text-zinc-900 flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-zinc-700 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm font-medium">{{ session('error') }}</div>
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 rounded-2xl bg-zinc-50 border border-zinc-200 text-zinc-700 flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-zinc-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm font-medium">{{ session('info') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-zinc-100 border border-zinc-300 text-zinc-800 shadow-sm">
                    <div class="flex items-center gap-2 font-bold text-sm mb-1">
                        <svg class="w-5 h-5 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Please correct the following:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Main Body Content -->
        @php
            $fullWidthRoutes = ['bags', 'sizing', 'parts', 'fabric', 'sales', 'production', 'invoices', 'expenses', 'ledgers'];
            $isFullWidth = collect($fullWidthRoutes)->contains(fn($prefix) => str_starts_with(Route::currentRouteName() ?? '', $prefix));
        @endphp
        <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 py-6">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="border-t border-zinc-200 bg-white py-4 text-center text-xs text-zinc-400">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2">
                <div>
                    <strong class="text-zinc-700">LoomFlow</strong> &copy; {{ date('Y') }} - Intelligent Textile Loom & Mill ERP Management
                </div>
                <div class="flex items-center gap-4 text-[11px]">
                    <span class="inline-flex items-center gap-1.5 text-zinc-600">
                        <span class="h-2 w-2 rounded-full bg-zinc-500"></span> Live Looms Connected
                    </span>
                    <a href="{{ route('ledgers.index') }}" class="hover:text-zinc-800 transition-colors">General Ledgers</a>
                    <a href="{{ route('dashboard') }}" class="hover:text-zinc-800 transition-colors">Hub Cards</a>
                </div>
            </div>
        </footer>

    </div>

    <!-- Dropdown & Modal Helper Script -->
    <script>
        function toggleModuleSwitcher() {
            const dropdown = document.getElementById('moduleSwitcherDropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }

        // Close dropdown when clicked outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('moduleSwitcherContainer');
            const dropdown = document.getElementById('moduleSwitcherDropdown');
            if (container && dropdown && !container.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Modal Helpers
        function openModal(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.remove('hidden');
                if (el.classList.contains('items-center')) {
                    el.classList.add('flex');
                }
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('hidden');
                if (el.classList.contains('items-center')) {
                    el.classList.remove('flex');
                }
                document.body.classList.remove('overflow-hidden');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
