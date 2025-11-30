<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter un utilisateur')
                ->icon('heroicon-o-plus')
                ->modalWidth('2xl'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Widgets statistiques si nécessaire
        ];
    }
}
