<?php

namespace App\Models\RealEstate;

use App\Models\Scopes\PublishedScope;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;

#[ScopedBy([PublishedScope::class])]
class Regulation extends Model
{
    use ClearsResponseCache, HasHashid, HashidRouting, SoftDeletes;

    protected $fillable = [
        'label',
        'description',
        'mi_duration',
        'is_published',
    ];

    public function scopeMinimal($query)
    {
        return $query->select('id', 'label');
    }
}
