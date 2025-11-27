<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Refonte: Story 1.1.1 - Event Registration Model
class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'country',
        'phone',
        'study_level',
        'ip_address',
        'user_agent',
        'registered_at'
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    protected $dates = [
        'registered_at',
        'created_at',
        'updated_at',
    ];
}
