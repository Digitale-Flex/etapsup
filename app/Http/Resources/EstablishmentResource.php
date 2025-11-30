<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource pour les Établissements (EtatSup)
 *
 * MAPPING ETATSUP: Property = Establishment
 * Cette resource expose uniquement les champs pertinents pour le contexte éducatif.
 * Les champs immobiliers (room, bathroom, kitchen, price, etc.) sont exclus.
 */
class EstablishmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->hashid,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->whenHas('description'),
            'address' => $this->whenHas('address'),

            // Refonte Accueil Dynamique: Champs plats pour EstablishmentCard
            'logo' => $this->getFirstMediaUrl('images', 'thumb'),
            'type' => $this->propertyType?->label ?? $this->establishment_type,
            'city' => $this->city?->name,
            'country' => $this->city?->region?->country?->name,

            // Relations contexte éducatif (objets complets pour pages détails)
            'propertyType' => new PropertyTypeResource($this->whenLoaded('propertyType')), // Type d'établissement
            'category' => new SubCategoryResource($this->whenLoaded('category')), // Domaine d'études
            'subCategory' => new SubCategoryResource($this->whenLoaded('subCategory')), // Spécialisation
            'cityFull' => new CityResource($this->whenLoaded('city')),

            // Informations de contact établissement (Phase 2)
            'website' => $this->whenHas('website'),
            'phone' => $this->whenHas('phone'),
            'email' => $this->whenHas('email'),

            // Statistiques établissement (Phase 2)
            'studentCount' => $this->when(isset($this->student_count), $this->student_count),
            'ranking' => $this->when(isset($this->ranking), $this->ranking),

            // Frais de scolarité (Phase 2 - camelCase pour frontend)
            'tuitionRange' => $this->when(
                isset($this->tuition_min) || isset($this->tuition_max),
                fn () => [
                    'min' => $this->tuition_min,
                    'max' => $this->tuition_max,
                    'currency' => 'EUR',
                ]
            ),

            // Accréditations (Phase 2 - avec accessor/mutator Property)
            'accreditations' => $this->when(isset($this->accreditation_info), $this->accreditation_info),

            // Infrastructures campus
            'equipments' => EquipmentResource::collection($this->whenLoaded('equipments')),

            // Accréditations et certifications
            'regulations' => RegulationResource::collection($this->whenLoaded('regulations')),

            // Avis et notes étudiants
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'ratings' => $this->when($this->relationLoaded('ratings'), function () {
                return $this->formatRatings();
            }),

            // Images établissement
            'thumb' => $this->getFirstMediaUrl('images', 'thumb'),
            'images' => $this->when(
                $request->route() && $request->route()->named('establishments.show'),
                $this->getMedia('images')
            ),

            // Sprint 1: 5 sections fiche établissement (max 1000 chars - camelCase pour frontend)
            // sectionPresentation toujours retournée pour affichage page d'accueil
            'sectionPresentation' => $this->section_presentation,
            'sectionPrerequis' => $this->whenHas('section_prerequis'),
            'sectionConditionsFinancieres' => $this->whenHas('section_conditions_financieres'),
            'sectionSpecialisation' => $this->whenHas('section_specialisation'),
            'sectionCampus' => $this->whenHas('section_campus'),

            // Phase 3: Programmes d'études proposés
            'programs' => ProgramResource::collection($this->whenLoaded('programs')),
        ];
    }

    /**
     * Formate les données de notation
     *
     * @return array
     */
    private function formatRatings(): array
    {
        $ratings = $this->ratings;
        $totalRatings = $ratings->count();

        return [
            'average' => round($ratings->avg('score') ?? 0, 1),
            'count' => $totalRatings,
            'distribution' => collect(range(5, 1))->mapWithKeys(function ($stars) use ($ratings, $totalRatings) {
                $ratingCount = $ratings->where('score', $stars)->count();

                return [
                    $stars => [
                        'count' => $ratingCount,
                        'percentage' => $totalRatings > 0 ? round(($ratingCount / $totalRatings) * 100, 1) : 0,
                    ],
                ];
            }),
            'user_rating' => $this->when(
                auth()->check(),
                fn () => $ratings->where('user_id', auth()->id())->first()?->score
            ),
        ];
    }
}
