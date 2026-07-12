{{-- Checksheet Items Table --}}
<x-card>
    <x-table :headers="[
        ['key' => 'no', 'label' => 'No.'],
        ['key' => 'item_check', 'label' => 'Point Check'],
        ['key' => 'standard', 'label' => 'Standard'],
        ['key' => 'method', 'label' => 'Method'],
        ['key' => 'periode', 'label' => 'Periode'],
        ['key' => 'status', 'label' => 'Status'],
    ]" :rows="$items">

        {{-- No urut --}}
        @scope('cell_no', $item, $loop)
        <span class="text-base-content/50 text-sm">{{ $loop->iteration }}</span>
        @endscope

        {{-- Point check dengan tooltip penuh --}}
        @scope('cell_item_check', $item)
        <span class="font-medium" title="{{ $item->item_check }}">
            {{ Str::limit($item->item_check, 50) }}
        </span>
        @endscope

        {{-- Periode badge berwarna --}}
        @scope('cell_periode', $item)
        @php
            $map = [
                'D' => ['label' => 'Daily', 'class' => 'badge-info text-white'],
                'W' => ['label' => 'Weekly', 'class' => 'badge-warning'],
                'M' => ['label' => 'Monthly', 'class' => 'badge-secondary text-white'],
            ];
            $p = $map[$item->periode] ?? ['label' => $item->periode ?? '—', 'class' => 'badge-ghost'];
        @endphp
        <x-badge value="{{ $p['label'] }}" class="{{ $p['class'] }}" />
        @endscope

        {{-- Status badge --}}
        @scope('cell_status', $item)
        @if($item->is_active)
            <x-badge value="Aktif" class="badge-success badge-sm" />
        @else
            <x-badge value="Nonaktif" class="badge-error badge-sm" />
        @endif
        @endscope

        {{-- Action buttons --}}
        @scope('actions', $item)
        <div class="flex gap-1 items-center">
            {{-- Foto: hanya tampil jika ada --}}
            @if($item->photo_path)
                <button
                    onclick="document.getElementById('cs-photo-img').src='{{ Storage::url($item->photo_path) }}'; document.getElementById('cs-photo-dialog').showModal();"
                    class="btn btn-ghost btn-xs text-info" title="Lihat Foto">
                    <x-icon name="o-eye" class="w-4 h-4" />
                </button>
            @endif

            <x-button icon="o-pencil-square" class="btn-ghost btn-xs" wire:click="openEditItem({{ $item->id }})" spinner
                tooltip="Edit Item" />
            <button
                onclick="document.getElementById('cs-delete-dialog').dataset.id='{{ $item->id }}'; document.getElementById('cs-delete-label').textContent='{{ addslashes($item->item_check) }}'; document.getElementById('cs-delete-dialog').showModal();"
                class="btn btn-ghost btn-xs text-error" title="Hapus Item">
                <x-icon name="o-trash" class="w-4 h-4" />
            </button>
        </div>
        @endscope

        {{-- Empty state --}}
        <x-slot:empty>
            <div class="flex flex-col items-center justify-center py-14 text-base-content/40">
                <x-icon name="o-inbox" class="w-16 h-16 mb-3" />
                @if($asset_no)
                    <p class="font-semibold">Belum ada item pengecekan</p>
                    <p class="text-sm mt-1">Klik <strong>Tambah Item</strong> untuk membuat item pengecekan pertama.</p>
                @else
                    <p class="font-semibold">Pilih Line & Mesin</p>
                    <p class="text-sm mt-1">Gunakan filter di atas untuk memuat data checksheet mesin.</p>
                @endif
            </div>
        </x-slot:empty>

    </x-table>
</x-card>

{{-- DaisyUI Photo Lightbox Dialog --}}
<dialog id="cs-photo-dialog" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box bg-transparent shadow-none p-0 max-w-xl">
        <img id="cs-photo-img" src="" alt="Foto Item"
            class="w-full rounded-xl shadow-2xl object-contain max-h-[80vh]" />
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

{{-- DaisyUI Delete Confirm Dialog --}}
<dialog id="cs-delete-dialog" class="modal modal-bottom sm:modal-middle" x-data>
    <div class="modal-box">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 rounded-full bg-error/10 flex items-center justify-center shrink-0">
                <x-icon name="o-trash" class="w-5 h-5 text-error" />
            </div>
            <div>
                <h3 class="font-bold text-lg">Hapus Item?</h3>
                <p class="text-sm text-base-content/70 mt-1">
                    Item <span id="cs-delete-label" class="font-semibold text-base-content"></span>
                    akan dihapus permanen dan tidak dapat dikembalikan.
                </p>
            </div>
        </div>
        <div class="modal-action mt-6">
            <form method="dialog">
                <button class="btn btn-ghost btn-sm">Batal</button>
            </form>
            <button class="btn btn-error btn-sm text-white"
                @click="$wire.deleteItem(parseInt($el.closest('dialog').dataset.id)); $el.closest('dialog').close()">
                <x-icon name="o-trash" class="w-4 h-4" />
                Ya, Hapus
            </button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>