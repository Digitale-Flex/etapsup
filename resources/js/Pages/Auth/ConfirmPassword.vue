<script setup lang="ts">
// Refonte: Page Confirm Password avec charte EtapSup
import PasswordInput from '@/Components/PasswordInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { BContainer, BRow, BCol, BButton } from 'bootstrap-vue-next';

defineOptions({ layout: GuestLayout });

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Confirmer le mot de passe - EtapSup" />

    <div class="confirm-password-page">
        <BContainer class="py-5">
            <BRow class="justify-content-center min-vh-80 align-items-center">
                <BCol lg="5" md="8">
                    <div class="confirm-card">
                        <!-- Logo & Title -->
                        <div class="text-center mb-4">
                            <h2 class="logo-text">EtapSup</h2>
                            <div class="confirm-icon">🔒</div>
                            <h1 class="confirm-title">Zone sécurisée</h1>
                            <p class="confirm-subtitle">
                                Ceci est une zone sécurisée de l'application. Veuillez confirmer votre mot de passe avant de continuer.
                            </p>
                        </div>

                        <!-- Confirm Password Form -->
                        <form @submit.prevent="submit" class="confirm-form">
                            <PasswordInput
                                v-model="form.password"
                                :error="form.errors.password"
                                :state="form.errors.password ? false : null"
                                label="Mot de passe"
                                name="password"
                                place-holder="Entrez votre mot de passe"
                                id="password"
                                required
                                autofocus
                                :disabled="form.processing"
                            />

                            <!-- Submit Button -->
                            <BButton
                                type="submit"
                                variant="primary"
                                size="lg"
                                class="w-100 submit-button mt-4"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Confirmation...</span>
                                <span v-else>Confirmer</span>
                            </BButton>
                        </form>
                    </div>
                </BCol>
            </BRow>
        </BContainer>

        <!-- Footer Copyright -->
        <div class="confirm-footer text-center py-4">
            <p class="mb-0 text-muted">
                &copy; 2025 EtapSup. Tous droits réservés.
            </p>
        </div>
    </div>
</template>

<style scoped>
.confirm-password-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    display: flex;
    flex-direction: column;
}

.min-vh-80 {
    min-height: 80vh;
}

.confirm-card {
    background: white;
    border-radius: 1.5rem;
    padding: 3rem 2.5rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
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

.confirm-icon {
    font-size: 4rem;
    margin: 1.5rem 0 1rem;
}

.confirm-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.5rem;
}

.confirm-subtitle {
    color: #64748b;
    font-size: 0.95rem;
    margin-bottom: 0;
    line-height: 1.6;
}

.confirm-form {
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

.confirm-footer {
    margin-top: auto;
}

@media (max-width: 768px) {
    .confirm-card {
        padding: 2rem 1.5rem;
    }

    .logo-text {
        font-size: 2rem;
    }

    .confirm-title {
        font-size: 1.5rem;
    }

    .confirm-icon {
        font-size: 3rem;
    }
}
</style>
