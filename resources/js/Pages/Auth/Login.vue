<script setup lang="ts">
// Refonte: Page Login avec charte EtapSup
import PasswordInput from '@/Components/PasswordInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { BContainer, BRow, BCol, BFormGroup, BFormInput, BFormCheckbox, BFormInvalidFeedback, BButton } from 'bootstrap-vue-next';

defineOptions({ layout: GuestLayout });

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const redirectUrl = computed(() => {
    return new URL(window.location.href).searchParams.get('redirect') || '';
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
    redirect: redirectUrl.value,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <Head title="Connexion - EtapSup" />

    <div class="login-page">
        <BContainer class="py-5">
            <BRow class="justify-content-center min-vh-80 align-items-center">
                <BCol lg="5" md="8">
                    <div class="login-card">
                        <!-- Logo & Title -->
                        <div class="text-center mb-4">
                            <Link href="/" class="logo-link">
                                <h2 class="logo-text">EtapSup</h2>
                            </Link>
                            <h1 class="login-title">Bienvenue !</h1>
                            <p class="login-subtitle">
                                Connectez-vous pour accéder à votre espace
                            </p>
                        </div>

                        <!-- Status Message -->
                        <div v-if="status" class="alert alert-success mb-4">
                            {{ status }}
                        </div>

                        <!-- Login Form -->
                        <form @submit.prevent="submit" class="login-form">
                            <!-- Email -->
                            <BFormGroup label="Adresse email" label-for="email" class="mb-3">
                                <BFormInput
                                    v-model="form.email"
                                    :state="form.errors.email ? false : null"
                                    type="email"
                                    id="email"
                                    placeholder="votre@email.com"
                                    required
                                    :disabled="form.processing"
                                    class="form-input"
                                />
                                <BFormInvalidFeedback v-if="form.errors.email">
                                    {{ form.errors.email }}
                                </BFormInvalidFeedback>
                            </BFormGroup>

                            <!-- Password -->
                            <BFormGroup label="Mot de passe" label-for="password" class="mb-3">
                                <PasswordInput
                                    v-model="form.password"
                                    :error="form.errors.password"
                                    :state="form.errors.password ? false : null"
                                    label=""
                                    name="password"
                                    place-holder="••••••••"
                                    id="password"
                                    required
                                    :disabled="form.processing"
                                />
                            </BFormGroup>

                            <!-- Remember & Forgot -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <BFormCheckbox v-model="form.remember" :disabled="form.processing">
                                    Se souvenir de moi
                                </BFormCheckbox>
                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="forgot-link"
                                >
                                    Mot de passe oublié ?
                                </Link>
                            </div>

                            <!-- Submit Button -->
                            <BButton
                                type="submit"
                                variant="primary"
                                size="lg"
                                class="w-100 submit-button"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Connexion...</span>
                                <span v-else>Se connecter</span>
                            </BButton>
                        </form>

                        <!-- Register Link -->
                        <div class="register-link text-center mt-4">
                            <p class="mb-0">
                                Vous n'avez pas de compte ?
                                <Link :href="route('register')" class="link-primary">
                                    Créer un compte
                                </Link>
                            </p>
                        </div>

                        <!-- Back to Home -->
                        <div class="text-center mt-4">
                            <Link href="/" class="back-link">
                                ← Retour à l'accueil
                            </Link>
                        </div>
                    </div>
                </BCol>
            </BRow>
        </BContainer>

        <!-- Footer Copyright -->
        <div class="login-footer text-center py-4">
            <p class="mb-0 text-muted">
                &copy; 2025 EtapSup. Tous droits réservés.
            </p>
        </div>
    </div>
</template>

<style scoped>
.login-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    display: flex;
    flex-direction: column;
}

.min-vh-80 {
    min-height: 80vh;
}

.login-card {
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

.login-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1a202c;
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
}

.login-subtitle {
    color: #64748b;
    font-size: 1rem;
    margin-bottom: 0;
}

.login-form {
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

.forgot-link {
    color: #1e3a8a;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: color 0.2s ease;
}

.forgot-link:hover {
    color: #dc2626;
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

.register-link p {
    color: #64748b;
    font-size: 0.95rem;
}

.link-primary {
    color: #1e3a8a;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s ease;
}

.link-primary:hover {
    color: #dc2626;
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

.login-footer {
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
    .login-card {
        padding: 2rem 1.5rem;
    }

    .logo-text {
        font-size: 2rem;
    }

    .login-title {
        font-size: 1.5rem;
    }
}
</style>
