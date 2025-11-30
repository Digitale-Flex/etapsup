<?php

namespace App\Filament\Resources\Settings\DegreeLevelResource\Pages;

use App\Filament\Resources\Settings\DegreeLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDegreeLevel extends EditRecord
{
    protected static string $resource = DegreeLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
        ];
    }
}
