<?php

namespace App\Filament\Resources\Settings\EstablishmentTypeResource\Pages;

use App\Filament\Resources\Settings\EstablishmentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstablishmentTypes extends ListRecords
{
    protected static string $resource = EstablishmentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouveau type')
                ->icon('heroicon-o-plus'),
        ];
    }
}
