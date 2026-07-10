<x-modal wire:model="editModal" title="Edit Asset" separator>
    <div class="grid grid-cols-2 gap-3">
        <x-input label="Asset No" wire:model="editAssetNo" readonly class="col-span-2 opacity-60" />
        <x-select label="Line Name" wire:model="editLineName" :options="$lineOptions" option-value="id" option-label="name" placeholder="Select line" />
        <x-input label="Machine Name" wire:model="editMachineName" class="col-span-2" />
        <x-input label="Maker" wire:model="editMaker" />
        <x-select label="Rank" wire:model="editRank"
            :options="[['id'=>'A','name'=>'A'],['id'=>'B','name'=>'B'],['id'=>'C','name'=>'C'],['id'=>'D','name'=>'D']]"
            option-value="id" option-label="name" placeholder="Select rank" />
        <x-input label="Year" wire:model="editYear" type="number" min="1945" max="2100" />
        <div class="col-span-2">
            <label class="label text-sm font-semibold">Replace Photo (optional)</label>
            <input type="file" wire:model="editPhoto" accept="image/jpeg,image/png,image/jpg"
                   class="file-input file-input-bordered file-input-sm w-full" />
        </div>
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
        <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
    </x-slot:actions>
</x-modal>
