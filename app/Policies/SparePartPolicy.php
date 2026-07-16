<?php

namespace App\Policies;

use App\Models\SparePart;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SparePartPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('sparepart.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SparePart $sparePart): bool
    {
        return $user->hasPermissionTo('sparepart.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('sparepart.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SparePart $sparePart): bool
    {
        return $user->hasPermissionTo('sparepart.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SparePart $sparePart): bool
    {
        return $user->hasPermissionTo('sparepart.manage');
    }

    public function requestPart(User $user, SparePart $sparePart = null): bool
    {
        return $user->hasPermissionTo('sparepart.request');
    }

    public function approvePart(User $user, SparePart $sparePart = null): bool
    {
        return $user->hasPermissionTo('sparepart.approve_part');
    }

    public function approveOrder(User $user, SparePart $sparePart = null): bool
    {
        return $user->hasPermissionTo('sparepart.approve_order');
    }
}
