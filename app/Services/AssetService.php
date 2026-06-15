<?php

namespace App\Services;

use App\Repositories\Contracts\AssetRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AssetService
{
    public function __construct(
        private readonly AssetRepositoryInterface $assetRepository
    ) {}

    public function getPaginatedAssets(int $perPage = 10, string $search = '')
    {
        return $this->assetRepository->getAllPaginated($perPage, $search);
    }

    public function getAssetById(int $id)
    {
        return $this->assetRepository->findById($id);
    }

    public function getDistinctLines(): array
    {
        return DB::table('assets')->distinct()->orderBy('line_name')->pluck('line_name')->filter()->toArray();
    }

    public function createAsset(array $data, $photo = null): mixed
    {
        if ($photo) {
            $data['machine_photo'] = $photo->store('assets/photos', 'public');
        }
        return $this->assetRepository->create($data);
    }

    public function updateAsset(int $id, array $data, $photo = null): mixed
    {
        if ($photo) {
            $data['machine_photo'] = $photo->store('assets/photos', 'public');
        }
        return $this->assetRepository->update($id, $data);
    }

    public function deleteAsset(int $id): mixed
    {
        return $this->assetRepository->delete($id);
    }
}
