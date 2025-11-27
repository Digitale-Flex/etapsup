@php
    use Jenssegers\Date\Date;
     Date::setLocale('fr');
     $toWord = new NumberFormatter("fr", NumberFormatter::SPELLOUT);
@endphp
<html lang="fr">
<head>
    <title>Certificat de location</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .bg-certificate {
            background-image: url('{{ asset('/images/certificate/bg.png') }}');
        }
    </style>
</head>
<body class="relative">
<header>
    <img src="{{ asset('/images/certificate/header.jpg') }}">
</header>

<div class="container mx-auto mt-3 max-w-[1280] px-14">
    <div class="bg-certificate bg-center bg-no-repeat bg-contain">
        <div class="text-center text-[#003b4f] font-bold text-xl border-4 py-1">
            ATTESTATION DE LOGEMENT n°{{ date('y') .'-'. str_pad($request->id, 8, '0', STR_PAD_LEFT) }}
        </div>
        <p class="text-[#003b4f] mt-2 font-bold text-[10px]">
            ma-reza.com est un site de réservation en ligne des logements du parc locatif YOD INVEST et de ses partenaires.
        </p>

        <p class="my-3 leading-[1.5em] text-sm text-justify">
            La société Yod Invest, société par action simplifiée, au capital de 10 000 Euros, dont le siège social est situé au 231 rue Saint-Honoré, 75001 Paris, immatriculée au
            registre du commerce et des sociétés RCS PARIS sous le numéro 920 090 347, Titulaire de la Carte Professionnelle de transaction (T) sur immeubles et fonds de commerce
            délivrée par la Chambre du Commerce et de l’industrie sous le N° CPI 4502 2022 000 000 011, certifie que :
        </p>

        <p class="text-sm leading-[1.5em]">
            Monsieur/Madame <span class="">{{ $request->user->full_name  }}</span> née le <span class="">{{ Date::parse($request->user->date_birth)->format('jS F Y') }}</span> à <span class="">{{ $request->user->place_birth }}</span>
            au <span class="">{{ $request->user->country->name }}</span>, a préréservé un logement au sein du parc locatif de Yod Invest ou de ses partenaires via la
            plateforme ma-reza, à l’adresse suivante :
        </p>

        <p class="my-3 text-sm leading-[1.5em]">
            <b>{{ $request->address  }}</b> pour un montant de loyer mensuel de <span class="">{{ number_format($price, 2, ',', ' ') }}€</span>.
        </p>

        <div class="text-sm leading-[1.5em]">
            L’entrée effective dans les lieux, envisagée au <span class="">{{ $request->rental_start->format('d/m/y') }}</span>, demeure conditionnée à
            <ul class="list-disc pl-12 my-2">
                <li>La signature d’un contrat de bail figurant en annexe,</li>
                <li>La présentation des documents justificatifs requis (visa étudiant valide, pièce d'identité, justificatifs de ressources financières, assurance habitation,
                    etc.).
                </li>
                <li>Le paiement du dépôt de garantie et du premier mois de loyer conformément aux conditions prévues dans le contrat de bail</li>
            </ul>
            Le logement préréservé est garanti à l'étudiant à compter de la date de délivrance de cette attestation.
        </div>

        <div class="text-justify text-sm leading-[1.5em] my-3">
            En cas d’indisponibilité du logement initialement préréservé, Yod Invest s’engage à proposer une alternative de logement présentant des caractéristiques similaires.
            Cette alternative est proposée sans obligation de résultat, mais avec un engagement à accompagner le locataire dans sa recherche.

            <p class="mt-3">Ce document est strictement personnel et ne peut être cédé, revendu ou modifié.</p>
        </div>

        <p class="text-sm">
            Fait à Paris, le <span class="">{{ $request->created_at->format('d/m/y') }}</span>
        </p>

        <div class="text-center text-sm w-[300px] mt-4">
            <p>YOD INVEST</p>
            <p>Représenté par Emery MUNDELE</p>
            <img src="{{ asset('/images/certificate/sign.png') }}" class="mx-auto mt-2" width="120px">
        </div>
    </div>
</div>

<footer class="absolute bottom-0">
    <div class="container mx-auto max-w-[1280] px-14">
        <div class="grid grid-cols-3 items-end">
            <div class="col-span-2 pr-24">
                <img src="{{ asset('/images/certificate/bloc.jpg') }}">
            </div>
            <div class="col-span-1 flex justify-end">
                {{ $qrcode }}
            </div>
        </div>
    </div>
    <img src="{{ asset('/images/certificate/footer.jpg') }}">
</footer>
</body>
