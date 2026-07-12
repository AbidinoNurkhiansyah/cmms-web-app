{{-- Filter & Machine Context Panel --}}

{{-- Row 1: Filter Dropdowns (2 kolom bersih) --}}
<div class="grid grid-cols-2 gap-4 mb-4">
    <x-select
        id="filter_line"
        label="Line Name"
        wire:model.live="line_name"
        :options="$lines"
        option-value="line_name"
        option-label="line_name"
        placeholder="— Pilih Line —"
        icon="o-building-office-2" />

    <x-select
        id="filter_machine"
        label="Mesin / Asset"
        wire:model.live="asset_no"
        :options="$machines"
        option-value="asset_no"
        option-label="machine_name"
        placeholder="— Pilih Mesin —"
        icon="o-cog-6-tooth" />
</div>

{{-- Row 2: Context Bar (Machine Info + Document Status) — muncul setelah mesin dipilih --}}
@if($asset_no && $selectedAsset)
<div class="rounded-xl border border-base-300 bg-base-100 mb-5 flex flex-col md:flex-row items-stretch divide-y md:divide-y-0 md:divide-x divide-base-300">

    {{-- Machine Info --}}
    <div class="flex items-center gap-4 px-5 py-4 flex-1 min-w-0">
        @if($selectedAsset->machine_photo)
            <img src="{{ Storage::url($selectedAsset->machine_photo) }}"
                 alt="{{ $selectedAsset->machine_name }}"
                 class="w-12 h-12 rounded-lg object-cover border border-base-300 shrink-0" />
        @else
            <div class="w-12 h-12 rounded-lg bg-base-200 border border-base-300 flex items-center justify-center shrink-0">
                <x-icon name="o-cpu-chip" class="w-6 h-6 text-base-content/30" />
            </div>
        @endif
        <div class="min-w-0">
            <div class="font-bold text-sm truncate">{{ $selectedAsset->machine_name }}</div>
            <div class="flex flex-wrap gap-x-3 gap-y-0.5 mt-0.5">
                <span class="text-xs text-base-content/50">
                    <span class="font-medium text-base-content/70">Asset:</span> {{ $selectedAsset->asset_no }}
                </span>
                <span class="text-xs text-base-content/50">
                    <span class="font-medium text-base-content/70">Line:</span> {{ $selectedAsset->line_name }}
                </span>
                @if($selectedAsset->maker)
                    <span class="text-xs text-base-content/50">
                        <span class="font-medium text-base-content/70">Maker:</span> {{ $selectedAsset->maker }}
                    </span>
                @endif
            </div>
        </div>
        @if($selectedAsset->machine_rank)
            <x-badge value="Rank {{ $selectedAsset->machine_rank }}" class="{{
                match($selectedAsset->machine_rank) {
                    'A' => 'badge-error text-white',
                    'B' => 'badge-warning',
                    'C' => 'badge-info text-white',
                    'D' => 'badge-success text-white',
                    default => 'badge-ghost'
                }
            }} ml-auto shrink-0" />
        @endif
    </div>

    {{-- Document Status --}}
    <div class="flex items-center gap-3 px-5 py-4 md:w-72 shrink-0">
        <div class="flex-1 min-w-0">
            <div class="text-xs font-semibold uppercase tracking-widest text-base-content/40 mb-1.5">
                Dokumen Aktif
            </div>
            @if($currentDoc)
                <div class="flex items-baseline gap-2 flex-wrap">
                    <span class="font-bold text-base font-mono text-primary">{{ $currentDoc->doc_no }}</span>
                    @if($currentDoc->item_revisi)
                        <x-badge value="{{ $currentDoc->item_revisi }}" class="badge-outline badge-sm" />
                    @endif
                </div>
                <div class="text-xs text-base-content/40 mt-0.5">
                    {{ $currentDoc->tanggal_revisi?->format('d M Y') }}
                </div>
            @else
                <span class="text-sm text-base-content/30 italic">Belum ada dokumen</span>
            @endif
        </div>
        <button
            wire:click="$set('historyModal', true)"
            class="btn btn-ghost btn-sm gap-1.5 shrink-0 {{ $currentDoc ? 'text-primary' : 'text-base-content/30' }}"
            title="Lihat Riwayat Revisi">
            <x-icon name="o-clock" class="w-4 h-4" />
            <span class="text-xs hidden sm:inline">Histori</span>
        </button>
    </div>

</div>
@elseif(!$asset_no)
{{-- Hint saat belum pilih mesin --}}
<div class="rounded-xl border border-dashed border-base-300 px-4 py-3 mb-5 flex items-center gap-2 text-base-content/40">
    <x-icon name="o-cursor-arrow-rays" class="w-4 h-4 shrink-0" />
    <span class="text-sm">Pilih <strong class="font-semibold">Line</strong> dan <strong class="font-semibold">Mesin</strong> untuk memuat data checksheet.</span>
</div>
@endif
