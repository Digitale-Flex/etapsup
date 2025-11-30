<template>
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5 text-center">
                            <!-- Icône succès -->
                            <div class="mb-4">
                                <div class="success-icon mx-auto"
                                     style="width: 80px; height: 80px; background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="white"
                                         viewBox="0 0 16 16">
                                        <path
                                            d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Titre -->
                            <h2 class="fw-bold mb-3" style="color: #1e3a8a;">Paiement réussi !</h2>

                            <!-- Message -->
                            <p class="text-muted mb-4">
                                Votre paiement des frais de dossier a été validé avec succès.<br>
                                Vous allez recevoir un reçu par email sous peu.
                            </p>

                            <!-- Session ID (debug) -->
                            <div v-if="session_id" class="alert alert-light border mb-4">
                                <small class="text-muted">Session ID: <code>{{ session_id }}</code></small>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="d-grid gap-2">
                                <Link :href="route('dashboard')"
                                      class="btn btn-lg text-white"
                                      style="background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);">
                                    Retour au tableau de bord
                                </Link>
                                <Link :href="route('establishments.index')"
                                      class="btn btn-lg btn-outline-secondary">
                                    Voir les établissements
                                </Link>
                            </div>

                            <!-- Info supplémentaire -->
                            <div class="mt-4">
                                <p class="text-muted small mb-0">
                                    💳 Votre reçu Stripe est disponible dans la section "Mes factures" du tableau de bord.
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
import { Link } from '@inertiajs/vue3';

// Sprint1 Feature 1.7.1 — Page succès paiement Stripe Checkout

defineProps<{
    session_id?: string;
}>();
</script>

<style scoped>
.success-icon {
    animation: scaleIn 0.5s ease-out;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
    }
    to {
        transform: scale(1);
    }
}
</style>
