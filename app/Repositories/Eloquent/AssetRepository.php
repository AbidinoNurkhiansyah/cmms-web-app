<?php

namespace App\Repositories\Eloquent;

use App\Models\Asset;
use App\Repositories\Contracts\AssetRepositoryInterface;

class AssetRepository implements AssetRepositoryInterface
{
    public function getAllPaginated(int $perPage = 10, string $search = '')
    {
        // Agent.md rule 2: SELECT spesifik & manfaatkan Scout
        if (!empty($search)) {
            return Asset::search($search)->paginate($perPage);
        }
        
        return Asset::select('id', 'asset_no', 'machine_name', 'machine_rank', 'line_name', 'maker', 'manufacture_year')
                    ->latest()
                    ->paginate($perPage);
    }

    public function findById(int $id)
    {
        return Asset::findOrFail($id);
    }

    public function create(array $data)
    {
        return Asset::create($data);
    }

    public function update(int $id, array $data)
    {
        $asset = Asset::findOrFail($id);
        $asset->update($data);
        return $asset;
    }

    public function delete(int $id)
    {
        $asset = Asset::findOrFail($id);
        return $asset->delete();
    }
}
