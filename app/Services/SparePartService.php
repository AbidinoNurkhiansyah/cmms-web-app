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
        return $this->sparePartRepository->create($data);
    }

    public function updateSparePart(int $id, array $data, $photo = null): mixed
    {
        if ($photo) {
            $data['part_photo'] = $photo->store('spare-parts/photos', 'public');
        }
        return $this->sparePartRepository->update($id, $data);
    }

    public function deleteSparePart(int $id): mixed
    {
        return $this->sparePartRepository->delete($id);
    }
}
