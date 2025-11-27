<?php

namespace App\States\CertificateRequest;

use App\States\CertificateRequest\CertificateRequestState;

class PaymentValidated extends CertificateRequestState
{
    public static string $name = 'payment_validated';

    public function label(): string
    {
        return 'Reçu';
    }
}
