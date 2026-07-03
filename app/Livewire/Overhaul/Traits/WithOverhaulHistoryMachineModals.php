<?php

namespace App\Livewire\Overhaul\Traits;

use App\Services\OverhaulHistoryMachineService;
use App\Livewire\Traits\WithAssetSelection;
use App\Livewire\Traits\WithSpareparts;
use App\Models\OverhaulHistoryMachine;

trait WithOverhaulHistoryMachineModals
{
    use WithAssetSelection, WithSpareparts;

    public bool $addModal = false;
    public bool $editModal = false;


    public ?int $formId = null;
    
    // Main Form Data
    public string $tgl_berlaku = '';
    public string $row_date = '';
    public string $problem = '';
    public string $cause = '';
    public string $corrective_action = '';
    public $pic_id = null;
    public string $frequency = '';
    
    // Part change is stored as JSON, but we manage it via WithSpareparts trait (array of IDs)
    public array $part_change = [];

    public function mountWithOverhaulHistoryMachineModals()
    {
        $this->tgl_berlaku = date('Y-m-d');
        $this->row_date = date('Y-m-d');
        $this->mountWithAssetSelection();
        $this->mountWithSpareparts();
    }

    public function resetForm()
    {
        $this->reset([
            'formId', 'tgl_berlaku', 'row_date', 'LineName', 'MachineNo', 'MachineName', 
            'asset_id', 'problem', 'cause', 'corrective_action', 'pic_id', 'frequency', 'part_change'
        ]);
        $this->tgl_berlaku = date('Y-m-d');
        $this->row_date = date('Y-m-d');
        $this->usedSpareparts = [['spare_part_id' => '', 'qty' => 1]];
    }

    public function openAdd()
    {
        $this->resetForm();
        $this->addModal = true;
    }

    public function saveAdd(OverhaulHistoryMachineService $service)
    {
        $this->validate([
            'asset_id' => 'required',
            'tgl_berlaku' => 'required|date',
            'row_date' => 'required|date',
        ]);
        
        $validSpareparts = array_filter($this->usedSpareparts, fn($s) => !empty($s['spare_part_id']));

        $data = [
            'asset_id' => $this->asset_id,
            'tgl_berlaku' => $this->tgl_berlaku,
            'row_date' => $this->row_date,
            'problem' => $this->problem,
            'cause' => $this->cause,
            'corrective_action' => $this->corrective_action,
            'part_change' => $validSpareparts, // JSON encoded by model cast
            'pic_id' => $this->pic_id,
            'frequency' => $this->frequency,
        ];

        $service->create($data);

        $this->addModal = false;
        $this->success('History Machine record created successfully.');
    }

    public function openEdit(int $id)
    {
        $this->resetForm();
        
        $record = OverhaulHistoryMachine::with('asset')->find($id);
        if (!$record) {
            $this->error('Record not found.');
            return;
        }

        $this->formId = $id;
        $this->tgl_berlaku = $record->tgl_berlaku ? $record->tgl_berlaku->format('Y-m-d') : '';
        $this->row_date = $record->row_date ? $record->row_date->format('Y-m-d') : '';
        
        if ($record->asset) {
            $this->LineName = $record->asset->line_name ?? '';
            $this->MachineNo = $record->asset->asset_no ?? '';
            $this->MachineName = $record->asset->machine_name ?? '';
            $this->asset_id = $record->asset_id;
        }
        
        $this->mountWithAssetSelection();
        $this->mountWithSpareparts();
        
        $this->problem = $record->problem ?? '';
        $this->cause = $record->cause ?? '';
        $this->corrective_action = $record->corrective_action ?? '';
        
        $this->usedSpareparts = is_array($record->part_change) && count($record->part_change) > 0 
                                ? $record->part_change 
                                : [['spare_part_id' => '', 'qty' => 1]];
                                
        $this->pic_id = $record->pic_id;
        $this->frequency = $record->frequency ?? '';
        
        $this->editModal = true;
    }

    public function saveEdit(OverhaulHistoryMachineService $service)
    {
        $this->validate([
            'asset_id' => 'required',
            'tgl_berlaku' => 'required|date',
            'row_date' => 'required|date',
        ]);

        $validSpareparts = array_filter($this->usedSpareparts, fn($s) => !empty($s['spare_part_id']));

        $data = [
            'asset_id' => $this->asset_id,
            'tgl_berlaku' => $this->tgl_berlaku,
            'row_date' => $this->row_date,
            'problem' => $this->problem,
            'cause' => $this->cause,
            'corrective_action' => $this->corrective_action,
            'part_change' => $validSpareparts,
            'pic_id' => $this->pic_id,
            'frequency' => $this->frequency,
        ];

        $service->update($this->formId, $data);

        $this->editModal = false;
        $this->success('History Machine record updated successfully.');
    }

    public function deleteRecord(int $id, OverhaulHistoryMachineService $service)
    {
        $service->delete($id);
        $this->success('Record deleted successfully.');
    }

    public function openDetail(int $id)
    {
        return $this->redirect(route('overhaul.history-machine.show', $id), navigate: true);
    }
}
