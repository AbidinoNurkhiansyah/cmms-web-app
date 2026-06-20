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
        $this->spareparts = SparePart::all();
        if (empty($this->usedSpareparts)) {
            $this->usedSpareparts = [['spare_part_id' => '', 'qty' => 1]];
        }
    }

    public function searchSparepart(string $value = '')
    {
        if (empty($value)) {
            $this->spareparts = SparePart::all();
        } else {
            $this->spareparts = SparePart::where('part_name', 'like', "%{$value}%")->get();
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
