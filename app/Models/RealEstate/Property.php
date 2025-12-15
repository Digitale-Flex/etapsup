<?php

namespace App\Models\RealEstate;

use App\Models\City;
use App\Models\Rating;
use App\Models\Scopes\PublishedScope;
use App\Models\Settings\EstablishmentType;
use App\Models\Settings\TrainingType;
use App\Models\Settings\CareerField;
use App\Models\Settings\DegreeLevel;
use App\Traits\ClearsResponseCache;
use App\Traits\HasReservations;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Casts\Attribute;

#[ScopedBy([PublishedScope::class])]
class Property extends Model implements HasMedia
{
    use ClearsResponseCache, HasFactory, HasHashid, HashidRouting, HasReservations, HasSlug, InteractsWithMedia, SoftDeletes;

    // Constantes pour les filtres EtapSup (Sprint 1)
    public const ESTABLISHMENT_TYPES = [
        'Universités',
        'Écoles de commerce',
        'Écoles d\'ingénieurs',
        'Écoles de médecine',
        'Écoles de droit',
        'Écoles hôtelières'
    ];

    public const TRAINING_TYPES = [
        'Initiale',
        'Alternance',
        'Professionnelle',
        'Certifiante',
        'Individualisée',
        'E-learning'
    ];

    public const CAREER_FIELDS = [
        'Commerce',
        'Ingénieurs',
        'Santé',
        'Banque',
        'Immobilier',
        'Communication'
    ];

    public const DEGREE_LEVELS = [
        'CAP',
        'Bac',
        'BTS',
        'Licence',
        'Mastères',
        'Doctorat'
    ];

    protected $fillable = [
        'property_type_id',
        'category_id',
        'sub_category_id',
        'city_id',
        'country_id', // Ajouté pour cohérence formulaire Filament (Pays + Ville)
        'title',
        'price',
        'description',
        'room',
        'bathroom',
        'dining_room',
        'kitchen',
        'living_room',
        'balcony',
        'outdoor_space',
        'address',
        'regulation',
        'discount',
        'airbnb',
        'cleaning_fees',
        'is_published',
        // EtatSup: Colonnes éducatives
        'website',
        'phone',
        'email',
        'student_count',
        'ranking',
        'tuition_min',
        'tuition_max',
        'accreditation_info',
        // Sprint 1: Filtres de recherche
        'establishment_type',
        'training_type',
        'career_field',
        'degree_level',
        // Sprint 1: Foreign keys vers Settings
        'establishment_type_id',
        'training_type_id',
        'career_field_id',
        'degree_level_id',
        // Sprint 1: Sections fiche établissement
        'section_presentation',
        'section_prerequis',
        'section_conditions_financieres',
        'section_specialisation',
        'section_campus',
        // Sprint1 Feature 1.5.1: Frais et commission par établissement
        'commission',
        'frais_dossier',
        'acompte_scolarite',
    ];

    protected function casts(): array
    {
        return [
            'balcony' => 'boolean',
            'outdoor_space' => 'boolean',
            'is_published' => 'boolean',
            'discount' => 'boolean',
            // EtatSup: Casts colonnes éducatives
            'tuition_min' => 'decimal:2',
            'tuition_max' => 'decimal:2',
            'accreditation_info' => 'array',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    /**
     * Route model binding par slug (override HashidRouting)
     * Permet les URLs SEO-friendly: /establishments/inseec-1
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useFallbackUrl(url('/images/cover.jpg'))
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('thumb')
                    ->fit(Fit::Contain, 372, 209);
                $this
                    ->addMediaConversion('hd')
                    ->withResponsiveImages()
                    ->fit(Fit::Contain, 1280);
            });
    }

    public function scopeHasImages($query)
    {
        return $query->whereHas('media', function ($q) {
            $q->where('collection_name', 'images');
        });
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withDefault([
            'label' => 'Non catégorisé'
        ]);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Country::class);
    }

    // EtatSup: Relations vers Settings pour paramétrabilité
    public function establishmentType(): BelongsTo
    {
        return $this->belongsTo(EstablishmentType::class);
    }

    public function trainingType(): BelongsTo
    {
        return $this->belongsTo(TrainingType::class);
    }

    public function careerField(): BelongsTo
    {
        return $this->belongsTo(CareerField::class);
    }

    public function degreeLevel(): BelongsTo
    {
        return $this->belongsTo(DegreeLevel::class);
    }

    public function equipments(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class);
    }

    public function regulations(): BelongsToMany
    {
        return $this->belongsToMany(Regulation::class);
    }

    public function layouts(): BelongsToMany
    {
        return $this->belongsToMany(Layout::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function SubCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function scopeWithoutPublished($query)
    {
        return $query->withoutGlobalScope(PublishedScope::class);
    }

    public function comments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Comment::class,
            Reservation::class,
            'property_id',
            'commentable_id',
            'id',
            'id'
        )->where('commentable_type', Reservation::class);
    }

    public function ratings(): HasManyThrough
    {
        return $this->hasManyThrough(
            Rating::class,
            Reservation::class,
            'property_id',
            'ratingable_id',
            'id',
            'id'
        )->where('ratingable_type', Reservation::class);
    }

    // EtatSup: Relation avec les programmes d'études
    public function programs(): HasMany
    {
        return $this->hasMany(\App\Models\Program::class, 'establishment_id');
    }

    /**
     * Les candidatures pour cet établissement (EtatSup Phase 4)
     */
    public function applications(): HasMany
    {
        return $this->hasMany(\App\Models\Application::class, 'property_id');
    }

    /**
     * Accessor/Mutator pour accreditation_info (EtatSup)
     *
     * Garantit la structure JSON cohérente :
     * {
     *   "national": bool,
     *   "international": string[],
     *   "labels": string[]
     * }
     *
     * Audit QA: Correction RISQUE 1 (validation JSON)
     */
    protected function accreditationInfo(): Attribute
    {
        return Attribute::make(
            get: fn (?array $value) => $value ?? [
                'national' => false,
                'international' => [],
                'labels' => [],
            ],
            set: fn (?array $value) => $value ? [
                'national' => (bool) ($value['national'] ?? false),
                'international' => array_values((array) ($value['international'] ?? [])),
                'labels' => array_values((array) ($value['labels'] ?? [])),
            ] : null,
        );
    }
}
