<script setup lang="ts">
import { Reservation } from '@/Types/index';
import { Link } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { computed } from 'vue';

interface Props {
    reservation: Reservation;
}
const props = defineProps<Props>();

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
                        📚 {{ reservation.property.propertyType?.label || 'Programme' }}
                    </li>
                    <li class="nav-item">
                        📍 {{ reservation.property.city?.name || 'Non spécifié' }}{{ reservation.property.city?.country?.name ? ', ' + reservation.property.city.country.name : '' }}
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
                >Voir les détails</Link
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
                <span>📅 Date de candidature</span>
                <h6 class="mb-0">
                    {{ dayjs(reservation.created_at || reservation.start_date).format('DD MMM YYYY') }}
                </h6>
            </b-col>
            <b-col sm="6" md="4">
                <span>🎓 Domaine d'études</span>
                <h6 class="mb-0">
                    {{ reservation.property.category?.label || 'Non spécifié' }}
                </h6>
            </b-col>
            <b-col sm="6" md="4">
                <span>🏫 Type d'établissement</span>
                <h6 class="mb-0">
                    {{ reservation.property.propertyType?.label || 'Établissement' }}
                </h6>
            </b-col>
        </b-row>
    </b-card-body>
</template>

<!-- UI-Fix-2.1: Harmonisation couleurs palette EtapSup -->
<style scoped>
.status-upcoming {
    color: #ffc107; /* Jaune pour À venir */
}

.status-progress {
    color: #1e3a8a; /* Violet EtapSup pour En cours (remplace #28a745 vert Mareza) */
}

.status-completed {
    color: #1e3a8a; /* Bleu marine EtapSup pour Terminé (remplace #10b981 vert) */
}
</style>
