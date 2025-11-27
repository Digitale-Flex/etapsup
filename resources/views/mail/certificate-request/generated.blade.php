@php
    use Carbon\Carbon;
@endphp

<x-mail::message>
# Votre certificat de logement est prêt

Cher(e) {{ $certificateRequest->user->name }},

Nous sommes heureux de vous informer que votre certificat de logement a été généré avec succès. Vous trouverez ce document en pièce jointe à cet email.

## Détails du certificat

- **Numéro de demande :** {{ date('y') .'-'. str_pad($certificateRequest->id, 8, '0', STR_PAD_LEFT) }}
- **Date de génération:** {{ Carbon::parse($certificateRequest->created_at)->locale('fr')->translatedFormat('jS F Y') }}
- **Validité :** 2 mois à partir de la date de génération

## Prochaines étapes

1. **Vérification :** Veuillez vérifier attentivement toutes les informations sur le certificat.
2. **Utilisation :** Vous pouvez utiliser ce certificat pour vos démarches de location comme convenu.
3. **Conservation :** Conservez ce document en lieu sûr, vous pourriez en avoir besoin ultérieurement.

## Informations importantes

- Ce certificat est valable pour une durée de 2 mois à compter de sa date d'émission.
- En cas d'erreur ou de question concernant votre certificat, veuillez nous contacter immédiatement.
- N'hésitez pas à nous contacter si vous avez besoin d'assistance supplémentaire dans vos démarches de location.

Nous vous remercions pour votre confiance et vous souhaitons une excellente expérience dans votre recherche de logement.

Cordialement,
L'équipe {{ config('app.name') }}
</x-mail::message>
