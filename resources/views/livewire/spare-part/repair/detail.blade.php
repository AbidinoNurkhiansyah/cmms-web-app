<?php

use App\Models\SparePartRepair;
use Livewire\Volt\Component;

new class extends Component {
    public SparePartRepair $detailRecord;

    public function mount(int $id)
    {
        $this->detailRecord = SparePartRepair::with(['sparePart', 'pic1', 'pic2', 'pic3'])->findOrFail($id);
    }
};
?>
<div>
    <x-header separator>
        <x-slot:title>
            <div class="flex items-center gap-3">
                <x-button icon="o-arrow-left" link="{{ route('spare-parts.repair.index') }}" class="btn-ghost btn-sm"
                    no-wire />
                <span>Detail Repair: <span
                        class="text-primary">{{ $detailRecord->sparePart->part_number ?? '-' }}</span></span>
            </div>
        </x-slot:title>
        <x-slot:subtitle>
            <div class="ml-12 mt-1">
                {{ $detailRecord->sparePart->part_name ?? '-' }}
            </div>
        </x-slot:subtitle>
    </x-header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6">
        <!-- Left Column: Images (col-span-5) -->
        <div class="lg:col-span-5 flex flex-col gap-6">
            <div class="bg-base-200/50 rounded-xl p-4 border border-base-200 shadow-sm">
                <h3 class="text-sm font-semibold text-base-content/80 uppercase tracking-wider mb-3">Picture Before</h3>
                <div
                    class="h-44 w-full bg-base-300 rounded-lg flex items-center justify-center overflow-hidden border border-base-200 relative group">
                    @if($detailRecord->file_before)
                        <img src="{{ Storage::url($detailRecord->file_before) }}" alt="Before"
                            class="h-full w-full object-contain group-hover:scale-105 transition-transform duration-300" />
                    @else
                        <div class="text-center opacity-50">
                            <x-icon name="o-photo" class="w-12 h-12 mx-auto mb-2" />
                            <span class="text-sm">No Image</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-base-200/50 rounded-xl p-4 border border-base-200 shadow-sm">
                <h3 class="text-sm font-semibold text-base-content/80 uppercase tracking-wider mb-3">Picture After</h3>
                <div
                    class="h-44 w-full bg-base-300 rounded-lg flex items-center justify-center overflow-hidden border border-base-200 relative group">
                    @if($detailRecord->file_after)
                        <img src="{{ Storage::url($detailRecord->file_after) }}" alt="After"
                            class="h-full w-full object-contain group-hover:scale-105 transition-transform duration-300" />
                    @else
                        <div class="text-center opacity-50">
                            <x-icon name="o-photo" class="w-12 h-12 mx-auto mb-2" />
                            <span class="text-sm">No Image</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Details (col-span-7) -->
        <div
            class="lg:col-span-7 bg-base-100 rounded-xl p-6 border border-base-200 shadow-sm h-full flex flex-col justify-between gap-4">

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <span
                        class="block text-[10px] font-semibold text-base-content/60 uppercase tracking-wider mb-1">Date</span>
                    <div class="text-sm font-medium text-base-content">
                        {{ $detailRecord->date ? $detailRecord->date->format('d M Y') : '-' }}
                    </div>
                </div>

                <div>
                    <span
                        class="block text-[10px] font-semibold text-base-content/60 uppercase tracking-wider mb-1">Rack</span>
                    <div class="text-sm font-medium text-base-content">
                        {{ $detailRecord->rack ?: '-' }}
                    </div>
                </div>

                <div>
                    <span
                        class="block text-[10px] font-semibold text-base-content/60 uppercase tracking-wider mb-1">Qty</span>
                    <div class="text-sm font-medium text-base-content">
                        {{ $detailRecord->qty }}
                    </div>
                </div>

                <div>
                    <span
                        class="block text-[10px] font-semibold text-base-content/60 uppercase tracking-wider mb-1">Price</span>
                    <div class="text-sm font-medium text-success">
                        {{ $detailRecord->sparePart ? 'Rp ' . number_format($detailRecord->sparePart->price_idr, 0, ',', '.') : '-' }}
                    </div>
                </div>
            </div>

            <div class="divider my-0"></div>

            <div>
                <span class="block text-[10px] font-semibold text-base-content/60 uppercase tracking-wider mb-2">Person
                    In Charge (PIC)</span>
                <div class="flex flex-wrap gap-1.5">
                    @if($detailRecord->pic1)
                        <div class="badge badge-primary badge-outline badge-sm gap-1 py-1.5 px-2">
                            <x-icon name="o-user" class="w-3 h-3" />
                            {{ $detailRecord->pic1->name }}
                        </div>
                    @endif
                    @if($detailRecord->pic2)
                        <div class="badge badge-primary badge-outline badge-sm gap-1 py-1.5 px-2">
                            <x-icon name="o-user" class="w-3 h-3" />
                            {{ $detailRecord->pic2->name }}
                        </div>
                    @endif
                    @if($detailRecord->pic3)
                        <div class="badge badge-primary badge-outline badge-sm gap-1 py-1.5 px-2">
                            <x-icon name="o-user" class="w-3 h-3" />
                            {{ $detailRecord->pic3->name }}
                        </div>
                    @endif
                    @if(!$detailRecord->pic1 && !$detailRecord->pic2 && !$detailRecord->pic3)
                        <span class="text-xs text-base-content/60">-</span>
                    @endif
                </div>
            </div>

            <div class="divider my-0"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col">
                    <span class="block text-xs font-semibold text-base-content/60 uppercase tracking-wider mb-2">Item
                        Repair</span>
                    <div
                        class="bg-base-200/50 p-3 rounded-lg border border-base-200 text-sm whitespace-pre-wrap h-full">
                        {{ $detailRecord->item_repair ?: '-' }}
                    </div>
                </div>

                <div class="flex flex-col">
                    <span class="block text-xs font-semibold text-base-content/60 uppercase tracking-wider mb-2">Part
                        Usage</span>
                    <div
                        class="bg-base-200/50 p-3 rounded-lg border border-base-200 text-sm whitespace-pre-wrap h-full">
                        {{ $detailRecord->part_usage ?: '-' }}
                    </div>
                </div>
            </div>

            <div class="flex flex-col">
                <span class="block text-xs font-semibold text-base-content/60 uppercase tracking-wider mb-2">Review &
                    Countermeasures</span>
                <div class="bg-base-200/50 p-3 rounded-lg border border-base-200 text-sm whitespace-pre-wrap h-full">
                    {{ $detailRecord->review ?: '-' }}
                </div>
            </div>

        </div>
    </div>
</div>