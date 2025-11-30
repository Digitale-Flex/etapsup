<?php

namespace App\Filament\Resources\Settings\EstablishmentTypeResource\Pages;

use App\Filament\Resources\Settings\EstablishmentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEstablishmentType extends EditRecord
{
    protected static string $resource = EstablishmentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
        ];
    }
}
