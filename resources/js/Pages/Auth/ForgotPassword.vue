<script setup lang="ts">
// Refonte: Page Forgot Password avec charte EtapSup
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { BContainer, BRow, BCol, BFormGroup, BFormInput, BFormInvalidFeedback, BButton } from 'bootstrap-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Mot de passe oublié - EtapSup" />

    <div class="forgot-password-page">
        <BContainer class="py-5">
            <BRow class="justify-content-center min-vh-80 align-items-center">
                <BCol lg="5" md="8">
                    <div class="forgot-card">
                        <!-- Logo & Title -->
                        <div class="text-center mb-4">
                            <Link href="/" class="logo-link">
                                <h2 class="logo-text">EtapSup</h2>
                            </Link>
                            <h1 class="forgot-title">Mot de passe oublié ?</h1>
                            <p class="forgot-subtitle">
                                Pas de souci ! Entrez votre email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
                            </p>
                        </div>

                        <!-- Status Message -->
                        <div v-if="status" class="alert alert-success mb-4">
                            {{ status }}
                        </div>

                        <!-- Forgot Password Form -->
                        <form @submit.prevent="submit" class="forgot-form">
                            <!-- Email -->
                            <BFormGroup label="Adresse email" label-for="email" class="mb-4">
                                <BFormInput
                                    v-model="form.email"
                                    :state="form.errors.email ? false : null"
                                    type="email"
                                    id="email"
                                    placeholder="votre@email.com"
                                    required
                                    autofocus
                                    :disabled="form.processing"
                                    class="form-input"
                                />
                                <BFormInvalidFeedback v-if="form.errors.email">
                                    {{ form.errors.email }}
                                </BFormInvalidFeedback>
                            </BFormGroup>

                            <!-- Submit Button -->
                            <BButton
                                type="submit"
                                variant="primary"
                                size="lg"
                                class="w-100 submit-button"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Envoi en cours...</span>
                                <span v-else>Envoyer le lien de réinitialisation</span>
                            </BButton>
                        </form>

                        <!-- Back to Login -->
                        <div class="text-center mt-4">
                            <Link :href="route('login')" class="back-link">
                                ← Retour à la connexion
                            </Link>
                        </div>
                    </div>
                </BCol>
            </BRow>
        </BContainer>

        <!-- Footer Copyright -->
        <div class="forgot-footer text-center py-4">
            <p class="mb-0 text-muted">
                &copy; 2025 EtapSup. Tous droits réservés.
            </p>
        </div>
    </div>
</template>

<style scoped>
.forgot-password-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    display: flex;
    flex-direction: column;
}

.min-vh-80 {
    min-height: 80vh;
}

.forgot-card {
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0;
}

.forgot-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1a202c;
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
}

.forgot-subtitle {
    color: #64748b;
    font-size: 0.95rem;
    margin-bottom: 0;
    line-height: 1.6;
}

.forgot-form {
    margin-top: 2rem;
}

.form-input {
    padding: 0.875rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 1rem;
    transition: all 0.2s ease;
}

.form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.submit-button {
    background: linear-gradient(45deg, #ed2939, #cc1f2d);
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

.back-link {
    color: #64748b;
    text-decoration: none;
    font-size: 0.875rem;
    transition: color 0.2s ease;
}

.back-link:hover {
    color: #667eea;
}

.forgot-footer {
    margin-top: auto;
}

.alert-success {
    background: #d1fae5;
    border: 1px solid #6ee7b7;
    color: #065f46;
    padding: 1rem;
    border-radius: 0.5rem;
}

@media (max-width: 768px) {
    .forgot-card {
        padding: 2rem 1.5rem;
    }

    .logo-text {
        font-size: 2rem;
    }

    .forgot-title {
        font-size: 1.5rem;
    }
}
</style>
