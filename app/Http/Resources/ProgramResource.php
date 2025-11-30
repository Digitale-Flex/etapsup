<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource pour les Programmes d'études (EtatSup)
 *
 * Expose les informations des programmes proposés par les établissements
 */
class ProgramResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->whenHas('description'),

            // Niveau de diplôme
            'degreeLevel' => $this->whenLoaded('degreeLevel', function () {
                return [
                    'id' => $this->degreeLevel->hashid,
                    'label' => $this->degreeLevel->label,
                ];
            }),

            // Domaine d'études
            'studyField' => $this->whenLoaded('studyField', function () {
                return [
                    'id' => $this->studyField->hashid,
                    'label' => $this->studyField->label,
                ];
            }),

            // Spécialisation
            'specialization' => $this->whenLoaded('specialization', function () {
                return [
                    'id' => $this->specialization->hashid,
                    'label' => $this->specialization->label,
                ];
            }),

            // Détails académiques
            'duration' => $this->whenHas('duration'),
            'language' => $this->whenHas('language'),

            // Frais de scolarité
            'tuitionFee' => $this->when(isset($this->tuition_fee), function () {
                return [
                    'amount' => $this->tuition_fee,
                    'currency' => 'EUR',
                    'formatted' => number_format($this->tuition_fee, 2, ',', ' ') . ' €',
                ];
            }),

            // Statut de publication
            'isPublished' => $this->is_published,
        ];
    }
}
