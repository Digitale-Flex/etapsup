<?php

namespace App\Http\Controllers;

use App\Http\Resources\EstablishmentResource;
use App\Models\City;
use App\Models\Country;
use App\Models\RealEstate\Category;
use App\Models\RealEstate\Property;
use App\Models\RealEstate\PropertyType;
use App\Models\Settings\EstablishmentType;
use App\Models\Settings\TrainingType;
use App\Models\Settings\CareerField;
use App\Models\Settings\DegreeLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Controller pour la liste des établissements (EtatSup)
 *
 * MAPPING ETATSUP:
 * - Property = Establishment (Établissement scolaire)
 * - PropertyType = EstablishmentType (Type d'établissement)
 * - Category = StudyField (Domaine d'études)
 */
class EstablishmentController extends Controller
{
    /**
     * Affiche la liste des établissements avec filtres
     * Story 1.3.1: Filtrer les établissements (style Diplomeo)
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // Construction de la requête de base
        $query = Property::query()
            ->with([
                'propertyType',      // Type d'établissement
                'category',          // Domaine d'études principal
                'city.country',      // Ville -> Pays (A20: migration region_id → country_id)
                'ratings',           // Notes
            ])
            ->where('is_published', true);

        // Filtre par pays (via city -> country) - Sprint 1 + A20
        if ($request->filled('country_id')) {
            $query->whereHas('city', function ($q) use ($request) {
                $q->where('country_id', $request->country_id);
            });
        }

        // Filtres Sprint 1 selon PRD (Foreign Keys vers Settings)
        if ($request->filled('establishment_type_id')) {
            $query->where('establishment_type_id', $request->establishment_type_id);
        }

        if ($request->filled('training_type_id')) {
            $query->where('training_type_id', $request->training_type_id);
        }

        if ($request->filled('career_field_id')) {
            $query->where('career_field_id', $request->career_field_id);
        }

        if ($request->filled('degree_level_id')) {
            $query->where('degree_level_id', $request->degree_level_id);
        }

        // Recherche par nom
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Tri
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        switch ($sortBy) {
            case 'name':
                $query->orderBy('title', $sortOrder);
                break;
            case 'rating':
                $query->withAvg('ratings', 'score')
                      ->orderBy('ratings_avg_score', $sortOrder);
                break;
            case 'ranking':
                // Phase 2: Tri par classement (1er = meilleur, donc ASC)
                $query->orderBy('ranking', 'asc');
                break;
            case 'student_count':
                // Phase 2: Tri par nombre d'étudiants
                $query->orderBy('student_count', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        // Pagination
        $establishments = $query->paginate(12)->withQueryString();

        // Transform avec EstablishmentResource
        $establishments->getCollection()->transform(function ($establishment) {
            return new EstablishmentResource($establishment);
        });

        // Données pour les filtres (Sprint 1 PRD + A20)
        $filters = [
            'countries' => Country::whereHas('cities.properties', function ($q) {
                $q->where('is_published', true);
            })->get(['id', 'name']),

            // Nouveaux filtres Sprint 1 (paramétrables depuis Settings)
            'establishment_types' => EstablishmentType::where('is_published', true)->get(['id', 'label']),
            'training_types' => TrainingType::where('is_published', true)->get(['id', 'label']),
            'career_fields' => CareerField::where('is_published', true)->get(['id', 'label']),
            'degree_levels' => DegreeLevel::where('is_published', true)->get(['id', 'label']),
        ];

        // Filtres actifs (Sprint 1)
        $currentFilters = [
            'country_id' => $request->country_id,
            'establishment_type_id' => $request->establishment_type_id,
            'training_type_id' => $request->training_type_id,
            'career_field_id' => $request->career_field_id,
            'degree_level_id' => $request->degree_level_id,
            'search' => $request->search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ];

        return Inertia::render('Establishments/Index', [
            'establishments' => $establishments,
            'filters' => $filters,
            'currentFilters' => $currentFilters,
        ]);
    }

    /**
     * Affiche la fiche détaillée d'un établissement
     * Story 1.3.2: Fiche établissement avec informations éducatives complètes
     *
     * @param Property $establishment
     * @return \Inertia\Response
     */
    public function show(Property $establishment)
    {
        // Vérifier que l'établissement est publié (sécurité)
        abort_if(!$establishment->is_published, 404);

        // Charger toutes les relations nécessaires
        $establishment->load([
            'propertyType',        // Type d'établissement
            'category',            // Domaine d'études principal
            'subCategory',         // Spécialisation
            'city.country',        // Géolocalisation (A20: migration region_id → country_id)
            'equipments',          // Infrastructures campus
            'regulations',         // Certifications et normes
            'comments',            // Avis étudiants
            'ratings',             // Notes
            'programs' => function ($query) {
                // Charger uniquement les programmes publiés avec leurs relations
                $query->where('is_published', true)
                      ->with(['studyField', 'degreeLevel', 'specialization']);
            },
        ]);

        return Inertia::render('Establishments/Show', [
            'establishment' => new EstablishmentResource($establishment),
        ]);
    }
}
