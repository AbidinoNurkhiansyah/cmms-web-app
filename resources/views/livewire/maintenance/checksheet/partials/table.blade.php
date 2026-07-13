<x-card>
    <x-table :headers="[
        ['key' => 'asset_no', 'label' => 'Asset No'],
        ['key' => 'machine_name', 'label' => 'Machine Name'],
        ['key' => 'line_name', 'label' => 'Line Name'],
    ]" :rows="$this->assets" with-pagination>
        @scope('cell_asset_no', $asset)
            <x-badge value="{{ $asset->asset_no }}" class="badge-primary font-semibold" />
        @endscope

        @scope('actions', $asset)
            <x-button label="Select" icon="o-arrow-right" class="btn-primary btn-sm"
                wire:click="selectAsset('{{ $asset->asset_no }}')" />
        @endscope
    </x-table>
</x-card>
