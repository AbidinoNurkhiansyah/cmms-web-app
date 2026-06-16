<?php

use App\Services\CartyService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, Toast;

    public string $search = '';
    public string $statusFilter = '';
    public bool $deleteModal = false;
    public ?int $deleteId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function with(CartyService $service): array
    {
        $records = $service->getPaginated(15, $this->search, $this->statusFilter);
        $records->getCollection()->transform(function($item, $key) use ($records) {
            $item->index = $records->firstItem() + $key;
            return $item;
        });

        return [
            'records' => $records,
        ];
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->deleteModal = true;
    }

    public function deleteRecord(CartyService $service): void
    {
        if ($this->deleteId) {
            $service->delete($this->deleteId);
            $this->success('Carty Record deleted.');
        }
        $this->deleteModal = false;
        $this->deleteId = null;
    }
};
?>

<div>
    <x-header title="Carty / Maintenance" separator>
        <x-slot:actions>
            <div class="flex flex-row flex-wrap items-center gap-2">
                <x-select 
                    wire:model.live="statusFilter" 
                    :options="[['id'=>'','name'=>'All Status'],['id'=>'Open','name'=>'Open'],['id'=>'Close','name'=>'Close']]" 
                    option-value="id" option-label="name" 
                />
                <x-input placeholder="Search problem, line..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
                <x-button label="Add Carty" icon="o-plus" class="btn-primary" link="{{ route('maintenance.cardty.create') }}" />
            </div>
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table striped
            :headers="[
                ['key' => 'index',        'label' => '#'],
                ['key' => 'Date',         'label' => 'Date'],
                ['key' => 'LineName',     'label' => 'Line'],
                ['key' => 'MachineName',  'label' => 'Machine'],
                ['key' => 'Problem',      'label' => 'Problem'],
                ['key' => 'Status',       'label' => 'Status', 'class' => 'text-center'],
                ['key' => 'DownTime',     'label' => 'DownTime (m)', 'class' => 'text-center'],
                ['key' => 'PIC',          'label' => 'PIC'],
            ]"
            :rows="$records"
            with-pagination
        >
            @scope('cell_Date', $r)
                {{ $r->Date ? $r->Date->format('Y-m-d') : '-' }}
            @endscope

            @scope('cell_Status', $r)
                <x-badge label="{{ $r->Status }}" 
                    class="{{ 
                        match(strtolower($r->Status)) {
                            'open' => 'badge-error',
                            'close' => 'badge-success',
                            default => 'badge-ghost'
                        }
                    }}" 
                />
            @endscope

            @scope('actions', $r)
                <div class="flex gap-1">
                    <x-button icon="o-pencil-square" class="btn-ghost btn-xs" link="{{ route('maintenance.cardty.edit', $r->id) }}" />
                    <x-button icon="o-trash" class="btn-ghost btn-xs text-error" 
                        wire:click="confirmDelete({{ $r->id }})" />
                </div>
            @endscope
        </x-table>
    </x-card>

    <!-- Delete Confirmation Modal -->
    <x-modal wire:model="deleteModal" class="backdrop-blur">
        <div class="flex flex-col items-center justify-center text-center gap-4 py-4">
            <x-icon name="o-exclamation-triangle" class="w-16 h-16 text-error" />
            <div>
                <h3 class="font-bold text-lg">Hapus Record Ini?</h3>
                <p class="text-base-content/70 mt-2">Data yang sudah dihapus tidak dapat dikembalikan lagi. Anda yakin?</p>
            </div>
        </div>
        
        <x-slot:actions>
            <x-button label="Batal" wire:click="$set('deleteModal', false)" class="btn-ghost" />
            <x-button label="Ya, Hapus" wire:click="deleteRecord" class="btn-error" spinner="deleteRecord" />
        </x-slot:actions>
    </x-modal>
</div>
