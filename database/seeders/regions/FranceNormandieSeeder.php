<?php

namespace Database\Seeders\regions;

use App\Models\Country;
use Database\Seeders\base\BaseRegionSeeder;

class FranceNormandieSeeder extends BaseRegionSeeder
{
    protected string $regionName = 'Normandie';

    /**
     * Run the database seeds.
     *
     * @throws \Exception
     */
    public function run(): void
    {
        $france = $this->getFrance();
        $csvFile = $this->getCsvPath();

        if (! file_exists($csvFile)) {
            $this->command->error('Le fichier CSV des communes n\'a pas été trouvé.');

            return;
        }

        $this->command->info("Traitement des communes de {$this->regionName}...");

        // Lecture optimisée du CSV avec SplFileObject
        $file = new \SplFileObject($csvFile);
        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
        $file->setCsvControl(';');

        // Sauter l'en-tête
        $file->fgetcsv();

        $cities = [];
        while (! $file->eof()) {
            $data = $file->fgetcsv();
            if ($data && $data[0] === $this->regionName) {
                $cities[] = $data[1];
            }
        }

        $this->createRegionWithCities($cities, $france);
    }
}
