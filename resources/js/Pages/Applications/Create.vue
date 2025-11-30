<script setup lang="ts">
// Clone adapté de RealEstate/Reservation/Index.vue
// V0 simplifié : pas de calendrier, pas de calculs prix, pas de Stripe
import AppHead from '@/Components/AppHead.vue';
import CustomStickyElement from '@/Components/CustomStickyElement.vue';
import ApplicationForm from '@/Pages/Applications/Partials/ApplicationForm.vue';
import EstablishmentCard from '@/Pages/Applications/Partials/EstablishmentCard.vue';
import Hero from '@/Pages/Applications/Partials/Hero.vue';
import type { Establishment } from '@/Types/establishment';

interface PageProps {
    establishment: Establishment;
    draftData?: any;
    user?: any;
    stripeKey: string;
    intent: string;
}

const props = defineProps<PageProps>();
</script>

<template>
    <AppHead :title="`Candidature - ${establishment.title}`">
        <meta
            head-key="description"
            name="description"
            :content="`Soumettez votre candidature à ${establishment.title}`"
        />
    </AppHead>

    <main class="pb-5 application-page">
        <Hero :establishment="establishment" />

        <b-container class="pt-4">
            <b-row class="g-4">
                <!-- Formulaire principal (colonne gauche) -->
                <b-col xl="8">
                    <ApplicationForm
                        :establishment="establishment"
                        :draftData="draftData"
                        :user="user"
                        :stripeKey="stripeKey"
                        :intent="intent"
                    />
                </b-col>

                <!-- Sidebar (colonne droite) -->
                <aside class="col-xl-4">
                    <div class="vstack gap-4">
                        <CustomStickyElement
                            id="establishment-info"
                            data-sticky
                            data-margin-top="100"
                            data-sticky-for="1199"
                        >
                            <!-- Card avec infos établissement -->
                            <EstablishmentCard :establishment="establishment" />

                            <!-- Informations complémentaires -->
                            <BCard no-body class="border-0 shadow-diplomeo mt-3" v-if="establishment.website || establishment.phone">
                                <BCardBody class="p-3">
                                    <h6 class="mb-3 fw-bold">
                                        <i class="bi bi-info-circle me-2 text-primary"></i>
                                        Informations de contact
                                    </h6>
                                    <div class="vstack gap-2">
                                        <a
                                            v-if="establishment.website"
                                            :href="establishment.website"
                                            target="_blank"
                                            class="contact-link"
                                        >
                                            <i class="bi bi-globe me-2"></i>
                                            Site web
                                        </a>
                                        <a
                                            v-if="establishment.phone"
                                            :href="`tel:${establishment.phone}`"
                                            class="contact-link"
                                        >
                                            <i class="bi bi-telephone me-2"></i>
                                            {{ establishment.phone }}
                                        </a>
                                    </div>
                                </BCardBody>
                            </BCard>

                            <!-- Aide -->
                            <BCard no-body class="border-0 bg-light mt-3">
                                <BCardBody class="p-3 text-center">
                                    <i class="bi bi-question-circle display-6 text-primary mb-2"></i>
                                    <h6 class="mb-2">Besoin d'aide ?</h6>
                                    <p class="small text-muted mb-0">
                                        Notre équipe est là pour vous accompagner dans votre candidature.
                                    </p>
                                </BCardBody>
                            </BCard>
                        </CustomStickyElement>
                    </div>
                </aside>
            </b-row>
        </b-container>
    </main>
</template>

<style scoped>
.application-page {
    background-color: #f8fafc;
    min-height: 100vh;
}

.shadow-diplomeo {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
}

.contact-link {
    display: flex;
    align-items: center;
    padding: 0.5rem;
    color: #64748b;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.contact-link:hover {
    background-color: #f8fafc;
    color: #1e3a8a;
}
</style>
