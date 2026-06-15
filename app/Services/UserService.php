<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function getPaginatedUsers(int $perPage = 25, string $search = '')
    {
        return $this->userRepository->getAllPaginated($perPage, $search);
    }

    public function getUserById(int $id)
    {
        return $this->userRepository->findById($id);
    }

    public function createUser(array $data): mixed
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->userRepository->create($data);
    }

    public function updateUser(int $id, array $data): mixed
    {
        // Only hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        return $this->userRepository->update($id, $data);
    }

    public function updateStatus(int $id, string $status): mixed
    {
        return $this->userRepository->updateStatus($id, $status);
    }

    public function updatePhoto(int $id, $file): mixed
    {
        $path = $file->store('users/photos', 'public');
        return $this->userRepository->update($id, ['photo' => $path]);
    }

    public function changePassword(int $id, string $newPassword): mixed
    {
        return $this->userRepository->update($id, [
            'password' => Hash::make($newPassword),
        ]);
    }
}
