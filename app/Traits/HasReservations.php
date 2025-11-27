<?php

namespace App\Traits;

use App\Models\RealEstate\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Spatie\Period\Period;

trait HasReservations
{
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function isAvailableForPeriod(Carbon $startDate, Carbon $endDate): bool
    {
        $period = Period::make($startDate, $endDate);

        return ! $this->reservations()
           // ->where('state', '!=', 'cancelled')
            ->overlappingWithPeriod($period)
            ->exists();
    }

    public function getAvailabilityForPeriod(Period $period): bool
    {
        return ! $this->reservations()
            ->active()
            ->overlappingWithPeriod($period)
            ->exists();
    }

    public function getBlockedDates(Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->reservations()
            ->active()
            ->where('end_date', '>=', $startDate)
            ->where('start_date', '<=', $endDate)
            ->get(['start_date', 'end_date'])
            ->map(function ($reservation) {
                return [
                    'start' => $reservation->start_date->format('Y-m-d'),
                    'end' => $reservation->end_date->format('Y-m-d'),
                ];
            });
    }
}
