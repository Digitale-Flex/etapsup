<?php

namespace App\Filament\Resources\RealEstate\RegulationResource\Pages;

use App\Filament\Resources\RealEstate\RegulationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegulation extends EditRecord
{
    protected static string $resource = RegulationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
