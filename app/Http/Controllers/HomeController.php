<?php

namespace App\Http\Controllers;

use App\Http\Filters\PropertyFilter;
use App\Http\Resources\CityResource;
use App\Http\Resources\EquipmentResource;
use App\Http\Resources\LayoutResource;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\PropertyTypeResource;
use App\Http\Resources\SubCategoryResource;
use App\Models\City;
use App\Models\Country;
use App\Models\Program;
use App\Models\RealEstate\Category;
use App\Models\RealEstate\Equipment;
use App\Models\RealEstate\Layout;
use App\Models\RealEstate\Property;
use App\Models\RealEstate\PropertyType;
use App\Models\RealEstate\Regulation;
use App\Models\RealEstate\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __construct(
        protected readonly Property     $property,
        protected readonly PropertyType $propertyType,
        protected readonly Category     $category,
        protected readonly SubCategory $subCategory,
        protected readonly Regulation   $regulation,
        protected readonly Layout       $layout,
        protected readonly Equipment    $equipment,
        protected readonly City       $city,
    ){}
    public function index(Request $request, PropertyFilter $filter): \Inertia\Response
    {
        // Refonte Accueil Dynamique: Récupération 6 établissements réels
        $establishments = \Illuminate\Support\Facades\Cache::remember('home_establishments', 3600, function() {
            return Property::with([
                'propertyType',
                'city.region.country',
                'ratings',
                'programs' => fn($q) => $q->where('is_published', true)
                    ->with(['studyField', 'degreeLevel'])
            ])
                ->where('is_published', true)
                ->inRandomOrder()
                ->limit(6)
                ->get()
                ->map(function($property) {
                    return new \App\Http\Resources\EstablishmentResource($property);
                });
        });

        // Pays
        $countries = Country::select('id', 'name as name')->get();

        // Villes
        $cities = City::select('id', 'name as name')->get();

        // Domaines d'études (Categories)
        $studyFields = Category::select('id', 'label as name')
            ->where('is_published', true)
            ->get();

        // Types d'établissements (PropertyTypes)
        $establishmentTypes = PropertyType::select('id', 'label as name')
            ->where('is_published', true)
            ->get();

        // Statistiques réelles
        $stats = [
            'totalEstablishments' => Property::count(),
            'totalStudents' => 2000, // Hardcodé pour l'instant (pas de table users students)
            'totalCountries' => Country::count(),
            'totalPrograms' => Program::count(),
        ];

        return Inertia::render('Home/Index', [
            'establishments' => $establishments,
            'countries' => $countries,
            'cities' => $cities,
            'studyFields' => $studyFields,
            'establishmentTypes' => $establishmentTypes,
            'stats' => $stats
        ]);
    }

    protected function formatFilters(Request $request): array
    {
        $filters = $request->only([
            'types',
            'category',
            'regulations',
            'price_min',
            'price_max',
            'rooms',
            'bathrooms',
            'city'
        ]);

        // Convert string numbers to actual numbers
        if (isset($filters['price_min'])) {
            $filters['price_min'] = (float)$filters['price_min'];
        }
        if (isset($filters['price_max'])) {
            $filters['price_max'] = (float)$filters['price_max'];
        }
        if (isset($filters['rooms'])) {
            $filters['rooms'] = (int)$filters['rooms'];
        }
        if (isset($filters['bathrooms'])) {
            $filters['bathrooms'] = (int)$filters['bathrooms'];
        }

        // Convert single values to arrays where needed
        if (isset($filters['types']) && !is_array($filters['types'])) {
            $filters['types'] = [$filters['types']];
        }
        if (isset($filters['regulations']) && !is_array($filters['regulations'])) {
            $filters['regulations'] = [$filters['regulations']];
        }
        if (isset($filters['city'])) {
            $filters['city'] = (int)$filters['city'];
        }

        return $filters;
    }
}
