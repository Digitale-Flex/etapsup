<?php

namespace App\Filament\Partner\Resources\UserResource\Pages;

use App\Filament\Partner\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Spatie\Permission\Models\Role;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter un nouveau utilisateur')
                ->icon('heroicon-o-user')
                ->modalIcon('heroicon-o-plus')
                ->modalWidth('lg')
                ->using(function (array $data, $record): User {
                    $data['partner_id'] = auth()->user()->partner_id;
                    $user = User::create($data);
                    $partnerRole = Role::findByName('partner');
                    $user->assignRole($partnerRole);

                    return $user;
                }),
        ];
    }
}
