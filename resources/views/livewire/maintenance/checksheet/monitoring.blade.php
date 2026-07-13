<?php

use App\Services\ChecksheetService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, Toast;

    public string $search = '';

    // Modals
    public bool   $addModal             = false;
    public bool   $editModal            = false;

    // Form Fields
    public ?int   $formId               = null;
    public string $noDoc                = '';
    public string $date                 = '';
    public string $picSL                = '';
    public string $assetNo              = '';
    public string $itemsId              = '';
    public string $result               = 'OK';
    public string $approval_stl         = '';
    public bool   $apvProd              = false;
    public bool   $apvWeek              = false;
    public bool   $apvMonth             = false;
    public string $approval_mtc         = '';
    public string $keterangan           = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function with(ChecksheetService $service): array
    {
        return [
            'records' => $service->getPaginated(15, $this->search),
        ];
    }

    public function openAdd(): void
    {
        $this->reset(['formId','noDoc','date','picSL','assetNo','itemsId','result','approval_stl','apvProd','apvWeek','apvMonth','approval_mtc','keterangan']);
        $this->date = date('Y-m-d');
        $this->result = 'OK';
        $this->addModal = true;
    }

    public function saveAdd(ChecksheetService $service): void
    {
        $this->validate([
            'noDoc'   => 'required|string',
            'date'    => 'required|date',
            'assetNo' => 'required|string',
            'picSL'   => 'required|string',
        ]);

        $service->create([
            'noDoc'        => $this->noDoc,
            'date'         => $this->date,
            'picSL'        => $this->picSL,
            'assetNo'      => $this->assetNo,
            'itemsId'      => $this->itemsId,
            'result'       => $this->result,
            'approval_stl' => $this->approval_stl,
            'apvProd'      => $this->apvProd,
            'apvWeek'      => $this->apvWeek,
            'apvMonth'     => $this->apvMonth,
            'approval_mtc' => $this->approval_mtc,
            'keterangan'   => $this->keterangan,
        ]);

        $this->addModal = false;
        $this->success('Checksheet Created.');
    }

    public function openEdit(int $id, ChecksheetService $service): void
    {
        $record = $service->getPaginated()->getCollection()->firstWhere('id', $id);
        if(!$record) {
            $this->error('Record not found.');
            return;
        }

        $this->formId       = $id;
        $this->noDoc        = $record->noDoc ?? '';
        $this->date         = $record->date ? $record->date->format('Y-m-d') : '';
        $this->picSL        = $record->picSL ?? '';
        $this->assetNo      = $record->assetNo ?? '';
        $this->itemsId      = $record->itemsId ?? '';
        $this->result       = $record->result ?? 'OK';
        $this->approval_stl = $record->approval_stl ?? '';
        $this->apvProd      = $record->apvProd ?? false;
        $this->apvWeek      = $record->apvWeek ?? false;
        $this->apvMonth     = $record->apvMonth ?? false;
        $this->approval_mtc = $record->approval_mtc ?? '';
        $this->keterangan   = $record->keterangan ?? '';
        
        $this->editModal    = true;
    }

    public function saveEdit(ChecksheetService $service): void
    {
        $this->validate([
            'noDoc'   => 'required|string',
            'date'    => 'required|date',
            'assetNo' => 'required|string',
            'picSL'   => 'required|string',
        ]);

        $service->update($this->formId, [
            'noDoc'        => $this->noDoc,
            'date'         => $this->date,
            'picSL'        => $this->picSL,
            'assetNo'      => $this->assetNo,
            'itemsId'      => $this->itemsId,
            'result'       => $this->result,
            'approval_stl' => $this->approval_stl,
            'apvProd'      => $this->apvProd,
            'apvWeek'      => $this->apvWeek,
            'apvMonth'     => $this->apvMonth,
            'approval_mtc' => $this->approval_mtc,
            'keterangan'   => $this->keterangan,
        ]);

        $this->editModal = false;
        $this->success('Checksheet Updated.');
    }

    public function deleteRecord(int $id, ChecksheetService $service): void
    {
        $service->delete($id);
        $this->success('Checksheet deleted.');
    }
};
?>

<div>
    <x-header title="Checksheet Transactions" separator progress-indicator>
        <x-slot:middle class="!justify-end gap-2">
            <x-input placeholder="Search NoDoc, Asset..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Checksheet" icon="o-plus" class="btn-primary" wire:click="openAdd" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table
            :headers="[
                ['key' => 'noDoc',        'label' => 'No Doc'],
                ['key' => 'date',         'label' => 'Date'],
                ['key' => 'assetNo',      'label' => 'Asset No'],
                ['key' => 'picSL',        'label' => 'PIC SL'],
                ['key' => 'result',       'label' => 'Result'],
                ['key' => 'keterangan',   'label' => 'Description'],
            ]"
            :rows="$records"
            with-pagination
        >
            @scope('cell_date', $r)
                {{ $r->date ? $r->date->format('Y-m-d') : '-' }}
            @endscope

            @scope('cell_result', $r)
                <x-badge label="{{ $r->result }}" 
                    class="{{ 
                        match(strtoupper($r->result)) {
                            'OK' => 'badge-success',
                            'NG' => 'badge-error',
                            default => 'badge-ghost'
                        }
                    }}" 
                />
            @endscope

            @scope('actions', $r)
                <div class="flex gap-1">
                    <x-button icon="o-pencil-square" class="btn-ghost btn-xs" wire:click="openEdit({{ $r->id }})" />
                    <x-button icon="o-trash" class="btn-ghost btn-xs text-error" 
                        wire:click="deleteRecord({{ $r->id }})" 
                        wire:confirm="Delete this record? This cannot be undone." />
                </div>
            @endscope
        </x-table>
    </x-card>

    {{-- Add Modal --}}
    <x-modal wire:model="addModal" title="New Checksheet" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="No Doc" wire:model="noDoc" />
            <x-input label="Date" type="date" wire:model="date" />
            <x-input label="Asset No" wire:model="assetNo" />
            <x-input label="Items ID" wire:model="itemsId" />
            <x-input label="PIC SL" wire:model="picSL" />
            <x-select label="Result" wire:model="result"
                :options="[['id'=>'OK','name'=>'OK'],['id'=>'NG','name'=>'NG']]"
                option-value="id" option-label="name" />
            <x-textarea label="Keterangan" wire:model="keterangan" class="col-span-2" rows="2" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
            <x-button label="Submit" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
        </x-slot:actions>
    </x-modal>

    {{-- Edit Modal --}}
    <x-modal wire:model="editModal" title="Edit Checksheet" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="No Doc" wire:model="noDoc" />
            <x-input label="Date" type="date" wire:model="date" />
            <x-input label="Asset No" wire:model="assetNo" />
            <x-input label="Items ID" wire:model="itemsId" />
            <x-input label="PIC SL" wire:model="picSL" />
            <x-select label="Result" wire:model="result"
                :options="[['id'=>'OK','name'=>'OK'],['id'=>'NG','name'=>'NG']]"
                option-value="id" option-label="name" />
                
            <div class="col-span-2 mt-4 grid grid-cols-2 gap-3 border border-base-200 p-3 rounded">
                <div class="col-span-2 font-bold text-sm">Approvals</div>
                <x-checkbox label="Approval Prod" wire:model="apvProd" />
                <x-checkbox label="Approval Week" wire:model="apvWeek" />
                <x-checkbox label="Approval Month" wire:model="apvMonth" />
                <div class="col-span-2">
                    <x-input label="Approval STL" wire:model="approval_stl" />
                </div>
                <div class="col-span-2">
                    <x-input label="Approval MTC" wire:model="approval_mtc" />
                </div>
            </div>

            <x-textarea label="Keterangan" wire:model="keterangan" class="col-span-2" rows="2" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
            <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
        </x-slot:actions>
    </x-modal>
</div>
