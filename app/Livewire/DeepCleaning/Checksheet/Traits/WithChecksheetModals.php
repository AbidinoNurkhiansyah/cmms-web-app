<?php

namespace App\Livewire\DeepCleaning\Checksheet\Traits;

use App\Models\TpmChecksheet;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Attributes\Computed;

trait WithChecksheetModals
{
    // Generate Modal state
    public $generateModal = false;
    public $generateMachine = '';
    public $generateYear = '';
    public $generateTypes = [];

    // Input/Edit Modal state
    public $inputModal = false;
    public $inputMonth = '';
    public $inputRemark = '';
    public $inputGataMm = null;
    public $inputClampKn = null;
    public $inputRunOutKelurusan = null;
    public $inputRunOutPutaran = null;

    public function updatedInputMonth($value)
    {
        if ($value && $this->selectedType && $this->selectedYear && $this->selectedMachine) {
            $record = TpmChecksheet::where('type', $this->selectedType)
                ->where('machineNo', $this->selectedMachine)
                ->whereYear('checked_date', $this->selectedYear)
                ->whereMonth('checked_date', $value)
                ->first();

            if ($record) {
                $this->inputRemark = $record->remark;
                $this->inputGataMm = $record->gata_mm;
                $this->inputClampKn = $record->clamp_kN;
                $this->inputRunOutKelurusan = $record->run_out_kelurusan;
                $this->inputRunOutPutaran = $record->run_out_putaran;
            } else {
                $this->resetInputFields();
            }
        } else {
            $this->resetInputFields();
        }
    }

    private function resetInputFields()
    {
        $this->inputRemark = '';
        $this->inputGataMm = null;
        $this->inputClampKn = null;
        $this->inputRunOutKelurusan = null;
        $this->inputRunOutPutaran = null;
    }

    #[Computed]
    public function allMachiningCenters()
    {
        // For the generator modal
        return Asset::where('machine_name', 'LIKE', '%MACHINING CENTER%')
            ->get()
            ->map(function($asset) {
                return [
                    'id' => $asset->asset_no,
                    'name' => $asset->line_name . ' - ' . $asset->machine_name . ' (' . $asset->asset_no . ')'
                ];
            });
    }
    
    #[Computed]
    public function generateYearOptions()
    {
        $current = (int)date('Y');
        $years = [];
        for ($i = $current; $i >= $current - 10; $i--) {
            $years[] = ['id' => $i, 'name' => (string)$i];
        }
        return $years;
    }

    #[Computed]
    public function filledMonths()
    {
        if (!$this->selectedType || !$this->selectedYear || !$this->selectedMachine) {
            return [];
        }
        
        // Months where data is NOT NULL for the required field
        $fieldToCheck = 'remark'; // Fallback
        if ($this->selectedType === 'CLAMP ARBOR') $fieldToCheck = 'clamp_kN';
        elseif ($this->selectedType === 'GATA-GATA') $fieldToCheck = 'gata_mm';
        elseif ($this->selectedType === 'RUN OUT') $fieldToCheck = 'run_out_kelurusan';
        
        return TpmChecksheet::where('type', $this->selectedType)
            ->where('machineNo', $this->selectedMachine)
            ->whereYear('checked_date', $this->selectedYear)
            ->whereNotNull($fieldToCheck)
            ->get()
            ->map(fn($item) => (int) Carbon::parse($item->checked_date)->format('n'))
            ->toArray();
    }

    public function generateChecksheet()
    {
        $this->validate([
            'generateMachine' => 'required',
            'generateYear' => 'required',
            'generateTypes' => 'required|array|min:1',
        ]);

        $insertedCount = 0;

        foreach ($this->generateTypes as $type) {
            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::create($this->generateYear, $month, 1)->format('Y-m-d');
                
                // Only create if not exists
                $exists = TpmChecksheet::where('type', $type)
                    ->where('machineNo', $this->generateMachine)
                    ->where('checked_date', $date)
                    ->exists();

                if (!$exists) {
                    TpmChecksheet::create([
                        'type' => $type,
                        'machineNo' => $this->generateMachine,
                        'checked_date' => $date,
                    ]);
                    $insertedCount++;
                }
            }
        }

        $this->success("Successfully generated $insertedCount months of checksheet templates.");
        $this->generateModal = false;
        
        // Reset generator fields
        $this->generateMachine = '';
        $this->generateYear = '';
        $this->generateTypes = [];

        // Update charts in case the generated data matches the currently viewed filters
        $this->updateCharts();
    }

    public function saveInputData()
    {
        $this->validate([
            'inputMonth' => 'required|numeric|min:1|max:12',
            'inputGataMm' => 'required_if:selectedType,GATA-GATA',
            'inputClampKn' => 'required_if:selectedType,CLAMP ARBOR',
            'inputRunOutKelurusan' => 'required_if:selectedType,RUN OUT',
            'inputRunOutPutaran' => 'required_if:selectedType,RUN OUT',
        ]);

        $date = Carbon::create($this->selectedYear, $this->inputMonth, 1)->format('Y-m-d');
        
        $record = TpmChecksheet::firstOrNew([
            'type' => $this->selectedType,
            'machineNo' => $this->selectedMachine,
            'checked_date' => $date,
        ]);

        $record->pic = Auth::user()->name ?? 'System';
        $record->remark = $this->inputRemark;

        if ($this->selectedType === 'GATA-GATA') {
            $record->gata_mm = $this->inputGataMm;
        } elseif ($this->selectedType === 'CLAMP ARBOR') {
            $record->clamp_kN = $this->inputClampKn;
        } elseif ($this->selectedType === 'RUN OUT') {
            $record->run_out_kelurusan = $this->inputRunOutKelurusan;
            $record->run_out_putaran = $this->inputRunOutPutaran;
        }

        $record->save();

        $this->success('Data saved successfully!');
        $this->inputModal = false;
        $this->resetInputFields();
        $this->inputMonth = '';

        // Update charts to reflect new data
        $this->updateCharts();
    }
}
