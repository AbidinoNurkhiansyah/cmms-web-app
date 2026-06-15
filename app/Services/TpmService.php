<?php

namespace App\Services;

use App\Models\Tpm;

class TpmService
{
    public function getPaginated(int $perPage = 15, string $search = '', string $status = '')
    {
        return Tpm::query()
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('LineName', 'like', "%{$search}%")
                  ->orWhere('MachineName', 'like', "%{$search}%")
                  ->orWhere('MachineNo', 'like', "%{$search}%")
                  ->orWhere('pic', 'like', "%{$search}%");
            }))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        $data['status'] = $data['status'] ?? 'Scheduled';
        return Tpm::create($data);
    }

    public function update(int $id, array $data)
    {
        $record = Tpm::findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        return Tpm::findOrFail($id)->delete();
    }
}
