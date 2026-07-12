{{-- Modal: Form Revisi Dokumen --}}
<x-modal wire:model="revisiModal" title="Form Revisi Dokumen" separator>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-input id="rev_doc_no" label="No. Dokumen" wire:model="revDocNo" placeholder="Contoh: MTC/CS/001"
            icon="o-document-text" />
        <x-input id="rev_tanggal" type="date" label="Tanggal Revisi" wire:model="revTanggal" icon="o-calendar" />
        <div class="md:col-span-2">
            <x-textarea id="rev_item_revisi" label="Item yang Direvisi" wire:model="revItemRevisi" rows="3"
                placeholder="Tuliskan poin-poin yang diubah pada revisi ini..." />
        </div>
        <div class="md:col-span-2">
            <x-textarea id="rev_keterangan" label="Keterangan Tambahan (opsional)" wire:model="revKeterangan" rows="2"
                placeholder="Catatan atau alasan revisi..." />
        </div>
    </div>

    <x-slot:actions>
        <x-button label="Batal" @click="$wire.revisiModal = false" />
        <x-button label="Simpan Revisi" icon="o-check" class="btn-primary" wire:click="saveRevisi"
            spinner="saveRevisi" />
    </x-slot:actions>
</x-modal>