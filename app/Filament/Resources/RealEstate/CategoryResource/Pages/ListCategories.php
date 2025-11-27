<?php

namespace App\Filament\Resources\RealEstate\CategoryResource\Pages;

use App\Filament\Resources\RealEstate\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Ajouter une catégorie')
                ->modalIcon('heroicon-o-plus')
                ->modalHeading('Ajouter une catégorie')
                ->modalWidth('lg'),
        ];
    }
}
