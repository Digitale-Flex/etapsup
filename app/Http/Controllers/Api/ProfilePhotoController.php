<?php

namespace App\Http\Controllers\Api;

// Sprint1 Update: Feature 1.1.1 — Espace Étudiant (Connexion & Profil)

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Contrôleur pour l'upload de photo de profil étudiant
 *
 * Feature 1.1.1 — Espace Étudiant (Connexion & Profil)
 * Endpoint: POST /api/v1/profile/photo
 */
class ProfilePhotoController extends Controller
{
    /**
     * Upload de la photo de profil
     *
     * Critères d'acceptation :
     * - Formats acceptés : jpg, jpeg, png
     * - Taille max : 2 Mo
     * - Sanctum authentication required
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'photo' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048', // 2 Mo max
            ],
        ], [
            'photo.required' => 'Veuillez sélectionner une photo',
            'photo.image' => 'Le fichier doit être une image',
            'photo.mimes' => 'Formats acceptés : JPG, JPEG, PNG',
            'photo.max' => 'La taille maximale est de 2 Mo',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = $request->user();

            // Supprimer l'ancienne photo si elle existe
            $user->clearMediaCollection('photo');

            // Ajouter la nouvelle photo avec Spatie MediaLibrary
            // La conversion 'thumb' est automatiquement générée (définie dans User model ligne 110-113)
            $media = $user->addMediaFromRequest('photo')
                ->toMediaCollection('photo');

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil mise à jour avec succès',
                'photo_url' => $media->getUrl(),
                'photo_thumb_url' => $media->getUrl('thumb'),
            ], 200);

        } catch (\Exception $e) {
            logger()->error('Erreur upload photo profil', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload de la photo',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
