{{-- Modal Machine --}}
<x-modal wire:model="machineModal" title="Machine Information">
    @if(count($machinePartDetails) > 0)
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Line</th>
                        <th>Asset No</th>
                        <th>Machine</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($machinePartDetails as $detail)
                        <tr>
                            <td>{{ $detail->line }}</td>
                            <td>{{ $detail->asset_no }}</td>
                            <td>{{ $detail->machine }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-4 bg-base-200 rounded-lg text-center break-words">
            Tidak ada data mapping mesin yang tersedia.
        </div>
    @endif

    <x-slot:actions>
        <x-button label="Close" wire:click="$set('machineModal', false)" />
    </x-slot:actions>
</x-modal>

{{-- Modal Edit Repair --}}
<x-modal wire:model="editModal" title="Edit Repair Data">
    <div class="grid gap-4">
        <x-input label="Rack Repair" wire:model="editRack" />
        <x-input label="QTY Repair" type="number" wire:model="editQty" />
    </div>
    
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal', false)" />
        <x-button label="Submit" class="btn-success text-white" wire:click="saveEditRepair" spinner="saveEditRepair" />
    </x-slot:actions>
</x-modal>

{{-- Modal Print Label --}}
<x-modal wire:model="printModal" title="Print Label" class="backdrop-blur">
    @if($printId)
        <div class="w-full overflow-hidden rounded-xl bg-gray-100">
            <iframe src="{{ route('spare-parts.print-label', $printId) }}" class="w-full h-[350px] border-none"></iframe>
        </div>
    @endif
    <x-slot:actions>
        <x-button label="Close" wire:click="$set('printModal', false)" />
    </x-slot:actions>
</x-modal>
