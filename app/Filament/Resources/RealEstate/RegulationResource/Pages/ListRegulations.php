<?php

namespace App\Filament\Resources\RealEstate\RegulationResource\Pages;

use App\Filament\Resources\RealEstate\RegulationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegulations extends ListRecords
{
    protected static string $resource = RegulationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Ajouter un règlement intérieur')
                ->modalIcon('heroicon-o-plus')
                ->modalHeading('Ajouter un règlement intérieur')
                ->modalWidth('lg'),
        ];
    }
}
