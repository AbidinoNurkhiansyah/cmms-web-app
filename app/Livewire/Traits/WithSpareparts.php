<?php

namespace App\Livewire\Traits;

use App\Models\SparePart;
use Illuminate\Support\Collection;

trait WithSpareparts
{
    public Collection $spareparts;
    public array $usedSpareparts = []; // Dynamic Spareparts array
    // Legacy single field fallback
    public string $sparepartName = '';
    public ?int $sparepartQty = null;

    public function mountWithSpareparts(): void
    {
        $this->spareparts = SparePart::select('id', 'part_name')->get()->unique('part_name')->values();
        if (empty($this->usedSpareparts)) {
            $this->usedSpareparts = [['spare_part_id' => '', 'qty' => 1]];
        }
    }

    public function searchSparepart(string $value = '')
    {
        if (empty($value)) {
            $this->spareparts = SparePart::select('id', 'part_name')->get()->unique('part_name')->values();
        } else {
            $this->spareparts = SparePart::select('id', 'part_name')->where('part_name', 'like', "%{$value}%")->get()->unique('part_name')->values();
        }
    }

    public function addSparepart()
    {
        $this->usedSpareparts[] = ['spare_part_id' => '', 'qty' => 1];
    }

    public function removeSparepart($index)
    {
        unset($this->usedSpareparts[$index]);
        $this->usedSpareparts = array_values($this->usedSpareparts);
    }
}
