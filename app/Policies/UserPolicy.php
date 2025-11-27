<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('viewAny User')  || $user->hasRole(['partner']);
    }


    public function view(User $user): bool
    {
        return $user->can('view User')  || $user->hasRole(['partner']);
    }


    public function create(User $user): bool
    {
        return $user->can('create User');
    }


    public function update(User $user): bool
    {
        return $user->can('update User') || $user->hasRole(['partner']);
    }


    public function delete(User $user): bool
    {
        return $user->can('delete User');
    }


    public function restore(User $user): bool
    {
        return $user->can('restore User');
    }


    public function forceDelete(User $user): bool
    {
        return $user->can('forceDelete User');
    }
}
