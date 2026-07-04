<?php

use Livewire\Volt\Component;
use App\Models\Asset;
use Livewire\Attributes\Layout;

new #[Layout('layouts.guest')] class extends Component {
    public $assets = [];

    public function mount()
    {
        $ids = request()->query('assets');
        if ($ids) {
            $idArray = explode(',', $ids);
            // Ambil data aset berdasarkan ID yang dipisahkan koma
            $this->assets = Asset::whereIn('id', $idArray)->get();
        }
    }
}; ?>

<div class="p-8 bg-white min-h-screen text-black print:p-0 print:bg-transparent flex flex-col justify-center items-center" 
     x-data 
     x-init="if({{ count($assets) > 0 ? 'true' : 'false' }}) setTimeout(() => window.print(), 500)">
    @if(count($assets) > 1)
        <div class="mb-6 w-full print:hidden flex justify-between items-center bg-base-200 p-4 rounded-lg">
            <div>
                <h1 class="text-2xl font-bold">Cetak QR Code Massal</h1>
                <p class="text-sm text-base-content/70">Tekan tombol cetak atau (Ctrl+P) untuk mencetak halaman ini.</p>
            </div>
            <div class="flex gap-2">
                <x-button label="Tutup Tab" icon="o-x-mark" onclick="window.close()" class="btn-ghost" />
                <x-button label="Cetak Sekarang" icon="o-printer" onclick="window.print()" class="btn-primary" />
            </div>
        </div>
    @endif

    <!-- Container for QR Codes -->
    <div class="flex flex-wrap justify-center gap-6 print:gap-4 print:w-full">
        @php $isSingle = count($assets) === 1; @endphp
        
        @foreach($assets as $asset)
            <div class="{{ $isSingle ? 'w-80 p-8 border-4' : 'w-48 p-4 border-2' }} flex flex-col items-center justify-center border-gray-300 rounded-xl text-center page-break-inside-avoid shrink-0">
                <div class="font-bold {{ $isSingle ? 'text-xl mb-6' : 'text-sm mb-3' }} uppercase tracking-wider">{{ $asset->asset_no }}</div>
                
                <div class="bg-white p-2 flex justify-center w-full">
                    {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size($isSingle ? 250 : 120)->generate(url('/overhaul/history-machine?filter_asset_id=' . $asset->id)) !!}
                </div>
                
                <div class="{{ $isSingle ? 'text-lg mt-6' : 'text-xs mt-3' }} truncate w-full font-semibold" title="{{ $asset->machine_name }}">
                    {{ $asset->machine_name }}
                </div>
                <div class="{{ $isSingle ? 'text-sm mt-2' : 'text-[10px] mt-1' }} text-gray-500">{{ $asset->line_name ?? '-' }}</div>
            </div>
        @endforeach
    </div>

    @if(count($assets) === 0)
        <div class="text-center py-20 text-gray-500">
            Tidak ada QR Code yang dipilih untuk dicetak.
        </div>
    @endif
</div>
