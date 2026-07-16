<x-card title="List of Calls">
    <x-slot:menu>
        <div class="flex flex-col md:flex-row items-center gap-2">
            <x-select wire:model.live="statusFilter" :options="[['id' => '', 'name' => 'All Status'], ['id' => 'CALL', 'name' => 'CALL'], ['id' => 'REPAIR', 'name' => 'REPAIR'], ['id' => 'DONE', 'name' => 'DONE']]"
                option-value="id" option-label="name" />
            <x-input placeholder="Search line, machine, info..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
            <x-button label="Add Call" icon="o-plus" class="btn-primary" wire:click="openAdd" spinner />
        </div>
    </x-slot:menu>

    <x-table :headers="[
        ['key' => 'date_in', 'label' => 'Date In'],
        ['key' => 'line_name', 'label' => 'Line'],
        ['key' => 'machine', 'label' => 'Machine'],
        ['key' => 'stop_info', 'label' => 'Problem'],
        ['key' => 'duration', 'label' => 'Duration'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'name_pic', 'label' => 'PIC'],
        ['key' => 'actions', 'label' => ''],
    ]" :rows="$records" with-pagination>
        @scope('cell_date_in', $r)
        <div>{{ $r->date_in ? $r->date_in->format('Y-m-d') : '-' }}</div>
        <div class="text-xs text-base-content/70">{{ $r->time_in ? $r->time_in->format('H:i') : '-' }}</div>
        @endscope

        @scope('cell_duration', $r)
        @php
            $start = $r->time_in ? $r->time_in->timestamp : null;
            $end = $r->finish_time ? $r->finish_time->timestamp : now()->timestamp;
            if ($start && $start <= $end) {
                $diff = $end - $start;
                $hours = floor($diff / 3600);
                $mins = floor(($diff % 3600) / 60);
                echo sprintf('%02d:%02d', $hours, $mins);
            } else {
                echo '-';
            }
        @endphp
        @endscope

        @scope('cell_status', $r)
        <x-badge label="{{ $r->status }}" class="{{ 
                    match (strtoupper($r->status)) {
        'CALL' => 'badge-error',
        'REPAIR' => 'badge-warning',
        'DONE' => 'badge-success',
        default => 'badge-ghost'
    }
                }}" />
        @endscope

        @scope('actions', $r)
        <div class="flex gap-1">
            <x-button icon="o-pencil-square" class="btn-ghost btn-xs" wire:click="openEdit({{ $r->id }})" spinner />
            <x-button icon="o-trash" class="btn-ghost btn-xs text-error" wire:click="confirmDelete({{ $r->id }})"
                spinner />
        </div>
        @endscope
    </x-table>
</x-card>