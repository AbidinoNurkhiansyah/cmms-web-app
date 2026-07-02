<?php

namespace App\Livewire\Overhaul\Traits;

use App\Services\OverhaulService;
use App\Livewire\Traits\WithAssetSelection;

trait WithOverhaulModals
{
    use WithAssetSelection;

    public bool $addModal = false;
    public bool $editModal = false;

    public ?int $formId = null;
    
    // Main Form Data
    public string $date = '';
    public string $start_time = '';
    public string $end_time = '';
    public string $PIC = '';
    public string $pic1 = '';
    public string $pic2 = '';
    public string $pic3 = '';
    public string $problem = '';
    public string $description = '';
    public $repair_time = null;
    public $work_time = null;

    public string $explanation = '';
    public string $next_improvement = '';
    public string $yokotenkai = '';

    // Uploaded photos
    public $photo_before_1 = null;
    public $photo_after_1 = null;
    public $photo_before_2 = null;
    public $photo_after_2 = null;

    // Existing photos (for preview)
    public $existing_photo_before_1 = null;
    public $existing_photo_after_1 = null;
    public $existing_photo_before_2 = null;
    public $existing_photo_after_2 = null;

    // Relational Arrays
    public array $steps = [];
    public array $spareparts = [];

    public function mountWithOverhaulModals()
    {
        $this->date = date('Y-m-d');
        // Initial empty row for UI
        $this->addStep();
        $this->addSparepart();
        $this->mountWithAssetSelection();
    }

    public function addStep()
    {
        $this->steps[] = ['step_repair' => '', 'minutes' => '', 'obstacle' => ''];
    }

    public function removeStep($index)
    {
        unset($this->steps[$index]);
        $this->steps = array_values($this->steps);
    }

    public function addSparepart()
    {
        $this->spareparts[] = ['type' => '', 'qty' => '', 'maker' => '', 'remarks' => ''];
    }

    public function removeSparepart($index)
    {
        unset($this->spareparts[$index]);
        $this->spareparts = array_values($this->spareparts);
    }

    public function calculateTime()
    {
        if ($this->start_time && $this->end_time) {
            $start = \Carbon\Carbon::parse($this->start_time);
            $end = \Carbon\Carbon::parse($this->end_time);
            $diffInMinutes = $start->diffInMinutes($end);
            $this->repair_time = $diffInMinutes;
        }
    }

    public function resetForm()
    {
        $this->reset([
            'formId', 'date', 'start_time', 'end_time', 'LineName', 'MachineNo', 'MachineName', 
            'asset_id', 'PIC', 'pic1', 'pic2', 'pic3', 'problem', 'description', 'repair_time', 'work_time',
            'explanation', 'next_improvement', 'yokotenkai',
            'photo_before_1', 'photo_after_1', 'photo_before_2', 'photo_after_2',
            'existing_photo_before_1', 'existing_photo_after_1', 'existing_photo_before_2', 'existing_photo_after_2'
        ]);
        $this->date = date('Y-m-d');
        $this->steps = [];
        $this->spareparts = [];
        $this->addStep();
        $this->addSparepart();
    }

    public function openAdd()
    {
        $this->resetForm();
        $this->addModal = true;
    }

    public function saveAdd(OverhaulService $service)
    {
        $this->validate([
            'date' => 'required|date',
            'LineName' => 'required|string',
            'asset_id' => 'required',
        ]);

        $this->calculateTime();

        $data = [
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'LineName' => $this->LineName,
            'MachineNo' => $this->MachineNo,
            'MachineName' => $this->MachineName,
            'asset_no' => $this->MachineNo,
            'PIC' => $this->PIC,
            'pic1' => $this->pic1,
            'pic2' => $this->pic2,
            'pic3' => $this->pic3,
            'problem' => $this->problem,
            'description' => $this->description,
            'repair_time' => $this->repair_time,
            'work_time' => $this->work_time,
            'explanation' => $this->explanation,
            'next_improvement' => $this->next_improvement,
            'yokotenkai' => $this->yokotenkai,
        ];

        // filter empty arrays
        $validSteps = array_filter($this->steps, fn($s) => !empty($s['step_repair']));
        $validSpareparts = array_filter($this->spareparts, fn($s) => !empty($s['type']));

        $relations = [
            'steps' => $validSteps,
            'spareparts' => $validSpareparts,
        ];

        $photos = [
            'photo_before_1' => $this->photo_before_1,
            'photo_after_1' => $this->photo_after_1,
            'photo_before_2' => $this->photo_before_2,
            'photo_after_2' => $this->photo_after_2,
        ];

        $service->create($data, $relations, $photos);

        $this->addModal = false;
        $this->success('Overhaul Report created successfully.');
    }

    public function openEdit(int $id)
    {
        $this->resetForm();
        
        $record = \App\Models\Overhaul::with(['steps', 'spareparts'])->find($id);
        if (!$record) {
            $this->error('Record not found.');
            return;
        }

        $this->formId = $id;
        $this->date = $record->date ? $record->date->format('Y-m-d') : '';
        $this->start_time = $record->start_time ? $record->start_time->format('Y-m-d\TH:i') : '';
        $this->end_time = $record->end_time ? $record->end_time->format('Y-m-d\TH:i') : '';
        $this->LineName = $record->LineName ?? '';
        $this->MachineNo = $record->asset_no ?? $record->MachineNo ?? '';
        $this->MachineName = $record->MachineName ?? '';
        
        $this->mountWithAssetSelection();
        
        $this->PIC = $record->PIC ?? '';
        $this->pic1 = $record->pic1 ?? '';
        $this->pic2 = $record->pic2 ?? '';
        $this->pic3 = $record->pic3 ?? '';
        $this->problem = $record->problem ?? '';
        $this->description = $record->description ?? '';
        $this->repair_time = $record->repair_time;
        $this->work_time = $record->work_time;
        $this->explanation = $record->explanation ?? '';
        $this->next_improvement = $record->next_improvement ?? '';
        $this->yokotenkai = $record->yokotenkai ?? '';

        $this->existing_photo_before_1 = $record->photo_before_1;
        $this->existing_photo_after_1 = $record->photo_after_1;
        $this->existing_photo_before_2 = $record->photo_before_2;
        $this->existing_photo_after_2 = $record->photo_after_2;

        $this->steps = $record->steps->map(fn($s) => ['step_repair' => $s->step_repair, 'minutes' => $s->minutes, 'obstacle' => $s->obstacle])->toArray();
        if (empty($this->steps)) $this->addStep();

        $this->spareparts = $record->spareparts->map(fn($s) => ['type' => $s->type, 'qty' => $s->qty, 'maker' => $s->maker, 'remarks' => $s->remarks])->toArray();
        if (empty($this->spareparts)) $this->addSparepart();

        $this->editModal = true;
    }

    public function saveEdit(OverhaulService $service)
    {
        $this->validate([
            'date' => 'required|date',
            'LineName' => 'required|string',
            'asset_id' => 'required',
        ]);

        $this->calculateTime();

        $data = [
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'LineName' => $this->LineName,
            'MachineNo' => $this->MachineNo,
            'MachineName' => $this->MachineName,
            'asset_no' => $this->MachineNo,
            'PIC' => $this->PIC,
            'pic1' => $this->pic1,
            'pic2' => $this->pic2,
            'pic3' => $this->pic3,
            'problem' => $this->problem,
            'description' => $this->description,
            'repair_time' => $this->repair_time,
            'work_time' => $this->work_time,
            'explanation' => $this->explanation,
            'next_improvement' => $this->next_improvement,
            'yokotenkai' => $this->yokotenkai,
        ];

        // filter empty arrays
        $validSteps = array_filter($this->steps, fn($s) => !empty($s['step_repair']));
        $validSpareparts = array_filter($this->spareparts, fn($s) => !empty($s['type']));

        $relations = [
            'steps' => $validSteps,
            'spareparts' => $validSpareparts,
        ];

        $photos = [
            'photo_before_1' => $this->photo_before_1,
            'photo_after_1' => $this->photo_after_1,
            'photo_before_2' => $this->photo_before_2,
            'photo_after_2' => $this->photo_after_2,
        ];

        $service->update($this->formId, $data, $relations, $photos);

        $this->editModal = false;
        $this->success('Overhaul Report updated successfully.');
    }

    public function deleteRecord(int $id, OverhaulService $service)
    {
        $service->delete($id);
        $this->success('Overhaul Report deleted.');
    }
}
