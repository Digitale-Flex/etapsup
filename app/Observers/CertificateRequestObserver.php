<?php

namespace App\Observers;

use App\Mail\Certificate\CertificateRequestStateChanged;
use App\Models\Certificate\CertificateRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CertificateRequestObserver
{
    /**
     * Handle the CertificateRequest "created" event.
     */
    public function created(CertificateRequest $certificateRequest): void
    {
        //
    }

    /**
     * Handle the CertificateRequest "updated" event.
     */
    public function updated(CertificateRequest $certificateRequest): void
    {
        if ($certificateRequest->isDirty('state')) {
            Mail::to($certificateRequest->user->email)->queue(new CertificateRequestStateChanged($certificateRequest, $certificateRequest->state));
        }
    }

    /**
     * Handle the CertificateRequest "deleted" event.
     */
    public function deleted(CertificateRequest $certificateRequest): void
    {
        //
    }

    /**
     * Handle the CertificateRequest "restored" event.
     */
    public function restored(CertificateRequest $certificateRequest): void
    {
        //
    }

    /**
     * Handle the CertificateRequest "force deleted" event.
     */
    public function forceDeleted(CertificateRequest $certificateRequest): void
    {
        //
    }
}
