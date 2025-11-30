<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\RealEstate\Category;
use App\Models\RealEstate\Property;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les catégories (domaines d'études)
        $scienceTech = Category::where('label', 'Sciences & Technologies')->first();
        $commerce = Category::where('label', 'Commerce & Management')->first();
        $sante = Category::where('label', 'Santé & Médecine')->first();
        $droit = Category::where('label', 'Droit & Sciences Politiques')->first();
        $arts = Category::where('label', 'Arts & Design')->first();

        // Récupérer les properties (établissements)
        $properties = Property::limit(5)->get();

        if ($properties->count() < 5) {
            $this->command->warn('Pas assez de properties pour créer les programmes');
            return;
        }

        $programs = [
            [
                'establishment_id' => $properties[0]->id,
                'study_field_id' => $scienceTech?->id,
                'name' => 'Licence Informatique et Systèmes d\'Information',
                'description' => 'Formation complète en développement logiciel, bases de données et architecture système',
                'degree_level' => 'Licence',
                'duration' => 6,
                'language' => 'Français',
                'tuition_fee' => 1500.00,
                'is_published' => true,
            ],
            [
                'establishment_id' => $properties[1]->id,
                'study_field_id' => $commerce?->id,
                'name' => 'Master Commerce International',
                'description' => 'Expertise en stratégies commerciales internationales et gestion des échanges',
                'degree_level' => 'Master',
                'duration' => 4,
                'language' => 'Français',
                'tuition_fee' => 3000.00,
                'is_published' => true,
            ],
            [
                'establishment_id' => $properties[2]->id,
                'study_field_id' => $sante?->id,
                'name' => 'Doctorat en Médecine',
                'description' => 'Formation médicale complète avec spécialisations cliniques',
                'degree_level' => 'Doctorat',
                'duration' => 12,
                'language' => 'Français',
                'tuition_fee' => 5000.00,
                'is_published' => true,
            ],
            [
                'establishment_id' => $properties[3]->id,
                'study_field_id' => $droit?->id,
                'name' => 'Master Droit des Affaires',
                'description' => 'Spécialisation en droit commercial, fiscal et des sociétés',
                'degree_level' => 'Master',
                'duration' => 4,
                'language' => 'Français',
                'tuition_fee' => 2500.00,
                'is_published' => true,
            ],
            [
                'establishment_id' => $properties[4]->id,
                'study_field_id' => $arts?->id,
                'name' => 'Licence Design Graphique et Multimédia',
                'description' => 'Créativité numérique, design UX/UI et communication visuelle',
                'degree_level' => 'Licence',
                'duration' => 6,
                'language' => 'Français',
                'tuition_fee' => 2000.00,
                'is_published' => true,
            ],
        ];

        foreach ($programs as $programData) {
            if ($programData['study_field_id']) {
                Program::create($programData);
            }
        }

        $this->command->info('Programmes d\'études créés avec succès !');
    }
}
