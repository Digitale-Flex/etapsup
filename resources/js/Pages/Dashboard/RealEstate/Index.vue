<script setup lang="ts">
import Pagination from '@/Components/Pagination.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ReservationTabs from '@/Pages/Dashboard/RealEstate/Partials/ReservationTabs.vue';
import { PaginationMeta, Reservation } from '@/Types/index';
import { Head } from '@inertiajs/vue3';

defineOptions({ layout: DashboardLayout });

interface ReservationsPaginated {
    data: Reservation[];
    meta: PaginationMeta;
}

const props = defineProps<{
    reservations: ReservationsPaginated;
}>();
</script>

<template>
    <Head title="Mes Réservation" />
    <b-card no-body class="border bg-transparent">
        <b-card-header class="border-bottom bg-transparent">
            <h5 class="card-header-title">Mes réservations</h5>
        </b-card-header>

        <div v-if="!reservations.data.length" class="py-4 text-center">
            <p class="text-info mb-0">
                Vous n'avez pas encore effectué de réservation.
            </p>
        </div>

        <template v-else
            ><ReservationTabs :reservations="reservations.data"
        /></template>
    </b-card>

    <Pagination class="mt-5" :pagination="reservations.meta" />
</template>

<style scoped></style>
