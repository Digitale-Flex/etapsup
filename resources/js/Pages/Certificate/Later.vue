<script setup lang="ts">
import AppHead from '@/Components/AppHead.vue';
import CityListBox from '@/Pages/Certificate/Partials/CityListBox.vue';
import FormLater from '@/Pages/Certificate/Partials/FormLater.vue';
import { City, Country, Genre, Partner, RentalDeposit } from '@/Types/index';
import { Deferred, Link } from '@inertiajs/vue3';
import { BIconHouse } from 'bootstrap-icons-vue';

interface Props {
    countries: Country[];
    partners: Partner[];
    cities?: City[];
    genres: Genre[];
    rental_deposits: RentalDeposit[];
}

defineProps<Props>();

const seoMeta = {
    title: 'Payer plus tard',
    description:
        'Choisissez de payer ultérieurement si vous préférez différer le paiement. Votre demande sera traitée après réception du paiement, ce qui peut prendre plus de temps.',
};
</script>

<template>
    <AppHead :title="seoMeta.title">
        <meta
            head-key="description"
            name="description"
            :content="seoMeta.description"
        />
    </AppHead>

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
                                            :href="route('certificate.option')"
                                            >Demande d'attestation de
                                            logement</Link
                                        >
                                    </li>
                                    <li class="breadcrumb-item active">
                                        Payer plus tard
                                    </li>
                                </ol>
                            </nav>
                            <h1 class="h3 card-title m-0">
                                Demande d'attestation de logement
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

        <section>
            <b-container>
                <b-row class="g-4 g-lg-5">
                    <b-col xl="8">
                        <div class="vstack gap-5">
                            <FormLater
                                :countries
                                :partners
                                :cities
                                :genres
                                :rental_deposits
                            />
                        </div>
                    </b-col>

                    <aside class="col-xl-4">
                        <b-row class="g-4">
                            <Deferred data="cities">
                                <template #fallback>
                                    <div>Chargement...</div>
                                </template>
                                <city-list-box :cities="cities" />
                            </Deferred>
                        </b-row>
                    </aside>
                </b-row>
            </b-container>
        </section>
    </section>
</template>

<style scoped></style>
