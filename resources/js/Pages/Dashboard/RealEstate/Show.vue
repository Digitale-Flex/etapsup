<script setup lang="ts">
import CustomGLightbox from '@/Components/CustomGLightbox.vue';
import TextClamp from '@/Components/TextClamp.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ReservationReview from '@/Pages/Dashboard/RealEstate/Partials/ReservationReview.vue';
import { Reservation } from '@/Types/index';
import { faUsers } from '@fortawesome/free-solid-svg-icons';
import { Head, Link } from '@inertiajs/vue3';
import { BIconFullscreen, BIconGeoAlt } from 'bootstrap-icons-vue';
import dayjs from 'dayjs';
import { computed } from 'vue';

defineOptions({ layout: DashboardLayout });
interface PropsPage {
    reservation: Reservation;
}

const props = defineProps<PropsPage>();

// Convertir l'objet images en tableau
const imagesArray = computed(() => {
    return Object.values(props.reservation.property?.images || {});
});

// Trier les images par ordre
const sortedImages = computed(() => {
    return imagesArray.value
        .slice() // Crée une copie du tableau pour éviter de modifier l'original
        .sort((a, b) => a.order - b.order)
        .map((img) => img.original_url);
});

// Nombre total d'images restantes
const remainingImagesCount = computed(() => {
    return Math.max(0, sortedImages.value.length - 4);
});
</script>

<template>
    <Head title="Mes Réservation" />

    <b-row class="g-4 g-xl-5">
        <b-col xxl="6">
            <b-row class="g-2 g-sm-4">
                <!-- Limiter l'affichage à 4 images avec .slice(0, 4) -->
                <b-col
                    v-for="(image, idx) in sortedImages.slice(0, 3)"
                    cols="6"
                    :key="idx"
                >
                    <CustomGLightbox :link="image">
                        <b-card
                            no-body
                            class="card-element-hover card-overlay-hover overflow-hidden"
                        >
                            <img :src="image" class="rounded-3" alt="" />
                            <div class="hover-element w-100 h-100">
                                <div
                                    class="position-absolute top-50 start-50 translate-middle bg-dark rounded-1 lh-1 p-2"
                                >
                                    <BIconFullscreen class="text-white" />
                                </div>
                            </div>
                        </b-card>
                    </CustomGLightbox>
                </b-col>
                <!-- Afficher un indicateur pour les images restantes -->
                <b-col cols="6" v-if="remainingImagesCount > 0">
                    <b-card
                        no-body
                        class="card-element-hover card-overlay-hover overflow-hidden"
                    >
                        <div class="position-relative">
                            <img
                                :src="sortedImages[4]"
                                class="rounded-3"
                                alt=""
                            />
                            <div
                                class="position-absolute w-100 h-100 bg-dark d-flex align-items-center justify-content-center start-0 top-0 bg-opacity-50"
                            >
                                <span class="fs-5 text-white"
                                    >+{{ remainingImagesCount }}</span
                                >
                            </div>
                        </div>
                    </b-card>
                </b-col>
            </b-row>
        </b-col>

        <b-col xxl="6">
            <Link
                :href="
                    route('properties.show', {
                        property: reservation.property.slug,
                    })
                "
                prefetch
                ><h5>
                    <span class="fw-light"
                        >{{ reservation.property.propertyType.label }}: </span
                    >{{ reservation.property.title }}
                </h5></Link
            >
            <small class="fw-bold items-center">
                <BIconGeoAlt class="mb-1 me-2" />
                {{ reservation.property.address }}
            </small>
            <p class="mb-4 mt-3">
                <TextClamp
                    :text="reservation.property.description"
                    :max-lines="3"
                    auto-resize
                />
            </p>

            <b-row class="g-4">
                <b-col class="d-none" cols="6" md="4">
                    <div class="d-flex align-items-center">
                        <div
                            class="icon-lg bg-primary text-primary rounded-2 bg-opacity-10"
                        >
                            <font-awesome-icon :icon="faUsers" />
                        </div>
                        <div class="ms-2">
                            <small>Invité(s)</small>
                            <h6 class="mb-0 mt-1">{{ reservation.guests }}</h6>
                        </div>
                    </div>
                </b-col>
                <b-col class="d-none" cols="6" md="4">
                    <div class="d-flex align-items-center">
                        <div
                            class="icon-lg bg-primary text-primary rounded-2 bg-opacity-10"
                        >
                            <font-awesome-icon :icon="faUsers" />
                        </div>
                        <div class="ms-2">
                            <small>Taxes</small>
                            <h6 class="mb-0 mt-1"></h6>
                        </div>
                    </div>
                </b-col>

                <div
                    class="bg-light border-secondary d-inline-block mt-4 rounded border border-opacity-25 p-3"
                >
                    <h6 class="small">
                        Reservation {{ reservation.status.label }} :
                    </h6>
                    <div class="hstack gap-md-5 mt-2 flex-wrap gap-4">
                        <div>
                            <small>Arrivé :</small>
                            <h6 class="fw-normal mb-0">
                                {{
                                    dayjs(reservation.start_date).format(
                                        'DD/MM/YYYY',
                                    )
                                }}
                            </h6>
                        </div>
                        <div>
                            <small>Départ :</small>
                            <h6 class="fw-normal mb-0">
                                {{
                                    dayjs(reservation.end_date).format(
                                        'DD/MM/YYYY',
                                    )
                                }}
                            </h6>
                        </div>
                        <div>
                            <small>
                                {{
                                    reservation.type === 'séjour'
                                        ? 'Payer'
                                        : 'Réservation'
                                }}
                            </small>
                            <!-- UI-Fix-2.3: Remplacer text-success (vert) par text-primary (bleu EtapSup) -->
                            <h6 class="text-primary mb-0">
                                {{
                                    new Intl.NumberFormat('fr-FR', {
                                        style: 'currency',
                                        currency: 'EUR',
                                    }).format(Number(reservation.price))
                                }}
                            </h6>
                        </div>
                    </div>
                </div>
            </b-row>
        </b-col>

        <b-col cols="4">
            <div
                class="bg-light border-secondary d-inline-block mt-4 rounded border border-opacity-25 p-3"
            >
                <div
                    v-if="reservation.type === 'séjour'"
                    class="hstack gap-md-5 mt-2 flex-wrap gap-4"
                >
                    <div>
                        <small>Frais de service :</small>
                        <h6 class="fw-normal mb-0">
                            {{ reservation.fees.serviceFees }}
                        </h6>
                    </div>
                    <div>
                        <small>Consommable :</small>
                        <h6 class="fw-normal mb-0">
                            {{ reservation.fees.consumable }}
                        </h6>
                    </div>
                    <div>
                        <small>Taxe de séjour :</small>
                        <h6 class="fw-normal mb-0">
                            {{ reservation.fees.touristTax }}
                        </h6>
                    </div>
                    <div>
                        <small>Frais de menage :</small>
                        <h6 class="fw-normal mb-0">
                            {{ reservation.fees.cleaningFees }}
                        </h6>
                    </div>
                    <div>
                        <small>Frais de service :</small>
                        <h6 class="fw-normal mb-0">
                            {{ reservation.fees.serviceFees }}
                        </h6>
                    </div>
                </div>

                <div v-else class="hstack gap-md-5 mt-2 flex-wrap gap-4">
                    <div>
                        <small>Caution :</small>
                        <h6 class="fw-normal mb-0">
                            {{ reservation.fees.caution }}
                        </h6>
                    </div>
                    <div>
                        <small>1<sup>er</sup> mois de loyer :</small>
                        <h6 class="fw-normal mb-0">
                            {{ reservation.fees.firstMonthRent }}
                        </h6>
                    </div>
                    <div>
                        <small>Frais de réservation :</small>
                        <h6 class="fw-normal mb-0">
                            {{ reservation.fees.applicationFees }}
                        </h6>
                    </div>
                </div>
            </div>
        </b-col>

        <b-col>
            <ReservationReview :reservation can-review />
        </b-col>
    </b-row>
</template>

<style scoped></style>
