    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
        <x-card class="cursor-pointer hover:shadow-lg transition-shadow border-l-4 border-success text-center" wire:click="$set('showTpmModal', true)">
            <h6 class="text-sm font-semibold mb-2">TPM Records</h6>
            <h4 class="text-3xl font-bold text-success">{{ $stats['tpm'] }}</h4>
            <p class="text-xs opacity-60 mt-1">Records Found</p>
        </x-card>
        
        <x-card class="cursor-pointer hover:shadow-lg transition-shadow border-l-4 border-error text-center" wire:click="$set('showProblemModal', true)">
            <h6 class="text-sm font-semibold mb-2">Problem Log</h6>
            <h4 class="text-3xl font-bold text-error">{{ $stats['problem'] }}</h4>
            <p class="text-xs opacity-60 mt-1">Issues Logged</p>
        </x-card>

        <x-card class="cursor-pointer hover:shadow-lg transition-shadow border-l-4 border-primary text-center" wire:click="$set('showOverhaulModal', true)">
            <h6 class="text-sm font-semibold mb-2">Overhaul</h6>
            <h4 class="text-3xl font-bold text-primary">{{ $stats['overhaul'] }}</h4>
            <p class="text-xs opacity-60 mt-1">OH Made</p>
        </x-card>

        <x-card class="cursor-pointer hover:shadow-lg transition-shadow border-l-4 border-warning text-center" wire:click="$set('showWorkOrderModal', true)">
            <h6 class="text-sm font-semibold mb-2">Work Orders</h6>
            <h4 class="text-3xl font-bold text-warning">{{ $stats['work_order'] }}</h4>
            <p class="text-xs opacity-60 mt-1">Requests Made</p>
        </x-card>

        <x-card class="cursor-pointer hover:shadow-lg transition-shadow border-l-4 border-info text-center" wire:click="$set('showOneHourModal', true)">
            <h6 class="text-sm font-semibold mb-2">One Hour Over</h6>
            <h4 class="text-3xl font-bold text-info">{{ $stats['one_hour_over'] }}</h4>
            <p class="text-xs opacity-60 mt-1">Records Found</p>
        </x-card>
    </div>
