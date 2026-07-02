<tr wire:click="openViewItem({{ $item->id }})" class="hover:cursor-pointer hover:bg-base-200/50 transition-colors [&>td]:!py-1.5 [&>td]:!px-2">
    <td class="font-semibold text-xs">
        {{ $item->itemcheck }}
    </td>
    <td class="text-center">
        <x-badge value="{{ $item->status ?? 'Undone' }}" 
            class="{{ match(strtolower($item->status ?? 'Undone')) {
                'done' => 'badge-success badge-outline',
                'undone' => 'badge-warning badge-outline',
                default => 'badge-ghost badge-outline'
            } }} badge-sm font-semibold rounded-full" 
        />
    </td>
    <td>
        <div class="text-xs leading-tight">
            {{ Str::limit($item->description, 60, '...') }}
        </div>
    </td>
    <td>
        <div class="text-xs leading-tight">
            {{ Str::limit($item->action, 60, '...') }}
        </div>
    </td>
    <td class="text-center whitespace-nowrap">
        <x-button icon="o-pencil-square" class="btn-ghost btn-xs btn-circle text-info" wire:click.stop="openEditItem({{ $item->id }})" />
        <x-button icon="o-trash" class="btn-ghost btn-xs btn-circle text-error" wire:click.stop="confirmDeleteItem({{ $item->id }})" />
    </td>
</tr>
