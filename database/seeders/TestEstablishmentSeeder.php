<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\RealEstate\Category;
use App\Models\RealEstate\Equipment;
use App\Models\RealEstate\Property;
use App\Models\RealEstate\PropertyType;
use App\Models\RealEstate\Regulation;
use App\Models\RealEstate\SubCategory;
use Illuminate\Database\Seeder;

/**
 * Seeder de test pour créer des établissements (EtatSup)
 * MAPPING: Property = Establishment
 */
class TestEstablishmentSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les données de référence
        $universitePublique = PropertyType::where('label', 'Université publique')->first();
        $ecoleIngenieur = PropertyType::where('label', 'École d\'ingénieurs')->first();

        $sciencesTech = Category::where('label', 'Sciences & Technologies')->first();
        $commerce = Category::where('label', 'Commerce & Management')->first();

        $dakar = City::where('name', 'Dakar')->first();
        $abidjan = City::where('name', 'Abidjan')->first();

        $biblio = Equipment::where('label', 'Bibliothèque universitaire')->first();
        $labo = Equipment::where('label', 'Laboratoires de recherche')->first();
        $wifi = Equipment::where('label', 'Wifi campus')->first();

        // Établissement 1: Université Cheikh Anta Diop
        if ($dakar && $universitePublique && $sciencesTech) {
            $ucad = Property::create([
                'title' => 'Université Cheikh Anta Diop',
                'slug' => 'universite-cheikh-anta-diop',
                'description' => 'L\'Université Cheikh Anta Diop de Dakar (UCAD) est la plus grande université du Sénégal. Fondée en 1957, elle offre une formation de qualité dans de nombreux domaines. L\'UCAD est reconnue pour son excellence académique en Afrique de l\'Ouest et accueille des milliers d\'étudiants chaque année. Les programmes couvrent les sciences, les lettres, le droit, la médecine et bien plus encore.',
                'address' => 'Avenue Cheikh Anta Diop, Dakar',
                'city_id' => $dakar->id,
                'property_type_id' => $universitePublique->id,
                'establishment_type' => Property::ESTABLISHMENT_TYPES[0],
                'training_type' => Property::TRAINING_TYPES[0],
                'career_field' => Property::CAREER_FIELDS[2],
                'degree_level' => Property::DEGREE_LEVELS[3],
                'category_id' => $sciencesTech->id,
                'price' => 0, // Dummy value - not exposed in API
                'is_published' => true,
            ]);

            // Attacher les équipements
            if ($biblio) $ucad->equipments()->attach($biblio->id);
            if ($labo) $ucad->equipments()->attach($labo->id);
            if ($wifi) $ucad->equipments()->attach($wifi->id);

            $this->command->info('✅ Établissement créé: ' . $ucad->title);
        }

        // Établissement 2: École Polytechnique de Dakar
        if ($dakar && $ecoleIngenieur && $sciencesTech) {
            $epd = Property::create([
                'title' => 'École Polytechnique de Dakar',
                'slug' => 'ecole-polytechnique-dakar',
                'description' => 'L\'École Polytechnique de Dakar forme des ingénieurs de haut niveau dans les domaines du génie civil, électrique, mécanique et informatique. Créée pour répondre aux besoins en ingénierie du continent africain, l\'école propose des formations théoriques et pratiques de qualité avec des stages en entreprise.',
                'address' => 'Route de la Corniche Ouest, Dakar',
                'city_id' => $dakar->id,
                'property_type_id' => $ecoleIngenieur->id,
                'establishment_type' => Property::ESTABLISHMENT_TYPES[2],
                'training_type' => Property::TRAINING_TYPES[1],
                'career_field' => Property::CAREER_FIELDS[1],
                'degree_level' => Property::DEGREE_LEVELS[4],
                'category_id' => $sciencesTech->id,
                'price' => 0, // Dummy value - not exposed in API
                'is_published' => true,
            ]);

            if ($biblio) $epd->equipments()->attach($biblio->id);
            if ($labo) $epd->equipments()->attach($labo->id);

            $this->command->info('✅ Établissement créé: ' . $epd->title);
        }

        // Établissement 3: Institut National Polytechnique Abidjan
        if ($abidjan && $ecoleIngenieur && $sciencesTech) {
            $inpa = Property::create([
                'title' => 'Institut National Polytechnique Félix Houphouët-Boigny',
                'slug' => 'inp-hb-abidjan',
                'description' => 'L\'Institut National Polytechnique Félix Houphouët-Boigny (INP-HB) est la plus grande école d\'ingénieurs de Côte d\'Ivoire. Elle forme des ingénieurs et des techniciens supérieurs dans divers domaines : mines, géologie, télécommunications, informatique, et bien d\'autres. L\'établissement est reconnu pour son excellence et ses partenariats internationaux.',
                'address' => 'BP 1093 Yamoussoukro',
                'city_id' => $abidjan->id,
                'property_type_id' => $ecoleIngenieur->id,
                'establishment_type' => Property::ESTABLISHMENT_TYPES[2],
                'training_type' => Property::TRAINING_TYPES[2],
                'career_field' => Property::CAREER_FIELDS[1],
                'degree_level' => Property::DEGREE_LEVELS[4],
                'category_id' => $sciencesTech->id,
                'price' => 0, // Dummy value - not exposed in API
                'is_published' => true,
            ]);

            if ($labo) $inpa->equipments()->attach($labo->id);
            if ($wifi) $inpa->equipments()->attach($wifi->id);

            $this->command->info('✅ Établissement créé: ' . $inpa->title);
        }

        $this->command->info('🎓 Seeders d\'établissements de test terminés avec succès !');
    }
}
