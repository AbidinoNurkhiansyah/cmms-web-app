<?php

use Livewire\Volt\Component;

new class extends Component {
    public \App\Models\WorkOrder $workOrder;
    public array $spareparts = [];

    public function mount(int $id)
    {
        $wo = \App\Models\WorkOrder::find($id);
        if (!$wo) {
            abort(404, 'Work Order not found.');
        }
        $this->workOrder = $wo;
        $this->spareparts = \App\Models\WorkOrderSparepart::with('sparepart')
            ->where('work_order_id', $wo->id)
            ->get()
            ->toArray();
    }
};
?>
<div>
    <x-header subtitle="Work Order Document" separator>
        <x-slot:title>
            <div class="flex items-center gap-3">
                <x-button icon="o-arrow-left" class="btn-circle btn-ghost btn-sm"
                    link="{{ route('work-orders.index') }}" wire:navigate />
                <span>WO #{{ $workOrder->id }}</span>
            </div>
        </x-slot:title>
        <x-slot:actions>
            <x-button label="Edit / Process" icon="o-pencil-square" class="btn-primary"
                link="{{ route('work-orders.edit', $workOrder->id) }}" />
        </x-slot:actions>
    </x-header>

    <div class="max-w-full mx-auto space-y-4">
        <x-card class="shadow-sm" padding="p-4 sm:p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column: Context & Details --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Request Info --}}
                    <div>
                        <div class="text-xs text-base-content/50 font-bold uppercase mb-2">Request Information</div>
                        <div
                            class="grid grid-cols-2 md:grid-cols-3 gap-4 bg-base-100 rounded-lg border border-base-200">
                            <div>
                                <div class="text-[11px] text-base-content/60 uppercase tracking-wider mb-1">Date
                                    Requested</div>
                                <div class="font-medium">
                                    {{ $workOrder->date ? $workOrder->date->format('d M Y') : '-' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-[11px] text-base-content/60 uppercase tracking-wider mb-1">Target Date
                                    (Due)</div>
                                <div class="font-medium">
                                    {{ $workOrder->target_date ? $workOrder->target_date->format('d M Y') : '-' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-[11px] text-base-content/60 uppercase tracking-wider mb-1">Priority
                                </div>
                                <x-badge label="{{ $workOrder->priority }}" class="{{ 
                                        match (strtolower($workOrder->priority)) {
        'critical', 'high' => 'badge-error',
        'medium', 'normal' => 'badge-warning',
        'low' => 'badge-info',
        default => 'badge-ghost'
    }
                                    }}" />
                            </div>
                            <div>
                                <div class="text-[11px] text-base-content/60 uppercase tracking-wider mb-1">Order Type
                                </div>
                                <div class="font-medium">{{ $workOrder->order_type ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-[11px] text-base-content/60 uppercase tracking-wider mb-1">Requester
                                </div>
                                <div class="font-medium">{{ $workOrder->requester ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-[11px] text-base-content/60 uppercase tracking-wider mb-1">Department
                                </div>
                                <div class="font-medium">{{ $workOrder->department ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Asset & Problem --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Asset Info --}}
                        <div>
                            <div class="text-xs text-base-content/50 font-bold uppercase mb-2">Asset / Machine Info
                            </div>
                            <div class="bg-base-100 border border-base-200 rounded-lg flex flex-col gap-3 h-full">
                                <div>
                                    <span class="text-[11px] text-base-content/60 uppercase tracking-wider">Line:</span>
                                    <div class="font-semibold">{{ $workOrder->LineName ?? '-' }}</div>
                                </div>
                                <div>
                                    <span class="text-[11px] text-base-content/60 uppercase tracking-wider">Machine
                                        No:</span>
                                    <div class="font-semibold">{{ $workOrder->MachineNo ?? '-' }}</div>
                                </div>
                                <div>
                                    <span class="text-[11px] text-base-content/60 uppercase tracking-wider">Machine
                                        Name:</span>
                                    <div class="font-semibold">{{ $workOrder->MachineName ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Problem Description --}}
                        <div>
                            <div class="text-xs text-base-content/50 font-bold uppercase mb-2">Problem Description</div>
                            <div
                                class="bg-base-100 text-base-content rounded-lg font-medium text-sm leading-relaxed border border-base-200 h-full">
                                {{ $workOrder->problem_description ?? 'No description provided.' }}

                                @if($workOrder->foto_req)
                                    <div class="mt-4">
                                        <span
                                            class="text-[11px] text-base-content/60 uppercase tracking-wider block mb-2">Request
                                            Photo:</span>
                                        <img src="{{ Storage::url($workOrder->foto_req) }}" alt="Request Photo"
                                            class="max-h-48 rounded-lg shadow-sm border border-base-200 object-cover">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Spareparts Used --}}
                    @if(count($spareparts) > 0)
                        <div>
                            <div class="text-xs text-base-content/50 font-bold uppercase mb-2">Spareparts Used</div>
                            <div class="overflow-x-auto rounded-lg border border-base-200">
                                <table class="table table-sm w-full">
                                    <thead class="bg-base-200 text-base-content/70">
                                        <tr>
                                            <th class="uppercase text-[11px] tracking-wider">Part Number</th>
                                            <th class="uppercase text-[11px] tracking-wider">Name</th>
                                            <th class="uppercase text-[11px] tracking-wider text-center w-24">Qty</th>
                                            <th class="uppercase text-[11px] tracking-wider">Remarks / Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($spareparts as $sp)
                                            <tr class="hover bg-base-100">
                                                <td class="text-sm">{{ $sp['sparepart']['part_number'] ?? '-' }}</td>
                                                <td class="font-medium text-sm">
                                                    {{ $sp['sparepart']['part_name'] ?? 'Unknown Item' }}
                                                </td>
                                                <td class="text-center font-bold">{{ $sp['qty'] }}</td>
                                                <td class="text-sm text-base-content/70">{{ $sp['remarks'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right Column: Execution & Status --}}
                <div class="space-y-6">
                    {{-- Team & Status --}}
                    <div>
                        <div class="text-xs text-base-content/50 font-bold uppercase mb-2">Team & Status</div>
                        <div class="grid grid-cols-2 gap-4 bg-base-100 rounded-lg border border-base-200">
                            <div>
                                <span class="text-[11px] text-base-content/60 uppercase tracking-wider">Status:</span>
                                <div class="mt-1">
                                    <x-badge label="{{ $workOrder->status }}" class="{{ 
                                            match (strtolower($workOrder->status)) {
        'open' => 'badge-error',
        'in progress' => 'badge-warning',
        'done' => 'badge-success',
        default => 'badge-ghost'
    }
                                        }}" />
                                </div>
                            </div>
                            <div>
                                <span class="text-[11px] text-base-content/60 uppercase tracking-wider">Actual
                                    Date:</span>
                                <div class="font-semibold mt-1">
                                    {{ $workOrder->actual_date ? \Carbon\Carbon::parse($workOrder->actual_date)->format('d M Y') : '-' }}
                                </div>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[11px] text-base-content/60 uppercase tracking-wider">Team /
                                    PIC:</span>
                                <div class="font-semibold text-primary mt-1">{{ $workOrder->pic ?? 'Unassigned' }}</div>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[11px] text-base-content/60 uppercase tracking-wider">Assigned
                                    Technicians:</span>
                                <div class="font-semibold mt-1">
                                    @php
                                        $techs = array_filter([$workOrder->pic1, $workOrder->pic2, $workOrder->pic3]);
                                    @endphp
                                    {{ count($techs) > 0 ? implode(', ', $techs) : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Taken --}}
                    <div>
                        <div class="text-xs text-base-content/50 font-bold uppercase mb-2">Action Taken</div>
                        <div
                            class="bg-base-100 text-base-content rounded-lg font-medium text-sm leading-relaxed border border-base-200">
                            <div class="mb-4">
                                <span
                                    class="text-[11px] text-base-content/60 uppercase tracking-wider block mb-1">Confirmation
                                    Note:</span>
                                {{ $workOrder->confirmation_note ?? 'No confirmation note provided yet.' }}
                            </div>

                            @if($workOrder->foto_confirm1 || $workOrder->foto_confirm2)
                                <div class="grid grid-cols-1 gap-4 mt-2">
                                    @if($workOrder->foto_confirm1)
                                        <div>
                                            <span
                                                class="text-[11px] text-base-content/60 uppercase tracking-wider block mb-2">Completion
                                                Photo 1:</span>
                                            <img src="{{ Storage::url($workOrder->foto_confirm1) }}" alt="Completion Photo 1"
                                                class="max-h-48 w-full rounded-lg shadow-sm border border-base-200 object-cover">
                                        </div>
                                    @endif
                                    @if($workOrder->foto_confirm2)
                                        <div>
                                            <span
                                                class="text-[11px] text-base-content/60 uppercase tracking-wider block mb-2">Completion
                                                Photo 2:</span>
                                            <img src="{{ Storage::url($workOrder->foto_confirm2) }}" alt="Completion Photo 2"
                                                class="max-h-48 w-full rounded-lg shadow-sm border border-base-200 object-cover">
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </x-card>
    </div>
</div>