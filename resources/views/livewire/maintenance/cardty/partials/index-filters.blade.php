<!-- Accordion Filters -->
<div x-show="$wire.filterDrawer" x-collapse>
    <div class="mb-4 p-4 rounded-xl bg-base-100 border border-base-200 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1">
                <x-input label="Start Date" type="date" wire:model.live="startDateFilter" />
            </div>
            <div class="flex-1">
                <x-input label="End Date" type="date" wire:model.live="endDateFilter" />
            </div>
            <div class="flex-1">
                <x-select label="Status" wire:model.live="statusFilter" :options="[['id' => '', 'name' => 'All Status'], ['id' => 'Permanent', 'name' => 'Permanent'], ['id' => 'Temporary', 'name' => 'Temporary']]"
                    option-value="id" option-label="name" />
            </div>
            <div class="flex-none">
                <x-button label="Clear Filters" icon="o-x-mark"
                    wire:click="$set('startDateFilter', ''); $set('endDateFilter', ''); $set('statusFilter', ''); $set('search', '')"
                    class="btn-ghost" />
            </div>
        </div>
    </div>
</div>
