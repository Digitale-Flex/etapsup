<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Seeder;

/**
 * Seeder pour créer les villes de test (EtatSup)
 * Structure: Country -> Region -> City
 */
class CitySeeder extends Seeder
{
    public function run(): void
    {
        // Créer ou récupérer les pays
        $senegal = Country::firstOrCreate(
            ['name' => 'Sénégal'],
            [
                'code' => 'SN',
                'iso' => 'SN',
                'nationality' => 'Sénégalaise',
                'is_published' => true
            ]
        );

        $coteDivoire = Country::firstOrCreate(
            ['name' => 'Côte d\'Ivoire'],
            [
                'code' => 'CI',
                'iso' => 'CI',
                'nationality' => 'Ivoirienne',
                'is_published' => true
            ]
        );

        // Créer les régions
        $dakarRegion = Region::firstOrCreate(
            ['name' => 'Dakar', 'country_id' => $senegal->id],
            ['is_published' => true]
        );

        $thiesRegion = Region::firstOrCreate(
            ['name' => 'Thiès', 'country_id' => $senegal->id],
            ['is_published' => true]
        );

        $abidjanRegion = Region::firstOrCreate(
            ['name' => 'Abidjan', 'country_id' => $coteDivoire->id],
            ['is_published' => true]
        );

        $yamoussoukroRegion = Region::firstOrCreate(
            ['name' => 'Yamoussoukro', 'country_id' => $coteDivoire->id],
            ['is_published' => true]
        );

        // Créer les villes dans les régions
        $dakar = City::firstOrCreate(
            ['name' => 'Dakar', 'region_id' => $dakarRegion->id],
            ['is_published' => true]
        );

        $thies = City::firstOrCreate(
            ['name' => 'Thiès', 'region_id' => $thiesRegion->id],
            ['is_published' => true]
        );

        $abidjan = City::firstOrCreate(
            ['name' => 'Abidjan', 'region_id' => $abidjanRegion->id],
            ['is_published' => true]
        );

        $yamoussoukro = City::firstOrCreate(
            ['name' => 'Yamoussoukro', 'region_id' => $yamoussoukroRegion->id],
            ['is_published' => true]
        );

        $this->command->info('✅ Pays créés: ' . Country::count());
        $this->command->info('✅ Régions créées: ' . Region::count());
        $this->command->info('✅ Villes créées: ' . City::count());
    }
}
