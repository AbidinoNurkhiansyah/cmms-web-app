<?php

use App\Services\SparePartService;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.guest')] class extends Component {
    public $part;

    public function mount(int $id, SparePartService $sparePartService)
    {
        $this->part = $sparePartService->getSparePartById($id);
        if (!$this->part) {
            abort(404);
        }
    }
};
?>
<div id="print-root" class="flex flex-col items-center justify-center min-h-[350px] bg-gray-100 p-4">
    <div class="mb-4 text-center no-print">
        <h1 class="text-2xl font-bold">Label Preview</h1>
        <p class="text-sm text-gray-500">Please review before printing.</p>
    </div>

    <!-- Container Print (70mm x 30mm) -->
    <div id="label" class="bg-white" style="width: 70mm; height: 30mm; padding: 2mm; font-size: 8pt;">
        <div class="flex border border-black h-full">
            <!-- Kiri (QR Code + ID) -->
            <div class="w-1/3 border-r border-black flex flex-col items-center justify-between p-1">
                <div id="qrcode" class="mb-1 w-full flex justify-center"></div>
                <div class="border-t border-black pt-1 w-full text-center text-[7pt] font-bold overflow-hidden whitespace-nowrap" title="{{ $part->part_number }}">
                    {{ $part->part_number }}
                </div>
            </div>

            <!-- Kanan (Part Name, Rack, Maker) -->
            <div class="w-2/3 flex flex-col justify-between p-1">
                <div class="text-[8pt] font-bold leading-tight overflow-hidden" style="max-height: 24pt;">
                    {{ $part->part_name }}
                </div>
                <div class="border-t border-black pt-1 flex justify-between font-semibold mt-auto">
                    <span>{{ $part->no_rack ?: '-' }}</span>
                    <span class="overflow-hidden whitespace-nowrap ml-1 max-w-[30mm]">{{ $part->maker ?: '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex gap-4 no-print">
        <x-button label="Print Label" class="btn-primary" icon="o-printer" onclick="window.print()" />
    </div>

    <!-- Script for QR Code only (No heavy PDF libraries needed) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        @media print {
            /* Sembunyikan semua elemen selain label */
            body > *:not(livewire), 
            .no-print {
                display: none !important;
            }
            body, html {
                margin: 0 !important;
                padding: 0 !important;
                height: 100% !important;
                background-color: white !important;
            }
            body * {
                visibility: hidden;
            }
            #print-root {
                min-height: 0 !important;
                height: auto !important;
                padding: 0 !important;
                display: block !important;
                background-color: white !important;
            }
            #label, #label * {
                visibility: visible;
            }
            #label {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
            }
            @page {
                size: 70mm 30mm;
                margin: 0;
            }
        }
    </style>

    <script>
        // Eksekusi langsung tanpa menunggu DOMContentLoaded penuh agar QR code muncul instan
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $part->part_number }}",
            width: 60,
            height: 60,
            correctLevel: QRCode.CorrectLevel.H
        });
    </script>
</div>
