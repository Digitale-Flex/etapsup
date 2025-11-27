<?php

namespace App\Models\Certificate;

use App\Models\Scopes\PublishedScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;

#[ScopedBy([PublishedScope::class])]
class Coupon extends Model
{
    use SoftDeletes, HasHashid, HashidRouting;

    protected $fillable = [
        'partner_id',
        'code',
        'discount_amount',
        'is_published'
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function scopeForPartner($query, ?User $user = null)
    {
        $user = $user ?? auth()->user();
        return $user->hasRole('partner')
            ? $query->where('partner_id', $user->partner_id)
            : $query;
    }
}
