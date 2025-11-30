<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\City;
use App\Models\RealEstate\PropertyType;
use App\Models\RealEstate\Property;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EstablishmentsCountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // France: 3 établissements
        $this->createFranceEstablishments();

        // Canada: 3 établissements
        $this->createCanadaEstablishments();

        // USA: 3 établissements
        $this->createUSAEstablishments();
    }

    private function createFranceEstablishments()
    {
        $france = Country::firstOrCreate(
            ['name' => 'France'],
            ['iso' => 'FR', 'code' => '+33', 'nationality' => 'Française', 'is_published' => true]
        );
        $ileDeFrance = Region::firstOrCreate([
            'name' => 'Île-de-France',
            'country_id' => $france->id
        ]);
        $paris = City::firstOrCreate([
            'name' => 'Paris',
            'region_id' => $ileDeFrance->id
        ]);

        $rhoneAlpes = Region::firstOrCreate([
            'name' => 'Auvergne-Rhône-Alpes',
            'country_id' => $france->id
        ]);
        $lyon = City::firstOrCreate([
            'name' => 'Lyon',
            'region_id' => $rhoneAlpes->id
        ]);

        $paca = Region::firstOrCreate([
            'name' => 'Provence-Alpes-Côte d\'Azur',
            'country_id' => $france->id
        ]);
        $marseille = City::firstOrCreate([
            'name' => 'Marseille',
            'region_id' => $paca->id
        ]);

        $propertyType = PropertyType::firstOrCreate(['label' => 'Université']);
        $category = \App\Models\Category::firstOrCreate(['label' => 'Enseignement Supérieur']);

        $establishments = [
            [
                'title' => 'Sorbonne Université',
                'slug' => 'sorbonne-universite-paris',
                'city_id' => $paris->id,
                'description' => 'Université prestigieuse au cœur de Paris, reconnue mondialement pour ses programmes en sciences, lettres et médecine.',
                'price' => 500,
            ],
            [
                'title' => 'Sciences Po Lyon',
                'slug' => 'sciences-po-lyon',
                'city_id' => $lyon->id,
                'description' => 'Institut d\'études politiques de Lyon, formation d\'excellence en sciences politiques et relations internationales.',
                'price' => 450,
            ],
            [
                'title' => 'Aix-Marseille Université',
                'slug' => 'aix-marseille-universite',
                'city_id' => $marseille->id,
                'description' => 'Plus grande université francophone, offrant une large gamme de formations dans tous les domaines.',
                'price' => 400,
            ],
        ];

        foreach ($establishments as $data) {
            Property::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'city_id' => $data['city_id'],
                    'property_type_id' => $propertyType->id,
                    'category_id' => $category->id,
                    'is_published' => true,
                ]
            );
        }
    }

    private function createCanadaEstablishments()
    {
        $canada = Country::firstOrCreate(
            ['name' => 'Canada'],
            ['iso' => 'CA', 'code' => '+1', 'nationality' => 'Canadienne', 'is_published' => true]
        );

        $quebec = Region::firstOrCreate([
            'name' => 'Québec',
            'country_id' => $canada->id
        ]);
        $montreal = City::firstOrCreate([
            'name' => 'Montréal',
            'region_id' => $quebec->id
        ]);
        $quebecCity = City::firstOrCreate([
            'name' => 'Québec',
            'region_id' => $quebec->id
        ]);

        $ontario = Region::firstOrCreate([
            'name' => 'Ontario',
            'country_id' => $canada->id
        ]);
        $toronto = City::firstOrCreate([
            'name' => 'Toronto',
            'region_id' => $ontario->id
        ]);

        $propertyType = PropertyType::firstOrCreate(['label' => 'Université']);
        $category = \App\Models\Category::firstOrCreate(['label' => 'Enseignement Supérieur']);

        $establishments = [
            [
                'title' => 'McGill University',
                'slug' => 'mcgill-university-montreal',
                'city_id' => $montreal->id,
                'description' => 'Université de recherche de renommée mondiale située à Montréal, excellence académique et diversité culturelle.',
                'price' => 15000,
            ],
            [
                'title' => 'Université Laval',
                'slug' => 'universite-laval-quebec',
                'city_id' => $quebecCity->id,
                'description' => 'La plus ancienne université francophone d\'Amérique, campus moderne et programmes innovants.',
                'price' => 12000,
            ],
            [
                'title' => 'University of Toronto',
                'slug' => 'university-of-toronto',
                'city_id' => $toronto->id,
                'description' => 'Institution de classe mondiale, leader en recherche et innovation, située au cœur de Toronto.',
                'price' => 16000,
            ],
        ];

        foreach ($establishments as $data) {
            Property::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'city_id' => $data['city_id'],
                    'property_type_id' => $propertyType->id,
                    'category_id' => $category->id,
                    'is_published' => true,
                ]
            );
        }
    }

    private function createUSAEstablishments()
    {
        $usa = Country::firstOrCreate(['name' => 'USA']);

        $massachusetts = Region::firstOrCreate([
            'name' => 'Massachusetts',
            'country_id' => $usa->id
        ]);
        $boston = City::firstOrCreate([
            'name' => 'Boston',
            'region_id' => $massachusetts->id
        ]);

        $california = Region::firstOrCreate([
            'name' => 'California',
            'country_id' => $usa->id
        ]);
        $losAngeles = City::firstOrCreate([
            'name' => 'Los Angeles',
            'region_id' => $california->id
        ]);

        $newYork = Region::firstOrCreate([
            'name' => 'New York',
            'country_id' => $usa->id
        ]);
        $newYorkCity = City::firstOrCreate([
            'name' => 'New York',
            'region_id' => $newYork->id
        ]);

        $propertyType = PropertyType::firstOrCreate(['label' => 'Université']);
        $category = \App\Models\Category::firstOrCreate(['label' => 'Enseignement Supérieur']);

        $establishments = [
            [
                'title' => 'Harvard University',
                'slug' => 'harvard-university-boston',
                'city_id' => $boston->id,
                'description' => 'L\'université la plus prestigieuse au monde, excellence académique légendaire et réseau d\'alumni incomparable.',
                'price' => 50000,
            ],
            [
                'title' => 'UCLA - University of California',
                'slug' => 'ucla-los-angeles',
                'city_id' => $losAngeles->id,
                'description' => 'Campus spectaculaire à Los Angeles, programmes de renommée mondiale et vie étudiante exceptionnelle.',
                'price' => 45000,
            ],
            [
                'title' => 'Columbia University',
                'slug' => 'columbia-university-new-york',
                'city_id' => $newYorkCity->id,
                'description' => 'Université Ivy League au cœur de Manhattan, formation d\'élite et opportunités uniques à New York.',
                'price' => 55000,
            ],
        ];

        foreach ($establishments as $data) {
            Property::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'city_id' => $data['city_id'],
                    'property_type_id' => $propertyType->id,
                    'category_id' => $category->id,
                    'is_published' => true,
                ]
            );
        }
    }
}
