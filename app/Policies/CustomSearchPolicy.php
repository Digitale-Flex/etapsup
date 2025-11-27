<?php

namespace App\Policies;

use App\Models\CustomSearch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomSearchPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('viewAny CustomSearch');
    }


    public function view(User $user, CustomSearch $customSearch): bool
    {
        return $user->can('view CustomSearch');
    }


    public function create(User $user): bool
    {
        return $user->can('create CustomSearch');
    }


    public function update(User $user, CustomSearch $customSearch): bool
    {
        return $user->can('update CustomSearch');
    }


    public function delete(User $user, CustomSearch $customSearch): bool
    {
        return $user->can('delete CustomSearch');
    }


    public function restore(User $user, CustomSearch $customSearch): bool
    {
        return $user->can('restore CustomSearch');
    }


    public function forceDelete(User $user, CustomSearch $customSearch): bool
    {
        return $user->can('forceDelete CustomSearch');
    }
}
