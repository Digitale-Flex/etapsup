<?php

namespace App\Filament\Resources\Settings\CareerFieldResource\Pages;

use App\Filament\Resources\Settings\CareerFieldResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCareerField extends EditRecord
{
    protected static string $resource = CareerFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
        ];
    }
}
