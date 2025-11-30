<template>
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5 text-center">
                            <!-- Icône annulation -->
                            <div class="mb-4">
                                <div class="cancel-icon mx-auto"
                                     style="width: 80px; height: 80px; background: #ffc107; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="white"
                                         viewBox="0 0 16 16">
                                        <path
                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                        <path
                                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Titre -->
                            <h2 class="fw-bold mb-3" style="color: #ffc107;">Paiement annulé</h2>

                            <!-- Message -->
                            <p class="text-muted mb-4">
                                Vous avez annulé le paiement.<br>
                                Aucun montant n'a été prélevé sur votre carte.
                            </p>

                            <!-- Application ID (si fourni) -->
                            <div v-if="application_id" class="alert alert-light border mb-4">
                                <small class="text-muted">Candidature: <code>{{ application_id }}</code></small>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="d-grid gap-2">
                                <Link :href="route('dashboard')"
                                      class="btn btn-lg text-white"
                                      style="background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);">
                                    Retour au tableau de bord
                                </Link>
                                <button @click="retryPayment" class="btn btn-lg btn-outline-primary">
                                    Réessayer le paiement
                                </button>
                            </div>

                            <!-- Info supplémentaire -->
                            <div class="mt-4">
                                <p class="text-muted small mb-0">
                                    ℹ️ Vous pouvez réessayer le paiement à tout moment depuis votre tableau de bord.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';

// Sprint1 Feature 1.7.1 — Page annulation paiement Stripe Checkout

const props = defineProps<{
    application_id?: string;
}>();

const retryPayment = () => {
    // Retourner au dashboard pour réessayer
    router.visit(route('dashboard'));
};
</script>

<style scoped>
.cancel-icon {
    animation: shake 0.5s ease-out;
}

@keyframes shake {
    0%,
    100% {
        transform: translateX(0);
    }
    25% {
        transform: translateX(-10px);
    }
    75% {
        transform: translateX(10px);
    }
}
</style>
