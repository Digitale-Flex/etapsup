<script setup lang="ts">
import AppHead from '@/Components/AppHead.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PaymentStatus from '@/Pages/Dashboard/CertificateRequest/Partials/PaymentStatus.vue';
import { CertificateRequest, User } from '@/Types/index';
import dayjs from 'dayjs';
import 'dayjs/locale/fr';

defineOptions({ layout: DashboardLayout });

defineProps<{
    certificateRequest: CertificateRequest;
    user: User;
}>();
dayjs.locale('fr');

const formatBudget = (amount: number): string => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
};
const formatDate = (date: string): string => {
    return dayjs(date).format('D MMMM YYYY');
};

// Ajoutez cette fonction utilitaire
const getRentalDepositNames = (
    certificateRequest: CertificateRequest,
): string => {
    console.log(certificateRequest.rentalDeposits);
    // Nouveau système (plusieurs cautionnements)
    if (certificateRequest.rentalDeposits?.length) {
        return certificateRequest.rentalDeposits
            .map((rd) => rd.name)
            .join(', ');
    }

    // Ancien système (un seul cautionnement)
    if (certificateRequest.rentalDeposit) {
        return certificateRequest.rentalDeposit.name;
    }

    return 'Non spécifié';
};
</script>

<template>
    <AppHead
        :title="`${certificateRequest.genre.name + ', ' + certificateRequest.city.name}`"
    />
    <b-card no-body class="shadow">
        <b-card-header class="border-bottom">
            <h5 class="mb-0">
                Informations relative au dossier ID:
                {{ certificateRequest.reference }}
            </h5>
        </b-card-header>
        <b-card-body>
            <payment-status
                :state="certificateRequest.state"
                :id="certificateRequest.id"
                :file="certificateRequest?.file"
                :paid="certificateRequest.paid"
            />
            <b-row class="pb-3">
                <b-col md="6">
                    <ul class="list-group list-group-borderless">
                        <li class="list-group-item mb-3">
                            <span>Statut :</span>
                            <span
                                class="h6 fw-normal mb-0 ms-2"
                                :class="certificateRequest.state.color"
                                >{{ certificateRequest.state.label }}</span
                            >
                        </li>
                        <li
                            v-if="certificateRequest.partner"
                            class="list-group-item mb-3"
                        >
                            <span>Partenaire de suivi :</span>
                            <span class="h6 fw-normal mb-0 ms-2">{{
                                certificateRequest.partner?.name
                            }}</span>
                        </li>
                        <li class="list-group-item mb-3">
                            <span>Type de logement :</span>
                            <span class="h6 fw-normal mb-0 ms-2">{{
                                certificateRequest.genre.name
                            }}</span>
                        </li>
                        <li class="list-group-item mb-3">
                            <span>Ville souhaité :</span>
                            <span class="h6 fw-normal mb-0 ms-2">{{
                                certificateRequest.city.name +
                                ', ' +
                                certificateRequest.city.region.name
                            }}</span>
                        </li>
                        <li class="list-group-item mb-3">
                            <span>Cautionnement locatif :</span>
                            <span class="h6 fw-normal mb-0 ms-2">{{
                                getRentalDepositNames(certificateRequest)
                            }}</span>
                        </li>
                        <li class="list-group-item mb-3">
                            <span>Budget :</span>
                            <span class="h6 fw-normal mb-0 ms-2">{{
                                formatBudget(certificateRequest.budget)
                            }}</span>
                        </li>
                        <li class="list-group-item mb-3">
                            <span>Début de la location :</span>
                            <span class="h6 fw-normal mb-0 ms-2">{{
                                formatDate(certificateRequest.rentalStart)
                            }}</span>
                        </li>
                        <li class="list-group-item mb-3">
                            <span>Durée de la location :</span>
                            <span class="h6 fw-normal mb-0 ms-2"
                                >{{ certificateRequest.duration }} mois</span
                            >
                        </li>
                    </ul>
                </b-col>
                <b-col md="6">
                    <ul class="list-group list-group-borderless">
                        <li class="list-group-item mb-3">
                            <span>Nom et prénom :</span>
                            <span class="h6 fw-normal mb-0 ms-2">{{
                                user.full_name
                            }}</span>
                        </li>
                        <li class="list-group-item mb-3">
                            <span>Email :</span>
                            <span class="h6 fw-normal mb-0 ms-2">{{
                                user.email
                            }}</span>
                        </li>
                        <li class="list-group-item mb-3">
                            <span>Téléphone :</span>
                            <span class="h6 fw-normal mb-0 ms-2">{{
                                user.phone
                            }}</span>
                        </li>
                        <li class="list-group-item mb-3">
                            <span>Date de naissance :</span>
                            <span class="h6 fw-normal mb-0 ms-2">{{
                                formatDate(user.date_birth)
                            }}</span>
                        </li>
                        <li class="list-group-item mb-3">
                            <span>Lieu de naissance :</span>
                            <span class="h6 fw-normal mb-0 ms-2">{{
                                user.place_birth
                            }}</span>
                        </li>
                        <li class="list-group-item mb-3">
                            <span>Pays :</span>
                            <span class="h6 fw-normal mb-0 ms-2">{{
                                user.country.name
                            }}</span>
                        </li>
                        <li class="list-group-item mb-3">
                            <span>Nationalité :</span>
                            <span class="h6 fw-normal mb-0 ms-2">{{
                                user.nationality
                            }}</span>
                        </li>
                        <li class="list-group-item mb-3">
                            <span>Numéro de passport :</span>
                            <span class="h6 fw-normal mb-0 ms-2">{{
                                user.passport_number
                            }}</span>
                        </li>
                    </ul>
                </b-col>
                <b-col cols="12">
                    <ul class="list-group list-group-borderless">
                        <li class="list-group-item">
                            <span>Note complémentaire : </span>
                            <span
                                class="h6 fw-normal mb-0"
                                v-html="certificateRequest.furtherInformation"
                            />
                        </li>
                    </ul>
                </b-col>
            </b-row>
        </b-card-body>
    </b-card>
</template>

<style scoped></style>
