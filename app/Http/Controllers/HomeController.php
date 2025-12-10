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
                'city.country', // A20: region supprimé
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

    // Refonte: Nouvelle landing page moderne /accueil inspirée de Diplomeo
    public function accueil(): \Inertia\Response
    {
        // Récupération 6 établissements populaires/récents
        $featuredEstablishments = \Illuminate\Support\Facades\Cache::remember('accueil_establishments', 3600, function() {
            return Property::with([
                'propertyType',
                'city.country', // A20: region supprimé
                'ratings',
                'media'
            ])
                ->where('is_published', true)
                ->orderBy('ranking', 'asc')
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get()
                ->map(function($property) {
                    return [
                        'id' => $property->hashid,
                        'slug' => $property->slug,
                        'title' => $property->title,
                        'city' => $property->city?->name ?? 'Non spécifié',
                        'country' => $property->city?->region?->country?->name ?? 'Non spécifié',
                        'type' => $property->propertyType?->label ?? 'Établissement',
                        'ranking' => $property->ranking,
                        'studentCount' => $property->student_count ?? rand(100, 500),
                        'image' => $property->getFirstMediaUrl('images', 'thumb') ?: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800',
                    ];
                });
        });

        // Pays
        $countries = Country::select('id', 'name')
            ->whereHas('cities.properties', function($q) { // A20
                $q->where('is_published', true);
            })
            ->get();

        // Domaines d'études populaires (top 8)
        $studyFields = Category::select('id', 'label as name')
            ->where('is_published', true)
            ->withCount(['properties' => fn($q) => $q->where('is_published', true)])
            ->orderBy('properties_count', 'desc')
            ->limit(8)
            ->get();

        // Statistiques réelles
        $stats = [
            'totalEstablishments' => Property::where('is_published', true)->count(),
            'totalStudents' => 2500, // Hardcodé
            'totalCountries' => Country::whereHas('cities.properties', function($q) {
                $q->where('is_published', true);
            })->count(),
            'totalPrograms' => Program::count(),
        ];

        // Témoignages (données statiques pour MVP)
        $testimonials = [
            [
                'name' => 'Sophie Martin',
                'country' => 'France',
                'photo' => 'https://ui-avatars.com/api/?name=Sophie+Martin&background=1e3a8a&color=fff&size=128',
                'rating' => 5,
                'text' => 'EtapSup m\'a aidé à trouver l\'université parfaite pour mes études en gestion. Le processus était simple et l\'accompagnement excellent !',
            ],
            [
                'name' => 'Mohamed Diallo',
                'country' => 'Sénégal',
                'photo' => 'https://ui-avatars.com/api/?name=Mohamed+Diallo&background=1e3a8a&color=fff&size=128',
                'rating' => 5,
                'text' => 'Grâce à EtapSup, j\'ai pu comparer plusieurs établissements et choisir celui qui correspondait le mieux à mon projet professionnel.',
            ],
            [
                'name' => 'Amina Kouassi',
                'country' => 'Côte d\'Ivoire',
                'photo' => 'https://ui-avatars.com/api/?name=Amina+Kouassi&background=1e3a8a&color=fff&size=128',
                'rating' => 5,
                'text' => 'L\'équipe EtapSup a été d\'un grand soutien durant tout mon processus d\'admission. Je recommande vivement leurs services !',
            ],
        ];

        return Inertia::render('Home/Accueil', [
            'featuredEstablishments' => $featuredEstablishments,
            'countries' => $countries,
            'studyFields' => $studyFields,
            'stats' => $stats,
            'testimonials' => $testimonials,
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
