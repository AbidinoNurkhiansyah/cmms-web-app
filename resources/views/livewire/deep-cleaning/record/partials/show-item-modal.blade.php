<x-modal wire:model="itemModal" title="{{ $editingItemId ? 'Edit Finding' : 'Add Finding' }}" separator box-class="max-w-4xl">
    <div class="grid grid-cols-1 gap-3">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <x-input label="Item Check" wire:model="itemcheck" list="itemcheck-options" placeholder="e.g. V-Belt, Motor Area, etc." />
                <datalist id="itemcheck-options">
                    @foreach($itemcheckOptions as $option)
                        <option value="{{ $option }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div>
                <x-select label="Status" wire:model="status" :options="[['id'=>'Undone','name'=>'Undone'],['id'=>'Done','name'=>'Done']]" option-value="id" option-label="name" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="space-y-2 p-3 bg-error/5 rounded-xl border border-error/20">
                <div class="font-bold text-error text-sm flex items-center gap-2">
                    <x-icon name="o-exclamation-triangle" class="w-4 h-4" /> BEFORE
                </div>
                <x-input label="Finding / Problem" wire:model="description" list="description-options" placeholder="Tulis masalah atau temuan..." />
                <datalist id="description-options">
                    @foreach($descriptionOptions as $option)
                        <option value="{{ $option }}"></option>
                    @endforeach
                </datalist>
                
                <div class="flex items-center gap-2 mt-2">
                    <div class="flex-1">
                        <input type="file" wire:model="before_photo" class="file-input file-input-bordered file-input-sm w-full" accept="image/*" />
                        @if ($before_photo)
                            <div class="text-[10px] text-success mt-1">New image selected</div>
                        @endif
                    </div>
                    @if(!$before_photo && $existing_before)
                        <div class="w-10 h-10 border rounded overflow-hidden shrink-0">
                            <img src="{{ asset('storage/' . $existing_before) }}" class="object-cover w-full h-full" />
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-2 p-3 bg-success/5 rounded-xl border border-success/20">
                <div class="font-bold text-success text-sm flex items-center gap-2">
                    <x-icon name="o-check-circle" class="w-4 h-4" /> AFTER
                </div>
                <x-input label="Action Taken" wire:model="action" list="action-options" placeholder="Tulis tindakan perbaikan..." />
                <datalist id="action-options">
                    @foreach($actionOptions as $option)
                        <option value="{{ $option }}"></option>
                    @endforeach
                </datalist>
                
                <div class="flex items-center gap-2 mt-2">
                    <div class="flex-1">
                        <input type="file" wire:model="after_photo" class="file-input file-input-bordered file-input-sm w-full" accept="image/*" />
                        @if ($after_photo)
                            <div class="text-[10px] text-success mt-1">New image selected</div>
                        @endif
                    </div>
                    @if(!$after_photo && $existing_after)
                        <div class="w-10 h-10 border rounded overflow-hidden shrink-0">
                            <img src="{{ asset('storage/' . $existing_after) }}" class="object-cover w-full h-full" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost btn-sm" wire:click="$set('itemModal',false)" />
        <x-button label="Save Finding" class="btn-primary btn-sm" wire:click="saveItem" spinner="saveItem" />
    </x-slot:actions>
</x-modal>
