<?php

use App\Services\SparePartRepairService;
use App\Models\User;
use App\Models\SparePart;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use App\Livewire\SparePart\Repair\Traits\WithRepairModals;
use App\Livewire\Traits\WithSpareparts;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination, WithFileUploads, Toast, WithRepairModals, WithSpareparts;

    public string $search = '';
    
    public function mount()
    {
        $this->mountWithRepairModals();
        $this->mountWithSpareparts();
    }

    #[Computed]
    public function users()
    {
        return User::select('id', 'name')->get();
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
        return app(SparePartRepairService::class)->getPaginated(10, $this->search);
    }

    public function goToDetail($id)
    {
        $this->redirectRoute('spare-parts.repair.show', ['id' => $id], navigate: true);
    }
};
?>

<div>
    @include('livewire.spare-part.repair.partials.header')
    @include('livewire.spare-part.repair.partials.table')
    @include('livewire.spare-part.repair.partials.modal-form')
</div>
