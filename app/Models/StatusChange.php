<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StatusChange extends Model
{
    protected $fillable = [
        'from_state',
        'to_state',
        'notes',
        'status_changeable_id',
        'status_changeable_type',
    ];

    public function status_changeable(): MorphTo
    {
        return $this->morphTo();
    }
}
