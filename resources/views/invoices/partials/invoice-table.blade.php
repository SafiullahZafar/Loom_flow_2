<div class="overflow-x-auto">
    <table class="w-full min-w-[850px] text-left text-xs">
        <thead class="border-b border-zinc-100 bg-white text-[10px] font-semibold uppercase tracking-wider text-zinc-500">
            <tr><th class="px-4 py-3">Invoice / Date</th><th class="px-4 py-3">Customer</th>@unless($compact)<th class="px-4 py-3">Sales / Contract Link</th>@endunless<th class="px-4 py-3 text-right">Total</th><th class="px-4 py-3 text-right">Received</th><th class="px-4 py-3 text-right">Balance Due</th><th class="px-4 py-3">Payment Status</th></tr>
        </thead>
        <tbody class="divide-y divide-zinc-100">
            @forelse($rows as $invoice)
                @php
                    $paid = min((float) $invoice->total, (float) ($invoice->allocations_sum_amount ?? 0));
                    $due = max(0, (float) $invoice->total - $paid);
                    $paymentStatus = $due <= 0 ? 'Paid' : ($paid > 0 ? 'Partially paid' : ($invoice->due_date && $invoice->due_date->lt(today()) ? 'Overdue' : 'Unpaid'));
                @endphp
                <tr class="hover:bg-zinc-50">
                    <td class="px-4 py-3 font-mono font-bold text-zinc-900">{{ $invoice->invoice_no }}<span class="block text-[10px] font-sans font-normal text-zinc-400">{{ $invoice->invoice_date->format('d M Y') }}{{ $invoice->due_date ? ' · due '.$invoice->due_date->format('d M Y') : '' }}</span><span class="block max-w-56 truncate text-[10px] font-sans font-normal text-zinc-500">{{ $invoice->items->pluck('description')->implode(', ') }}</span></td>
                    <td class="px-4 py-3 font-semibold text-zinc-700">{{ $invoice->customer->name }}</td>
                    @unless($compact)<td class="px-4 py-3 text-zinc-600">@if($invoice->sale)<span>Sale {{ $invoice->sale->slip_no }}</span>@endif @if($invoice->contract)<span class="block text-[10px] text-zinc-400">Contract {{ $invoice->contract->contract_no }}</span>@elseif(!$invoice->sale)<span class="text-zinc-400">Manual invoice</span>@endif</td>@endunless
                    <td class="px-4 py-3 text-right font-mono font-semibold">Rs. {{ number_format((float) $invoice->total, 2) }}</td>
                    <td class="px-4 py-3 text-right font-mono text-emerald-700">Rs. {{ number_format($paid, 2) }}</td>
                    <td class="px-4 py-3 text-right font-mono font-bold {{ $due > 0 ? 'text-amber-700' : 'text-zinc-500' }}">Rs. {{ number_format($due, 2) }}</td>
                    <td class="px-4 py-3"><span class="rounded-full px-2 py-1 text-[10px] font-bold {{ $paymentStatus === 'Paid' ? 'bg-emerald-50 text-emerald-700' : ($paymentStatus === 'Overdue' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-800') }}">{{ $paymentStatus }}</span></td>
                </tr>
            @empty
                <tr><td colspan="{{ $compact ? 6 : 7 }}" class="px-5 py-10 text-center text-zinc-400">No invoices recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
