<?php

namespace App\States\CertificateRequest;

use Spatie\ModelStates\Exceptions\InvalidConfig;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class CertificateRequestState extends State
{
    abstract public function label(): string;

    /**
     * @throws InvalidConfig
     */
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(PaymentPending::class)
            ->allowTransition(PaymentPending::class, PaymentVerification::class)
            ->allowTransition(PaymentPending::class, PaymentInvalid::class)
            ->allowTransition(PaymentPending::class, PaymentValidated::class)
            ->allowTransition(PaymentPending::class, CertificateGenerated::class)
            ->allowTransition(PaymentVerification::class, PaymentValidated::class)
            ->allowTransition(PaymentVerification::class, PaymentInvalid::class)
            ->allowTransition(PaymentInvalid::class, PaymentVerification::class)
            ->allowTransition(PaymentInvalid::class, PaymentValidated::class)
            ->allowTransition(PaymentValidated::class, CertificateGenerated::class);
    }
}
