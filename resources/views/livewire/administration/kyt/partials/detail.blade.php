<x-modal wire:model="detailModal" title="Detail KYT" class="backdrop-blur-sm" box-class="w-11/12 max-w-5xl">
    @if($this->detailKyt)
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Kolom Kiri: Gambar -->
        <div class="w-full md:w-5/12 flex flex-col items-center justify-start">
            @if($this->detailKyt->img)
                <img src="{{ $this->getImageUrl($this->detailKyt->img) }}" alt="Foto KYT" class="w-full rounded shadow-md object-contain border border-base-200">
            @else
                <div class="w-full aspect-square bg-base-200 flex flex-col items-center justify-center rounded border border-dashed border-base-300 text-gray-400">
                    <x-icon name="o-photo" class="w-16 h-16 opacity-50 mb-2" />
                    <p class="text-sm">Tidak ada foto</p>
                </div>
            @endif
        </div>
        
        <!-- Kolom Kanan: Data -->
        <div class="w-full md:w-7/12 space-y-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 font-semibold">No. ID</p>
                    <p>#{{ $this->detailKyt->no }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">Tanggal Kejadian</p>
                    <p>{{ \Carbon\Carbon::parse($this->detailKyt->date)->format('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">User Pelapor</p>
                    <p>{{ $this->detailKyt->user->name ?? '-' }} (JID: {{ $this->detailKyt->userId }})</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">Lokasi Kejadian</p>
                    <p>{{ $this->detailKyt->lokasi }}</p>
                </div>
            </div>
            
            <div class="border-t pt-3 mt-1 text-sm">
                <p class="text-gray-500 font-semibold mb-1">Potensi Bahaya</p>
                <p class="whitespace-pre-wrap bg-base-200 p-3 rounded-md">{{ $this->detailKyt->bahaya }}</p>
            </div>
            <div class="text-sm">
                <p class="text-gray-500 font-semibold mb-1">Resiko yang Ditimbulkan</p>
                <p class="whitespace-pre-wrap bg-base-200 p-3 rounded-md">{{ $this->detailKyt->resiko }}</p>
            </div>
            <div class="text-sm">
                <p class="text-gray-500 font-semibold mb-1">Tindakan Pencegahan (Countermeasure)</p>
                <p class="whitespace-pre-wrap bg-base-200 p-3 rounded-md">{{ $this->detailKyt->countermeasure }}</p>
            </div>
            
            <div class="grid grid-cols-2 text-xs text-gray-400 mt-2 border-t pt-3">
                <div>
                    <p>Dibuat: {{ $this->detailKyt->created_at ? $this->detailKyt->created_at->format('d/m/Y H:i') : '-' }}</p>
                </div>
                <div class="text-right">
                    <p>Diupdate: {{ $this->detailKyt->updated_at ? $this->detailKyt->updated_at->format('d/m/Y H:i') : '-' }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
    <x-slot:actions>
        <x-button label="Tutup" wire:click="$set('detailModal', false)" class="btn-primary" />
    </x-slot:actions>
</x-modal>
