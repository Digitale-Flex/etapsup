<?php

namespace App\Models\Certificate;

use App\Models\Country;
use App\Models\Scopes\PublishedScope;
use App\Models\User;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

#[ScopedBy([PublishedScope::class])]
class Partner extends Model
{
    use ClearsResponseCache, HasHashid, HashidRouting, HasSlug, SoftDeletes;

    protected $fillable = [
        'country_id',
        'label',
        'city',
        'address',
        'note',
        'managers',
        'code',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'managers' => 'array',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('label')
            ->saveSlugsTo('slug');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeIsPublished($query)
    {
        return $query->where('is_published', true);
    }

    public function coupons(): HasMany|Partner
    {
        return $this->hasMany(Coupon::class);
    }

    public function lastCoupon()
    {
        return $this->hasOne(Coupon::class)->latest();
    }

    public function scopeMinimal($query)
    {
        return $query->select('id', 'label');
    }
}
