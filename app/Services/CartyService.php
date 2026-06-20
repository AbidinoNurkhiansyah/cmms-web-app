<?php

namespace App\Services;

use App\Repositories\Contracts\CartyRepositoryInterface;

class CartyService
{
    public function __construct(private CartyRepositoryInterface $repo) {}

    public function getPaginated(int $perPage = 15, string $search = '', string $status = '', string $startDate = '', string $endDate = '')
    {
        return $this->repo->getAllPaginated($perPage, $search, $status, $startDate, $endDate);
    }

    public function getById(int $id)
    {
        return $this->repo->findById($id);
    }

    public function create(array $data)
    {
        $usedSpareparts = $data['usedSpareparts'] ?? [];
        unset($data['usedSpareparts']);

        $data['Status'] = $data['Status'] ?? 'Open';
        $record = $this->repo->create($data);

        if (!empty($usedSpareparts)) {
            $syncData = [];
            foreach ($usedSpareparts as $sp) {
                if (!empty($sp['spare_part_id']) && $sp['qty'] > 0) {
                    $syncData[$sp['spare_part_id']] = ['qty' => $sp['qty']];
                }
            }
            $record->spareParts()->sync($syncData);
        }

        return $record;
    }

    public function update(int $id, array $data)
    {
        $usedSpareparts = $data['usedSpareparts'] ?? [];
        unset($data['usedSpareparts']);

        $record = $this->repo->update($id, $data);

        $syncData = [];
        if (!empty($usedSpareparts)) {
            foreach ($usedSpareparts as $sp) {
                if (!empty($sp['spare_part_id']) && $sp['qty'] > 0) {
                    $syncData[$sp['spare_part_id']] = ['qty' => $sp['qty']];
                }
            }
        }
        $record->spareParts()->sync($syncData);

        return $record;
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    public function getLines(): array
    {
        return $this->repo->getDistinctLines();
    }
}
