<?php

namespace App\Policies;

use App\Models\City;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CityPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('viewAny City');
    }


    public function view(User $user, City $city): bool
    {
        return $user->can('view City');
    }


    public function create(User $user): bool
    {
        return $user->can('create City');
    }


    public function update(User $user, City $city): bool
    {
        return $user->can('update City');
    }


    public function delete(User $user, City $city): bool
    {
        return $user->can('delete City');
    }


    public function restore(User $user, City $city): bool
    {
        return $user->can('restore City');
    }


    public function forceDelete(User $user, City $city): bool
    {
        return $user->can('forceDelete City');
    }
}
