<div class="flex flex-wrap items-center gap-2 mb-6">
    <a href="{{ route('factory.orders') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('factory.orders') ? 'bg-[#133e2b] text-white shadow-2xs' : 'bg-white border border-[#e2e8e0] text-stone-700 hover:bg-stone-50 hover:border-stone-300' }}">
        1. Orders &middot; Parta &middot; Beams
    </a>
    <a href="{{ route('production.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('production.*') ? 'bg-[#133e2b] text-white shadow-2xs' : 'bg-white border border-[#e2e8e0] text-stone-700 hover:bg-stone-50 hover:border-stone-300' }}">
        2. Loom Meter Readings
    </a>
    <a href="{{ route('factory.checking') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('factory.checking') ? 'bg-[#133e2b] text-white shadow-2xs' : 'bg-white border border-[#e2e8e0] text-stone-700 hover:bg-stone-50 hover:border-stone-300' }}">
        3. Checking &amp; Thaans
    </a>
    <a href="{{ route('factory.parchis') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('factory.parchis') ? 'bg-[#133e2b] text-white shadow-2xs' : 'bg-white border border-[#e2e8e0] text-stone-700 hover:bg-stone-50 hover:border-stone-300' }}">
        4. Kachi &middot; Pakki &middot; Returns
    </a>
    <a href="{{ route('factory.reports') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('factory.reports') ? 'bg-[#133e2b] text-white shadow-2xs' : 'bg-white border border-[#e2e8e0] text-stone-700 hover:bg-stone-50 hover:border-stone-300' }}">
        Reports &middot; Mazdoori &middot; Stoppages
    </a>
    <a href="{{ route('invoices.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('invoices.*') ? 'bg-[#133e2b] text-white shadow-2xs' : 'bg-white border border-[#e2e8e0] text-stone-700 hover:bg-stone-50 hover:border-stone-300' }}">
        5. Invoices &amp; Accounts
    </a>
</div>

<div class="mb-5 flex flex-wrap items-center justify-between gap-2 text-xs text-stone-500 bg-white/60 border border-[#e2e8e0] px-4 py-2.5 rounded-xl">
    <div class="flex items-center gap-2">
        <span class="h-2 w-2 rounded-full bg-[#15803d]"></span>
        <span><strong>Grey Fabric Flow:</strong> Single operator mill &middot; Records preserved with original order IDs</span>
    </div>
    <span class="text-[11px] text-stone-400">All calculations in Pakistani Rupees &amp; Metres</span>
</div>
