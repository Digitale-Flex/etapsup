@php
    use Carbon\Carbon;
@endphp

<x-mail::message>
# Confirmation de votre réservation

Cher(e) {{ $reservation->user->name }},

Nous avons le plaisir de confirmer votre réservation pour le logement "{{ $property->title }}". Vous trouverez ci-dessous tous les détails de votre séjour.

- **Numéro de réservation :** {{ date('y') .'-'. str_pad($reservation->id, 8, '0', STR_PAD_LEFT) }}
- **Date de réservation :** {{ Carbon::parse($reservation->created_at)->locale('fr')->translatedFormat('jS F Y') }}
- **Propriété :** {{ $property->title }}
- **Adresse :** {{ $property->address }}
- **Durée du séjour :**
- Arrivée : {{ Carbon::parse($reservation->start_date)->locale('fr')->translatedFormat('l jS F Y') }}
- Départ : {{ Carbon::parse($reservation->end_date)->locale('fr')->translatedFormat('l jS F Y') }}
- **Nombre de personnes :** {{ $reservation->guests }}

Nous vous souhaitons un excellent séjour !

Cordialement,
L'équipe {{ config('app.label') }}

{{ config('app.label') }}
</x-mail::message>
