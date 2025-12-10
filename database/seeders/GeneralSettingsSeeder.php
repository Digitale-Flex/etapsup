<?php

namespace Database\Seeders;

use App\Settings\GeneralSettings;
use Illuminate\Database\Seeder;

/**
 * Seeder pour initialiser les settings généraux de l'application
 *
 * Fix pour: MissingSettings 'App\Settings\GeneralSettings', and the following properties were missing: livret_path
 */
class GeneralSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = app(GeneralSettings::class);

        // Initialiser livret_path si non défini
        if (!property_exists($settings, 'livret_path') || is_null($settings->livret_path)) {
            $settings->livret_path = null;
            $settings->save();

            $this->command->info('✅ GeneralSettings initialisé (livret_path = null)');
        } else {
            $this->command->info('ℹ️  GeneralSettings déjà initialisé');
        }
    }
}
