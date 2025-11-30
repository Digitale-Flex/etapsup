<?php

namespace App\Filament\Resources\RealEstate\SubCategoryResource\Pages;

use App\Filament\Resources\RealEstate\SubCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubCategories extends ListRecords
{
    protected static string $resource = SubCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouvelle formation')
                ->icon('heroicon-o-plus'),
        ];
    }
}
