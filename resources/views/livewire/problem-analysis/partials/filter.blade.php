<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <!-- Year -->
    <x-select 
        wire:model.live="selectedYear" 
        :options="collect($years)->map(fn($y) => ['id' => $y, 'name' => $y])->toArray()" 
        option-value="id" 
        option-label="name" 
        label="Years"
        placeholder="Select Year"
        class="w-full"
    />

    <!-- Select By -->
    <x-select 
        wire:model.live="selectedBy" 
        :options="[
            ['id' => 'Machine', 'name' => 'Machine'],
            ['id' => 'Line', 'name' => 'Line']
        ]" 
        option-value="id" 
        option-label="name" 
        label="Select By"
        placeholder="Select Category"
        :disabled="!$selectedYear"
        class="w-full"
    />

    <!-- Month -->
    <x-select 
        wire:model.live="selectedMonth" 
        :options="collect($months)->map(fn($m) => ['id' => $m, 'name' => $monthNames[$m] ?? $m])->toArray()" 
        option-value="id" 
        option-label="name" 
        label="Month"
        placeholder="Select Month"
        :disabled="!$selectedBy"
        class="w-full"
    />

    <!-- Reset Button -->
    <div class="flex items-end pb-1">
        <x-button label="Reset" icon="o-arrow-path" wire:click="resetFilters" class="btn-outline w-full" />
    </div>
</div>
