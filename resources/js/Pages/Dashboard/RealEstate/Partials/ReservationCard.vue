<script setup lang="ts">
import { Reservation } from '@/Types/index';
import { Link } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { computed } from 'vue';

interface Props {
    reservation: Reservation;
}
const props = defineProps<Props>();

const nights = computed(() => {
    const { start_date, end_date, type } = props.reservation;

    if (start_date && end_date) {
        const unit = type === 'séjour' ? 'day' : 'month';
        const diff = dayjs(end_date).diff(dayjs(start_date), unit);

        // Ajoute 1 seulement si c'est un calcul en jours ('séjour')
        return unit === 'day' ? diff + 1 : diff;
    }
    return 0;
});

const statusClass = computed(() => {
    switch (props.reservation.status.value) {
        case 'upcoming':
            return 'status-upcoming';
        case 'progress':
            return 'status-progress';
        case 'completed':
            return 'status-completed';
        default:
            return '';
    }
});

const price = computed(() => props.reservation.fees?.price);

console.log(props.reservation.fees.price);
</script>

<template>
    <b-card-header
        class="border-bottom d-md-flex justify-content-md-between align-items-center"
    >
        <div class="d-flex align-items-center">
            <div class="icon-lg flex-shrink-0">
                <Link
                    :href="
                        route('properties.show', {
                            property: reservation.property.slug,
                        })
                    "
                >
                    <BImg :src="reservation.property.thumb" rounded />
                </Link>
            </div>
            <div class="ms-2">
                <Link
                    :href="
                        route('properties.show', {
                            property: reservation.property.slug,
                        })
                    "
                >
                    <b-card-title tag="h6" class="mb-0">{{
                        reservation.property.title
                    }}</b-card-title>
                </Link>

                <ul class="nav nav-divider small">
                    <li class="nav-item">
                        {{ price }} x {{ nights }}
                        {{ reservation.type === 'séjour' ? 'nuit(s)' : 'mois' }}
                    </li>
                    <li class="nav-item" v-show="reservation.type === 'séjour'">
                        {{ reservation.guests }} invité(s)
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-md-0 mt-2">
            <Link
                :href="
                    route('dashboard.reservations.show', {
                        reservation: reservation.id,
                    })
                "
                class="btn btn-primary-soft mb-0"
                >Details</Link
            >

            <p
                v-if="reservation.status"
                :class="statusClass"
                class="text-md-end mb-0"
            >
                {{ reservation.status.label }}
            </p>
        </div>
    </b-card-header>

    <b-card-body>
        <b-row class="g-3">
            <b-col sm="6" md="4">
                <span>Date d'arrivée</span>
                <h6 class="mb-0">
                    {{ dayjs(reservation.start_date).format('DD MMM YYYY') }}
                </h6>
            </b-col>
            <b-col sm="6" md="4">
                <span>Date de départ</span>
                <h6 class="mb-0">
                    {{ dayjs(reservation.end_date).format('DD MMM YYYY') }}
                </h6>
            </b-col>
            <b-col sm="6" md="4">
                <span>{{
                    reservation.type === 'séjour' ? 'Payer' : 'Réservation'
                }}</span>
                <h6 class="mb-0">
                    {{
                        new Intl.NumberFormat('fr-FR', {
                            style: 'currency',
                            currency: 'EUR',
                        }).format(Number(reservation.price))
                    }}
                </h6>
            </b-col>
        </b-row>
    </b-card-body>
</template>

<style scoped>
.status-upcoming {
    color: #ffc107;
}

.status-progress {
    color: #28a745;
}

.status-completed {
    color: #dc3545;
}
</style>
