<?php

namespace App\Services;

use App\Models\SparePartRepair;
use Illuminate\Support\Facades\Storage;

class SparePartRepairService
{
    /**
     * Get paginated records with relationships.
     */
    public function getPaginated($perPage = 10, $search = '')
    {
        return SparePartRepair::with(['sparePart', 'pic1', 'pic2', 'pic3'])
            ->when($search, function ($query, $search) {
                $query->whereHas('sparePart', function ($q) use ($search) {
                    $q->where('part_number', 'like', "%{$search}%")
                      ->orWhere('part_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Store a newly created repair record.
     */
    public function store(array $data)
    {
        $data = $this->handleFileUploads($data);
        return SparePartRepair::create($data);
    }

    /**
     * Update an existing repair record.
     */
    public function update(int $id, array $data)
    {
        $repair = SparePartRepair::findOrFail($id);
        $data = $this->handleFileUploads($data, $repair);
        
        $repair->update($data);
        return $repair;
    }

    /**
     * Delete a repair record and its files.
     */
    public function delete(int $id)
    {
        $repair = SparePartRepair::findOrFail($id);
        
        if ($repair->file_before) {
            Storage::disk('public')->delete($repair->file_before);
        }
        if ($repair->file_after) {
            Storage::disk('public')->delete($repair->file_after);
        }
        
        return $repair->delete();
    }

    /**
     * Handle file uploads for before and after pictures.
     */
    private function handleFileUploads(array $data, ?SparePartRepair $repair = null): array
    {
        if (isset($data['file_before_upload'])) {
            if ($repair && $repair->file_before) {
                Storage::disk('public')->delete($repair->file_before);
            }
            $data['file_before'] = $data['file_before_upload']->store('repairpart/before', 'public');
            unset($data['file_before_upload']);
        }

        if (isset($data['file_after_upload'])) {
            if ($repair && $repair->file_after) {
                Storage::disk('public')->delete($repair->file_after);
            }
            $data['file_after'] = $data['file_after_upload']->store('repairpart/after', 'public');
            unset($data['file_after_upload']);
        }

        return $data;
    }
}
