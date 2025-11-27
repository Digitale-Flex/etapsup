<?php

namespace App\States\Reservation;

class Confirmed extends ReservationState
{
    public static string $name = 'confirmed';

    public function label(): string
    {
        return 'confirmer';
    }
}
