<?php

namespace App\Filament\Resources\RealEstate\PropertyResource\Pages;

use App\Filament\Resources\RealEstate\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('details')
                ->label('Voir l\'établissement')
                ->icon('gmdi-open-in-new-r')
                ->url(fn (): string => route('establishments.show', $this->record->hashid))
                ->openUrlInNewTab()
                ->link(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
