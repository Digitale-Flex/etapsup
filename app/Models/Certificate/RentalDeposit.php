<?php

namespace App\Models\Certificate;

use App\Models\Scopes\PublishedScope;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;

#[ScopedBy([PublishedScope::class])]
class RentalDeposit extends Model
{
    use ClearsResponseCache, HasHashid, HashidRouting, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_published',
    ];

    public $timestamps = false;

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeMinimal($query)
    {
        return $query->select('id', 'name');
    }
}
