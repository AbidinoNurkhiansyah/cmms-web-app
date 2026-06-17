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
            <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-2">
                <!-- Mobile Row 1 / Desktop Col 1: Status Filter -->
                <div class="w-full sm:w-40">
                    <x-select 
                        wire:model.live="statusFilter" 
                        :options="[['id'=>'','name'=>'All Status'],['id'=>'Open','name'=>'Open'],['id'=>'Close','name'=>'Close']]" 
                        option-value="id" option-label="name" 
                    />
                </div>
                
                <!-- Mobile Row 2 / Desktop Col 2 & 3: Search and Add -->
                <div class="flex flex-row w-full sm:w-auto gap-2">
                    <div class="flex-1 sm:w-64">
                        <x-input placeholder="Search problem, line..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
                    </div>
                    @can('wr.create')
                        <div class="flex-none">
                            <x-button icon="o-plus" class="btn-primary" link="{{ route('maintenance.cardty.create') }}">
                                <span class="hidden sm:inline">Add Carty</span>
                            </x-button>
                        </div>
                    @endcan
                </div>
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
                ['key' => 'action',       'label' => 'Action', 'class' => 'text-center'],
            ]"
            :rows="$records"
            with-pagination
        >
            @scope('cell_Date', $r)
                {{ $r->Date ? $r->Date->format('Y-m-d') : '-' }}
            @endscope

            @scope('cell_Status', $r)
                @php
                    $status = strtolower($r->Status);
                    $isError = $status === 'open';
                    $isSuccess = $status === 'close';
                    
                    $bgColor = $isError ? 'bg-red-100 dark:bg-red-900/30' : ($isSuccess ? 'bg-green-100 dark:bg-green-900/30' : 'bg-gray-100 dark:bg-gray-800');
                    $textColor = $isError ? 'text-red-600 dark:text-red-400' : ($isSuccess ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400');
                    $dotColor = $isError ? 'bg-red-500' : ($isSuccess ? 'bg-green-500' : 'bg-gray-500');
                @endphp
                <div class="text-center">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $bgColor }} {{ $textColor }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                        {{ $r->Status }}
                    </div>
                </div>
            @endscope

            @scope('cell_PIC', $r)
                @if(is_array($r->pics) && count($r->pics) > 0)
                    {{ implode(', ', $r->pics) }}
                @else
                    {{ $r->PIC ?? '-' }}
                @endif
            @endscope

            @scope('cell_DownTime', $r)
                <div class="text-center">{{ $r->DownTime }}</div>
            @endscope

            @scope('cell_action', $r)
                <div class="flex gap-1 justify-center">
                    @can('wr.update')
                        <x-button icon="o-pencil-square" class="btn-ghost btn-xs" link="{{ route('maintenance.cardty.edit', $r->id) }}" />
                    @endcan
                    @can('wr.delete')
                        <x-button icon="o-trash" class="btn-ghost btn-xs text-error" 
                            wire:click="confirmDelete({{ $r->id }})" />
                    @endcan
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
