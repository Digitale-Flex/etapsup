<?php

namespace App\Models\Certificate;

use App\Models\Scopes\PublishedScope;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([PublishedScope::class])]
class Location extends Model
{
    use ClearsResponseCache, SoftDeletes;

    protected $fillable = [
        'locatable_type',
        'locatable_id',
        'price',
        'addresses',
        'is_published',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'addresses' => 'array',
        ];
    }

    public function locatable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function addressCount(): Attribute
    {
        return Attribute::make(
            get: fn () => is_array($this->addresses) ? count($this->addresses) : 0,
        );
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeHaveAddresses($query)
    {
        return $query->whereRaw('JSON_LENGTH(addresses) > 0');
    }
}
