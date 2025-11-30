<?php

namespace App\Models\RealEstate;

use App\Models\Scopes\PublishedScope;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;

#[ScopedBy([PublishedScope::class])]
class PropertyType extends Model
{
    use ClearsResponseCache, HasHashid, HashidRouting, SoftDeletes;

    protected $fillable = [
        'label',
        'description',
        'is_published',
    ];

    public function scopeMinimal($query)
    {
        return $query->select('id', 'label');
    }

    /**
     * Relation inverse: PropertyType a plusieurs Properties (Établissements)
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'property_type_id');
    }
}
