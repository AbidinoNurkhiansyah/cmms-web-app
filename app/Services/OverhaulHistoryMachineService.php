<?php

namespace App\Services;

use App\Models\OverhaulHistoryMachine;

class OverhaulHistoryMachineService
{
    public function getPaginated(int $perPage = 15, string $search = '', array $filters = [])
    {
        return OverhaulHistoryMachine::with(['asset', 'pic'])
            ->when($search, fn($q) => $q->whereHas('asset', function ($query) use ($search) {
                $query->where('asset_no', 'like', "%{$search}%")
                      ->orWhere('machine_name', 'like', "%{$search}%")
                      ->orWhere('line_name', 'like', "%{$search}%");
            })->orWhere('problem', 'like', "%{$search}%")
              ->orWhere('cause', 'like', "%{$search}%"))
            ->when(isset($filters['asset_id']) && $filters['asset_id'], fn($q) => $q->where('asset_id', $filters['asset_id']))
            ->when(isset($filters['tgl_berlaku']) && $filters['tgl_berlaku'], fn($q) => $q->whereDate('tgl_berlaku', $filters['tgl_berlaku']))
            ->orderByDesc('tgl_berlaku')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        // Handle part_change if it comes as an array of objects/IDs
        // the model casts part_change to array, so we can just pass it directly if it's an array
        return OverhaulHistoryMachine::create($data);
    }

    public function update(int $id, array $data)
    {
        $record = OverhaulHistoryMachine::findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        $record = OverhaulHistoryMachine::findOrFail($id);
        return $record->delete();
    }
}
