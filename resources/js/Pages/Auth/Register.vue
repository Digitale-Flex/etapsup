<script setup lang="ts">
// Refonte: Page Register avec charte EtapSup
import PasswordInput from '@/Components/PasswordInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { BContainer, BRow, BCol, BFormGroup, BFormInput, BFormCheckbox, BFormInvalidFeedback, BButton } from 'bootstrap-vue-next';

defineOptions({ layout: GuestLayout });

const form = useForm({
    surname: '',
    name: '',
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <Head title="Inscription - EtapSup" />

    <div class="register-page">
        <BContainer class="py-5">
            <BRow class="justify-content-center min-vh-80 align-items-center">
                <BCol lg="6" md="10">
                    <div class="register-card">
                        <!-- Logo & Title -->
                        <div class="text-center mb-4">
                            <Link href="/" class="logo-link">
                                <h2 class="logo-text">EtapSup</h2>
                            </Link>
                            <h1 class="register-title">Créer un compte</h1>
                            <p class="register-subtitle">
                                Rejoignez EtapSup et réalisez votre rêve d'étudier à l'étranger
                            </p>
                        </div>

                        <!-- Register Form -->
                        <form @submit.prevent="submit" class="register-form">
                            <BRow>
                                <!-- Nom -->
                                <BCol md="6">
                                    <BFormGroup label="Nom" label-for="surname" class="mb-3">
                                        <BFormInput
                                            v-model="form.surname"
                                            :state="form.errors.surname ? false : null"
                                            id="surname"
                                            placeholder="Votre nom"
                                            autocomplete="family-name"
                                            required
                                            minlength="3"
                                            :disabled="form.processing"
                                            class="form-input"
                                        />
                                        <BFormInvalidFeedback v-if="form.errors.surname">
                                            {{ form.errors.surname }}
                                        </BFormInvalidFeedback>
                                    </BFormGroup>
                                </BCol>

                                <!-- Prénom -->
                                <BCol md="6">
                                    <BFormGroup label="Prénom" label-for="name" class="mb-3">
                                        <BFormInput
                                            v-model="form.name"
                                            :state="form.errors.name ? false : null"
                                            id="name"
                                            placeholder="Votre prénom"
                                            autocomplete="given-name"
                                            required
                                            minlength="3"
                                            :disabled="form.processing"
                                            class="form-input"
                                        />
                                        <BFormInvalidFeedback v-if="form.errors.name">
                                            {{ form.errors.name }}
                                        </BFormInvalidFeedback>
                                    </BFormGroup>
                                </BCol>
                            </BRow>

                            <!-- Email -->
                            <BFormGroup label="Adresse email" label-for="email" class="mb-3">
                                <BFormInput
                                    v-model="form.email"
                                    :state="form.errors.email ? false : null"
                                    type="email"
                                    id="email"
                                    placeholder="votre@email.com"
                                    autocomplete="email"
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
                                    place-holder="Minimum 8 caractères"
                                    id="password"
                                    autocomplete="new-password"
                                    required
                                    minLength="8"
                                    :disabled="form.processing"
                                />
                                <small class="text-muted">Le mot de passe doit contenir au moins 8 caractères</small>
                            </BFormGroup>

                            <!-- Remember -->
                            <div class="mb-4">
                                <BFormCheckbox v-model="form.remember" :disabled="form.processing">
                                    Rester connecté
                                </BFormCheckbox>
                            </div>

                            <!-- Submit Button -->
                            <BButton
                                type="submit"
                                variant="primary"
                                size="lg"
                                class="w-100 submit-button"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Création du compte...</span>
                                <span v-else>Créer mon compte</span>
                            </BButton>
                        </form>

                        <!-- Login Link -->
                        <div class="login-link text-center mt-4">
                            <p class="mb-0">
                                Vous avez déjà un compte ?
                                <Link :href="route('login')" class="link-primary">
                                    Se connecter
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
        <div class="register-footer text-center py-4">
            <p class="mb-0 text-muted">
                &copy; 2025 EtapSup. Tous droits réservés.
            </p>
        </div>
    </div>
</template>

<style scoped>
.register-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    display: flex;
    flex-direction: column;
}

.min-vh-80 {
    min-height: 80vh;
}

.register-card {
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

.register-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1a202c;
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
}

.register-subtitle {
    color: #64748b;
    font-size: 1rem;
    margin-bottom: 0;
}

.register-form {
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

.login-link p {
    color: #64748b;
    font-size: 0.95rem;
}

.link-primary {
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s ease;
}

.link-primary:hover {
    color: #ed2939;
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

.register-footer {
    margin-top: auto;
}

@media (max-width: 768px) {
    .register-card {
        padding: 2rem 1.5rem;
    }

    .logo-text {
        font-size: 2rem;
    }

    .register-title {
        font-size: 1.5rem;
    }
}
</style>
