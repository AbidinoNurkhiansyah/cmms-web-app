<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getAllPaginated(int $perPage = 25, string $search = '')
    {
        $query = User::query()->orderBy('jid_no');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('jid_no', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id)
    {
        return User::findOrFail($id);
    }

    public function findByUsername(string $username)
    {
        return User::where('username', $username)->first();
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(int $id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function updateStatus(int $id, string $status)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => $status]);
        return $user;
    }
}
