<?php

namespace App\Livewire\Traits;

use App\Models\Asset;
use Illuminate\Support\Collection;

trait WithAssetSelection
{
    public ?int $asset_id = null;
    public string $LineName = '';
    public string $MachineNo = '';
    public string $MachineName = '';
    public array $lineNames = [];
    public Collection $machines;

    public function mountWithAssetSelection(): void
    {
        $lines = Asset::whereNotNull('line_name')->distinct()->pluck('line_name');
        $this->lineNames = $lines->map(fn($line) => ['name' => $line])->toArray();
        
        if ($this->LineName) {
            $this->machines = Asset::where(['line_name' => $this->LineName])->get();
            $matchedAsset = $this->machines->first(fn($asset) => $asset->asset_no === $this->MachineNo);
            if ($matchedAsset) {
                $this->asset_id = $matchedAsset->id;
            }
        } else {
            $this->machines = collect();
        }
    }

    public function searchLine(string $value = '')
    {
        if (empty($value)) {
            $lines = Asset::whereNotNull('line_name')->distinct()->pluck('line_name');
        } else {
            $lines = Asset::whereNotNull('line_name')
                ->where('line_name', 'like', "%{$value}%")
                ->distinct()
                ->pluck('line_name');
        }
        $this->lineNames = $lines->map(fn($line) => ['name' => $line])->toArray();
    }

    public function searchMachine(string $value = '')
    {
        if ($this->LineName) {
            $query = Asset::where(['line_name' => $this->LineName]);
            if (!empty($value)) {
                $query->where('machine_name', 'like', "%{$value}%");
            }
            $this->machines = $query->get();
        } else {
            $this->machines = collect();
        }
    }

    public function updatedLineName($value)
    {
        $this->asset_id = null;
        $this->MachineNo = '';
        $this->MachineName = '';
        
        if ($value) {
            $this->machines = Asset::where(['line_name' => $value])->get();
        } else {
            $this->machines = collect();
        }
    }

    public function updatedAssetId($value)
    {
        if ($value) {
            $asset = Asset::find($value);
            if ($asset) {
                $this->MachineNo = $asset->asset_no ?? '';
                $this->MachineName = $asset->machine_name ?? '';
            }
        } else {
            $this->MachineNo = '';
            $this->MachineName = '';
        }
    }
}
