<div id="modalYarnQualities" class="fixed inset-0 z-[60] hidden items-center justify-center bg-zinc-950/50 p-4 backdrop-blur-sm">
    <div class="w-full max-w-2xl rounded-3xl border border-zinc-200 bg-white p-6 shadow-2xl">
        <div class="flex items-start justify-between border-b border-zinc-100 pb-4">
            <div>
                <h2 class="text-lg font-extrabold text-zinc-900">Yarn Quality List</h2>
                <p class="mt-1 text-xs text-zinc-500">Add a yarn quality with its fabric construction and stored weight calculation for sizing predictions.</p>
            </div>
            <button type="button" onclick="closeModal('modalYarnQualities')" class="rounded-lg p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-900" aria-label="Close yarn quality list">✕</button>
        </div>

        <div class="mt-4 max-h-40 overflow-y-auto rounded-xl border border-zinc-100 bg-zinc-50 p-3">
            @forelse($yarnQualities as $yarnQuality)
                <span class="mb-1 mr-1 inline-flex rounded-lg border border-zinc-200 bg-white px-2.5 py-1 text-xs font-semibold text-zinc-700">{{ $yarnQuality->name }}</span>
            @empty
                <span class="text-xs text-zinc-400">No yarn qualities yet.</span>
            @endforelse
        </div>

        <form action="{{ route('yarn-qualities.store') }}" method="POST" class="mt-4 space-y-3">
            @csrf
            <div>
                <label for="newYarnQualityName" class="mb-1 block text-xs font-semibold text-zinc-600">New Yarn Quality *</label>
                <input id="newYarnQualityName" type="text" name="name" required maxlength="100" value="{{ old('name') }}" placeholder="e.g. 30/1 PC Combed" class="w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                @error('name')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <label class="text-xs font-semibold text-zinc-600">Construction<input type="text" name="construction" maxlength="150" value="{{ old('construction') }}" placeholder="e.g. 40x40 / 133x72" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2"></label>
                <label class="text-xs font-semibold text-zinc-600">Width (inches)<input type="number" name="width_inches" required min="20" max="150" step="0.01" value="{{ old('width_inches', 63) }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2"></label>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <label class="text-xs font-semibold text-zinc-600">Read (ends per inch)<input type="number" name="read" required min="0.0001" step="0.0001" value="{{ old('read') }}" placeholder="60" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2"></label>
                <label class="text-xs font-semibold text-zinc-600">Pick (picks per inch)<input type="number" name="pick" required min="0.0001" step="0.0001" value="{{ old('pick') }}" placeholder="66" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2"></label>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <label class="text-xs font-semibold text-zinc-600">Warp yarn count<input type="number" name="warp_count" required min="0.0001" step="0.0001" value="{{ old('warp_count') }}" placeholder="58.5" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2"></label>
                <label class="text-xs font-semibold text-zinc-600">Weft yarn count<input type="number" name="weft_count" required min="0.0001" step="0.0001" value="{{ old('weft_count') }}" placeholder="58.5" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2"></label>
                <label class="text-xs font-semibold text-zinc-600">Rate per meter<input type="number" name="unit_cost_meter" required min="0" step="0.01" value="{{ old('unit_cost_meter', 0) }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2"></label>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-3 text-[10px] text-zinc-500">
                <strong class="block text-zinc-700">Stored weight calculation</strong>
                <span>Warp lb/m = TRUNC(Read × Width ÷ Warp Count ÷ 20 × 1.0936 ÷ 40, 4)</span><br>
                <span>Weft lb/m = TRUNC(Pick × Width ÷ Weft Count ÷ 20 × 1.0936 ÷ 40, 4)</span>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-3">
                <button type="button" onclick="closeModal('modalYarnQualities')" class="rounded-xl bg-zinc-100 px-4 py-2 text-xs font-semibold text-zinc-600">Close</button>
                <button type="submit" class="rounded-xl bg-zinc-900 px-5 py-2 text-xs font-bold text-white hover:bg-zinc-700">Add Yarn Quality</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const createdYarnQuality = @js(session('createdYarnQuality'));
    if (createdYarnQuality) {
        document.querySelectorAll('[data-yarn-quality-select]').forEach((select) => {
            select.value = createdYarnQuality;
        });
        document.getElementById('inpYarnQuality')?.dispatchEvent(new Event('change'));
    }
    @if($errors->hasAny(['name', 'construction', 'read', 'pick', 'warp_count', 'weft_count', 'width_inches', 'unit_cost_meter']))
        openModal('modalYarnQualities');
    @endif
</script>
@endpush
