<x-mail::message>
# Merci pour votre séjour avec Yod Invest !

Cher(e) {{ $reservation->user->name }},

Nous espérons que vous avez passé un excellent séjour avec **[Nom de la plateforme]** et que votre expérience a été à la hauteur de vos attentes.

Votre départ marque la fin de votre séjour, mais nous restons à votre disposition pour toute question ou assistance. Si vous avez rencontré le moindre souci ou si vous avez
besoin d’informations supplémentaires (facture, objets oubliés, etc.), n’hésitez pas à nous contacter.

- **Besoin d’un justificatif de séjour ?** Vous pouvez le télécharger directement depuis votre espace client.
- **Une prochaine aventure en vue ?** Découvrez nos nouvelles offres et destinations disponibles en cliquant ci-dessous.

<x-mail::button url="https://ma-reza.com">
    Découvrir nos offres
</x-mail::button>

Merci encore pour votre confiance, et nous espérons vous accueillir à nouveau très bientôt !

Bien cordialement,
L'équipe {{ config('app.label') }}
{{ config('app.name') }}
</x-mail::message>
