<?php

namespace App\Policies;

use App\Models\DeepCleaningSchedule;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DeepCleaningSchedulePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('pm.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DeepCleaningSchedule $deepCleaningSchedule): bool
    {
        return $user->hasPermissionTo('pm.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('pm.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DeepCleaningSchedule $deepCleaningSchedule): bool
    {
        return $user->hasPermissionTo('pm.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DeepCleaningSchedule $deepCleaningSchedule): bool
    {
        return $user->hasPermissionTo('pm.manage');
    }

    public function manage(User $user, DeepCleaningSchedule $deepCleaningSchedule = null): bool
    {
        return $user->hasPermissionTo('pm.manage');
    }

    public function approve(User $user, DeepCleaningSchedule $deepCleaningSchedule = null): bool
    {
        return $user->hasPermissionTo('pm.approve');
    }
}
