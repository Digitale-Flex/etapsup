<?php

namespace Database\Seeders\base;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Kdabrow\SeederOnce\SeederOnce;

class BaseRegionSeeder extends SeederOnce
{
    protected string $regionName;

    protected static array $regionStats = [];

    private array $normalizedCache = [];

    private array $existingCities = [];

    private array $existingRegions = [];

    protected const BATCH_SIZE = 5000;

    protected const CACHE_TTL = 3600;

    protected function getOrCreateRegion(string $regionName, Country $france): ?Region
    {
        // Ignorer les noms vides ou invalides
        $normalizedRegionName = $this->normalizeName($regionName);
        if (empty($normalizedRegionName)) {
            return null;
        }

        // Charger les régions si pas encore fait
        if (empty($this->existingRegions)) {
            $regions = Region::where('country_id', $france->id)->get();
            foreach ($regions as $region) {
                $normalizedName = $this->normalizeName($region->name);
                $this->existingRegions[$normalizedName] = $region;
            }

            $this->command->info('Régions existantes chargées : '.count($this->existingRegions));
            foreach ($this->existingRegions as $name => $region) {
                $this->command->info("- $name (ID: {$region->id})");
            }
        }

        // Vérifier si la région existe déjà
        if (! isset($this->existingRegions[$normalizedRegionName])) {
            $this->command->info("Création de la nouvelle région : $normalizedRegionName");

            $region = Region::create([
                'name' => $normalizedRegionName,
                'country_id' => $france->id,
                'is_published' => true,
            ]);

            $this->existingRegions[$normalizedRegionName] = $region;

            return $region;
        }

        return $this->existingRegions[$normalizedRegionName];
    }

    protected function normalizeName(string $name): string
    {
        if ($name === 'Region' || $name === 'Région') {
            return '';  // Ignorer la ligne d'en-tête
        }

        $cacheKey = md5($name);
        if (isset($this->normalizedCache[$cacheKey])) {
            return $this->normalizedCache[$cacheKey];
        }

        // Force l'encodage en UTF-8 avec détection de l'encodage source
        $name = mb_convert_encoding($name, 'UTF-8', mb_detect_encoding($name, 'UTF-8, ISO-8859-1'));

        // Corrections spécifiques pour les noms de régions
        $corrections = [
            'ile-de-france' => 'Île-de-France',
            'ile de france' => 'Île-de-France',
            'auvergne-rhone-alpes' => 'Auvergne-Rhône-Alpes',
            'auvergne-rhône-alpes' => 'Auvergne-Rhône-Alpes',
            'provence-alpes-cote d\'azur' => 'Provence-Alpes-Côte d\'Azur',
            'bourgogne-franche-comte' => 'Bourgogne-Franche-Comté',
            'la reunion' => 'La Réunion',
            'reunion' => 'La Réunion',
            'la réunion' => 'La Réunion',
            'mayotte' => 'Mayotte',
            'guyane' => 'Guyane',
            'guadeloupe' => 'Guadeloupe',
            'martinique' => 'Martinique',
        ];

        // Normalisation de base
        $name = preg_replace('/\s+/', ' ', trim($name));
        $name = mb_strtolower($name);

        // Création d'un tableau de recherche/remplacement pour les caractères spéciaux
        $search = ['?', "\xE2\x80\x99", '�'];
        $replace = ['é', "'", 'ô'];
        $name = str_replace($search, $replace, $name);

        // Appliquer les corrections si nécessaire
        if (isset($corrections[$name])) {
            $name = $corrections[$name];
        } else {
            // Normalisation standard si pas de correction spécifique
            $name = preg_replace('/\s*[\-\–\—]\s*/', '-', $name);
            $name = preg_replace('/[\'\'\'\`\´]/', "'", $name);
            $name = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
        }

        $this->normalizedCache[$cacheKey] = $name;

        return $name;
    }

    protected function loadExistingCities(Region $region): void
    {
        $cities = City::where('region_id', $region->id)
            ->select('name', 'budget', 'is_published')
            ->get();

        $this->existingCities = [];
        foreach ($cities as $city) {
            $normalizedName = $this->normalizeName($city->name);
            $this->existingCities[$normalizedName] = [
                'budget' => $city->budget,
                'is_published' => $city->is_published,
            ];
        }

        $this->command->info(sprintf(
            'Chargement de %d villes existantes pour la région %s',
            count($this->existingCities),
            $region->name
        ));
    }

    protected function processCityBatch(array $cities, Region $region): void
    {
        $newCities = [];
        $skippedCount = 0;
        $cityNames = [];
        $debugCount = 0;

        foreach ($cities as $city) {
            if (empty($city)) {
                continue;
            }

            $normalizedName = $this->normalizeName($city);
            $debugCount++;

            if (empty($normalizedName)) {
                continue;
            }

            // Log pour debug
            if ($debugCount <= 5) {
                $this->command->info("Debug - Original: '$city' => Normalisé: '$normalizedName'");
            }

            // Vérifier les doublons dans le lot actuel et la BDD
            if (in_array($normalizedName, $cityNames)) {
                $this->command->info("Ville dupliquée ignorée: $normalizedName");

                continue;
            }

            if (isset($this->existingCities[$normalizedName])) {
                $skippedCount++;
                if ($debugCount <= 5) {
                    $this->command->info("Ville existante ignorée: $normalizedName");
                }

                continue;
            }

            $cityNames[] = $normalizedName;
            $newCities[] = [
                'name' => $normalizedName,
                'region_id' => $region->id,
                'budget' => 499,
                'is_published' => true,
            ];
        }

        if (! empty($newCities)) {
            try {
                City::insert($newCities);
                $this->command->info(sprintf(
                    'Ajout de %d nouvelles villes (ignoré %d villes existantes) pour la région %s',
                    count($newCities),
                    $skippedCount,
                    $region->name
                ));
            } catch (\Exception $e) {
                $this->command->error("Erreur lors de l'insertion des villes: ".$e->getMessage());
                throw $e;
            }
        } else {
            $this->command->warn('Aucune nouvelle ville à ajouter pour '.$region->name);
        }
    }

    protected function createRegionWithCities(array $cities, Country $france): void
    {
        DB::beginTransaction();

        try {
            $region = $this->getOrCreateRegion($this->regionName, $france);
            if (! $region) {
                $this->command->warn("Région ignorée : {$this->regionName}");
                DB::rollBack();

                return;
            }

            // Log pour debug
            $this->command->info("Traitement des villes pour la région : {$this->regionName}");
            $this->command->info('Nombre de villes à traiter : '.count($cities));

            // Charger les villes existantes pour éviter les doublons
            $this->loadExistingCities($region);

            // Traiter les villes par lots
            foreach (array_chunk($cities, self::BATCH_SIZE) as $cityBatch) {
                $this->processCityBatch($cityBatch, $region);
            }

            DB::commit();
            $this->command->info("Transaction validée avec succès pour la région : {$this->regionName}");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Erreur lors du traitement de la région {$this->regionName}: ".$e->getMessage());
            throw $e;
        }
    }

    protected function displayRegionSummary(): void
    {
        $this->command->newLine();
        $this->command->info('=== Rapport d\'importation détaillé ===');

        foreach (self::$regionStats as $region => $stats) {
            $this->command->info(sprintf(
                '%s: %d communes totales (%d existantes, %d ajoutées)',
                $region,
                $stats['total'],
                $stats['skipped'],
                $stats['added']
            ));
        }

        // Afficher la liste des régions
        $this->command->newLine();
        $this->command->info('=== Régions dans la base de données ===');
        foreach ($this->existingRegions as $normalizedName => $region) {
            $this->command->info(sprintf(
                '- %s (ID: %d)',
                $region->name,
                $region->id
            ));
        }
    }

    protected function getFrance(): Country
    {
        return Cache::remember('country_france', self::CACHE_TTL, function () {
            return Country::where('name', 'France')
                ->orWhere('iso', 'FR')
                ->orWhere('code', '33')
                ->firstOrFail();
        });
    }

    protected function getCsvPath(): string
    {
        return storage_path('app/data/Communes_francaises_2024_epure.csv');
    }
}
