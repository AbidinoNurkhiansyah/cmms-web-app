<?php

namespace App\Livewire\SparePart\StockTaking\Traits;

use App\Services\SparePartStockTakingService;

trait WithStockTakingModals
{
    public bool $isModalOpen = false;

    // Form fields
    public $date_stock;
    public $spare_part_id;
    public $in_qty = 0;
    public $out_qty = 0;
    public $last_stock = 0;
    public $check_stock = 0;
    public $remark;

    public function mountWithStockTakingModals()
    {
        $this->date_stock = date('Y-m-d');
    }

    public function rules()
    {
        return [
            'date_stock' => 'required|date',
            'spare_part_id' => 'required|exists:spare_parts,id',
            'in_qty' => 'required|integer|min:0',
            'out_qty' => 'required|integer|min:0',
            'last_stock' => 'required|integer|min:0',
            'check_stock' => 'required|integer|min:0',
            'remark' => 'nullable|string|max:255',
        ];
    }

    public function createStockTaking()
    {
        $this->resetValidation();
        $this->reset([
            'spare_part_id', 'in_qty', 'out_qty', 'last_stock', 'check_stock', 'remark'
        ]);
        $this->date_stock = date('Y-m-d');
        $this->isModalOpen = true;
    }

    public function saveStockTaking()
    {
        $validatedData = $this->validate();

        $service = app(SparePartStockTakingService::class);
        $service->store($validatedData);

        $this->success('Data Stock Taking berhasil ditambahkan.');
        $this->isModalOpen = false;
    }
}
