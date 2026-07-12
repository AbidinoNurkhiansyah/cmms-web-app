{{-- Modal: Tambah Item --}}
<x-modal wire:model="addModal" title="Tambah Item Check Sheet" separator box-class="max-w-3xl">

    <div class="flex flex-col md:flex-row gap-6">

        {{-- ── Kolom Kiri: Upload & Preview Foto ──────────────────────────── --}}
        <div class="md:w-56 shrink-0 flex flex-col gap-3">
            <div class="text-sm font-medium text-base-content/70 mb-1">Foto Bukti / Standar</div>

            {{-- Preview Area --}}
            <div x-data="{ preview: null }"
                 @change="
                    const file = $event.target.files[0];
                    if (file) preview = URL.createObjectURL(file);
                 ">

                {{-- Preview image --}}
                <div class="w-full h-48 rounded-xl border-2 border-dashed border-base-300 bg-base-200
                            flex items-center justify-center overflow-hidden mb-3">
                    <template x-if="preview">
                        <img :src="preview" class="w-full h-full object-cover rounded-xl" alt="Preview" />
                    </template>
                    <template x-if="!preview">
                        <div class="flex flex-col items-center gap-2 text-base-content/30 p-4 text-center">
                            <x-icon name="o-photo" class="w-10 h-10" />
                            <span class="text-xs">Foto akan ditampilkan di sini</span>
                        </div>
                    </template>
                </div>

                {{-- File input --}}
                <x-file
                    id="add_photo"
                    wire:model="addPhoto"
                    accept="image/png, image/jpeg"
                    hint="JPG, PNG. Maks 2MB." />
            </div>
        </div>

        {{-- Divider --}}
        <div class="hidden md:block w-px bg-base-300 self-stretch"></div>

        {{-- ── Kolom Kanan: Form Input ─────────────────────────────────────── --}}
        <div class="flex-1 flex flex-col gap-4">
            <x-input
                id="add_item_check"
                label="Point Check"
                wire:model="addItemCheck"
                placeholder="Contoh: Cek tekanan oli mesin"
                icon="o-clipboard-document-list" />

            <x-input
                id="add_standard"
                label="Standard"
                wire:model="addStandard"
                placeholder="Contoh: 5–7 bar"
                icon="o-adjustments-horizontal" />

            <x-input
                id="add_method"
                label="Method"
                wire:model="addMethod"
                placeholder="Contoh: Visual, Measurement"
                icon="o-eye" />

            <x-select
                id="add_periode"
                label="Periode Pengecekan"
                wire:model="addPeriode"
                :options="$periodeOptions"
                placeholder="— Pilih Periode —"
                icon="o-calendar" />

            <div class="mt-1">
                <x-toggle
                    id="add_is_active"
                    label="Item Aktif"
                    wire:model="addIsActive"
                    class="toggle-success" />
            </div>
        </div>

    </div>

    <x-slot:actions>
        <x-button label="Batal" @click="$wire.addModal = false" />
        <x-button label="Simpan Item" icon="o-check" class="btn-primary" wire:click="saveItem" spinner="saveItem" />
    </x-slot:actions>
</x-modal>
