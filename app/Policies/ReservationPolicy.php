<?php

namespace App\Policies;

use App\Models\RealEstate\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('viewAny Reservation');
    }


    public function view(User $user, Reservation $reservation): bool
    {
        return $user->can('view Reservation');
    }


    public function create(User $user): bool
    {
        return $user->can('create Reservation');
    }


    public function update(User $user, Reservation $reservation): bool
    {
        return $user->can('update Reservation');
    }


    public function delete(User $user, Reservation $reservation): bool
    {
        return $user->can('delete Reservation');
    }


    public function restore(User $user, Reservation $reservation): bool
    {
        return $user->can('restore Reservation');
    }


    public function forceDelete(User $user, Reservation $reservation): bool
    {
        return $user->can('forceDelete Reservation');
    }
}
