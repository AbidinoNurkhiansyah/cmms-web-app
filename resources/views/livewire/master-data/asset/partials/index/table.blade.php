<x-card>
    <x-table :headers="[
        ['key' => 'asset_no', 'label' => 'Asset No'],
        ['key' => 'machine_name', 'label' => 'Machine Name'],
        ['key' => 'line_name', 'label' => 'Line'],
        ['key' => 'maker', 'label' => 'Maker'],
        ['key' => 'machine_rank', 'label' => 'Rank'],
        ['key' => 'manufacture_year', 'label' => 'Year'],
    ]" :rows="$assets" with-pagination
        link="/master/assets/{id}">
        @scope('cell_asset_no', $asset)
        <span class="font-mono font-semibold">{{ $asset->asset_no }}</span>
        @endscope

        @scope('cell_machine_rank', $asset)
        @if($asset->machine_rank)
            <x-badge value="{{ $asset->machine_rank }}" class="{{
            match ($asset->machine_rank) {
                'A' => 'badge-error text-white',
                'B' => 'badge-warning',
                'C' => 'badge-info text-white',
                'D' => 'badge-success text-white',
                default => 'badge-ghost'
            }
                            }}" />
        @endif
        @endscope

        @scope('actions', $asset)
        <div class="flex gap-1">
            <x-button icon="o-pencil-square" class="btn-ghost btn-xs z-50" wire:click.stop="openEdit({{ $asset->id }})"
                spinner />
            <x-button icon="o-trash" class="btn-ghost btn-xs text-error z-50"
                wire:click.stop="deleteAsset({{ $asset->id }})"
                wire:confirm="Delete asset {{ $asset->asset_no }}? This cannot be undone." spinner />
        </div>
        @endscope
    </x-table>
</x-card>