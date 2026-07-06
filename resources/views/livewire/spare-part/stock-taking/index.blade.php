<?php

use App\Services\SparePartStockTakingService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination, Toast;

    public string $search = '';

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
</div>
