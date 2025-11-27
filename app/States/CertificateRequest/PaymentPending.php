<?php

namespace App\States\CertificateRequest;

use App\States\CertificateRequest\CertificateRequestState;

class PaymentPending extends CertificateRequestState
{
    public static string $name = 'payment_pending';

    public function label(): string
    {
        return 'En attente';
    }
}
