<?php

namespace App\Observers;

use App\Models\RealEstate\Reservation;
use App\Models\StatusChange;

class ReservationObserver
{
    /**
     * Handle the Reservation "created" event.
     */
    public function created(Reservation $reservation): void
    {
        /*StatusChange::create([
            'status_changeable_id' => $reservation->id,
            'status_changeable_type' => $reservation->getMorphClass(),
            'from_state' => $reservation->getOriginal('state'),
            'to_state' => $reservation->state,
            'notes' => 'Changement automatique via système',
        ]);*/
    }

    /**
     * Handle the Reservation "updated" event.
     */
    public function updated(Reservation $reservation): void
    {
       /* StatusChange::create([
            'status_changeable_id' => $reservation->id,
            'status_changeable_type' => $reservation->getMorphClass(),
            'from_state' => $reservation->getOriginal('state'),
            'to_state' => $reservation->state,
            'notes' => 'Changement automatique via système',
        ]); */
    }

    /**
     * Handle the Reservation "deleted" event.
     */
    public function deleted(Reservation $reservation): void
    {
        //
    }

    /**
     * Handle the Reservation "restored" event.
     */
    public function restored(Reservation $reservation): void
    {
        //
    }

    /**
     * Handle the Reservation "force deleted" event.
     */
    public function forceDeleted(Reservation $reservation): void
    {
        //
    }
}
