<?php

use App\Services\AssetService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, WithFileUploads, Toast;

    public string $search = '';

    // Add modal
    public bool   $addModal      = false;
    public string $addAssetNo    = '';
    public string $addLineName   = '';
    public string $addMachineName = '';
    public string $addMaker      = '';
    public string $addRank       = '';
    public ?int   $addYear       = null;
    public string $addClassify   = '';
    public $addPhoto             = null;

    // Edit modal
    public bool   $editModal      = false;
    public ?int   $editId         = null;
    public string $editAssetNo    = '';
    public string $editLineName   = '';
    public string $editMachineName = '';
    public string $editMaker      = '';
    public string $editRank       = '';
    public ?int   $editYear       = null;
    public string $editClassify   = '';
    public $editPhoto             = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function with(AssetService $assetService): array
    {
        return [
            'assets'    => $assetService->getPaginatedAssets(25, $this->search),
            'lineOptions' => collect($assetService->getDistinctLines())
                ->map(fn($l) => ['id' => $l, 'name' => $l])
                ->toArray(),
        ];
    }

    public function openAdd(): void
    {
        $this->reset(['addAssetNo','addLineName','addMachineName','addMaker','addRank','addYear','addClassify','addPhoto']);
        $this->addModal = true;
    }

    public function saveAdd(AssetService $assetService): void
    {
        $this->validate([
            'addAssetNo'     => 'required|string|max:50|unique:assets,asset_no',
            'addMachineName' => 'required|string|max:255',
            'addPhoto'       => 'nullable|image|max:4096',
        ]);

        $assetService->createAsset([
            'asset_no'         => strtoupper(trim($this->addAssetNo)),
            'line_name'        => $this->addLineName,
            'machine_name'     => $this->addMachineName,
            'maker'            => $this->addMaker,
            'machine_rank'     => $this->addRank,
            'manufacture_year' => $this->addYear,
            'classification'   => $this->addClassify,
        ], $this->addPhoto);

        $this->addModal = false;
        $this->success('Asset created.');
    }

    public function openEdit(int $id, AssetService $assetService): void
    {
        $a = $assetService->getAssetById($id);
        $this->editId          = $id;
        $this->editAssetNo     = $a->asset_no ?? '';
        $this->editLineName    = $a->line_name ?? '';
        $this->editMachineName = $a->machine_name ?? '';
        $this->editMaker       = $a->maker ?? '';
        $this->editRank        = $a->machine_rank ?? '';
        $this->editYear        = $a->manufacture_year;
        $this->editClassify    = $a->classification ?? '';
        $this->editPhoto       = null;
        $this->editModal       = true;
    }

    public function saveEdit(AssetService $assetService): void
    {
        $this->validate([
            'editMachineName' => 'required|string|max:255',
            'editPhoto'       => 'nullable|image|max:4096',
        ]);

        $assetService->updateAsset($this->editId, [
            'line_name'        => $this->editLineName,
            'machine_name'     => $this->editMachineName,
            'maker'            => $this->editMaker,
            'machine_rank'     => $this->editRank,
            'manufacture_year' => $this->editYear,
            'classification'   => $this->editClassify,
        ], $this->editPhoto);

        $this->editModal = false;
        $this->success('Asset updated.');
    }

    public function deleteAsset(int $id, AssetService $assetService): void
    {
        $assetService->deleteAsset($id);
        $this->success('Asset deleted.');
    }
};
?>

<div>
    <x-header title="Asset Management" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search asset..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Asset" icon="o-plus" class="btn-primary" wire:click="openAdd" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table
            :headers="[
                ['key' => 'asset_no',      'label' => 'Asset No'],
                ['key' => 'machine_name',  'label' => 'Machine Name'],
                ['key' => 'line_name',     'label' => 'Line'],
                ['key' => 'maker',         'label' => 'Maker'],
                ['key' => 'machine_rank',  'label' => 'Rank'],
                ['key' => 'manufacture_year', 'label' => 'Year'],
            ]"
            :rows="$assets"
            with-pagination
        >
            @scope('cell_asset_no', $asset)
                <a href="/assets/{{ $asset->id }}" wire:navigate class="link link-primary font-mono">{{ $asset->asset_no }}</a>
            @endscope

            @scope('cell_machine_rank', $asset)
                @if($asset->machine_rank)
                    <x-badge label="{{ $asset->machine_rank }}"
                        class="{{
                            match($asset->machine_rank) {
                                'A' => 'badge-error',
                                'B' => 'badge-warning',
                                'C' => 'badge-info',
                                'D' => 'badge-success',
                                default => 'badge-ghost'
                            }
                        }}" />
                @endif
            @endscope

            @scope('actions', $asset)
                <div class="flex gap-1">
                    <x-button icon="o-pencil-square" class="btn-ghost btn-xs" wire:click="openEdit({{ $asset->id }})" />
                    <x-button icon="o-trash" class="btn-ghost btn-xs text-error"
                        wire:click="deleteAsset({{ $asset->id }})"
                        wire:confirm="Delete asset {{ $asset->asset_no }}? This cannot be undone." />
                </div>
            @endscope
        </x-table>
    </x-card>

    {{-- Add Modal --}}
    <x-modal wire:model="addModal" title="New Asset" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Asset No" wire:model="addAssetNo" placeholder="e.g. 13XQID014" class="col-span-2" />
            <x-select label="Line Name" wire:model="addLineName" :options="$lineOptions" placeholder="Select line" option-value="id" option-label="name" />
            <x-input label="Machine Name" wire:model="addMachineName" class="col-span-2" />
            <x-input label="Maker" wire:model="addMaker" />
            <x-select label="Rank" wire:model="addRank"
                :options="[['id'=>'A','name'=>'A'],['id'=>'B','name'=>'B'],['id'=>'C','name'=>'C'],['id'=>'D','name'=>'D'],['id'=>'E','name'=>'E']]"
                option-value="id" option-label="name" placeholder="Select rank" />
            <x-input label="Year" wire:model="addYear" type="number" min="1945" max="2100" />
            <x-textarea label="Classification" wire:model="addClassify" class="col-span-2" rows="2" />
            <div class="col-span-2">
                <label class="label text-sm font-semibold">Machine Photo</label>
                <input type="file" wire:model="addPhoto" accept="image/jpeg,image/png,image/jpg"
                       class="file-input file-input-bordered file-input-sm w-full" />
                @error('addPhoto') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
            <x-button label="Submit" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
        </x-slot:actions>
    </x-modal>

    {{-- Edit Modal --}}
    <x-modal wire:model="editModal" title="Edit Asset" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Asset No" wire:model="editAssetNo" readonly class="col-span-2 opacity-60" />
            <x-select label="Line Name" wire:model="editLineName" :options="$lineOptions" option-value="id" option-label="name" placeholder="Select line" />
            <x-input label="Machine Name" wire:model="editMachineName" class="col-span-2" />
            <x-input label="Maker" wire:model="editMaker" />
            <x-select label="Rank" wire:model="editRank"
                :options="[['id'=>'A','name'=>'A'],['id'=>'B','name'=>'B'],['id'=>'C','name'=>'C'],['id'=>'D','name'=>'D'],['id'=>'E','name'=>'E']]"
                option-value="id" option-label="name" placeholder="Select rank" />
            <x-input label="Year" wire:model="editYear" type="number" min="1945" max="2100" />
            <x-textarea label="Classification" wire:model="editClassify" class="col-span-2" rows="2" />
            <div class="col-span-2">
                <label class="label text-sm font-semibold">Replace Photo (optional)</label>
                <input type="file" wire:model="editPhoto" accept="image/jpeg,image/png,image/jpg"
                       class="file-input file-input-bordered file-input-sm w-full" />
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
            <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
        </x-slot:actions>
    </x-modal>
</div>
