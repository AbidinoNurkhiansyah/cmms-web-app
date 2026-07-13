<x-header title="Work Orders" separator>
    <x-slot:middle class="!justify-end gap-2">
        <x-input placeholder="Search machine, problem, requester..." wire:model.live.debounce="search" clearable
            icon="o-magnifying-glass" />
    </x-slot:middle>
    <x-slot:actions>
        <x-button label="Export" icon="o-document-arrow-down" class="btn-outline btn-success" wire:click="openExport"
            spinner />
        <x-button label="Add Work Order" icon="o-plus" class="btn-primary" wire:click="openAdd" spinner />
    </x-slot:actions>
</x-header>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
    {{-- Tabs --}}
    <div class="tabs tabs-boxed w-fit shadow-sm bg-base-100">
        <a class="tab {{ $statusFilter === '' ? 'tab-active font-bold' : '' }}"
            wire:click="$set('statusFilter', '')">All</a>
        <a class="tab {{ $statusFilter === 'Open' ? 'tab-active font-bold text-error' : '' }}"
            wire:click="$set('statusFilter', 'Open')">Open</a>
        <a class="tab {{ $statusFilter === 'In Progress' ? 'tab-active font-bold text-warning' : '' }}"
            wire:click="$set('statusFilter', 'In Progress')">In Progress</a>
        <a class="tab {{ $statusFilter === 'Done' ? 'tab-active font-bold text-success' : '' }}"
            wire:click="$set('statusFilter', 'Done')">Done</a>
    </div>

    {{-- Legend --}}
    <div class="flex flex-wrap gap-4 text-[11px] md:text-xs">
        <div class="flex items-center gap-1.5 bg-base-200 px-3 py-1.5 rounded-lg">
            <span class="font-bold text-base-content/70 mr-1">Priority:</span>
            <span class="badge badge-error badge-sm text-[10px] text-white">High/Critical</span>
            <span class="badge badge-warning badge-sm text-[10px]">Normal</span>
            <span class="badge badge-info badge-sm text-[10px]">Low</span>
        </div>
        <div class="flex items-center gap-1.5 bg-base-200 px-3 py-1.5 rounded-lg">
            <span class="font-bold text-base-content/70 mr-1">Status:</span>
            <span class="badge badge-error badge-sm text-[10px] text-white">Open</span>
            <span class="badge badge-warning badge-sm text-[10px]">In Progress</span>
            <span class="badge badge-success badge-sm text-[10px]">Done</span>
        </div>
    </div>
</div>

<x-card class="shadow-sm">
    <x-table :headers="[
        ['key' => 'date', 'label' => 'Date'],
        ['key' => 'requester', 'label' => 'Requester'],
        ['key' => 'MachineName', 'label' => 'Machine'],
        ['key' => 'priority', 'label' => 'Priority'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'due_date', 'label' => 'Due Date'],
        ['key' => 'action', 'label' => 'Action', 'class' => 'text-center'],
    ]" :rows="$workOrders" with-pagination
        @row-click="Livewire.navigate('{{ url('/work-orders') }}/' + $event.detail.id)" class="hover:cursor-pointer">
        @scope('cell_date', $wo)
        <div class="text-sm">{{ $wo->date ? $wo->date->format('Y-m-d') : '-' }}</div>
        @endscope

        @scope('cell_requester', $wo)
        <div class="text-sm">
            <div class="font-medium">{{ $wo->requester ?? '-' }}</div>
            @if($wo->LineName)
                <div class="text-xs text-gray-500 mt-0.5">{{ $wo->LineName }}</div>
            @endif
        </div>
        @endscope

        @scope('cell_due_date', $wo)
        <div class="text-sm">
            <div>{{ $wo->target_date ? $wo->target_date->format('Y-m-d') : '-' }}</div>
            @if($wo->pic)
                <div class="text-xs font-medium text-primary mt-0.5">{{ $wo->pic }}</div>
            @endif
        </div>
        @endscope

        @scope('cell_priority', $wo)
        <x-badge label="{{ $wo->priority }}" class="{{ 
                    match (strtolower($wo->priority)) {
        'critical', 'high' => 'badge-error',
        'medium', 'normal' => 'badge-warning',
        'low' => 'badge-info',
        default => 'badge-ghost'
    }
                }}" />
        @endscope

        @scope('cell_status', $wo)
        <x-badge label="{{ $wo->status }}" class="{{ 
                    match (strtolower($wo->status)) {
        'open' => 'badge-error',
        'in progress' => 'badge-warning',
        'done' => 'badge-success',
        default => 'badge-ghost'
    }
                }}" />
        @endscope

        @scope('cell_action', $wo)
        <div class="flex justify-center gap-1" @click.stop>
            <x-button icon="o-pencil-square" class="btn-ghost btn-xs text-primary"
                link="{{ route('work-orders.edit', $wo->id) }}" />
            <x-button icon="o-trash" class="btn-ghost btn-xs text-error" wire:click="deleteWO({{ $wo->id }})"
                wire:confirm="Delete Work Order? This cannot be undone." />
        </div>
        @endscope
    </x-table>
</x-card>