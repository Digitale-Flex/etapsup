<?php

namespace Database\Seeders\regions;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Kdabrow\SeederOnce\SeederOnce;

class FranceRegionsSeeder extends SeederOnce
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cache::tags(['regions', 'cities'])->flush();

        $this->call([
            FranceIleDeFranceSeeder::class,
            FranceHautsDeFranceSeeder::class,
            FranceAuvergneRhoneAlpesSeeder::class,
            FranceProvenceAlpesCoteAzurSeeder::class,
            FranceNouvelleAquitaineSeeder::class,
            FrancePaysDeLaLoireSeeder::class,
            FranceOccitanieSeeder::class,
            FranceGrandEstSeeder::class,
            FranceBretagneSeeder::class,
            FranceNormandieSeeder::class,
            FranceBourgogneFrancheComteSeeder::class,
            FranceCentreValDeLoireSeeder::class,
            FranceCorseSeeder::class,
            FranceGuadeloupeSeeder::class,
            FranceMartiniqueSeeder::class,
            FranceGuyaneSeeder::class,
            FranceReunionSeeder::class,
            FranceMayotteSeeder::class,
        ]);
    }
}
