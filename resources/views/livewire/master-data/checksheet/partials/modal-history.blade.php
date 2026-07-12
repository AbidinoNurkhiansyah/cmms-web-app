{{-- Modal: History Revisi Dokumen --}}
<x-modal wire:model="historyModal" title="Riwayat Revisi Dokumen" separator box-class="w-11/12 max-w-4xl">

    @if($historyDocs->isNotEmpty())
        <div class="space-y-3">
            @foreach($historyDocs as $i => $doc)
                <div class="rounded-xl border border-base-300 bg-base-100 p-4 flex items-start gap-3 sm:gap-4">
                    {{-- Timeline indicator --}}
                    <div class="flex flex-col items-center shrink-0 pt-0.5">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs
                            {{ $i === 0 ? 'bg-primary text-primary-content' : 'bg-base-200 text-base-content/50' }}">
                            {{ $i + 1 }}
                        </div>
                        @if(!$loop->last)
                            <div class="w-px flex-1 bg-base-300 mt-1 min-h-[16px]"></div>
                        @endif
                    </div>
                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="font-bold font-mono text-sm">{{ $doc->doc_no }}</span>
                            @if($i === 0)
                                <x-badge value="Terbaru" class="badge-primary badge-sm" />
                            @endif
                            <span class="text-xs text-base-content/50 ml-auto">
                                {{ $doc->tanggal_revisi?->format('d M Y') ?? '—' }}
                            </span>
                        </div>
                        <p class="text-sm text-base-content/80 leading-relaxed">
                            <span class="font-medium">Item Revisi:</span> {{ $doc->item_revisi }}
                        </p>
                        @if($doc->keterangan)
                            <p class="text-xs text-base-content/60 mt-1">
                                <span class="font-medium">Keterangan:</span> {{ $doc->keterangan }}
                            </p>
                        @endif
                    </div>
                    {{-- Delete --}}
                    <button
                        onclick="document.getElementById('cs-history-delete-dialog').dataset.id='{{ $doc->id }}'; document.getElementById('cs-history-delete-label').textContent='{{ addslashes($doc->doc_no) }}'; document.getElementById('cs-history-delete-dialog').showModal();"
                        class="btn btn-ghost btn-xs text-error shrink-0"
                        title="Hapus Histori">
                        <x-icon name="o-trash" class="w-4 h-4" />
                    </button>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-10 text-base-content/40">
            <x-icon name="o-document-magnifying-glass" class="w-14 h-14 mb-3" />
            <p class="font-semibold">Belum ada riwayat revisi</p>
        </div>
    @endif

    <x-slot:actions>
        <x-button label="Tutup" @click="$wire.historyModal = false" />
    </x-slot:actions>
</x-modal>

{{-- DaisyUI Delete History Confirm Dialog --}}
<dialog id="cs-history-delete-dialog" class="modal modal-bottom sm:modal-middle" x-data>
    <div class="modal-box">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 rounded-full bg-error/10 flex items-center justify-center shrink-0">
                <x-icon name="o-trash" class="w-5 h-5 text-error" />
            </div>
            <div>
                <h3 class="font-bold text-lg">Hapus Riwayat Revisi?</h3>
                <p class="text-sm text-base-content/70 mt-1">
                    Riwayat dokumen <span id="cs-history-delete-label" class="font-semibold text-base-content"></span>
                    akan dihapus permanen.
                </p>
            </div>
        </div>
        <div class="modal-action mt-6">
            <form method="dialog">
                <button class="btn btn-ghost btn-sm">Batal</button>
            </form>
            <button
                class="btn btn-error btn-sm text-white"
                @click="$wire.deleteHistory(parseInt($el.closest('dialog').dataset.id)); $el.closest('dialog').close()">
                <x-icon name="o-trash" class="w-4 h-4" />
                Ya, Hapus
            </button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>
