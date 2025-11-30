<?php

namespace App\Models;

use App\Models\RealEstate\Property;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;

/**
 * Modèle Application (Candidature Étudiant)
 *
 * MAPPING ETATSUP: Reservation → Application
 *
 * Approche NON-DESTRUCTIVE: Réutilise la table `reservations` existante
 * avec un mapping conceptuel pour le contexte éducatif.
 *
 * Relations:
 * - user (étudiant candidat)
 * - property/establishment (établissement visé)
 * - program (programme d'études choisi)
 * - documents (CV, diplômes, etc.)
 *
 * Phase 4 - Sprint 1: Modèle de base
 */
class Application extends Model
{
    use ClearsResponseCache, HasFactory, HasHashid, HashidRouting, SoftDeletes;

    /**
     * Table réutilisée (approche NON-DESTRUCTIVE)
     */
    protected $table = 'reservations';

    /**
     * Champs fillable (mapping EtatSup)
     *
     * Mapping conceptuel:
     * - property_id → establishment_id (établissement)
     * - user_id → applicant_id (étudiant)
     * - notes → motivation_letter
     * - status → application_status
     * - price → tuition_amount
     */
    protected $fillable = [
        'property_id',      // establishment_id (mapping conceptuel)
        'user_id',          // applicant_id (mapping conceptuel)
        'status',           // application_status: pending, accepted, rejected, withdrawn
        'reason',           // rejection_reason si refusé
        'notes',            // motivation_letter ou notes internes
        'price',            // tuition_amount (frais de scolarité prévus)
        'start_date',       // academic_year_start
        'end_date',         // academic_year_end
        'address',          // current_address de l'étudiant
        'fees',             // application_fees (JSON: frais dossier, etc.)
        'state',            // processing_state: draft, submitted, under_review, finalized
        'stripe_payment_intent_id', // payment_id si paiement frais de dossier
        'guests',           // Obsolète pour EtatSup (peut stocker companions_count si nécessaire)
        // Feature 9 - Sprint 1: Accompagnement premium
        'accompagnement_premium',                   // L'étudiant a choisi l'accompagnement
        'accompagnement_paid',                      // L'accompagnement a été payé
        'accompagnement_stripe_payment_intent_id',  // ID du paiement Stripe pour l'accompagnement
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'fees' => 'json', // ✅ Changé de 'array' à 'json' pour cohérence
            'deleted_at' => 'datetime',
            // Feature 9 - Sprint 1: Accompagnement premium
            'accompagnement_premium' => 'boolean',
            'accompagnement_paid' => 'boolean',
        ];
    }

    /**
     * Relations
     */

    /**
     * L'étudiant qui postule
     * Mapping: user_id → applicant
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias pour cohérence avec table reservations
     */
    public function user(): BelongsTo
    {
        return $this->applicant();
    }

    /**
     * L'établissement visé
     * Mapping: property_id → establishment
     */
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    /**
     * Alias pour cohérence avec table reservations
     */
    public function property(): BelongsTo
    {
        return $this->establishment();
    }

    /**
     * Le programme d'études choisi (optionnel)
     *
     * Note: La relation program_id n'existe pas encore dans la table reservations.
     * Une migration future ajoutera ce champ. Pour l'instant, on peut utiliser
     * `notes` pour stocker temporairement l'ID du programme.
     *
     * @TODO: Ajouter migration pour program_id dans reservations
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    /**
     * Les documents de candidature (CV, diplômes, etc.)
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class, 'application_id');
    }

    /**
     * Scopes
     */

    /**
     * Candidatures en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Candidatures acceptées
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Candidatures rejetées
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Helpers
     */

    /**
     * Vérifie si la candidature est en attente
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Vérifie si la candidature est acceptée
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Vérifie si la candidature est rejetée
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Feature 9 - Sprint 1: Accompagnement premium
     */

    /**
     * Vérifie si l'accompagnement premium a été demandé
     */
    public function hasAccompagnement(): bool
    {
        return $this->accompagnement_premium === true;
    }

    /**
     * Vérifie si l'accompagnement premium a été payé
     */
    public function isAccompagnementPaid(): bool
    {
        return $this->accompagnement_paid === true;
    }

    /**
     * Vérifie si l'accompagnement est demandé mais non payé
     */
    public function isAccompagnementPending(): bool
    {
        return $this->hasAccompagnement() && !$this->isAccompagnementPaid();
    }
}
