<?php

namespace App\Filament\Resources\RealEstate\ReservationResource\Pages;

use App\Filament\Resources\RealEstate\ReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
