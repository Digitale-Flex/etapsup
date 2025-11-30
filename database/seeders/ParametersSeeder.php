<?php

namespace Database\Seeders;

use App\Models\RealEstate\Property;
use App\Models\Settings\CareerField;
use App\Models\Settings\DegreeLevel;
use App\Models\Settings\EstablishmentType;
use App\Models\Settings\TrainingType;
use Illuminate\Database\Seeder;

class ParametersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Ce seeder pré-remplit les tables de paramètres avec les valeurs
     * provenant des constantes Property (compatibilité descendante).
     */
    public function run(): void
    {
        // Types d'établissement
        foreach (Property::ESTABLISHMENT_TYPES as $type) {
            EstablishmentType::firstOrCreate(
                ['label' => $type],
                [
                    'is_published' => true,
                    'description' => null,
                ]
            );
        }

        // Types de formation
        foreach (Property::TRAINING_TYPES as $type) {
            TrainingType::firstOrCreate(
                ['label' => $type],
                [
                    'is_published' => true,
                    'description' => null,
                ]
            );
        }

        // Métiers / Secteurs professionnels
        foreach (Property::CAREER_FIELDS as $field) {
            CareerField::firstOrCreate(
                ['label' => $field],
                [
                    'is_published' => true,
                    'description' => null,
                ]
            );
        }

        // Niveaux de diplôme
        foreach (Property::DEGREE_LEVELS as $level) {
            DegreeLevel::firstOrCreate(
                ['label' => $level],
                [
                    'is_published' => true,
                    'description' => null,
                ]
            );
        }

        $this->command->info('✅ Paramètres pré-remplis avec succès !');
        $this->command->info('   - ' . count(Property::ESTABLISHMENT_TYPES) . ' types d\'établissement');
        $this->command->info('   - ' . count(Property::TRAINING_TYPES) . ' types de formation');
        $this->command->info('   - ' . count(Property::CAREER_FIELDS) . ' métiers');
        $this->command->info('   - ' . count(Property::DEGREE_LEVELS) . ' niveaux de diplôme');
    }
}
