<!-- Input/Edit Modal -->
<x-modal wire:model="inputModal" title="Input / Edit Data">
    <div class="space-y-4">
        <div class="{{ $inputMonth ? 'grid grid-cols-1 md:grid-cols-2 gap-4' : '' }}">
            <!-- Select Month (Col 1) -->
            <div>
                @php
                    $monthOptions = [];
                    foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $index => $label) {
                        $monthVal = $index + 1;
                        $isFilled = in_array($monthVal, $this->filledMonths);
                        $monthOptions[] = [
                            'id' => $monthVal, 
                            'name' => $label . ($isFilled ? ' (Sudah terisi)' : '')
                        ];
                    }
                @endphp
                <x-select label="Pilih Bulan" :options="$monthOptions" wire:model.live="inputMonth" placeholder="-- Pilih Bulan --" />
            </div>

            <!-- Dynamic Inputs (Col 2) -->
            <div>
                @if($inputMonth)
                    @if($selectedType === 'CLAMP ARBOR')
                        <x-input label="Clamp kN" type="number" step="0.01" wire:model="inputClampKn" required />
                    @elseif($selectedType === 'GATA-GATA')
                        <x-input label="Gata mm" type="number" step="0.01" wire:model="inputGataMm" required />
                    @elseif($selectedType === 'RUN OUT')
                        <div class="space-y-4">
                            <x-input label="Act Kelurusan (mikron)" type="number" step="0.01" wire:model="inputRunOutKelurusan" required />
                            <x-input label="Act Putaran (mikron)" type="number" step="0.01" wire:model="inputRunOutPutaran" required />
                        </div>
                    @endif
                @endif
            </div>
        </div>

        @if($inputMonth)
            <x-textarea label="Remark" wire:model="inputRemark" placeholder="Tambahkan catatan jika perlu..." rows="2" />
        @endif
    </div>
    <x-slot:actions>
        <x-button label="Tutup" @click="$wire.inputModal = false" />
        <x-button label="Simpan" class="btn-success text-white" wire:click="saveInputData" spinner="saveInputData" :disabled="!$inputMonth" />
    </x-slot:actions>
</x-modal>
