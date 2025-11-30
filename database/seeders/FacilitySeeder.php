<?php

namespace Database\Seeders;

use App\Models\RealEstate\Equipment;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facilities = [
            ['label' => 'Bibliothèque universitaire'],
            ['label' => 'Laboratoires de recherche'],
            ['label' => 'Résidence étudiante'],
            ['label' => 'Centre sportif'],
            ['label' => 'Restaurant universitaire'],
            ['label' => 'Wifi campus'],
            ['label' => 'Parking étudiant'],
            ['label' => 'Salle informatique'],
            ['label' => 'Amphithéâtre'],
            ['label' => 'Espace coworking'],
        ];

        foreach ($facilities as $facility) {
            Equipment::updateOrCreate(
                ['label' => $facility['label']],
                $facility
            );
        }

        $this->command->info('Infrastructures campus créées avec succès !');
    }
}
