@extends('layouts.app')

@section('title', 'Split Factory Bags')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <a href="{{ route('bags.index') }}" class="text-sm font-bold text-zinc-600 hover:text-zinc-900">&larr; Bags</a>
    <div>
        <h1 class="text-2xl font-black text-zinc-900">Split Existing Bags</h1>
        <p class="mt-1 text-sm text-zinc-500">Send bags already in the factory store to sizing. Availability is checked for the selected yarn quality.</p>
    </div>
    @if(session('error'))<div class="rounded-xl bg-red-50 p-3 text-sm font-semibold text-red-700">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="rounded-xl bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
    <form action="{{ route('bags.issue') }}" method="POST" class="space-y-5 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
        @csrf
        <div class="grid gap-4 sm:grid-cols-2">
            <label class="text-xs font-bold text-zinc-600">Yarn quality
                <select name="quality" id="splitQuality" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm">
                    <option value="">Select quality</option>
                    @foreach($qualityBalances as $balance)
                        <option value="{{ $balance['name'] }}" data-stock="{{ $balance['factory_stock'] }}">{{ $balance['name'] }} · {{ number_format((float) $balance['factory_stock'], 2) }} bags in factory</option>
                    @endforeach
                </select>
                <span id="splitStockMessage" class="mt-1 block text-xs text-zinc-500">Select a quality to see available stock.</span>
            </label>
            <label class="text-xs font-bold text-zinc-600">Number of bags to send
                <input type="number" name="quantity_bags" id="splitQuantity" required min="0.01" step="0.01" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm">
            </label>
            <label class="text-xs font-bold text-zinc-600">Cones in each bag
                <input type="number" name="cones_per_bag" required min="1" max="1000" step="1" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm">
            </label>
            <label class="text-xs font-bold text-zinc-600">Split reference
                <input type="text" name="reference_no" required maxlength="100" placeholder="Issue slip / reference" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm">
            </label>
            <label class="text-xs font-bold text-zinc-600">Date
                <input type="date" name="transaction_date" required value="{{ now()->toDateString() }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm">
            </label>
            <label class="text-xs font-bold text-zinc-600">Sizing mill / destination
                <input type="text" name="destination" required maxlength="150" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm">
            </label>
            <label class="text-xs font-bold text-zinc-600">Receiver / party
                <input type="text" name="party_name" required maxlength="150" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm">
            </label>
        </div>
        <label class="block text-xs font-bold text-zinc-600">Notes
            <input type="text" name="notes" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm">
        </label>
        <div class="flex justify-end gap-3 border-t border-zinc-100 pt-4">
            <a href="{{ route('bags.index') }}" class="rounded-xl bg-zinc-100 px-4 py-2 text-xs font-bold text-zinc-600">Cancel</a>
            <button type="submit" id="splitSubmit" disabled class="rounded-xl bg-zinc-900 px-5 py-2 text-xs font-black text-white disabled:cursor-not-allowed disabled:opacity-40">Send to Sizing</button>
        </div>
    </form>
</div>
<script>
    const qualitySelect = document.getElementById('splitQuality');
    const quantityInput = document.getElementById('splitQuantity');
    const stockMessage = document.getElementById('splitStockMessage');
    const splitSubmit = document.getElementById('splitSubmit');
    function updateSplitAvailability() {
        const stock = Number(qualitySelect.selectedOptions[0]?.dataset.stock || 0);
        quantityInput.max = stock;
        stockMessage.textContent = qualitySelect.value ? (stock > 0 ? `${stock.toFixed(2)} bags available for this quality.` : 'No bags of this quality are in factory stock.') : 'Select a quality to see available stock.';
        splitSubmit.disabled = stock <= 0 || Number(quantityInput.value || 0) <= 0 || Number(quantityInput.value) > stock;
    }
    qualitySelect.addEventListener('change', updateSplitAvailability);
    quantityInput.addEventListener('input', updateSplitAvailability);
</script>
@endsection
