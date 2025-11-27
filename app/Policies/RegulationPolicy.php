<?php

namespace App\Policies;

use App\Models\RealEstate\Regulation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegulationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('viewAny Regulation');
    }


    public function view(User $user, Regulation $regulation): bool
    {
        return $user->can('view Regulation');
    }


    public function create(User $user): bool
    {
        return $user->can('create Regulation');
    }


    public function update(User $user, Regulation $regulation): bool
    {
        return $user->can('update Regulation');
    }


    public function delete(User $user, Regulation $regulation): bool
    {
        return $user->can('delete Regulation');
    }


    public function restore(User $user, Regulation $regulation): bool
    {
        return $user->can('restore Regulation');
    }


    public function forceDelete(User $user, Regulation $regulation): bool
    {
        return $user->can('forceDelete Regulation');
    }
}
