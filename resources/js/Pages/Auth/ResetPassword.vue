<script setup lang="ts">
// Refonte: Page Reset Password avec charte EtapSup
import PasswordInput from '@/Components/PasswordInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { BContainer, BRow, BCol, BFormGroup, BFormInput, BFormInvalidFeedback, BButton } from 'bootstrap-vue-next';

defineOptions({ layout: GuestLayout });

const props = defineProps<{
    email: string;
    token: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <Head title="Réinitialisation du mot de passe - EtapSup" />

    <div class="reset-password-page">
        <BContainer class="py-5">
            <BRow class="justify-content-center min-vh-80 align-items-center">
                <BCol lg="5" md="8">
                    <div class="reset-card">
                        <!-- Logo & Title -->
                        <div class="text-center mb-4">
                            <Link href="/" class="logo-link">
                                <h2 class="logo-text">EtapSup</h2>
                            </Link>
                            <h1 class="reset-title">Nouveau mot de passe</h1>
                            <p class="reset-subtitle">
                                Choisissez un mot de passe sécurisé pour votre compte
                            </p>
                        </div>

                        <!-- Reset Password Form -->
                        <form @submit.prevent="submit" class="reset-form">
                            <!-- Email (read-only) -->
                            <BFormGroup label="Adresse email" label-for="email" class="mb-3">
                                <BFormInput
                                    v-model="form.email"
                                    :state="form.errors.email ? false : null"
                                    type="email"
                                    id="email"
                                    readonly
                                    disabled
                                    class="form-input"
                                />
                                <BFormInvalidFeedback v-if="form.errors.email">
                                    {{ form.errors.email }}
                                </BFormInvalidFeedback>
                            </BFormGroup>

                            <!-- New Password -->
                            <BFormGroup label="Nouveau mot de passe" label-for="password" class="mb-3">
                                <PasswordInput
                                    v-model="form.password"
                                    :error="form.errors.password"
                                    :state="form.errors.password ? false : null"
                                    label=""
                                    name="password"
                                    place-holder="Minimum 8 caractères"
                                    id="password"
                                    required
                                    :disabled="form.processing"
                                />
                            </BFormGroup>

                            <!-- Confirm Password -->
                            <BFormGroup label="Confirmer le mot de passe" label-for="password_confirmation" class="mb-4">
                                <PasswordInput
                                    v-model="form.password_confirmation"
                                    :error="form.errors.password_confirmation"
                                    :state="form.errors.password_confirmation ? false : null"
                                    label=""
                                    name="password_confirmation"
                                    place-holder="Retapez votre mot de passe"
                                    id="password_confirmation"
                                    required
                                    :disabled="form.processing"
                                />
                            </BFormGroup>

                            <!-- Submit Button -->
                            <BButton
                                type="submit"
                                variant="primary"
                                size="lg"
                                class="w-100 submit-button"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Réinitialisation en cours...</span>
                                <span v-else>Réinitialiser le mot de passe</span>
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
        <div class="reset-footer text-center py-4">
            <p class="mb-0 text-muted">
                &copy; 2025 EtapSup. Tous droits réservés.
            </p>
        </div>
    </div>
</template>

<style scoped>
.reset-password-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    display: flex;
    flex-direction: column;
}

.min-vh-80 {
    min-height: 80vh;
}

.reset-card {
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

.reset-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1a202c;
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
}

.reset-subtitle {
    color: #64748b;
    font-size: 0.95rem;
    margin-bottom: 0;
    line-height: 1.6;
}

.reset-form {
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
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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

.back-link {
    color: #64748b;
    text-decoration: none;
    font-size: 0.875rem;
    transition: color 0.2s ease;
}

.back-link:hover {
    color: #1e3a8a;
}

.reset-footer {
    margin-top: auto;
}

@media (max-width: 768px) {
    .reset-card {
        padding: 2rem 1.5rem;
    }

    .logo-text {
        font-size: 2rem;
    }

    .reset-title {
        font-size: 1.5rem;
    }
}
</style>
