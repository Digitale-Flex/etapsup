<?php

namespace App\Models\RealEstate;

use App\Models\City;
use App\Models\Rating;
use App\Models\Scopes\PublishedScope;
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

#[ScopedBy([PublishedScope::class])]
class Property extends Model implements HasMedia
{
    use ClearsResponseCache, HasFactory, HasHashid, HashidRouting, HasReservations, HasSlug, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'property_type_id',
        'category_id',
        'sub_category_id',
        'city_id',
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
    ];

    protected function casts(): array
    {
        return [
            'balcony' => 'boolean',
            'outdoor_space' => 'boolean',
            'is_published' => 'boolean',
            'discount' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
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
            Comment::class, // Modèle cible
            Reservation::class, // Modèle intermédiaire
            'property_id', // Clé étrangère sur Reservation
            'commentable_id', // Clé étrangère sur Comment
            'id', // Clé primaire sur Property
            'id' // Clé primaire sur Reservation
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
}
