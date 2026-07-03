<?php

use App\Services\OverhaulHistoryMachineService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Livewire\Overhaul\Traits\WithOverhaulHistoryMachineModals;
use Livewire\Attributes\Computed;
use App\Models\User;

new class extends Component {
    use WithPagination, Toast, WithOverhaulHistoryMachineModals;

    public string $search = '';
    public $filter_asset_id = null;
    public $filter_tgl_berlaku = null;
    
    public function mount()
    {
        $this->mountWithOverhaulHistoryMachineModals();
    }

    #[Computed]
    public function users()
    {
        return User::select('id', 'name')->get();
    }

    #[Computed]
    public function assets()
    {
        return \App\Models\Asset::select('id', 'machine_name')->get();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    
    public function updatedFilterAssetId(): void
    {
        $this->resetPage();
    }
    
    public function updatedFilterTglBerlaku(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filter_asset_id', 'filter_tgl_berlaku']);
        $this->resetPage();
    }

    #[Computed]
    public function records()
    {
        return app(OverhaulHistoryMachineService::class)->getPaginated(10, $this->search, [
            'asset_id' => $this->filter_asset_id,
            'tgl_berlaku' => $this->filter_tgl_berlaku,
        ]);
    }
};
?>

<div>
    @include('livewire.overhaul.history-machine.partials.header')
    @include('livewire.overhaul.history-machine.partials.table')
    @include('livewire.overhaul.history-machine.partials.modal-form')
</div>
