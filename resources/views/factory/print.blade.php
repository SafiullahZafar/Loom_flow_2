<!DOCTYPE html><html><head><meta charset="utf-8"><title>{{ $title }}</title><style>body{font-family:Arial,sans-serif;color:#222;margin:32px}h1{font-size:22px}h2{font-size:15px}table{border-collapse:collapse;width:100%;margin-top:16px}th,td{border:1px solid #bbb;padding:8px;font-size:13px;text-align:left}th{background:#eee}p{font-size:13px}.sub{color:#555}footer{margin-top:55px;display:flex;justify-content:space-between}@media print{button{display:none}}</style></head><body>
<h1>LoomFlow — {{ $title }}</h1><p class="sub">Contract: {{ $kachi->order?->order_no }} · Party: {{ $kachi->order?->party_name }} · Quality: {{ $kachi->order?->fabricStock?->quality_name }}</p>
@if(isset($pakki))
<p><strong>Pakki:</strong> {{ $pakki->parchi_no }} · <strong>Received:</strong> {{ $pakki->received_date }} · <strong>Original Kachi:</strong> {{ $kachi->parchi_no }}</p>
<table><tr><th>Dispatched metres</th><th>Accepted Thaans</th><th>Accepted metres</th><th>Rejected metres</th><th>Weaving bill (Rs)</th></tr><tr><td>{{ number_format((float)$kachi->dispatched_meters,2) }}</td><td>{{ $pakki->accepted_thaans }}</td><td>{{ number_format((float)$pakki->accepted_meters,2) }}</td><td>{{ number_format((float)$pakki->rejected_meters,2) }}</td><td>{{ number_format((float)$pakki->bill_amount,2) }}</td></tr></table>
<p class="sub">Bill: accepted metres × quality picks × contract rate per pick. No tax has been added automatically.</p>
@else
<p><strong>Kachi:</strong> {{ $kachi->parchi_no }} · <strong>Dispatch:</strong> {{ $kachi->dispatch_date }} · <strong>Vehicle:</strong> {{ $kachi->vehicle_no ?? '—' }}</p>
<table><thead><tr><th>#</th><th>Thaan Marking</th><th>Loom</th><th>Measured Length (m)</th></tr></thead><tbody>@foreach($kachi->items as $i=>$item)<tr><td>{{ $i+1 }}</td><td>{{ $item->roll?->roll_no }}</td><td>{{ $item->roll?->loom_no }}</td><td>{{ number_format((float)$item->length_meters,2) }}</td></tr>@endforeach</tbody><tfoot><tr><th>Total</th><th>{{ $kachi->thaan_count }} Thaans</th><th></th><th>{{ number_format((float)$kachi->dispatched_meters,2) }} m</th></tr></tfoot></table>
@endif
<footer><p>Prepared by: __________________</p><p>Received by: __________________</p></footer><button onclick="window.print()">Print</button>
</body></html>
