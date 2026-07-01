                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold">Spareparts List</h2>
                    <x-button icon="o-plus" label="Add Sparepart" class="btn-primary btn-sm" wire:click="openAddSp" />
                </div>

                @if($record->spareparts->isEmpty())
                    <div class="text-center py-10 bg-base-200 rounded-xl border border-dashed">
                        <x-icon name="o-wrench" class="w-12 h-12 text-base-content/30 mx-auto mb-2" />
                        <p class="text-base-content/70">No spareparts used.</p>
                        <x-button label="Add Sparepart" class="btn-outline btn-sm mt-3" wire:click="openAddSp" />
                    </div>
                @else
                    <x-card class="p-0 overflow-hidden shadow-sm border border-base-300" shadow="false">
                        <div class="overflow-x-auto">
                            <table class="table table-zebra table-sm w-full">
                                <thead class="bg-base-200">
                                    <tr>
                                        <th>Sparepart Name / ID</th>
                                        <th>Qty</th>
                                        <th>Used For (Item Check)</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($record->spareparts as $sp)
                                        <tr>
                                            <td class="font-semibold">{{ $sp->sparepart_id }}</td>
                                            <td>
                                                <div class="badge badge-neutral">{{ $sp->qty }}</div>
                                            </td>
                                            <td class="text-base-content/70">{{ $sp->itemcheck ?: '-' }}</td>
                                            <td class="text-right whitespace-nowrap">
                                                <x-button icon="o-pencil-square" class="btn-ghost btn-sm btn-circle text-info" wire:click="openEditSp({{ $sp->id }})" />
                                                <x-button icon="o-trash" class="btn-ghost btn-sm btn-circle text-error" wire:click="confirmDeleteSp({{ $sp->id }})" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                @endif
