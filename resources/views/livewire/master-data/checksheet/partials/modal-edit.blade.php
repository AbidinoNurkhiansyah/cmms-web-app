{{-- Modal: Edit Item --}}
<x-modal wire:model="editModal" title="Edit Item Check Sheet" separator box-class="max-w-3xl">

    <div class="flex flex-col md:flex-row gap-6">

        {{-- ── Kolom Kiri: Foto Saat Ini & Upload Baru ────────────────────── --}}
        <div class="md:w-56 shrink-0 flex flex-col gap-3">
            <div class="text-sm font-medium text-base-content/70 mb-1">Foto Bukti / Standar</div>

            {{-- Preview Area --}}
            <div>
                {{-- Preview image --}}
                <div class="w-full h-48 rounded-xl border-2 border-dashed border-base-300 bg-base-200
                            flex items-center justify-center overflow-hidden mb-3">
                    @if($editPhoto)
                        <img src="{{ $editPhoto->temporaryUrl() }}" class="w-full h-full object-cover rounded-xl" alt="Preview Baru" />
                    @elseif($editExistingPhoto)
                        <img src="{{ Storage::url($editExistingPhoto) }}"
                             class="w-full h-full object-cover rounded-xl"
                             alt="Foto Saat Ini" />
                    @else
                        <div class="flex flex-col items-center gap-2 text-base-content/30 p-4 text-center">
                            <x-icon name="o-photo" class="w-10 h-10" />
                            <span class="text-xs">Belum ada foto</span>
                        </div>
                    @endif
                </div>

                <div wire:loading wire:target="editPhoto" class="text-xs text-info text-center mb-2 w-full">
                    Uploading...
                </div>

                {{-- Label foto lama --}}
                @if($editExistingPhoto && !$editPhoto)
                    <div class="text-xs text-base-content/50 text-center mb-2">
                        Upload baru untuk mengganti foto ini
                    </div>
                @endif

                {{-- File input --}}
                <x-file
                    id="edit_photo"
                    wire:model="editPhoto"
                    accept="image/png, image/jpeg"
                    hint="JPG, PNG. Maks 2MB." />
            </div>
        </div>

        {{-- Divider --}}
        <div class="hidden md:block w-px bg-base-300 self-stretch"></div>

        {{-- ── Kolom Kanan: Form Input ─────────────────────────────────────── --}}
        <div class="flex-1 flex flex-col gap-4">
            <x-input
                id="edit_item_check"
                label="Point Check"
                wire:model="editItemCheck"
                icon="o-clipboard-document-list" />

            <x-input
                id="edit_standard"
                label="Standard"
                wire:model="editStandard"
                icon="o-adjustments-horizontal" />

            <x-input
                id="edit_method"
                label="Method"
                wire:model="editMethod"
                icon="o-eye" />

            <x-select
                id="edit_periode"
                label="Periode Pengecekan"
                wire:model="editPeriode"
                :options="$periodeOptions"
                icon="o-calendar" />

            <div class="mt-1">
                <x-toggle
                    id="edit_is_active"
                    label="Item Aktif"
                    wire:model="editIsActive"
                    class="toggle-success" />
            </div>
        </div>

    </div>

    <x-slot:actions>
        <x-button label="Batal" @click="$wire.editModal = false" />
        <x-button label="Perbarui Item" icon="o-check" class="btn-primary" wire:click="updateItem" spinner="updateItem" />
    </x-slot:actions>
</x-modal>
