<script setup lang="ts">
// Clone adapté de RealEstate/Reservation/Partials/Hero.vue
// Design EtatSup avec gradient purple
import { Link } from '@inertiajs/vue3';
import { BIconHouse, BIconMortarboard } from 'bootstrap-icons-vue';
import type { Establishment } from '@/Types/establishment';

interface Props {
    establishment: Establishment;
}
defineProps<Props>();
</script>

<template>
    <section class="py-0">
        <b-container>
            <b-card no-body class="bg-gradient-etatsup px-sm-5 overflow-hidden text-white">
                <b-row class="align-items-center g-4">
                    <b-col sm="9">
                        <b-card-body>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-dots mb-0 breadcrumb-white">
                                    <li class="breadcrumb-item">
                                        <Link href="/" class="text-white">
                                            <BIconHouse class="mb-1 me-1" />
                                            Accueil
                                        </Link>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <Link
                                            :href="`/establishments/${establishment.slug}`"
                                            class="text-white"
                                            prefetch
                                        >
                                            {{ establishment.title }}
                                        </Link>
                                    </li>
                                    <li class="breadcrumb-item active text-white">
                                        Candidature
                                    </li>
                                </ol>
                            </nav>
                            <h1 class="h3 card-title m-0 mt-2">
                                <BIconMortarboard class="me-2" />
                                Soumettez votre candidature
                            </h1>
                        </b-card-body>
                    </b-col>
                    <b-col sm="3" class="d-none d-sm-block text-end">
                        <img
                            src="/images/front/element/17.svg"
                            class="mb-n4"
                            alt=""
                            style="filter: brightness(0) invert(1);"
                        />
                    </b-col>
                </b-row>
            </b-card>
        </b-container>
    </section>
</template>

<style scoped>
.bg-gradient-etatsup {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
}

.breadcrumb-white .breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255, 255, 255, 0.7);
}
</style>
