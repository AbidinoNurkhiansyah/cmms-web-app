<div class="mb-4 flex flex-col md:flex-row gap-4 justify-end">
    @if(!$this->isSpecialUser)
        <div class="w-full md:w-1/3">
            <x-choices label="Filter by Line" wire:model.live="selectedLine" :options="$this->lines->map(fn($line) => ['id' => $line, 'name' => $line])" option-value="id" option-label="name" placeholder="-- All Lines --"
                single searchable />
        </div>
    @endif
    <div class="w-full md:w-1/3">
        <x-input label="Search Machine" wire:model.live.debounce.300ms="searchAsset" icon="o-magnifying-glass"
            placeholder="Asset No / Machine Name..." clearable />
    </div>
</div>
