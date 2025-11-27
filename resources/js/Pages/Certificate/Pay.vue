<script setup lang="ts">
import AppHead from "@/Components/AppHead.vue";
import {Link} from "@inertiajs/vue3";
import { BIconHouse } from 'bootstrap-icons-vue'
import {City, Country, Genre, Partner, Region, RentalDeposit} from "@/Types/index";
import CityListBox from "@/Pages/Certificate/Partials/CityListBox.vue";
import FormPay from "@/Pages/Certificate/Partials/FormPay.vue";

interface Props {
    countries: Country[],
    partners: Partner[],
    cities: City[],
    genres: Genre[],
    rental_deposits: RentalDeposit[]
}

defineProps<Props>();

const seoMeta = {
    title: "Payer maintenant",
    description: "Choisissez de payer ultérieurement si vous préférez différer le paiement. Votre demande sera traitée après réception du paiement, ce qui peut prendre plus de temps.",
}
</script>

<template>
    <AppHead :title="seoMeta.title">
        <meta head-key="description" name="description" :content="seoMeta.description" />
    </AppHead>

    <section class="py-0">
        <b-container>
            <b-card no-body class="bg-light overflow-hidden px-sm-5">
                <b-row class="align-items-center g-4">
                    <b-col sm="9">
                        <b-card-body>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-dots mb-0">
                                    <li class="breadcrumb-item">
                                        <Link href="/">
                                            <BIconHouse class="me-1 mb-1" />
                                            Accueil
                                        </Link>
                                    </li>
                                    <li class="breadcrumb-item"><Link :href="route('certificate.option')">Demande d'attestation de logement</Link></li>
                                    <li class="breadcrumb-item active">Payer maintenant</li>
                                </ol>
                            </nav>
                            <h1 class="m-0 h3 card-title">Demande d'attestation de logement</h1>
                        </b-card-body>
                    </b-col>
                    <b-col sm="3" class="text-end d-none d-sm-block">
                        <img src="/images/front/element/17.svg" class="mb-n4" alt="" />
                    </b-col>
                </b-row>
            </b-card>
        </b-container>

        <section>
            <b-container>
                <b-row class="g-4 g-lg-5">
                    <b-col xl="8">
                        <div class="vstack gap-5">
                           <FormPay :countries :partners :cities="cities" :genres :rental_deposits/>
                        </div>
                    </b-col>

                    <aside class="col-xl-4">
                        <b-row class="g-4">
                            <city-list-box :cities="cities" />
                        </b-row>
                    </aside>
                </b-row>
            </b-container>
        </section>
    </section>
</template>

<style scoped>

</style>
