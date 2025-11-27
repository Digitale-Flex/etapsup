<?php

namespace App\Filament\Resources\Account\EmployeeResource\Pages;

use App\Filament\Resources\Account\EmployeeResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter un compte')
                ->icon('heroicon-o-plus')
                ->modalIcon('heroicon-o-plus')
                ->modalWidth('xl')
                ->using(function (array $data): User {
                    $user = User::create($data);
                    $user->assignRole('account');
                    return $user;
                })
        ];
    }
}
