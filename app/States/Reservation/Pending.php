<?php

namespace App\States\Reservation;

class Pending extends ReservationState
{
    public static string $name = 'pending';

    public function label(): string
    {
        return 'en attente';
    }
}
