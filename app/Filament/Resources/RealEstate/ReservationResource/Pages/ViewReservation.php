<?php

namespace App\Filament\Resources\RealEstate\ReservationResource\Pages;

use App\Filament\Resources\RealEstate\ReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewReservation extends ViewRecord
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Détails de la candidature';
    }
}
