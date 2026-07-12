<x-header title="Work Orders" separator>
    <x-slot:middle class="!justify-end gap-2">
        <x-input placeholder="Search machine, problem, requester..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
    </x-slot:middle>
    <x-slot:actions>
        <x-button label="Add Work Order" icon="o-plus" class="btn-primary" wire:click="openAdd" />
    </x-slot:actions>
</x-header>

{{-- Tabs --}}
<div class="tabs tabs-boxed mb-4 w-fit shadow-sm bg-base-100">
    <a class="tab {{ $statusFilter === '' ? 'tab-active font-bold' : '' }}" wire:click="$set('statusFilter', '')">All</a>
    <a class="tab {{ $statusFilter === 'Open' ? 'tab-active font-bold text-error' : '' }}" wire:click="$set('statusFilter', 'Open')">Open</a>
    <a class="tab {{ $statusFilter === 'In Progress' ? 'tab-active font-bold text-warning' : '' }}" wire:click="$set('statusFilter', 'In Progress')">In Progress</a>
    <a class="tab {{ $statusFilter === 'Done' ? 'tab-active font-bold text-success' : '' }}" wire:click="$set('statusFilter', 'Done')">Done</a>
</div>

<x-card class="shadow-sm">
    <x-table
        :headers="[
            ['key' => 'date',           'label' => 'Date'],
            ['key' => 'requester',      'label' => 'Requester'],
            ['key' => 'MachineName',    'label' => 'Machine'],
            ['key' => 'problem_description', 'label' => 'Problem'],
            ['key' => 'priority',       'label' => 'Priority'],
            ['key' => 'status',         'label' => 'Status'],
            ['key' => 'pic',            'label' => 'Team'],
        ]"
        :rows="$workOrders"
        with-pagination
    >
        @scope('cell_date', $wo)
            <div class="text-sm">
                <div>{{ $wo->date ? $wo->date->format('Y-m-d') : '-' }}</div>
                @if($wo->target_date)
                    <div class="text-xs text-gray-500">T: {{ $wo->target_date->format('Y-m-d') }}</div>
                @endif
            </div>
        @endscope

        @scope('cell_priority', $wo)
            <x-badge label="{{ $wo->priority }}" 
                class="{{ 
                    match(strtolower($wo->priority)) {
                        'high' => 'badge-error',
                        'medium' => 'badge-warning',
                        'low' => 'badge-info',
                        default => 'badge-ghost'
                    }
                }}" 
            />
        @endscope
        
        @scope('cell_status', $wo)
            <x-badge label="{{ $wo->status }}" 
                class="{{ 
                    match(strtolower($wo->status)) {
                        'open' => 'badge-error',
                        'in progress' => 'badge-warning',
                        'done' => 'badge-success',
                        default => 'badge-ghost'
                    }
                }}" 
            />
        @endscope

        @scope('actions', $wo)
            <div class="flex gap-1">
                <x-button icon="o-pencil-square" class="btn-ghost btn-xs text-primary" wire:click="openEdit({{ $wo->id }})" />
                <x-button icon="o-trash" class="btn-ghost btn-xs text-error" 
                    wire:click="deleteWO({{ $wo->id }})" 
                    wire:confirm="Delete Work Order? This cannot be undone." />
            </div>
        @endscope
    </x-table>
</x-card>
