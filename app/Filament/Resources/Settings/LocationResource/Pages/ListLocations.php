<?php

namespace App\Filament\Resources\Settings\LocationResource\Pages;

use App\Filament\Resources\Settings\LocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLocations extends ListRecords
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter une localité')
                ->modalHeading('Ajouter une localité')
                ->modalWidth('xl'),
        ];
    }
}
