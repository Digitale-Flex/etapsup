<?php

namespace App\Policies;

use App\Models\Certificate\Coupon;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CouponPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('viewAny Coupon') || $user->hasRole(['partner']);
    }


    public function view(User $user, Coupon $coupon): bool
    {
        return $user->can('view Coupon')  || $user->hasRole(['partner']);
    }


    public function create(User $user): bool
    {
        return $user->can('create Coupon');
    }


    public function update(User $user, Coupon $coupon): bool
    {
        return $user->can('update Coupon');
    }


    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->can('delete Coupon');
    }


    public function restore(User $user, Coupon $coupon): bool
    {
        return $user->can('restore Coupon');
    }


    public function forceDelete(User $user, Coupon $coupon): bool
    {
        return $user->can('forceDelete Coupon');
    }
}
