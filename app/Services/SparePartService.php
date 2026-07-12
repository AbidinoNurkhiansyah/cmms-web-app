<?php

namespace App\Services;

use App\Repositories\Contracts\SparePartRepositoryInterface;

class SparePartService
{
    public function __construct(
        private readonly SparePartRepositoryInterface $sparePartRepository
    ) {}

    public function getPaginatedSpareParts(int $perPage = 10, string $search = '')
    {
        return $this->sparePartRepository->getAllPaginated($perPage, $search);
    }

    public function getSparePartById(int $id)
    {
        return $this->sparePartRepository->findById($id);
    }

    public function createSparePart(array $data, $photo = null): mixed
    {
        if ($photo) {
            $data['part_photo'] = $photo->store('spare-parts/photos', 'public');
        }
        
        $machineAssetNos = $data['machine'] ?? [];
        if (is_array($machineAssetNos)) {
            $data['machine'] = implode(', ', $machineAssetNos);
        }

        $sparePart = $this->sparePartRepository->create($data);
        
        if (is_array($machineAssetNos) && !empty($machineAssetNos)) {
            $this->syncMachineSpareParts($sparePart->id, $machineAssetNos);
        }

        return $sparePart;
    }

    public function updateSparePart(int $id, array $data, $photo = null): mixed
    {
        if ($photo) {
            $data['part_photo'] = $photo->store('spare-parts/photos', 'public');
        }
        
        $machineAssetNos = $data['machine'] ?? [];
        if (is_array($machineAssetNos)) {
            $data['machine'] = implode(', ', $machineAssetNos);
        }

        $sparePart = $this->sparePartRepository->update($id, $data);
        
        if (is_array($machineAssetNos)) {
            $this->syncMachineSpareParts($id, $machineAssetNos);
        }

        return $sparePart;
    }

    public function deleteSparePart(int $id): mixed
    {
        \App\Models\MachineSparePart::where('spare_part_id', $id)->delete();
        return $this->sparePartRepository->delete($id);
    }
    
    private function syncMachineSpareParts(int $sparePartId, array $assetNos): void
    {
        $existingPivot = \App\Models\MachineSparePart::where('spare_part_id', $sparePartId)->pluck('asset_no')->toArray();
        
        $toInsert = array_diff($assetNos, $existingPivot);
        $toDelete = array_diff($existingPivot, $assetNos);
        
        if (!empty($toDelete)) {
            \App\Models\MachineSparePart::where('spare_part_id', $sparePartId)
                ->whereIn('asset_no', $toDelete)
                ->delete();
        }
        
        if (!empty($toInsert)) {
            $assets = \App\Models\Asset::whereIn('asset_no', $toInsert)->get();
            $insertData = [];
            foreach ($assets as $asset) {
                $insertData[] = [
                    'spare_part_id' => $sparePartId,
                    'line' => $asset->line_name ?? '-',
                    'asset_no' => $asset->asset_no,
                    'machine' => $asset->machine_name ?? '-',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($insertData)) {
                \App\Models\MachineSparePart::insert($insertData);
            }
        }
    }
}
