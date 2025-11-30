<?php

namespace App\Models;

use App\Models\RealEstate\Category;
use App\Models\RealEstate\Property;
use App\Models\RealEstate\SubCategory;
use App\Models\Settings\DegreeLevel;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Program extends Model
{
    use ClearsResponseCache, HasFactory, HasHashid, HashidRouting, HasSlug, SoftDeletes;

    protected $fillable = [
        'establishment_id',
        'study_field_id',
        'specialization_id',
        'name',
        'description',
        'degree_level',
        'degree_level_id',
        'duration',
        'language',
        'tuition_fee',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'tuition_fee' => 'decimal:2',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    // Relations (temporaire avec anciens noms, sera mis à jour après renommage)
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'establishment_id');
    }

    public function studyField(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'study_field_id');
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'specialization_id');
    }

    public function degreeLevel(): BelongsTo
    {
        return $this->belongsTo(DegreeLevel::class, 'degree_level_id');
    }

    /**
     * Les candidatures pour ce programme
     * Phase 4: Relation activée
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
