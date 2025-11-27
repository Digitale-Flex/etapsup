<?php

namespace App\States\Reservation;

class Completed extends ReservationState
{
    public static string $name = 'completed';

    public function label(): string
    {
        return 'complet';
    }
}
