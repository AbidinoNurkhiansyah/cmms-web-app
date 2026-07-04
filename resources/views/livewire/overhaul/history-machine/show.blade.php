<?php

use Livewire\Volt\Component;
use App\Models\OverhaulHistoryMachine;
use App\Models\SparePart;
use Livewire\Attributes\Layout;
use Illuminate\Support\Collection;

new #[Layout('layouts.app')] class extends Component {
    public OverhaulHistoryMachine $detail;
    public Collection $spareParts;

    public function mount($id)
    {
        $this->detail = OverhaulHistoryMachine::with(['asset', 'pic'])->findOrFail($id);
        
        // Bulk fetch spareparts to prevent N+1 query problem in the view
        $this->spareParts = collect();
        if (is_array($this->detail->part_change) && count($this->detail->part_change) > 0) {
            $partIds = collect($this->detail->part_change)->pluck('spare_part_id')->filter()->unique();
            $this->spareParts = SparePart::findMany($partIds)->keyBy('id');
        }
    }
}; ?>

<div class="space-y-4">
    <div class="flex items-center gap-4 pb-4 border-b border-base-200">
        <x-button icon="o-arrow-left" link="{{ route('overhaul.history-machine.index') }}" class="btn-circle btn-ghost"
            tooltip="Kembali" spinner wire:navigate />
        <div>
            <h1 class="text-2xl font-bold">Detail History Machine</h1>
            <p class="text-sm text-base-content/70 mt-1">Informasi lengkap overhaul history machine</p>
        </div>
    </div>

    <x-card>
        <div class="grid grid-cols-1 gap-4">
            <!-- Asset Info -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-3 bg-base-200/50 rounded-lg">
                <div>
                    <div class="text-xs text-base-content/70 font-medium">Asset No</div>
                    <div class="mt-1 font-bold text-sm">{{ $detail->asset->asset_no ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-base-content/70 font-medium">Machine Name</div>
                    <div class="mt-1 font-bold text-sm">{{ $detail->asset->machine_name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-base-content/70 font-medium">Line</div>
                    <div class="mt-1 font-bold text-sm">{{ $detail->asset->line_name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-base-content/70 font-medium">PIC</div>
                    <div class="mt-1 font-bold text-sm">{{ $detail->pic->name ?? '-' }}</div>
                </div>
            </div>

            <!-- Timing Info -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 border-l-4 border-primary pl-4">
                <div>
                    <div class="text-xs text-base-content/70 font-medium">Tgl Berlaku</div>
                    <div class="mt-1 font-bold text-base">
                        {{ $detail->tgl_berlaku ? $detail->tgl_berlaku->format('d M Y') : '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-base-content/70 font-medium">Row Date</div>
                    <div class="mt-1 font-bold text-base">
                        {{ $detail->row_date ? $detail->row_date->format('d M Y') : '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-base-content/70 font-medium">Frequency</div>
                    <div class="mt-1 font-bold text-base">{{ $detail->frequency ?: '-' }}</div>
                </div>
            </div>

            <!-- Text Content -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="bg-base-200/30 p-3 rounded-lg border border-base-200">
                    <div class="text-xs text-base-content/70 font-bold uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <x-icon name="o-exclamation-triangle" class="w-4 h-4 text-warning" />
                        Problem
                    </div>
                    <p class="text-sm whitespace-pre-line">{{ $detail->problem ?: '-' }}</p>
                </div>

                <div class="bg-base-200/30 p-3 rounded-lg border border-base-200">
                    <div class="text-xs text-base-content/70 font-bold uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <x-icon name="o-magnifying-glass" class="w-4 h-4 text-error" />
                        Cause
                    </div>
                    <p class="text-sm whitespace-pre-line">{{ $detail->cause ?: '-' }}</p>
                </div>

                <div class="bg-base-200/30 p-3 rounded-lg border border-base-200">
                    <div class="text-xs text-base-content/70 font-bold uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <x-icon name="o-check-circle" class="w-4 h-4 text-success" />
                        Corrective Action
                    </div>
                    <p class="text-sm whitespace-pre-line">{{ $detail->corrective_action ?: '-' }}</p>
                </div>
            </div>

            <!-- Part Change Details -->
            <div class="mt-2 border-t border-base-300 pt-4">
                <div class="text-xs text-base-content/70 font-bold uppercase tracking-wider mb-3">Part Change</div>
                @if($spareParts->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="table table-sm table-zebra w-full">
                            <thead class="bg-base-200">
                                <tr>
                                    <th class="w-12 text-center">No</th>
                                    <th>Sparepart Name</th>
                                    <th class="w-24 text-center">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detail->part_change as $idx => $sp)
                                    @php
                                        $part = $spareParts->get($sp['spare_part_id']);
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $idx + 1 }}</td>
                                        <td class="font-medium text-sm">
                                            {{ $part ? $part->part_name : 'Unknown Part (ID: ' . $sp['spare_part_id'] . ')' }}
                                        </td>
                                        <td class="text-center font-bold text-base">{{ $sp['qty'] ?? 1 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 bg-base-200/50 rounded-lg text-center text-sm text-base-content/60 italic">
                        No parts changed for this overhaul history.
                    </div>
                @endif
            </div>

        </div>
    </x-card>
</div>