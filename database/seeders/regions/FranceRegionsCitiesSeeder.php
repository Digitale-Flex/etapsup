<?php

namespace Database\Seeders\regions;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Support\Facades\DB;
use Kdabrow\SeederOnce\SeederOnce;

class FranceRegionsCitiesSeeder extends SeederOnce
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactive temporairement les clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Récupération de l'ID du pays France
        $france = Country::where('name', 'France')
            ->orWhere('iso', 'FR')
            ->orWhere('code', '33')
            ->first();

        if (! $france) {
            $this->command->error('Le pays France n\'a pas été trouvé dans la base de données.');

            return;
        }

        // Définition des régions avec leurs villes
        $regionsWithCities = [
            'Île-de-France' => [
                'Paris', 'Versailles', 'Saint-Denis', 'Boulogne-Billancourt',
                'Argenteuil', 'Colombes', 'Cergy', 'Yerres', 'Melun', 'Puteaux',
                'Vincennes', 'Montreuil', 'Clichy', 'Villeparisis', 'Noisy-le-Grand',
                'Les Ulis', 'Ivry-sur-Seine', 'Chevilly-Larue', 'Evry-Courcouronnes',
                'Choisy-le-Roi', 'La Garenne-Colombes', 'Nanterre', 'Bagnolet',
                'Créteil', 'Lognes',
            ],
            'Hauts-de-France' => [
                'Lille', 'Amiens', 'Calais', 'Dunkerque', 'Roubaix', 'Lens', 'Loos',
            ],
            'Auvergne-Rhône-Alpes' => [
                'Lyon', 'Grenoble', 'Saint-Étienne', 'Clermont-Ferrand',
                'Annecy', 'Bron', 'Villeurbanne',
            ],
            'Provence-Alpes-Côte d\'Azur' => [
                'Marseille', 'Nice', 'Toulon', 'Aix-en-Provence', 'Avignon', 'Biot',
            ],
            'Nouvelle-Aquitaine' => [
                'Bordeaux', 'Limoges', 'Poitiers', 'Pau', 'La Rochelle', 'Niort',
            ],
            'Pays de la Loire' => [
                'Nantes', 'Angers', 'Le Mans', 'Saint-Nazaire', 'Laval',
                'Saint-Barthélemy-d\'Anjou',
            ],
            'Occitanie' => [
                'Toulouse', 'Montpellier', 'Nîmes', 'Perpignan', 'Tarbes',
                'Béziers', 'Mende',
            ],
            'Grand Est' => [
                'Strasbourg', 'Reims', 'Metz', 'Nancy', 'Mulhouse', 'Troyes',
                'Vandœuvre-lès-Nancy',
            ],
            'Bretagne' => [
                'Rennes', 'Brest', 'Quimper', 'Vannes', 'Saint-Malo', 'Lorient',
            ],
            'Normandie' => [
                'Rouen', 'Caen', 'Le Havre', 'Cherbourg', 'Évreux',
            ],
            'Bourgogne-Franche-Comté' => [
                'Dijon', 'Besançon', 'Belfort', 'Nevers', 'Auxerre',
            ],
            'Centre-Val de Loire' => [
                'Orléans', 'Tours', 'Bourges', 'Blois', 'Chartres',
            ],
        ];

        // Supprime d'abord toutes les villes existantes
        City::truncate();

        // Supprime toutes les régions existantes
        Region::where('country_id', $france->id)->delete();

        // Crée les nouvelles régions et villes
        foreach ($regionsWithCities as $regionName => $cities) {
            $region = Region::create([
                'name' => $regionName,
                'country_id' => $france->id,
                'is_published' => true,
            ]);

            foreach ($cities as $cityName) {
                City::create([
                    'name' => $this->normalizeName($cityName),
                    'region_id' => $region->id,
                    'is_published' => true,
                ]);
            }
        }

        // Réactive les clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('Les régions et villes de France ont été mises à jour avec succès.');
    }

    /**
     * Normalise le nom (gestion de la casse et des accents)
     */
    private function normalizeName(string $name): string
    {
        // Convertit en UTF-8 si nécessaire
        if (! mb_check_encoding($name, 'UTF-8')) {
            $name = mb_convert_encoding($name, 'UTF-8');
        }

        // Supprime les espaces multiples
        $name = preg_replace('/\s+/', ' ', trim($name));

        // Gestion des tirets
        $name = str_replace(' - ', '-', $name);

        // Première lettre de chaque mot en majuscule
        return mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
    }
}
