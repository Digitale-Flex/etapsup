<?php

namespace App\Filament\Resources\RealEstate\LayoutResource\Pages;

use App\Filament\Resources\RealEstate\LayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLayouts extends ListRecords
{
    protected static string $resource = LayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Ajouter un aménagement')
                ->modalIcon('heroicon-o-plus')
                ->modalHeading('Ajouter un aménagement')
                ->modalWidth('lg'),
        ];
    }
}
