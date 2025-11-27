<?php

namespace App\Models;

use App\Models\Certificate\CertificateRequest;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Charge extends Model
{
    use ClearsResponseCache;

    protected $fillable = [
        'stripe_id',
        'certificate_request_id',
        'amount',
        'currency',
        'description',
    ];

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(CertificateRequest::class, 'certificate_request_id');
    }
}
