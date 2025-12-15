<?php

namespace App\Filament\Resources\ProgramResource\Pages;

use App\Filament\Resources\ProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgram extends EditRecord
{
    protected static string $resource = ProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // A6: Lien prévisualisation vers l'établissement du programme
            Actions\Action::make('preview')
                ->label('Voir sur le site')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => $this->record->establishment?->hashid
                    ? route('establishments.show', $this->record->establishment->hashid)
                    : null)
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->establishment?->hashid !== null)
                ->color('info'),
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
        ];
    }
}
