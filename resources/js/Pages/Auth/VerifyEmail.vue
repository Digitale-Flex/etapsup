<script setup lang="ts">
// Refonte: Page Verify Email avec charte EtapSup
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { BContainer, BRow, BCol, BButton, BAlert } from 'bootstrap-vue-next';

defineOptions({ layout: GuestLayout });

const props = defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <Head title="Vérification de l'email - EtapSup" />

    <div class="verify-email-page">
        <BContainer class="py-5">
            <BRow class="justify-content-center min-vh-80 align-items-center">
                <BCol lg="5" md="8">
                    <div class="verify-card">
                        <!-- Logo & Title -->
                        <div class="text-center mb-4">
                            <Link href="/" class="logo-link">
                                <h2 class="logo-text">EtapSup</h2>
                            </Link>
                            <div class="verify-icon">📧</div>
                            <h1 class="verify-title">Vérifiez votre email</h1>
                            <p class="verify-subtitle">
                                Merci de vous être inscrit ! Avant de commencer, veuillez vérifier votre adresse email
                                en cliquant sur le lien que nous venons de vous envoyer.
                            </p>
                        </div>

                        <!-- Success Message -->
                        <BAlert
                            v-if="verificationLinkSent"
                            variant="success"
                            show
                            class="mb-4"
                        >
                            <div class="d-flex align-items-center">
                                <span class="me-2">✅</span>
                                <div>
                                    Un nouveau lien de vérification a été envoyé à l'adresse email que vous avez fournie lors de l'inscription.
                                </div>
                            </div>
                        </BAlert>

                        <!-- Info Message -->
                        <div class="info-box mb-4">
                            <div class="info-icon">💡</div>
                            <div class="info-content">
                                <strong>Vous n'avez pas reçu l'email ?</strong>
                                <p class="mb-0 mt-1">
                                    Vérifiez votre dossier spam ou cliquez sur le bouton ci-dessous pour recevoir un nouveau lien.
                                </p>
                            </div>
                        </div>

                        <!-- Resend Form -->
                        <form @submit.prevent="submit" class="verify-form">
                            <BButton
                                type="submit"
                                variant="primary"
                                size="lg"
                                class="w-100 submit-button mb-3"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Envoi en cours...</span>
                                <span v-else>Renvoyer l'email de vérification</span>
                            </BButton>
                        </form>

                        <!-- Logout Link -->
                        <div class="text-center mt-4">
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="logout-link"
                            >
                                ← Se déconnecter
                            </Link>
                        </div>
                    </div>
                </BCol>
            </BRow>
        </BContainer>

        <!-- Footer Copyright -->
        <div class="verify-footer text-center py-4">
            <p class="mb-0 text-muted">
                &copy; 2025 EtapSup. Tous droits réservés.
            </p>
        </div>
    </div>
</template>

<style scoped>
.verify-email-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    display: flex;
    flex-direction: column;
}

.min-vh-80 {
    min-height: 80vh;
}

.verify-card {
    background: white;
    border-radius: 1.5rem;
    padding: 3rem 2.5rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
}

.logo-link {
    text-decoration: none;
}

.logo-text {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0;
}

.verify-icon {
    font-size: 4rem;
    margin: 1.5rem 0 1rem;
}

.verify-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.5rem;
}

.verify-subtitle {
    color: #64748b;
    font-size: 0.95rem;
    margin-bottom: 0;
    line-height: 1.6;
}

.info-box {
    display: flex;
    gap: 1rem;
    padding: 1.25rem;
    background: #eff6ff;
    border-left: 4px solid #1e3a8a;
    border-radius: 0.5rem;
}

.info-icon {
    font-size: 1.5rem;
    flex-shrink: 0;
}

.info-content {
    flex: 1;
}

.info-content strong {
    color: #1a202c;
    font-size: 0.95rem;
}

.info-content p {
    color: #64748b;
    font-size: 0.875rem;
    line-height: 1.5;
}

.verify-form {
    margin-top: 2rem;
}

.submit-button {
    background: linear-gradient(45deg, #dc2626, #dc2626);
    border: none;
    padding: 1rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.submit-button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(237, 41, 57, 0.3);
}

.submit-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.logout-link {
    background: none;
    border: none;
    color: #64748b;
    text-decoration: none;
    font-size: 0.875rem;
    transition: color 0.2s ease;
    cursor: pointer;
    padding: 0;
}

.logout-link:hover {
    color: #1e3a8a;
}

.verify-footer {
    margin-top: auto;
}

@media (max-width: 768px) {
    .verify-card {
        padding: 2rem 1.5rem;
    }

    .logo-text {
        font-size: 2rem;
    }

    .verify-title {
        font-size: 1.5rem;
    }

    .verify-icon {
        font-size: 3rem;
    }
}
</style>
