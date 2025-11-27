<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\regions\FranceRegionsCitiesSeeder;
use Database\Seeders\regions\FranceRegionsSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            CertificateRequestDataSeeder::class,
            FranceRegionsSeeder::class,
           // FranceRegionsCitiesSeeder::class,
            LayoutSeeder::class,
            SubCategorySeeder::class,
        ]);
    }
}
