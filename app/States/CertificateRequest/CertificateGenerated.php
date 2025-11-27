<?php

namespace App\States\CertificateRequest;

use App\States\CertificateRequest\CertificateRequestState;

class CertificateGenerated extends CertificateRequestState
{
    public static string $name = 'certificate_generated';

    public function label(): string
    {
        return 'Attestation générée';
    }
}
