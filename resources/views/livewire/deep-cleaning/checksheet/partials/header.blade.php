<!-- Header -->
<div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
            Checksheet {{ $selectedType ?: 'TPM' }} 
            @if($selectedYear) ({{ $selectedYear }}) @endif
        </h2>
        @if($selectedMachine && $this->chartData())
            <p class="text-gray-500 text-sm mt-1">
                Machine: {{ $selectedMachine }} | 
                Type: {{ $this->chartData()['isBt30'] ? 'BT 30' : 'BT 40' }}
            </p>
        @endif
    </div>
    
    <!-- Actions -->
    <div class="flex gap-2">
        <x-button label="Generate" icon="o-document-plus" class="btn-primary" @click="$wire.generateModal = true" />
    </div>
</div>
