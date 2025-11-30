<?php

namespace App\Filament\Resources\Settings\TrainingTypeResource\Pages;

use App\Filament\Resources\Settings\TrainingTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrainingType extends EditRecord
{
    protected static string $resource = TrainingTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
        ];
    }
}
