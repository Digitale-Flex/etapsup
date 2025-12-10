<?php

namespace Database\Seeders;

use App\Models\RealEstate\PropertyType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CleanLegacyPropertyTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Supprime les types immobiliers hérités de ma-Reza qui ne sont pas pertinents pour EtapSup
     */
    public function run(): void
    {
        $legacyTypes = [
            'Appartement',
            'Villa',
            'Chalet',
            'Studio',
            'Maison',
            'Duplex',
            'Loft',
            'Penthouse',
            'Chambre',
        ];

        $deleted = PropertyType::whereIn('label', $legacyTypes)->delete();

        $this->command->info("✅ Supprimé {$deleted} types immobiliers legacy (ma-Reza)");
        $this->command->info("🎓 Les types valides pour EtapSup sont conservés (Université, École, etc.)");
    }
}
