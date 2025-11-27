<?php

namespace App\States\CertificateRequest;

use App\States\CertificateRequest\CertificateRequestState;

class PaymentInvalid extends CertificateRequestState
{
    public static string $name = 'payment_invalide';

    public function label(): string
    {
        return 'Non reçu';
    }
}
