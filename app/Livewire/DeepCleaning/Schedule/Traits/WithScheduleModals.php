<?php

namespace App\Livewire\DeepCleaning\Schedule\Traits;

use Carbon\Carbon;
use App\Models\DeepCleaningSchedule;
use App\Models\DeepCleaningMachineItem;
use App\Models\DeepCleaning;

trait WithScheduleModals
{
    public function openGenerateModal()
    {
        $this->generateMonthYear = $this->selectedMonthYear ?: now()->format('Y-m');
        $this->generateTab = 'planning';
        $this->generateModal = true;
    }

    public function addToSchedule($line, $machine, $machineNo)
    {
        $parts = explode('-', $this->generateMonthYear);
        $planDate = Carbon::create($parts[0], $parts[1], 1)->format('Y-m-d');

        DeepCleaningSchedule::create([
            'planDate' => $planDate,
            'LineName' => $line,
            'NameMachine' => $machine,
            'machine_no' => $machineNo,
            'items' => [],
            'is_approved' => false,
            'postponed' => false,
        ]);

        $this->success("Added $machine to schedule.");
    }

    public function togglePostpone($id)
    {
        $schedule = DeepCleaningSchedule::find($id);
        if ($schedule) {
            $schedule->postponed = !$schedule->postponed;
            $schedule->save();
            $status = $schedule->postponed ? 'Postponed' : 'Active';
            $this->success("Schedule status changed to $status.");
        }
    }

    // Keep this for any existing standalone calls, though UI now uses modal
    public function updateActDate($id, $date)
    {
        $schedule = DeepCleaningSchedule::find($id);
        if ($schedule && $date) {
            $schedule->act_date = $date;
            $schedule->save();
            $this->success("Actual date saved.");
        }
    }

    public function toggleReport($id)
    {
        $schedule = DeepCleaningSchedule::find($id);
        if (!$schedule)
            return;

        if (!$schedule->act_date && !$schedule->is_approved) {
            $this->error("Please set Actual Date first before reporting.");
            return;
        }

        $schedule->is_approved = !$schedule->is_approved;
        $schedule->save();

        if ($schedule->is_approved) {
            // Auto create Deep Cleaning Record
            $report = DeepCleaning::create([
                'Date' => $schedule->act_date,
                'LineName' => $schedule->LineName,
                'MachineNo' => $schedule->machine_no,
                'MachineName' => $schedule->NameMachine,
                'description' => 'TPM',
                'status' => 'Done',
                'pics' => []
            ]);

            // Copy items
            $results = $schedule->items ?? [];
            foreach ($results as $itemCheck => $resultValue) {
                $report->items()->create([
                    'itemcheck' => $itemCheck,
                    'action' => $resultValue,
                    'status' => 'Done',
                    'description' => 'TPM Checklist'
                ]);
            }

            $this->success("Report Approved and Record Created.");
        } else {
            $this->info("Report Approval Canceled.");
        }
    }

    public function openEditModal($id)
    {
        $schedule = DeepCleaningSchedule::find($id);
        if ($schedule) {
            $this->editingId = $id;
            $this->editPlanDate = $schedule->planDate ? $schedule->planDate->format('Y-m-d') : '';
            $this->LineName = $schedule->LineName;
            $this->updatedLineName($this->LineName);
            $this->asset_id = $this->machines->firstWhere('asset_no', $schedule->machine_no)?->id;
            $this->updatedAssetId($this->asset_id);
            $this->editModal = true;
        }
    }

    public function saveEdit()
    {
        $this->validate([
            'editPlanDate' => 'required|date',
            'LineName' => 'required|string',
            'MachineName' => 'required|string',
        ]);

        $schedule = DeepCleaningSchedule::find($this->editingId);
        if ($schedule) {
            $schedule->update([
                'planDate' => $this->editPlanDate,
                'LineName' => $this->LineName,
                'NameMachine' => $this->MachineName,
                'machine_no' => $this->MachineNo,
            ]);
            $this->success("Schedule updated.");
            $this->editModal = false;
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->deleteModal = true;
    }

    public function deleteSchedule()
    {
        if ($this->deleteId) {
            DeepCleaningSchedule::destroy($this->deleteId);
            $this->success('Schedule deleted.');
        }
        $this->deleteModal = false;
        $this->deleteId = null;
    }

    // --- ITEM CHECK LOGIC ---
    public function openItemCheckModal($id)
    {
        $schedule = DeepCleaningSchedule::find($id);
        if (!$schedule)
            return;

        $this->itemCheckScheduleId = $id;
        $this->scheduleItems = is_array($schedule->items) ? $schedule->items : [];
        $this->actDate = $schedule->act_date ? $schedule->act_date->format('Y-m-d') : '';
        $this->newItemCheck = '';
        $this->newStandard = '';
        $this->itemCheckTab = 'execute'; // Default tab

        $this->loadMachineItems($schedule->LineName, $schedule->NameMachine);
        $this->itemCheckModal = true;
    }

    public function saveExecution()
    {
        $schedule = DeepCleaningSchedule::find($this->itemCheckScheduleId);
        if ($schedule) {
            $schedule->act_date = $this->actDate ?: null;
            $schedule->items = $this->scheduleItems;
            $schedule->save();
            $this->success("Schedule execution saved.");
        }
    }

    public function approveSchedule()
    {
        $schedule = DeepCleaningSchedule::find($this->itemCheckScheduleId);
        if (!$schedule)
            return;

        if (!$this->actDate) {
            $this->error("Please set Actual Date first before reporting.");
            return;
        }

        $schedule->act_date = $this->actDate;
        $schedule->items = $this->scheduleItems;
        $schedule->is_approved = true;
        $schedule->save();

        // Auto create Deep Cleaning Record
        $report = DeepCleaning::create([
            'Date' => $schedule->act_date,
            'LineName' => $schedule->LineName,
            'MachineNo' => $schedule->machine_no,
            'MachineName' => $schedule->NameMachine,
            'description' => 'TPM',
            'status' => 'Done',
            'pics' => []
        ]);

        // Copy items
        $results = $schedule->items ?? [];
        foreach ($results as $itemCheck => $resultValue) {
            $report->items()->create([
                'itemcheck' => $itemCheck,
                'action' => $resultValue,
                'status' => 'Done',
                'description' => 'TPM Checklist'
            ]);
        }

        $this->success("Report Approved and Record Created.");
        $this->itemCheckModal = false;
    }

    public function loadMachineItems($line, $machine)
    {
        $this->machineItems = DeepCleaningMachineItem::where('lineName', $line)
            ->where('machineName', $machine)
            ->get()
            ->toArray();
    }

    public function saveNewMachineItem()
    {
        $this->validate([
            'newItemCheck' => 'required|string',
            'newStandard' => 'required|string',
        ]);

        $schedule = DeepCleaningSchedule::find($this->itemCheckScheduleId);
        if ($schedule) {
            DeepCleaningMachineItem::create([
                'lineName' => $schedule->LineName,
                'machineName' => $schedule->NameMachine,
                'itemCheck' => $this->newItemCheck,
                'standard' => $this->newStandard,
            ]);
            $this->success("Machine Item added.");
            $this->loadMachineItems($schedule->LineName, $schedule->NameMachine);
            $this->newItemCheck = '';
            $this->newStandard = '';
        }
    }

    public function deleteMachineItem($id)
    {
        DeepCleaningMachineItem::destroy($id);
        $this->success("Machine Item deleted.");
        $schedule = DeepCleaningSchedule::find($this->itemCheckScheduleId);
        if ($schedule) {
            $this->loadMachineItems($schedule->LineName, $schedule->NameMachine);
        }
    }

    public function updateItemResult($itemCheck, $resultValue)
    {
        $this->scheduleItems[$itemCheck] = $resultValue;
        $schedule = DeepCleaningSchedule::find($this->itemCheckScheduleId);
        if ($schedule) {
            $schedule->items = $this->scheduleItems;
            $schedule->save();
        }
    }

    public function toggleItemStatus($itemCheck)
    {
        if (isset($this->scheduleItems[$itemCheck]) && $this->scheduleItems[$itemCheck] !== '') {
            unset($this->scheduleItems[$itemCheck]);
        } else {
            $this->scheduleItems[$itemCheck] = 'Done'; // default value
        }

        $schedule = DeepCleaningSchedule::find($this->itemCheckScheduleId);
        if ($schedule) {
            $schedule->items = $this->scheduleItems;
            $schedule->save();
        }
    }

}

