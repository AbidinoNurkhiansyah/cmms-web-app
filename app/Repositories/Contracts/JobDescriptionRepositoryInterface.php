<?php

namespace App\Repositories\Contracts;

interface JobDescriptionRepositoryInterface
{
    public function getAllPaginated($perPage = 10, $search = '');
    public function create(array $data);
    public function findById($id);
    public function update($id, array $data);
    public function delete($id);
    public function getAllUniqueUnits();
}
