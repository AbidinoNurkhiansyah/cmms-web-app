<x-header separator>
    <x-slot:title>
        <div class="flex items-center gap-3">
            @if(!$this->isSpecialUser)
                <x-button link="/maintenance/checksheet-monitoring" class="btn-circle btn-ghost btn-sm" icon="o-arrow-left" tooltip="Back to Monitoring" />
            @endif
            Checksheet: Asset Selection
        </div>
    </x-slot:title>
</x-header>
