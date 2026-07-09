{{-- Add Modal --}}
<x-modal wire:model="addModal" title="New Information Record" separator>
    <div class="grid grid-cols-2 gap-3">
        <x-input label="Date" type="date" wire:model="date" />
        <x-select label="User" wire:model="user_id" :options="$users" option-value="id" option-label="name" placeholder="Select User" />
        <x-input label="Source" wire:model="source" />
        <x-input label="Title" wire:model="title" />
            
        <div class="col-span-2 mt-2 border-t pt-2">
            <x-file label="Upload PDF or Image" wire:model="file_path" accept=".pdf,image/*" />
        </div>
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
        <x-button label="Submit" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
    </x-slot:actions>
</x-modal>

{{-- Edit Modal --}}
<x-modal wire:model="editModal" title="Edit Information Record" separator>
    <div class="grid grid-cols-2 gap-3">
        <x-input label="Date" type="date" wire:model="date" />
        <x-select label="User" wire:model="user_id" :options="$users" option-value="id" option-label="name" placeholder="Select User" />
        <x-input label="Source" wire:model="source" />
        <x-input label="Title" wire:model="title" />
            
        <div class="col-span-2 mt-2 border-t pt-2">
            <x-file label="Upload PDF or Image (Leave blank to keep current)" wire:model="edit_file_path" accept=".pdf,image/*" />
            
            @if($old_file_path)
                <div class="mt-2">
                    <label class="label text-sm font-semibold">Preview Old File</label>
                    <x-button label="View Old File" link="{{ Storage::url($old_file_path) }}" external class="btn-outline btn-primary btn-sm" />
                </div>
            @endif
            @error('edit_file_path') <span class="text-error text-sm">{{ $message }}</span> @enderror
        </div>
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
        <x-button label="Update" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
    </x-slot:actions>
</x-modal>

{{-- Delete Modal --}}
<x-modal wire:model="deleteModal" title="Konfirmasi Penghapusan" separator>
    <div>
        Apakah Anda yakin ingin menghapus data ini secara permanen beserta lampirannya? Tindakan ini tidak dapat dibatalkan.
    </div>
    <x-slot:actions>
        <x-button label="Batal" class="btn-ghost" wire:click="$set('deleteModal',false)" />
        <x-button label="Ya, Hapus" class="btn-error text-white" wire:click="deleteRecord" spinner="deleteRecord" />
    </x-slot:actions>
</x-modal>
