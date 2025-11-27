<?php

namespace App\Policies;

use App\Models\RealEstate\Property;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('viewAny Property');
    }


    public function view(User $user, Property $property): bool
    {
        return $user->can('view Property');
    }


    public function create(User $user): bool
    {
        return $user->can('create Property');
    }


    public function update(User $user, Property $property): bool
    {
        return $user->can('update Property');
    }


    public function delete(User $user, Property $property): bool
    {
        return $user->can('delete Property');
    }


    public function restore(User $user, Property $property): bool
    {
        return $user->can('restore Property');
    }


    public function forceDelete(User $user, Property $property): bool
    {
        return $user->can('forceDelete Property');
    }
}
