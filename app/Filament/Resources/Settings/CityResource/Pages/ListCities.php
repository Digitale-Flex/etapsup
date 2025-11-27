<?php

namespace App\Filament\Resources\Settings\CityResource\Pages;

use App\Filament\Imports\CityImporter;
use App\Filament\Resources\Settings\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCities extends ListRecords
{
    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter une ville')
                ->modalHeading('Ajouter une ville')
                ->modalWidth('md'),
        ];
    }
}
