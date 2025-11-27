<?php

namespace App\Policies;

use App\Models\RealEstate\Layout;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LayoutPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('viewAny Layout');
    }


    public function view(User $user, Layout $layout): bool
    {
        return $user->can('view Layout');
    }


    public function create(User $user): bool
    {
        return $user->can('create Layout');
    }


    public function update(User $user, Layout $layout): bool
    {
        return $user->can('update Layout');
    }


    public function delete(User $user, Layout $layout): bool
    {
        return $user->can('delete Layout');
    }


    public function restore(User $user, Layout $layout): bool
    {
        return $user->can('restore Layout');
    }


    public function forceDelete(User $user, Layout $layout): bool
    {
        return $user->can('forceDelete Layout');
    }
}
