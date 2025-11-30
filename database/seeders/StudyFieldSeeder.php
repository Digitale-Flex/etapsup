<?php

namespace Database\Seeders;

use App\Models\RealEstate\Category;
use Illuminate\Database\Seeder;

class StudyFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studyFields = [
            [
                'label' => 'Sciences & Technologies',
                'description' => 'Informatique, mathématiques, physique, chimie et sciences appliquées',
                'is_published' => true,
            ],
            [
                'label' => 'Commerce & Management',
                'description' => 'Business, gestion, marketing, finance et entrepreneuriat',
                'is_published' => true,
            ],
            [
                'label' => 'Santé & Médecine',
                'description' => 'Médecine, pharmacie, soins infirmiers et sciences de la santé',
                'is_published' => true,
            ],
            [
                'label' => 'Droit & Sciences Politiques',
                'description' => 'Droit, relations internationales, sciences politiques et diplomatie',
                'is_published' => true,
            ],
            [
                'label' => 'Arts & Design',
                'description' => 'Beaux-arts, design graphique, architecture et arts appliqués',
                'is_published' => true,
            ],
            [
                'label' => 'Ingénierie',
                'description' => 'Génie civil, mécanique, électrique, industriel et énergétique',
                'is_published' => true,
            ],
        ];

        foreach ($studyFields as $field) {
            Category::updateOrCreate(
                ['label' => $field['label']],
                $field
            );
        }

        $this->command->info('Domaines d\'études créés avec succès !');
    }
}
