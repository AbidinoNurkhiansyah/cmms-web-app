<?php

use App\Services\OverhaulService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use App\Livewire\Overhaul\Traits\WithOverhaulModals;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination, WithFileUploads, Toast, WithOverhaulModals;

    public string $search = '';

    public function mount()
    {
        $this->mountWithOverhaulModals();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function records()
    {
        return app(OverhaulService::class)->getPaginated(15, $this->search);
    }
};
?>

<div>
    @include('livewire.overhaul.partials.header')
    @include('livewire.overhaul.partials.table')
    @include('livewire.overhaul.partials.modal-form')
</div>
