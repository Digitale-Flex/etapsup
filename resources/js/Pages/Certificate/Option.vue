<script setup lang="ts">
import {
    faHandHoldingDollar,
    faHandHoldingHeart,
} from '@fortawesome/free-solid-svg-icons';
import { Link } from '@inertiajs/vue3';
import AppHead from "@/Components/AppHead.vue";

const options = [
    {
        title: 'Payer maintenant',
        subtitle: 'Traitement sous 48 heures',
        description:
            'Effectuez le paiement immédiatement pour garantir un traitement prioritaire. Votre demande sera traité dans un délai maximum de 48 heures ouvrées et vous recevrez le document par e-mail.',
        icon: faHandHoldingDollar,
        iconClass: 'text-yellow-500',
        buttonText: 'Payer et recevoir',
        to: 'certificate.pay.index',
    },
    {
        title: 'Payer plus tard',
        subtitle: 'Traitement différé',
        description:
            'Choisissez de payer ultérieurement si vous préférez différer le paiement. Votre demande sera traitée après réception du paiement, ce qui peut prendre plus de temps.',
        icon: faHandHoldingHeart,
        iconClass: 'text-blue-500',
        buttonText: 'Réserver sans payer',
        to: 'certificate.later.index',
    },
];
</script>

<template>
    <AppHead title="Demande d'attestation de logement" />

    <div class="bg-light py-24" styles="padding: 100px 0">
        <b-container>
            <div class="col-12 px-5 text-center" bis_skin_checked="1">
                <h4 class="mb-0">Demande d'attestation de logement</h4>
                <div
                    class="mx-auto mb-6 max-w-3xl text-center font-semibold text-gray-600"
                >
                    Choisissez votre option de paiement pour votre demande
                    d'attestation de logement. Le paiement immédiat permet une
                    génération et un envoi automatiques de votre attestation par
                    email.
                </div>
            </div>

            <b-row class="g-6">
                <b-col md="6" v-for="(option, idx) in options" :key="idx">
                    <Link
                        :href="route(option.to)"
                        class="bg-body d-flex rounded-3 h-100 p-4"
                    >
                        <h3 class="d-flex align-items-center">
                            <font-awesome-icon :icon="option.icon" />
                        </h3>
                        <div class="ms-3">
                            <h5>{{ option.title }}</h5>
                            <p class="mb-0">{{ option.description }}</p>
                        </div>
                    </Link>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<style scoped></style>
