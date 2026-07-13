            <div class="mt-2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-base text-base-content">Spareparts Used</h3>
                    <x-button label="Add Sparepart" icon="o-plus" class="btn-primary btn-sm" wire:click="addSp" spinner="addSp" />
                </div>

                {{-- Add Sparepart Modal --}}
                <x-modal wire:model="addSpModal" title="{{ $editSpId ? 'Edit Sparepart' : 'Add Sparepart' }}">
                    <div class="grid grid-cols-1 gap-4">
                        <x-choices label="Sparepart" wire:model="sp_id" :options="$spareparts" option-label="part_name"
                            option-value="id" searchable search-function="searchSparepart"
                            placeholder="Search by name..." single no-progress debounce="300ms" />
                        
                        <x-input label="Quantity" type="number" wire:model="sp_qty" min="1" />
                        
                        <x-input label="Remarks / Purpose" wire:model="sp_itemcheck" placeholder="Optional" />
                    </div>

                    <x-slot:actions>
                        <x-button label="Cancel" @click="$wire.addSpModal = false" />
                        <x-button label="Save" icon="o-check" class="btn-primary" wire:click="saveSp" spinner="saveSp" />
                    </x-slot:actions>
                </x-modal>

                {{-- Spareparts List --}}
                @if(count($workOrderSpareparts) === 0)
                    <div class="text-center py-6 text-base-content/50 border border-dashed rounded-lg">
                        <x-icon name="o-inbox" class="w-10 h-10 mx-auto mb-2 opacity-50" />
                        No spareparts added yet.
                    </div>
                @else
                    <div class="overflow-x-auto border border-base-200 rounded-lg">
                        <table class="table table-sm w-full">
                            <thead class="bg-base-200">
                                <tr>
                                    <th>Part Number</th>
                                    <th>Name</th>
                                    <th>Qty</th>
                                    <th>Remarks</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workOrderSpareparts as $sp)
                                    <tr>
                                        <td>{{ $sp->sparepart->part_number ?? '-' }}</td>
                                        <td class="font-medium">{{ $sp->sparepart->part_name ?? 'Unknown' }}</td>
                                        <td>{{ $sp->qty }}</td>
                                        <td>{{ $sp->remarks ?? '-' }}</td>
                                        <td class="text-right whitespace-nowrap">
                                            <x-button icon="o-pencil" class="btn-ghost btn-sm text-info"
                                                wire:click="editSp({{ $sp->id }})" spinner />
                                            <x-button icon="o-trash" class="btn-ghost btn-sm text-error"
                                                wire:click="deleteSp({{ $sp->id }})" spinner />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
