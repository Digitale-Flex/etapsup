<?php

namespace App\Models;

use App\Models\Certificate\CertificateRequest;
use App\Models\RealEstate\Property;
use App\Models\Scopes\PublishedScope;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;

#[ScopedBy([PublishedScope::class])]
class City extends Model
{
    use ClearsResponseCache, HasHashid, HashidRouting, SoftDeletes;

    protected $fillable = [
        'region_id',
        'name',
        'budget',
        'is_published',
    ];

    public $timestamps = false;

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst(strtolower($value)),
            set: fn (string $value) => ucfirst(strtolower($value))
        );
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function certificateRequests(): HasMany
    {
        return $this->hasMany(CertificateRequest::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function locations(): MorphMany
    {
        return $this->morphMany(Location::class, 'locatable');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /*  public function properties(): HasManyThrough
      {
          return $this->hasManyThrough(Property::class, Municipality::class);
      } */
    public function scopeMinimal($query)
    {
        return $query->select('id', 'name');
    }
}
