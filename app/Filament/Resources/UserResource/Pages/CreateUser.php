<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Extraire le rôle avant la création
        $role = $data['role'] ?? 'user';
        unset($data['role']);

        // Créer l'utilisateur
        $user = static::getModel()::create($data);

        // Assigner le rôle via Spatie Permission
        $user->assignRole($role);

        return $user;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Utilisateur créé avec succès';
    }
}
