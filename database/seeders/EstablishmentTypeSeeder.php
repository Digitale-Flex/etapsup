<?php

namespace Database\Seeders;

use App\Models\RealEstate\PropertyType;
use Illuminate\Database\Seeder;

class EstablishmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['label' => 'Université publique', 'is_published' => true],
            ['label' => 'Université privée', 'is_published' => true],
            ['label' => 'École d\'ingénieurs', 'is_published' => true],
            ['label' => 'École de commerce', 'is_published' => true],
            ['label' => 'École spécialisée', 'is_published' => true],
            ['label' => 'Institut de formation', 'is_published' => true],
        ];

        foreach ($types as $type) {
            PropertyType::updateOrCreate(
                ['label' => $type['label']],
                $type
            );
        }

        $this->command->info('Types d\'établissements créés avec succès !');
    }
}
