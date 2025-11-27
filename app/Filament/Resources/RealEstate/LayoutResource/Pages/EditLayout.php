<?php

namespace App\Filament\Resources\RealEstate\LayoutResource\Pages;

use App\Filament\Resources\RealEstate\LayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLayout extends EditRecord
{
    protected static string $resource = LayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
