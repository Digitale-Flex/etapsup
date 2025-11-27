<script setup lang="ts">
import { Property } from '@/Types/index';
import { Link } from '@inertiajs/vue3';
import { BIconHouse } from 'bootstrap-icons-vue';

interface Props {
    property: Property;
}
defineProps<Props>();
</script>

<template>
    <section class="py-0">
        <b-container>
            <b-card no-body class="bg-light px-sm-5 overflow-hidden">
                <b-row class="align-items-center g-4">
                    <b-col sm="9">
                        <b-card-body>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-dots mb-0">
                                    <li class="breadcrumb-item">
                                        <Link href="/">
                                            <BIconHouse class="mb-1 me-1" />
                                            Accueil
                                        </Link>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <Link
                                            :href="
                                                route('properties.show', {
                                                    property: property.slug,
                                                })
                                            "
                                            prefetch
                                        >
                                            {{ property.title }}
                                        </Link>
                                    </li>
                                    <li class="breadcrumb-item active">
                                        Réservation
                                    </li>
                                </ol>
                            </nav>
                            <h1 class="h3 card-title m-0">
                                Vérifiez votre réservation
                            </h1>
                        </b-card-body>
                    </b-col>
                    <b-col sm="3" class="d-none d-sm-block text-end">
                        <img
                            src="/images/front/element/17.svg"
                            class="mb-n4"
                            alt=""
                        />
                    </b-col>
                </b-row>
            </b-card>
        </b-container>
    </section>
</template>

<style scoped></style>
