<x-mail::message>
# Mise à jour de votre demande d'attestation

Cher(e) {{ $certificateRequest->user->name }},

Nous vous informons que l'état de votre demande d'attestation a été mis à jour.

**Numéro de demande :** {{ date('y') .'-'. str_pad($certificateRequest->id, 8, '0', STR_PAD_LEFT) }}

**Nouvel état :** Paiement {{ strtolower($newState->label()) }}

@switch($newState::class)
@case(\App\States\CertificateRequest\PaymentPending::class)
Votre demande est en attente de paiement. Veuillez procéder au paiement dès que possible pour que nous puissions traiter votre demande.
@break

@case(\App\States\CertificateRequest\PaymentVerification::class)
Nous avons bien reçu votre justificatif de paiement et sommes en train de le vérifier. Ce processus peut prendre jusqu'à 24 heures ouvrables.
@break

@case(\App\States\CertificateRequest\PaymentValidated::class)
Votre paiement a été validé avec succès. Nous allons maintenant procéder au traitement de votre demande d'attestation.
@break

@case(\App\States\CertificateRequest\PaymentInvalid::class)
Malheureusement, nous n'avons pas pu valider votre paiement. Cela peut être dû à plusieurs raisons.

Veuillez vérifier les informations de paiement que vous avez fournies et réessayer. Si le problème persiste, n'hésitez pas à nous contacter.
@break

@default
Si vous avez des questions concernant ce changement d'état, n'hésitez pas à nous contacter.
@endswitch

Vous pouvez suivre l'état de votre demande à tout moment en vous connectant à votre compte.

Merci de votre confiance.

Cordialement,
L'équipe {{ config('app.name') }}
</x-mail::message>
