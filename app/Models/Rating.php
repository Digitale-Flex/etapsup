<?php

namespace App\Models;

use App\Models\RealEstate\Property;
use App\Models\RealEstate\Reservation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Vinkla\Hashids\Facades\Hashids;

class Rating extends Model
{
    protected $fillable = [
        'user_id',
        'score',
        'ratingable_id',
        'ratingable_type',
    ];

    public function ratingable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}
