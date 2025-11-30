<?php

namespace App\Filament\Resources\Settings\CareerFieldResource\Pages;

use App\Filament\Resources\Settings\CareerFieldResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCareerFields extends ListRecords
{
    protected static string $resource = CareerFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouveau métier')
                ->icon('heroicon-o-plus'),
        ];
    }
}
