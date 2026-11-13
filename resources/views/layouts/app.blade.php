<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f6f4] text-[#131e18]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LoomFlow') - Factory Workspace</title>
    
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
                    colors: {
                        zinc: {
                            50: '#f5f7f5',
                            100: '#edf2ec',
                            200: '#e2e8e0',
                            300: '#cbd6cb',
                            400: '#8fa699',
                            500: '#647b6e',
                            600: '#385747',
                            700: '#1b4835',
                            800: '#143829',
                            900: '#133e2b',
                            950: '#0b261a'
                        },
                        forest: {
                            DEFAULT: '#133e2b',
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#133e2b',
                            950: '#0b261a',
                            card: '#184a34',
                            hover: '#1e543b',
                            border: '#205c41'
                        },
                        lime: {
                            DEFAULT: '#c6f16d',
                            accent: '#c6f16d',
                            hover: '#b5e759'
                        },
                        surface: '#f4f6f4'
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                }
            }
        }
    </script>

    <style>
        :root {
            --brand-forest: #133e2b;
            --brand-accent: #c6f16d;
            --brand-surface: #f4f6f4;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f6f4;
            color: #131e18;
            min-height: 100vh;
        }
        .card {
            background: #ffffff;
            border: 1px solid #e2e8e0;
            border-radius: 1.25rem;
        }
        .card-hover {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -8px rgba(19, 62, 43, 0.08);
            border-color: #cbd5c8;
        }
        .glass-card {
            background: #ffffff;
            border: 1px solid #e2e8e0;
            border-radius: 1.25rem;
        }
        .glass-card-hover {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .glass-card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -8px rgba(19, 62, 43, 0.08);
            border-color: #cbd5c8;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #0e2d20; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #236548; border-radius: 9999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #318a63; }
        
        .main-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .main-scrollbar::-webkit-scrollbar-track { background: #f4f6f4; }
        .main-scrollbar::-webkit-scrollbar-thumb { background: #d0d7cf; border-radius: 9999px; }
        .main-scrollbar::-webkit-scrollbar-thumb:hover { background: #aebdb0; }

        @media (min-width: 1024px) {
            #appSidebar.sidebar-collapsed {
                width: 5rem;
            }

            #appSidebar.sidebar-collapsed .sidebar-brand-copy,
            #appSidebar.sidebar-collapsed .sidebar-section-label,
            #appSidebar.sidebar-collapsed .sidebar-link-label,
            #appSidebar.sidebar-collapsed .sidebar-operator-copy {
                display: none;
            }

            #appSidebar.sidebar-collapsed .sidebar-brand-header {
                justify-content: center;
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            #appSidebar.sidebar-collapsed .sidebar-brand-header {
                position: relative;
                overflow: visible;
                justify-content: center;
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
            }

            #appSidebar.sidebar-collapsed .sidebar-brand-link {
                position: relative;
                gap: 0;
            }

            #appSidebar.sidebar-collapsed .sidebar-brand-mark {
                position: relative;
                cursor: pointer;
            }

            #appSidebar.sidebar-collapsed .sidebar-brand-mark:hover,
            #appSidebar.sidebar-collapsed .sidebar-brand-mark:focus-visible {
                background: #b5e759;
                transform: scale(1.06);
            }

            #appSidebar.sidebar-collapsed .sidebar-brand-mark:hover .sidebar-expand-indicator,
            #appSidebar.sidebar-collapsed .sidebar-brand-mark:focus-visible .sidebar-expand-indicator {
                opacity: 1;
                transform: scale(1);
            }

            #appSidebar.sidebar-collapsed .sidebar-collapse-toggle {
                display: none;
            }

            #appSidebar.sidebar-collapsed .sidebar-nav {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            #appSidebar.sidebar-collapsed .sidebar-nav a {
                position: relative;
                justify-content: center;
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            #appSidebar.sidebar-collapsed .sidebar-nav a:hover::after,
            #appSidebar.sidebar-collapsed .sidebar-nav a:focus-visible::after {
                content: attr(data-sidebar-label);
                position: absolute;
                left: calc(100% + 0.75rem);
                top: 50%;
                z-index: 30;
                transform: translateY(-50%);
                width: max-content;
                max-width: 16rem;
                padding: 0.55rem 0.75rem;
                border: 1px solid #205c41;
                border-radius: 0.75rem;
                background: #0f3323;
                color: #fff;
                box-shadow: 0 8px 20px rgb(0 0 0 / 18%);
                font-size: 0.75rem;
                font-weight: 700;
                pointer-events: none;
            }

            #appSidebar.sidebar-collapsed .sidebar-brand-link,
            #appSidebar.sidebar-collapsed .sidebar-logout-button {
                position: relative;
            }

            #appSidebar.sidebar-collapsed .sidebar-brand-tooltip,
            #appSidebar.sidebar-collapsed .sidebar-logout-button::after {
                position: absolute;
                left: calc(100% + 0.75rem);
                top: 50%;
                z-index: 40;
                transform: translateY(-50%);
                width: max-content;
                max-width: 16rem;
                padding: 0.55rem 0.75rem;
                border: 1px solid #205c41;
                border-radius: 0.75rem;
                background: #0f3323;
                color: #fff;
                box-shadow: 0 8px 20px rgb(0 0 0 / 18%);
                font-size: 0.75rem;
                font-weight: 700;
                white-space: nowrap;
                pointer-events: none;
                opacity: 0;
                visibility: hidden;
                transition: opacity 150ms ease, visibility 150ms ease;
            }

            #appSidebar.sidebar-collapsed .sidebar-logout-button::after {
                content: attr(data-sidebar-label);
            }

            #appSidebar.sidebar-collapsed .sidebar-brand-link:hover .sidebar-brand-tooltip,
            #appSidebar.sidebar-collapsed .sidebar-brand-link:focus-visible .sidebar-brand-tooltip,
            #appSidebar.sidebar-collapsed .sidebar-logout-button:hover::after,
            #appSidebar.sidebar-collapsed .sidebar-logout-button:focus-visible::after {
                opacity: 1;
                visibility: visible;
            }

            #appSidebar.sidebar-collapsed .sidebar-operator-panel {
                display: flex;
                justify-content: center;
                padding: 0.75rem 0.25rem;
            }

            #appSidebar.sidebar-collapsed .sidebar-operator-card {
                padding: 0.5rem;
            }

            #appSidebar.sidebar-collapsed .sidebar-user-info {
                display: none;
            }

            #appSidebar.sidebar-collapsed .sidebar-user-row {
                justify-content: center;
                margin-top: 0;
                padding-top: 0;
                border-top: 0;
            }

            #appSidebar.sidebar-collapsed .sidebar-logout-button {
                position: relative;
                width: 2.5rem;
                height: 2.5rem;
                justify-content: center;
                border: 1px solid #20583e;
                border-radius: 0.75rem;
                background: #0f3323;
                color: #fff;
            }

            #appSidebar.sidebar-collapsed .sidebar-logout-button:hover,
            #appSidebar.sidebar-collapsed .sidebar-logout-button:focus-visible {
                border-color: #c6f16d;
                background: #184c35;
                color: #c6f16d;
            }

            #appSidebar.sidebar-collapsed .sidebar-logout-button:hover::after,
            #appSidebar.sidebar-collapsed .sidebar-logout-button:focus-visible::after {
                content: attr(data-sidebar-label);
                position: absolute;
                left: calc(100% + 0.75rem);
                top: 50%;
                z-index: 30;
                transform: translateY(-50%);
                width: max-content;
                max-width: 16rem;
                padding: 0.55rem 0.75rem;
                border: 1px solid #205c41;
                border-radius: 0.75rem;
                background: #0f3323;
                color: #fff;
                box-shadow: 0 8px 20px rgb(0 0 0 / 18%);
                font-size: 0.75rem;
                font-weight: 700;
                pointer-events: none;
            }
        }
    </style>
</head>
<body class="h-full antialiased bg-[#f4f6f4] text-[#131e18]">
    <div class="min-h-screen flex">
        
        @php
            $currentRoute = Route::currentRouteName() ?? '';
            
            // Breadcrumb detection matching previous module names
            $pageTitle = 'Main Hub / Boxes';
            if (str_starts_with($currentRoute, 'factory.orders')) {
                $pageTitle = 'Factory Orders & Parta';
            } elseif (str_starts_with($currentRoute, 'factory.checking')) {
                $pageTitle = 'Checking / Thaan Stock';
            } elseif (str_starts_with($currentRoute, 'factory.reports')) {
                $pageTitle = 'Factory Reports & Wages';
            } elseif (str_starts_with($currentRoute, 'production')) {
                $pageTitle = '6. Production';
            } elseif (str_starts_with($currentRoute, 'sizing')) {
                $pageTitle = '2. Sizing & Beams';
            } elseif (str_starts_with($currentRoute, 'bags')) {
                $pageTitle = '1. Bags (Yarn)';
            } elseif (str_starts_with($currentRoute, 'parts')) {
                $pageTitle = '3. Parts Store';
            } elseif (str_starts_with($currentRoute, 'fabric')) {
                $pageTitle = '4. Fabric Stock';
            } elseif (str_starts_with($currentRoute, 'sales')) {
                $pageTitle = '5. Sales & Slips';
            } elseif (str_starts_with($currentRoute, 'invoices')) {
                $pageTitle = '7. Invoices & Accounts';
            } elseif (str_starts_with($currentRoute, 'ledgers')) {
                $pageTitle = 'Party Ledgers';
            } elseif (str_starts_with($currentRoute, 'expenses')) {
                $pageTitle = '8. Expenses';
            }
        @endphp

        <!-- BACKDROP FOR MOBILE SIDEBAR -->
        <div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm hidden lg:hidden transition-opacity"></div>

        <!-- LEFT SIDEBAR: FIXED HEIGHT (lg:sticky lg:top-0 lg:h-screen lg:max-h-screen), SLIGHTLY DARKER GREEN (#0f3323) -->
        <aside id="appSidebar" class="fixed inset-y-0 left-0 z-50 w-64 lg:sticky lg:top-0 lg:h-screen lg:max-h-screen bg-[#0f3323] text-white flex flex-col justify-between transition-all duration-300 ease-in-out -translate-x-full lg:translate-x-0 shrink-0 select-none border-r border-[#1a4d36] shadow-xl">
            
            <!-- Top Brand Header (Fixed at top of sidebar) -->
            <div class="sidebar-brand-header px-5 py-4 flex items-center justify-between border-b border-[#1a4d36] shrink-0 bg-[#0f3323]">
                <a href="{{ route('dashboard') }}" onclick="if (window.matchMedia('(min-width: 1024px)').matches && document.getElementById('appSidebar').classList.contains('sidebar-collapsed')) { toggleDesktopSidebar(event); }" class="sidebar-brand-link flex items-center gap-3 group" aria-label="Go to dashboard">
                    <span class="sidebar-brand-mark relative w-10 h-10 shrink-0 rounded-xl bg-[#c6f16d] text-[#0f3323] flex items-center justify-center font-black text-sm tracking-tight shadow-md transition-all">
                        LF
                        <span class="sidebar-expand-indicator absolute inset-0 flex items-center justify-center rounded-xl bg-[#c6f16d] opacity-0 scale-75 transition-all" aria-hidden="true">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </span>
                    <div class="sidebar-brand-copy">
                        <div class="text-white font-black text-lg tracking-tight leading-none">loomflow.</div>
                        <span class="block text-[9px] uppercase tracking-widest font-extrabold text-[#c6f16d] mt-1">FACTORY WORKSPACE</span>
                    </div>
                    <span class="sidebar-brand-tooltip">Expand sidebar</span>
                </a>
                <button type="button" onclick="toggleSidebar()" class="lg:hidden p-1.5 rounded-lg text-white hover:bg-[#1a4d36]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <button type="button" onclick="toggleDesktopSidebar()" class="sidebar-collapse-toggle hidden lg:flex items-center justify-center rounded-lg text-white hover:bg-[#1a4d36] transition-colors" aria-label="Collapse sidebar" aria-expanded="true" title="Collapse sidebar" data-sidebar-label="Collapse sidebar">
                    <svg class="sidebar-toggle-icon w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
            </div>

            <!-- Navigation List (Scrolls independently within fixed sidebar height - NO DROPDOWNS) -->
            <nav class="sidebar-nav px-3 py-3 space-y-5 overflow-y-auto custom-scrollbar flex-1">
                
                <!-- Section 1: FACTORY OPERATIONS -->
                <div>
                    <div class="sidebar-section-label px-3 mb-2 text-[11px] font-black uppercase tracking-wider text-white">
                        Factory Operations
                    </div>
                    <div class="space-y-1">
                        <!-- Main Hub / Boxes -->
                        <a href="{{ route('dashboard') }}" data-sidebar-label="Main Hub / Boxes" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ in_array($currentRoute, ['dashboard', 'home']) ? 'bg-[#184c35] text-white shadow-md border-2 border-[#c6f16d] font-bold' : 'text-white hover:bg-[#15422e] hover:text-white border-2 border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ in_array($currentRoute, ['dashboard', 'home']) ? 'text-[#c6f16d]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span class="sidebar-link-label truncate">Main Hub / Boxes</span>
                        </a>

                        <!-- Factory Orders & Parta -->
                        <a href="{{ route('factory.orders') }}" data-sidebar-label="Factory Orders &amp; Parta" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ str_starts_with($currentRoute, 'factory.orders') ? 'bg-[#184c35] text-white shadow-md border-2 border-[#c6f16d] font-bold' : 'text-white hover:bg-[#15422e] hover:text-white border-2 border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ str_starts_with($currentRoute, 'factory.orders') ? 'text-[#c6f16d]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="sidebar-link-label truncate">Factory Orders &amp; Parta</span>
                        </a>

                        <!-- Checking / Thaan Stock -->
                        <a href="{{ route('factory.checking') }}" data-sidebar-label="Checking / Thaan Stock" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ str_starts_with($currentRoute, 'factory.checking') ? 'bg-[#184c35] text-white shadow-md border-2 border-[#c6f16d] font-bold' : 'text-white hover:bg-[#15422e] hover:text-white border-2 border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ str_starts_with($currentRoute, 'factory.checking') ? 'text-[#c6f16d]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span class="sidebar-link-label truncate">Checking / Thaan Stock</span>
                        </a>

                        <!-- Sales Contracts -->
                        <a href="{{ route('sales.contracts') }}" data-sidebar-label="Sales Contracts" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ str_starts_with($currentRoute, 'sales.contracts') ? 'bg-[#184c35] text-white shadow-md border-2 border-[#c6f16d] font-bold' : 'text-white hover:bg-[#15422e] hover:text-white border-2 border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ str_starts_with($currentRoute, 'sales.contracts') ? 'text-[#c6f16d]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a2 2 0 01.707.293l4.414 4.414A2 2 0 0120 9.121V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                            </svg>
                            <span class="sidebar-link-label truncate">Sales Contracts</span>
                        </a>

                        <!-- 6. Production -->
                        <a href="{{ route('production.index') }}" data-sidebar-label="6. Production" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ str_starts_with($currentRoute, 'production') ? 'bg-[#184c35] text-white shadow-md border-2 border-[#c6f16d] font-bold' : 'text-white hover:bg-[#15422e] hover:text-white border-2 border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ str_starts_with($currentRoute, 'production') ? 'text-[#c6f16d]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span class="sidebar-link-label truncate">6. Production</span>
                        </a>

                        <!-- Factory Reports & Wages -->
                        <a href="{{ route('factory.reports') }}" data-sidebar-label="Factory Reports &amp; Wages" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ str_starts_with($currentRoute, 'factory.reports') ? 'bg-[#184c35] text-white shadow-md border-2 border-[#c6f16d] font-bold' : 'text-white hover:bg-[#15422e] hover:text-white border-2 border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ str_starts_with($currentRoute, 'factory.reports') ? 'text-[#c6f16d]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span class="sidebar-link-label truncate">Factory Reports &amp; Wages</span>
                        </a>
                    </div>
                </div>

                <!-- Section 2: STORE & ACCOUNTS -->
                <div>
                    <div class="sidebar-section-label px-3 mb-2 text-[11px] font-black uppercase tracking-wider text-white">
                        Store &amp; Accounts
                    </div>
                    <div class="space-y-1">
                        <!-- 1. Bags (Yarn) -->
                        <a href="{{ route('bags.index') }}" data-sidebar-label="1. Bags (Yarn)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ str_starts_with($currentRoute, 'bags') ? 'bg-[#184c35] text-white shadow-md border-2 border-[#c6f16d] font-bold' : 'text-white hover:bg-[#15422e] hover:text-white border-2 border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ str_starts_with($currentRoute, 'bags') ? 'text-[#c6f16d]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span class="sidebar-link-label truncate">1. Bags (Yarn)</span>
                        </a>

                        <!-- 2. Sizing & Beams -->
                        <a href="{{ route('sizing.index') }}" data-sidebar-label="2. Sizing &amp; Beams" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ str_starts_with($currentRoute, 'sizing') ? 'bg-[#184c35] text-white shadow-md border-2 border-[#c6f16d] font-bold' : 'text-white hover:bg-[#15422e] hover:text-white border-2 border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ str_starts_with($currentRoute, 'sizing') ? 'text-[#c6f16d]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span class="sidebar-link-label truncate">2. Sizing &amp; Beams</span>
                        </a>

                        <!-- 3. Parts Store -->
                        <a href="{{ route('parts.index') }}" data-sidebar-label="3. Parts Store" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ str_starts_with($currentRoute, 'parts') ? 'bg-[#184c35] text-white shadow-md border-2 border-[#c6f16d] font-bold' : 'text-white hover:bg-[#15422e] hover:text-white border-2 border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ str_starts_with($currentRoute, 'parts') ? 'text-[#c6f16d]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            </svg>
                            <span class="sidebar-link-label truncate">3. Parts Store</span>
                        </a>

                        <!-- 4. Fabric Stock -->
                        <a href="{{ route('fabric.index') }}" data-sidebar-label="4. Fabric Stock" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ str_starts_with($currentRoute, 'fabric') ? 'bg-[#184c35] text-white shadow-md border-2 border-[#c6f16d] font-bold' : 'text-white hover:bg-[#15422e] hover:text-white border-2 border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ str_starts_with($currentRoute, 'fabric') ? 'text-[#c6f16d]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                            <span class="sidebar-link-label truncate">4. Fabric Stock</span>
                        </a>

                        <!-- 5. Sales & Slips -->
                        <a href="{{ route('sales.index') }}" data-sidebar-label="5. Sales &amp; Slips" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ str_starts_with($currentRoute, 'sales') ? 'bg-[#184c35] text-white shadow-md border-2 border-[#c6f16d] font-bold' : 'text-white hover:bg-[#15422e] hover:text-white border-2 border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ str_starts_with($currentRoute, 'sales') ? 'text-[#c6f16d]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="sidebar-link-label truncate">5. Sales &amp; Slips</span>
                        </a>

                        <!-- 7. Invoices & Accounts -->
                        <a href="{{ route('invoices.index') }}" data-sidebar-label="7. Invoices &amp; Accounts" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ str_starts_with($currentRoute, 'invoices') ? 'bg-[#184c35] text-white shadow-md border-2 border-[#c6f16d] font-bold' : 'text-white hover:bg-[#15422e] hover:text-white border-2 border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ str_starts_with($currentRoute, 'invoices') ? 'text-[#c6f16d]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="sidebar-link-label truncate">7. Invoices &amp; Accounts</span>
                        </a>

                        <!-- 8. Expenses -->
                        <a href="{{ route('expenses.index') }}" data-sidebar-label="8. Expenses" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ str_starts_with($currentRoute, 'expenses') ? 'bg-[#184c35] text-white shadow-md border-2 border-[#c6f16d] font-bold' : 'text-white hover:bg-[#15422e] hover:text-white border-2 border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ str_starts_with($currentRoute, 'expenses') ? 'text-[#c6f16d]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            <span class="sidebar-link-label truncate">8. Expenses</span>
                        </a>

                        <!-- Party Ledgers -->
                        <a href="{{ route('ledgers.index') }}" data-sidebar-label="Party Ledgers" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ str_starts_with($currentRoute, 'ledgers') ? 'bg-[#184c35] text-white shadow-md border-2 border-[#c6f16d] font-bold' : 'text-white hover:bg-[#15422e] hover:text-white border-2 border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ str_starts_with($currentRoute, 'ledgers') ? 'text-[#c6f16d]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="sidebar-link-label truncate">Party Ledgers</span>
                        </a>
                    </div>
                </div>

            </nav>

            <!-- Bottom Operator & User Panel (Fixed at bottom of sidebar) -->
            <div class="sidebar-operator-panel p-3 shrink-0 border-t border-[#1a4d36] bg-[#0f3323]">
                <div class="sidebar-operator-card p-3 rounded-2xl bg-[#143d2b] border border-[#20583e] text-xs">
                    <div class="sidebar-operator-copy">
                    <div class="flex items-center gap-2 font-bold text-white">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#c6f16d]"></span>
                        </span>
                        <span>Single operator</span>
                    </div>
                    <p class="text-[10px] text-white/80 mt-0.5">Grey fabric weaving only</p>

                    @auth
                        <div class="sidebar-user-row flex items-center justify-between mt-2.5 pt-2.5 border-t border-[#20583e]">
                            <div class="sidebar-user-info truncate mr-2">
                                <div class="text-xs font-black text-white truncate">{{ Auth::user()->name }}</div>
                                <div class="text-[9px] text-[#c6f16d] uppercase tracking-wider font-extrabold">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</div>
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="sidebar-logout-button relative flex items-center gap-2 p-1.5 rounded-lg text-white hover:text-[#c6f16d] hover:bg-[#184c35] transition-colors" title="Sign out" aria-label="Sign out" data-sidebar-label="Sign out · {{ Auth::user()->name }}">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span class="sidebar-user-info">Sign out</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mt-2.5 pt-2.5 border-t border-[#20583e]">
                            <a href="{{ route('login') }}" class="block text-center py-1.5 px-3 rounded-lg bg-[#c6f16d] text-[#0f3323] font-black text-xs hover:bg-[#b5e759] transition-all">Sign In</a>
                        </div>
                    @endauth
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN VIEWPORT CONTAINER (Scrolls independently) -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen">
            
            <!-- TOP BAR HEADER -->
            <header class="sticky top-0 z-30 bg-[#f4f6f4]/95 backdrop-blur-md border-b border-[#e2e8e0] px-4 sm:px-8 py-3.5">
                <div class="flex items-center justify-between gap-4">
                    
                    <!-- Left: Mobile toggle & Breadcrumb -->
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="toggleSidebar()" class="lg:hidden p-2 rounded-xl bg-white border border-[#e2e8e0] text-[#133e2b] hover:bg-stone-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        
                        <div class="flex items-center gap-2 text-xs">
                            <span class="text-stone-500 font-medium">Workspace</span>
                            <span class="text-stone-300">/</span>
                            <span class="font-bold text-[#133e2b]">{{ $pageTitle }}</span>
                        </div>
                    </div>

                    <!-- Right: Search, Concept Pill, Avatar -->
                    <div class="flex items-center gap-3">
                        
                        <!-- Search Bar -->
                        <div class="relative hidden sm:block">
                            <svg class="w-3.5 h-3.5 text-stone-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" placeholder="Search LoomFlow..." class="pl-8 pr-3 py-1.5 text-xs bg-white border border-[#e2e8e0] rounded-full focus:outline-none focus:ring-2 focus:ring-[#133e2b]/15 focus:border-[#133e2b] text-[#131e18] placeholder-stone-400 w-48 md:w-60 shadow-2xs transition-all">
                        </div>

                        <!-- Mode Pill -->
                        <span class="hidden md:inline-flex px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-stone-600 bg-white border border-[#e2e8e0] rounded-full shadow-2xs">
                            Interactive Concept
                        </span>

                        <!-- Operator Avatar -->
                        @auth
                            <div class="w-8 h-8 rounded-full bg-[#c6f16d] text-[#133e2b] font-black text-xs flex items-center justify-center shadow-2xs border border-[#b4e857]" title="{{ Auth::user()->name }}">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-full bg-[#133e2b] text-white text-xs font-bold">Login</a>
                        @endauth
                    </div>

                </div>
            </header>

            <!-- FLASH NOTIFICATIONS -->
            <div class="px-4 sm:px-8 mt-4">
                @if(session('success'))
                    <div class="p-4 rounded-2xl bg-[#133e2b] text-white border border-[#205c41] flex items-start gap-3 shadow-sm">
                        <div class="w-6 h-6 rounded-full bg-[#c6f16d] text-[#133e2b] flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">✓</div>
                        <div class="text-sm font-medium">{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-3 shadow-sm">
                        <svg class="w-5 h-5 text-rose-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm font-medium">{{ session('error') }}</div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3 shadow-sm">
                        <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm font-medium">{{ session('info') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-sm">
                        <div class="flex items-center gap-2 font-bold text-sm mb-1">
                            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
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

            <!-- MAIN BODY CONTENT -->
            <main class="flex-1 px-4 sm:px-8 py-6">
                @yield('content')
            </main>

            <!-- SUBTLE BOTTOM NOTE -->
            <footer class="px-4 sm:px-8 py-6 text-center text-xs text-stone-400 border-t border-[#e2e8e0]">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                    <div>
                        <strong class="text-stone-700">LoomFlow</strong> &copy; {{ date('Y') }} &middot; Grey Fabric Weaving Mill Operations
                    </div>
                    <div class="text-[11px] text-stone-500">
                        INTERACTIVE UI PROTOTYPE &middot; Production &amp; Weaving Engine
                    </div>
                </div>
            </footer>

        </div>

    </div>

    <!-- Scripts and Modal Helpers -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) {
                sidebar.classList.toggle('-translate-x-full');
            }
            if (backdrop) {
                backdrop.classList.toggle('hidden');
            }
        }

        function toggleDesktopSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const toggle = document.querySelector('.sidebar-collapse-toggle');
            const brandLink = document.querySelector('.sidebar-brand-link');
            const icon = document.querySelector('.sidebar-toggle-icon');

            if (!sidebar) {
                return;
            }

            if (event) {
                event.preventDefault();
            }

            const isCollapsed = sidebar.classList.toggle('sidebar-collapsed');
            localStorage.setItem('loomflow-sidebar-collapsed', isCollapsed ? 'true' : 'false');

            if (toggle) {
                toggle.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
                toggle.setAttribute('aria-label', isCollapsed ? 'Expand sidebar' : 'Collapse sidebar');
                toggle.setAttribute('title', isCollapsed ? 'Expand sidebar' : 'Collapse sidebar');
                toggle.setAttribute('data-sidebar-label', isCollapsed ? 'Expand sidebar' : 'Collapse sidebar');
            }

            if (brandLink) {
                brandLink.setAttribute('aria-label', isCollapsed ? 'Expand sidebar' : 'Go to dashboard');
            }

            if (icon) {
                icon.classList.toggle('rotate-180', isCollapsed);
            }

            if (brandLink) {
                brandLink.setAttribute('aria-label', isCollapsed ? 'Expand sidebar' : 'Go to dashboard');
            }
        }

        if (window.matchMedia('(min-width: 1024px)').matches && localStorage.getItem('loomflow-sidebar-collapsed') === 'true') {
            const sidebar = document.getElementById('appSidebar');
            const toggle = document.querySelector('.sidebar-collapse-toggle');
            const brandLink = document.querySelector('.sidebar-brand-link');
            const icon = document.querySelector('.sidebar-toggle-icon');

            if (sidebar) {
                sidebar.classList.add('sidebar-collapsed');
            }

            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Expand sidebar');
                toggle.setAttribute('title', 'Expand sidebar');
                toggle.setAttribute('data-sidebar-label', 'Expand sidebar');
            }

            if (icon) {
                icon.classList.add('rotate-180');
            }

            if (brandLink) {
                brandLink.setAttribute('aria-label', 'Expand sidebar');
            }
        }

        // Modal Helpers for child views
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
