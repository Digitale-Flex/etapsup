<?php

namespace App\Filament\Resources\Settings\RegionResource\Pages;

use App\Filament\Imports\RegionImporter;
use App\Filament\Resources\Settings\RegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegions extends ListRecords
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
