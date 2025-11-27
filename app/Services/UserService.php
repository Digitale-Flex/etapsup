<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;

class UserService
{
    public function update($data): array
    {
        DB::beginTransaction();

        try {
            $userData = Arr::only($data, [
                'surname', 'name', 'email', 'phone', 'nationality',
                'passport_number', 'place_birth', 'date_birth', 'country_birth_id'
            ]);

            if (isset($userData['country_birth_id'])) {
                $userData['country_id'] = $userData['country_birth_id'];
                unset($userData['country_birth_id']);
            }

            $user = auth()->user();

            // Mettre à jour et rafraîchir l'instance
            $user->update($userData);
            $user->refresh(); // Pour s'assurer d'avoir les dernières données

            DB::commit();

            return [
                'success' => true,
                'message' => 'Profil mis à jour avec succès.',
                'user' => $user
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);

            return [
                'success' => false,
                'message' => 'Échec de la mise à jour du profil.',
                'error' => $e->getMessage()
            ];
        }
    }
}
