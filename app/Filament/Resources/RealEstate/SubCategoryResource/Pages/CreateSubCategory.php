<?php

namespace App\Filament\Resources\RealEstate\SubCategoryResource\Pages;

use App\Filament\Resources\RealEstate\SubCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSubCategory extends CreateRecord
{
    protected static string $resource = SubCategoryResource::class;

    protected static ?string $title = 'Nouvelle formation';
}
