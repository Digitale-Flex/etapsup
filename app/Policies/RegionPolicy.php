<?php

namespace App\Policies;

use App\Models\Region;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('viewAny Region');
    }


    public function view(User $user, Region $region): bool
    {
        return $user->can('view Region');
    }


    public function create(User $user): bool
    {
        return $user->can('create Region');
    }


    public function update(User $user, Region $region): bool
    {
        return $user->can('update Region');
    }


    public function delete(User $user, Region $region): bool
    {
        return $user->can('delete Region');
    }


    public function restore(User $user, Region $region): bool
    {
        return $user->can('restore Region');
    }


    public function forceDelete(User $user, Region $region): bool
    {
        return $user->can('forceDelete Region');
    }
}
