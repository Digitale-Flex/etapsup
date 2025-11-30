<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use App\Models\RealEstate\Category;
use App\Models\RealEstate\Equipment;
use App\Models\RealEstate\Property;
use App\Models\RealEstate\PropertyType;
use Illuminate\Database\Seeder;

/**
 * Seeder rapide pour créer les données minimales de test
 */
class QuickTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Création des données de test...');

        // 1. Pays
        $senegal = Country::firstOrCreate(
            ['iso' => 'SN'],
            ['name' => 'Sénégal', 'iso' => 'SN', 'code' => 'SEN', 'nationality' => 'Sénégalaise', 'is_published' => true]
        );

        $cotedivoire = Country::firstOrCreate(
            ['iso' => 'CI'],
            ['name' => 'Côte d\'Ivoire', 'iso' => 'CI', 'code' => 'CIV', 'nationality' => 'Ivoirienne', 'is_published' => true]
        );

        // 2. Régions
        $dakarRegion = Region::firstOrCreate(
            ['name' => 'Dakar', 'country_id' => $senegal->id],
            ['country_id' => $senegal->id]
        );

        $abidjanRegion = Region::firstOrCreate(
            ['name' => 'Abidjan', 'country_id' => $cotedivoire->id],
            ['country_id' => $cotedivoire->id]
        );

        // 3. Villes
        $dakar = City::firstOrCreate(
            ['name' => 'Dakar', 'country_id' => $senegal->id],
            ['region_id' => $dakarRegion->id]
        );

        $abidjan = City::firstOrCreate(
            ['name' => 'Abidjan', 'country_id' => $cotedivoire->id],
            ['region_id' => $abidjanRegion->id]
        );

        $this->command->info('✅ Pays et villes créés');

        // 4. Établissements de test
        $type = PropertyType::first();
        $category = Category::first();
        $equipment = Equipment::first();

        if ($type && $category) {
            $ucad = Property::firstOrCreate(
                ['slug' => 'universite-cheikh-anta-diop'],
                [
                    'title' => 'Université Cheikh Anta Diop (UCAD)',
                    'description' => 'L\'Université Cheikh Anta Diop de Dakar (UCAD) est la plus grande université du Sénégal. Fondée en 1957, elle offre une formation de qualité dans de nombreux domaines. L\'UCAD est reconnue pour son excellence académique en Afrique de l\'Ouest et accueille des milliers d\'étudiants chaque année.',
                    'address' => 'Avenue Cheikh Anta Diop, Fann, Dakar',
                    'city_id' => $dakar->id,
                    'property_type_id' => $type->id,
                    'establishment_type' => Property::ESTABLISHMENT_TYPES[0],
                    'training_type' => Property::TRAINING_TYPES[0],
                    'career_field' => Property::CAREER_FIELDS[2],
                    'degree_level' => Property::DEGREE_LEVELS[3],
                    'category_id' => $category->id,
                    'is_published' => true,
                ]
            );

            if ($equipment) {
                $ucad->equipments()->syncWithoutDetaching([$equipment->id]);
            }

            $this->command->info('✅ Établissement créé: ' . $ucad->title);
            $this->command->info('📌 URL de test: /properties/' . $ucad->slug);
        }

        $this->command->info('🎓 Données de test créées avec succès !');
    }
}
