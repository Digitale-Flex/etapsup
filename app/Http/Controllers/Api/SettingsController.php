<?php

namespace App\Http\Controllers\Api;

// Sprint1 Update: Feature 1.2.1 — Livret explicatif (PDF administrable)

use App\Http\Controllers\Controller;
use App\Settings\GeneralSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

/**
 * Contrôleur pour les settings publics accessibles aux étudiants
 *
 * Feature 1.2.1 : Récupération du livret explicatif
 * Endpoint: GET /api/v1/settings/livret
 */
class SettingsController extends Controller
{
    /**
     * Récupérer les informations du livret explicatif
     *
     * Retourne :
     * - livret_url : URL complète vers le PDF
     * - livret_available : boolean indiquant si un livret est disponible
     *
     * @param GeneralSettings $settings
     * @return JsonResponse
     */
    public function livret(GeneralSettings $settings): JsonResponse
    {
        try {
            $livretPath = $settings->livret_path;

            // Vérifier si un livret est configuré et s'il existe
            $livretAvailable = !empty($livretPath) && Storage::disk('public')->exists($livretPath);

            $livretUrl = $livretAvailable
                ? Storage::disk('public')->url($livretPath)
                : null;

            return response()->json([
                'success' => true,
                'livret_available' => $livretAvailable,
                'livret_url' => $livretUrl,
            ], 200);

        } catch (\Exception $e) {
            logger()->error('Erreur récupération livret explicatif', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'livret_available' => false,
                'livret_url' => null,
                'message' => 'Erreur lors de la récupération du livret',
            ], 500);
        }
    }
}
