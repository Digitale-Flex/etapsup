<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';

const toast = useToast();
const form = useForm({
    current: '',
    password: '',
    password_confirmation: '',
});

const size = ref<'small' | 'large' | undefined>(undefined);
const submit = () => {
    form.post(route('dashboard.profile.password'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Mise à jour', detail: 'Votre mot de passe a bien été modifier', life: 3000 });
            form.reset();
        },
        onError: (errors) => {},
    });
};

</script>

<template>
    <b-card no-body class="mt-4 border">
        <b-card-header class="border-bottom">
            <h4 class="card-header-title">Mettre à jour le mot de passe</h4>
        </b-card-header>

        <b-card-body>
            <b-form @submit.prevent="submit" class="row g-4 px-3">
                <b-col md="12">
                    <div class="d-flex flex-column">
                        <label for="current">Mot de passe actuel *</label>
                        <Password
                            id="current"
                            v-model="form.current"
                            :invalid="Boolean(form.errors.current)"
                            :disabled="form.processing"
                            :size="size"
                            :feedback="false"
                            toggleMask
                        />
                        <Message
                            v-if="form.errors.current"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ form.errors.current }}
                        </Message>
                    </div>
                </b-col>
                <b-col md="12">
                    <div class="d-flex flex-column">
                        <label for="password">Nouveau mot de passe *</label>
                        <Password
                            id="password"
                            v-model="form.password"
                            :invalid="Boolean(form.errors.password)"
                            :disabled="form.processing"
                            :size="size"
                            :feedback="false"
                            toggleMask
                        />
                        <Message
                            v-if="form.errors.password"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ form.errors.password }}
                        </Message>
                    </div>
                </b-col>
                <b-col md="12">
                    <div class="d-flex flex-column">
                        <label for="confirmation"
                            >Confirmer le mot de passe *</label
                        >
                        <Password
                            id="confirmation"
                            v-model="form.password_confirmation"
                            :invalid="Boolean(form.errors.password_confirmation)"
                            :disabled="form.processing"
                            :size="size"
                            :feedback="false"
                            toggleMask
                        />
                        <Message
                            v-if="form.errors.password_confirmation"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ form.errors.password_confirmation }}
                        </Message>
                    </div>
                </b-col>

                <b-col cols="12" class="mt-5 text-end">
                    <b-button
                        variant="primary"
                        type="submit"
                        :loading="form.processing"
                        :disabled="form.processing || !form.isDirty"
                        >Changer le mot de passe
                    </b-button>
                </b-col>
            </b-form>
        </b-card-body>
    </b-card>
</template>

<style scoped></style>
