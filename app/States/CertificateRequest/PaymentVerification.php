<?php

namespace App\States\CertificateRequest;

use App\States\CertificateRequest\CertificateRequestState;

class PaymentVerification extends CertificateRequestState
{
    public static string $name = 'payment_verification';

    public function label(): string
    {
        return 'En verification';
    }
}
