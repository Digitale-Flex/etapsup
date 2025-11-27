<x-mail::message>
# Confirmation de votre réservation - Yod Invest

Bonjour {{ $reservation->user->name }},

Nous vous remercions pour votre confiance et sommes ravis de vous confirmer votre réservation sur Yod Invest !

📍 **Détails de votre réservation** :
- Logement : {{ $reservation->property->title }}
- Adresse : {{ $reservation->property->address }}
- Dates de séjour : du {{ $reservation->start_date }} au {{ $reservation->end_date }}
- Montant total : {{ $reservation->price }}

Vous trouverez en pièce jointe le contrat de bail, que nous vous invitons à lire attentivement.

<x-mail::panel>
**Contact Yod Invest**

📩 [contact@yod-invest.fr](mailto:contact@yod-invest.fr)

📞 [+33 189 866 953](tel:+33189866953)
</x-mail::panel>

Nous avons hâte de vous accueillir et vous souhaitons d'ores et déjà un excellent séjour !

À très bientôt,
L'équipe Yod Invest
</x-mail::message>
