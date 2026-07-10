<x-modal wire:model="addModal" title="New Asset" separator>
    <div class="grid grid-cols-2 gap-3">
        <x-input label="Asset No" wire:model="addAssetNo" placeholder="e.g. 13XQID014" class="col-span-2" />
        <x-select label="Line Name" wire:model="addLineName" :options="$lineOptions" placeholder="Select line" option-value="id" option-label="name" />
        <x-input label="Machine Name" wire:model="addMachineName" class="col-span-2" />
        <x-input label="Maker" wire:model="addMaker" />
        <x-select label="Rank" wire:model="addRank"
            :options="[['id'=>'A','name'=>'A'],['id'=>'B','name'=>'B'],['id'=>'C','name'=>'C'],['id'=>'D','name'=>'D']]"
            option-value="id" option-label="name" placeholder="Select rank" />
        <x-input label="Year" wire:model="addYear" type="number" min="1945" max="2100" />
        <div class="col-span-2">
            <label class="label text-sm font-semibold">Machine Photo</label>
            <input type="file" wire:model="addPhoto" accept="image/jpeg,image/png,image/jpg"
                   class="file-input file-input-bordered file-input-sm w-full" />
            @error('addPhoto') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
        <x-button label="Submit" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
    </x-slot:actions>
</x-modal>
