<script setup lang="ts">
import ReservationCard from '@/Pages/Dashboard/RealEstate/Partials/ReservationCard.vue';
import { Reservation } from '@/Types/index';
import { BIconBriefcaseFill } from 'bootstrap-icons-vue';

interface Tab {
    reservations: Reservation[];
}
defineProps<Tab>();
</script>

<template>
    <b-tabs
        nav-class="nav nav-tabs nav-bottom-line nav-responsive nav-justified"
    >
        <b-tab>
            <template #title>
                <BIconBriefcaseFill class="fa-fw me-1" />
                Candidatures à suivre
            </template>

            <b-card-body>
                <b-card
                    no-body
                    class="mb-4 border"
                    v-for="(item, idx) in reservations"
                    :key="idx"
                >
                    <ReservationCard :reservation="item" />
                </b-card>
            </b-card-body>
        </b-tab>

        <!--<b-tab>
            <template #title>
                <BIconPatchCheck class="fa-fw me-1" />
                Réservation terminée
            </template>
            <b-card-body>
                <b-card-header
                    class="border-bottom d-md-flex justify-content-md-between align-items-center"
                >
                    <div class="d-flex align-items-center">
                        <div
                            class="icon-lg bg-light rounded-circle flex-shrink-0"
                        >
                            <font-awesome-icon :icon="faCar" />
                        </div>
                        <div class="ms-2">
                            <b-card-title tag="h6" class="mb-0"
                                >Chicago to San Antonio</b-card-title
                            >
                            <ul class="nav nav-divider small">
                                <li class="nav-item">
                                    Booking ID: CGDSUAHA12548
                                </li>
                                <li class="nav-item">Camry, Accord</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-md-0 mt-2">
                        <a href="#" class="btn btn-primary-soft mb-0"
                            >Manage Booking</a
                        >
                    </div>
                </b-card-header>

                <b-card-body>
                    <b-row class="g-3">
                        <b-col sm="6" md="4">
                            <span>Pickup address</span>
                            <h6 class="mb-0">40764 Winchester Rd</h6>
                        </b-col>

                        <b-col sm="6" md="4">
                            <span>Drop address</span>
                            <h6 class="mb-0">11185 Mary Ball Rd</h6>
                        </b-col>

                        <b-col md="4">
                            <span>Booked by</span>
                            <h6 class="mb-0">Frances Guerrero</h6>
                        </b-col>
                    </b-row>
                </b-card-body>
            </b-card-body>
        </b-tab> -->
    </b-tabs>
</template>

<style scoped></style>
