<?php

namespace App\Repositories\Eloquent;

use App\Models\Carty;
use App\Repositories\Contracts\CartyRepositoryInterface;

class CartyRepository implements CartyRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15, string $search = '', string $status = '', string $startDate = '', string $endDate = '')
    {
        $query = Carty::query()->orderByDesc('Date')->orderByDesc('id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('LineName', 'like', "%{$search}%")
                  ->orWhere('MachineName', 'like', "%{$search}%")
                  ->orWhere('Problem', 'like', "%{$search}%")
                  ->orWhere('MachineNo', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('Status', $status);
        }

        if ($startDate) {
            $query->whereDate('Date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('Date', '<=', $endDate);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id)
    {
        return Carty::findOrFail($id);
    }

    public function create(array $data)
    {
        return Carty::create($data);
    }

    public function update(int $id, array $data)
    {
        $record = Carty::findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        $record = Carty::findOrFail($id);
        return $record->delete();
    }

    public function getDistinctLines(): array
    {
        return Carty::select('LineName')
            ->distinct()
            ->whereNotNull('LineName')
            ->orderBy('LineName')
            ->pluck('LineName')
            ->toArray();
    }
}
