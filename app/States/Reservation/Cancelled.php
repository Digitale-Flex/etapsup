<?php

namespace App\States\Reservation;

class Cancelled extends ReservationState
{
    public static string $name = 'cancelled';

    public function label(): string
    {
        return 'annuler';
    }
}
