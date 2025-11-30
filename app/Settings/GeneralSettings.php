<?php

namespace App\Settings;

// Sprint1 Update: Feature 1.2.1 — Livret explicatif (PDF administrable)

use Spatie\LaravelSettings\Settings;

/**
 * Settings généraux de l'application EtapSup
 *
 * Feature 1.2.1 : Livret explicatif du parcours A → Z
 * - livret_path : chemin vers le PDF du livret d'arrivée étudiant
 */
class GeneralSettings extends Settings
{
    /**
     * Chemin vers le fichier PDF du livret explicatif
     * Uploadé par l'admin via Filament
     *
     * @var string|null
     */
    public ?string $livret_path;

    /**
     * Group name pour Spatie Settings
     *
     * @return string
     */
    public static function group(): string
    {
        return 'general';
    }
}
