<?php

namespace App\Services;

use App\Models\Checksheet;

class ChecksheetService
{
    public function getPaginated(int $perPage = 15, string $search = '')
    {
        return Checksheet::query()
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('noDoc', 'like', "%{$search}%")
                  ->orWhere('assetNo', 'like', "%{$search}%")
                  ->orWhere('picSL', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            }))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        return Checksheet::create($data);
    }

    public function update(int $id, array $data)
    {
        $record = Checksheet::findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        return Checksheet::findOrFail($id)->delete();
    }
}
