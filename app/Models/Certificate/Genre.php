<?php

namespace App\Models\Certificate;

use App\Models\Scopes\PublishedScope;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;
use Spatie\EloquentSortable\SortableTrait;

#[ScopedBy([PublishedScope::class])]
class Genre extends Model
{
    use ClearsResponseCache, HasHashid, HashidRouting, SoftDeletes, SortableTrait;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'is_published',
    ];

    public $sortable = [
        'order_column_name' => 'order_column',
        'sort_when_creating' => true,
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

    public function properties(): HasMany
    {
        // return $this->hasMany(RealEstate::class);
    }
}
