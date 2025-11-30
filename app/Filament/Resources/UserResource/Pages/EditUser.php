<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Charger le rôle actuel pour l'afficher dans le formulaire
        $user = $this->record;
        if ($user->roles->isNotEmpty()) {
            $data['role'] = $user->roles->first()->name;
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Extraire le rôle avant la mise à jour
        $role = $data['role'] ?? null;
        unset($data['role']);

        // Mettre à jour l'utilisateur
        $record->update($data);

        // Synchroniser le rôle via Spatie Permission
        if ($role) {
            $record->syncRoles([$role]);
        }

        return $record;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Utilisateur mis à jour avec succès';
    }
}
