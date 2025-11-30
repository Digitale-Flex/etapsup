<?php

namespace App\Filament\Resources\RealEstate\SubCategoryResource\Pages;

use App\Filament\Resources\RealEstate\SubCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubCategory extends EditRecord
{
    protected static string $resource = SubCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
