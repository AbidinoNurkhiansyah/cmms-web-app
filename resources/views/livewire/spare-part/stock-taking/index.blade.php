<?php

use App\Services\SparePartStockTakingService;
use App\Models\SparePart;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Livewire\SparePart\StockTaking\Traits\WithStockTakingModals;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination, Toast, WithStockTakingModals;

    public string $search = '';
    
    public function mount()
    {
        $this->mountWithStockTakingModals();
    }

    #[Computed]
    public function spareParts()
    {
        return SparePart::select('id', 'part_number', 'part_name', 'price_idr', 'last_stock')->get();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search']);
        $this->resetPage();
    }

    #[Computed]
    public function records()
    {
        return app(SparePartStockTakingService::class)->getAggregatedData(10, $this->search);
    }
};
?>

<div>
    @include('livewire.spare-part.stock-taking.partials.header')
    @include('livewire.spare-part.stock-taking.partials.table')
    @include('livewire.spare-part.stock-taking.partials.modal-form')
</div>
