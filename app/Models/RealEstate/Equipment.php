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
class Equipment extends Model
{
    use ClearsResponseCache, HasHashid, HashidRouting, SoftDeletes;

    protected $table = 'equipments';

    protected $fillable = [
        'label',
        'description',
        'is_published',
    ];

    public function scopeMinimal($query)
    {
        return $query->select('id', 'label');
    }
}
