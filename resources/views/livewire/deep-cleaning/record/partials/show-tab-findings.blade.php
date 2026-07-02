                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold">Findings Checklist</h2>
                    <x-button icon="o-plus" label="Add Finding" class="btn-primary btn-sm" wire:click="openAddItem" />
                </div>

                @if($record->items->isEmpty())
                    <div class="text-center py-10 bg-base-200 rounded-xl border border-dashed">
                        <x-icon name="o-inbox" class="w-12 h-12 text-base-content/30 mx-auto mb-2" />
                        <p class="text-base-content/70">No findings added yet.</p>
                        <x-button label="Add First Finding" class="btn-outline btn-sm mt-3" wire:click="openAddItem" />
                    </div>
                @else
                    <x-card class="p-0 overflow-hidden shadow-sm border border-base-300" shadow="false">
                        <div class="overflow-x-auto">
                            <table class="table table-zebra table-sm w-full">
                                <thead class="bg-base-200">
                                    <tr>
                                        <th>Item Check</th>
                                        <th class="text-center">Status</th>
                                        <th class="w-1/4">Problem / Finding</th>
                                        <th class="w-1/4">Action Taken</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($record->items as $item)
                                        @include('livewire.deep-cleaning.record.partials.show-finding-row')
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                @endif
