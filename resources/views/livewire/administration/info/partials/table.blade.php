<x-card>
    <x-table :headers="[
        ['key' => 'date', 'label' => 'Date'],
        ['key' => 'user_name', 'label' => 'User', 'sortable' => false],
        ['key' => 'source', 'label' => 'Source'],
        ['key' => 'title', 'label' => 'Title'],
        ['key' => 'file_path', 'label' => 'Action', 'class' => 'text-center w-32'],
    ]" :rows="$records" with-pagination>
        @scope('cell_date', $r)
        {{ $r->date ? $r->date->format('d M Y') : '-' }}
        @endscope

        @scope('cell_user_name', $r)
        {{ $r->user->name ?? '-' }}
        @endscope

        @scope('cell_file_path', $r)
        <div class="flex gap-2 justify-center">
            @if($r->file_path)
                <x-button icon="o-eye" link="{{ Storage::url($r->file_path) }}" external class="btn-ghost btn-xs text-info"
                    tooltip="View File" />
            @else
                <x-button icon="o-eye-slash" class="btn-ghost btn-xs disabled text-gray-400" disabled tooltip="No File" />
            @endif
            <x-button icon="o-pencil-square" class="btn-ghost btn-xs text-black dark:text-white"
                wire:click="openEdit({{ $r->id }})" tooltip="Edit" spinner />
            <x-button icon="o-trash" class="btn-ghost btn-xs text-error" wire:click="openDelete({{ $r->id }})"
                tooltip="Delete" spinner />
        </div>
        @endscope
    </x-table>
</x-card>