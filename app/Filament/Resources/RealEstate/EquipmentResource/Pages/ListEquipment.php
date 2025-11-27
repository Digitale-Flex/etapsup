<?php

namespace App\Filament\Resources\RealEstate\EquipmentResource\Pages;

use App\Filament\Resources\RealEstate\EquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEquipment extends ListRecords
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Ajouter un équipement')
                ->modalIcon('heroicon-o-plus')
                ->modalHeading('Ajouter un équipement')
                ->modalWidth('lg'),
        ];
    }
}
