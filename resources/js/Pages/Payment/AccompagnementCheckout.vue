<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';
import UserMenu from '@/Layouts/Partials/UserMenu.vue';

/**
 * Feature 9 - Sprint 1: Page de paiement pour accompagnement premium
 *
 * Cette page affiche les détails de l'accompagnement premium (299€)
 * et permet de lancer le paiement via Stripe Checkout
 */

interface Props {
    application: {
        id: string;
        hashid: string;
        establishment: {
            title: string;
            slug: string;
        };
        accompagnement_premium: boolean;
        accompagnement_paid: boolean;
    };
}

const props = defineProps<Props>();

const loading = ref(false);
const errorMessage = ref<string | null>(null);

const ACCOMPAGNEMENT_PRICE = 299; // €

// Services inclus dans l'accompagnement
const services = [
    {
        icon: 'bi-file-earmark-text',
        title: 'Optimisation de votre CV',
        description: 'Révision complète et conseils personnalisés pour un CV percutant',
    },
    {
        icon: 'bi-envelope-heart',
        title: 'Lettre de motivation sur mesure',
        description: 'Rédaction guidée et relecture professionnelle',
    },
    {
        icon: 'bi-mic',
        title: 'Préparation aux entretiens',
        description: 'Simulations d\'entretiens et conseils stratégiques',
    },
    {
        icon: 'bi-chat-dots',
        title: 'Suivi personnalisé',
        description: 'Conseiller dédié disponible par email et téléphone',
    },
    {
        icon: 'bi-calendar-check',
        title: 'Gestion des délais',
        description: 'Rappels et organisation de votre calendrier de candidature',
    },
    {
        icon: 'bi-trophy',
        title: 'Stratégie de réussite',
        description: 'Plan d\'action personnalisé selon votre profil',
    },
];

// Vérifier si déjà payé
const isAlreadyPaid = computed(() => props.application.accompagnement_paid);

/**
 * Lancer le paiement Stripe Checkout
 */
const handlePayment = async () => {
    if (isAlreadyPaid.value) {
        errorMessage.value = 'L\'accompagnement premium a déjà été payé.';
        return;
    }

    loading.value = true;
    errorMessage.value = null;

    try {
        const response = await axios.post(
            `/api/v1/applications/${props.application.hashid}/accompagnement/checkout`
        );

        if (response.data.success && response.data.session_url) {
            // Rediriger vers Stripe Checkout
            window.location.href = response.data.session_url;
        } else {
            errorMessage.value = 'Erreur lors de la création de la session de paiement.';
            loading.value = false;
        }
    } catch (error: any) {
        console.error('Erreur paiement accompagnement:', error);
        errorMessage.value = error.response?.data?.message || 'Une erreur est survenue.';
        loading.value = false;
    }
};

/**
 * Retourner à la candidature
 */
const goBack = () => {
    router.visit(`/applications/${props.application.hashid}`);
};
</script>

<template>
    <div>
        <Head title="Accompagnement Premium" />

        <!-- Header avec User Menu -->
        <UserMenu />

        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Header -->
                    <div class="text-center mb-5">
                        <div class="mb-3">
                            <i class="bi bi-award text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h1 class="display-5 fw-bold mb-3">Accompagnement Premium</h1>
                        <p class="lead text-muted">
                            Maximisez vos chances de réussite avec un accompagnement personnalisé
                        </p>
                    </div>

                    <!-- Établissement concerné -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-building text-primary me-3" style="font-size: 2rem;"></i>
                                <div>
                                    <h6 class="mb-1 text-muted">Candidature pour</h6>
                                    <h5 class="mb-0">{{ application.establishment.title }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message si déjà payé -->
                    <div v-if="isAlreadyPaid" class="alert alert-success d-flex align-items-center mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div>
                            <strong>Accompagnement déjà activé !</strong>
                            Vous bénéficiez déjà de l'accompagnement premium pour cette candidature.
                        </div>
                    </div>

                    <!-- Services inclus -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="bi bi-star-fill text-warning me-2"></i>
                                Ce que vous obtenez
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div
                                    v-for="service in services"
                                    :key="service.title"
                                    class="col-md-6"
                                >
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                                style="width: 48px; height: 48px;"
                                            >
                                                <i :class="service.icon" class="text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-1">{{ service.title }}</h6>
                                            <p class="text-muted small mb-0">{{ service.description }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="card border-0 shadow-sm bg-primary text-white mb-4">
                        <div class="card-body text-center py-4">
                            <h6 class="text-white-50 mb-2">Tarif unique</h6>
                            <h2 class="display-4 fw-bold mb-2">{{ ACCOMPAGNEMENT_PRICE }}€</h2>
                            <p class="mb-0 text-white-50">
                                Paiement sécurisé unique • Aucun abonnement
                            </p>
                        </div>
                    </div>

                    <!-- Error message -->
                    <div v-if="errorMessage" class="alert alert-danger d-flex align-items-center mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div>{{ errorMessage }}</div>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-3">
                        <button
                            v-if="!isAlreadyPaid"
                            class="btn btn-primary btn-lg"
                            :disabled="loading"
                            @click="handlePayment"
                        >
                            <span v-if="loading">
                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Redirection vers Stripe...
                            </span>
                            <span v-else>
                                <i class="bi bi-credit-card me-2"></i>
                                Payer {{ ACCOMPAGNEMENT_PRICE }}€ maintenant
                            </span>
                        </button>

                        <button
                            class="btn btn-outline-secondary"
                            :disabled="loading"
                            @click="goBack"
                        >
                            <i class="bi bi-arrow-left me-2"></i>
                            Retour à ma candidature
                        </button>
                    </div>

                    <!-- Sécurité Stripe -->
                    <div class="text-center mt-4">
                        <p class="text-muted small mb-0">
                            <i class="bi bi-shield-check me-1"></i>
                            Paiement 100% sécurisé via Stripe
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
