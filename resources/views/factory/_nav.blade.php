<div class="flex flex-wrap gap-2 mb-5">
    <a href="{{ route('factory.orders') }}" class="px-4 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('factory.orders') ? 'bg-zinc-900 text-white' : 'bg-zinc-200 text-zinc-900' }}">1. Orders · Parta · Beams</a>
    <a href="{{ route('production.index') }}" class="px-4 py-2 rounded-lg text-sm font-bold bg-zinc-200 text-zinc-900">2. Loom Meter Readings</a>
    <a href="{{ route('factory.checking') }}" class="px-4 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('factory.checking') ? 'bg-zinc-900 text-white' : 'bg-zinc-200 text-zinc-900' }}">3. Checking & Thaans</a>
    <a href="{{ route('factory.parchis') }}" class="px-4 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('factory.parchis') ? 'bg-zinc-900 text-white' : 'bg-zinc-200 text-zinc-900' }}">4. Kachi · Pakki · Returns</a>
    <a href="{{ route('factory.reports') }}" class="px-4 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('factory.reports') ? 'bg-zinc-900 text-white' : 'bg-zinc-200 text-zinc-900' }}">Reports · Mazdoori · Maintenance</a>
    <a href="{{ route('invoices.index') }}" class="px-4 py-2 rounded-lg text-sm font-bold bg-zinc-200 text-zinc-900">5. Invoices & Accounts</a>
</div>
@if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 p-4 mb-5"><strong>Please correct:</strong><ul class="list-disc pl-5 mt-2 text-sm">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<p class="mb-5 text-xs text-zinc-500">Grey fabric only · Single operator · No tax added by default · Records are kept against their original order.</p>
