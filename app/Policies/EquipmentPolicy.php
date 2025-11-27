<?php

namespace App\Policies;

use App\Models\RealEstate\Equipment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EquipmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('viewAny Equipment');
    }


    public function view(User $user, Equipment $equipment): bool
    {
        return $user->can('view Equipment');
    }


    public function create(User $user): bool
    {
        return $user->can('create Equipment');
    }


    public function update(User $user, Equipment $equipment): bool
    {
        return $user->can('update Equipment');
    }


    public function delete(User $user, Equipment $equipment): bool
    {
        return $user->can('delete Equipment');
    }


    public function restore(User $user, Equipment $equipment): bool
    {
        return $user->can('restore Equipment');
    }


    public function forceDelete(User $user, Equipment $equipment): bool
    {
        return $user->can('forceDelete Equipment');
    }
}
