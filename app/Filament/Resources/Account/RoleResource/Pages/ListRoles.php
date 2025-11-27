<?php

namespace App\Filament\Resources\Account\RoleResource\Pages;

use App\Filament\Resources\Account\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Créer un rôle')
            ->icon('heroicon-o-plus'),
        ];
    }
}
