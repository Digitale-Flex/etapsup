<?php

namespace App\Models;

use App\Models\Scopes\PublishedScope;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;

#[ScopedBy([PublishedScope::class])]
class Country extends Model
{
    use ClearsResponseCache, HasHashid, HashidRouting, SoftDeletes;

    protected $fillable = [
        'name',
        'iso',
        'code',
        'nationality',
        'is_published',
    ];

    public $timestamps = false;

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucwords(strtolower($value)),
            set: fn (string $value) => ucwords(strtolower($value))
        );
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    // A20: Relation directe avec cities après migration region_id → country_id
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    // Relation directe avec properties (via country_id)
    public function properties(): HasMany
    {
        return $this->hasMany(\App\Models\RealEstate\Property::class);
    }
}
