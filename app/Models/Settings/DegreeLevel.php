<?php

namespace App\Models\Settings;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DegreeLevel extends Model
{
    use ClearsResponseCache, HasFactory, SoftDeletes;

    protected $fillable = [
        'label',
        'description',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }
}
