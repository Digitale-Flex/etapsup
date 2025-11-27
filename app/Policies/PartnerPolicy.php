<?php

namespace App\Policies;

use App\Models\Certificate\Partner;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PartnerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('viewAny Partner');
    }


    public function view(User $user, Partner $partner): bool
    {
        return $user->can('view Partner');
    }


    public function create(User $user): bool
    {
        return $user->can('create Partner');
    }


    public function update(User $user, Partner $partner): bool
    {
        return $user->can('update Partner');
    }


    public function delete(User $user, Partner $partner): bool
    {
        return $user->can('delete Partner');
    }


    public function restore(User $user, Partner $partner): bool
    {
        return $user->can('restore Partner');
    }


    public function forceDelete(User $user, Partner $partner): bool
    {
        return $user->can('forceDelete Partner');
    }
}
