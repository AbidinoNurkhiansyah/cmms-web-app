<?php

namespace App\Services;

use App\Models\DeepCleaning;

class DeepCleaningService
{
    public function getPaginated(int $perPage = 15, string $search = '', string $status = '')
    {
        return DeepCleaning::query()
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('LineName', 'like', "%{$search}%")
                  ->orWhere('MachineName', 'like', "%{$search}%")
                  ->orWhere('MachineNo', 'like', "%{$search}%");
            }))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        $data['status'] = $data['status'] ?? 'Scheduled';
        return DeepCleaning::create($data);
    }

    public function update(int $id, array $data)
    {
        $record = DeepCleaning::findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        return DeepCleaning::findOrFail($id)->delete();
    }
}
