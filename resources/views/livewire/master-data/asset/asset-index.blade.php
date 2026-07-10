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
        $this->reset(['addAssetNo','addLineName','addMachineName','addMaker','addRank','addYear','addPhoto']);
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
    {{-- Header --}}
    @include('livewire.master-data.asset.partials.index.header')

    {{-- Main Table --}}
    @include('livewire.master-data.asset.partials.index.table')

    {{-- Modals --}}
    @include('livewire.master-data.asset.partials.index.add-modal')
    @include('livewire.master-data.asset.partials.index.edit-modal')
</div>
