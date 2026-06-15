<?php

namespace App\Repositories\Eloquent;

use App\Models\SparePart;
use App\Repositories\Contracts\SparePartRepositoryInterface;

class SparePartRepository implements SparePartRepositoryInterface
{
    public function getAllPaginated(int $perPage = 10, string $search = '')
    {
        $query = SparePart::query()->orderBy('part_name');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('part_name',   'like', "%{$search}%")
                  ->orWhere('part_number', 'like', "%{$search}%")
                  ->orWhere('no_rack',    'like', "%{$search}%")
                  ->orWhere('maker',      'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id)
    {
        return SparePart::findOrFail($id);
    }

    public function create(array $data)
    {
        return SparePart::create($data);
    }

    public function update(int $id, array $data)
    {
        $sparePart = SparePart::findOrFail($id);
        $sparePart->update($data);
        return $sparePart;
    }

    public function delete(int $id)
    {
        $sparePart = SparePart::findOrFail($id);
        return $sparePart->delete();
    }
}
