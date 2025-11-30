<script setup lang="ts">
import AppHead from '@/Components/AppHead.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Country, User } from '@/Types/index';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import PasswordForm from "@/Pages/Dashboard/Profile/Partials/PasswordForm.vue";
import {useToast} from "primevue/usetoast";

defineOptions({ layout: DashboardLayout });

const props = defineProps<{
    user: User;
    countries?: Country[];
}>();
const size = ref<'small' | 'large' | undefined>(undefined);

interface FormData {
    surname: string;
    name: string;
    email: string;
    phone: string;
    place_birth: string;
    date_birth: Date | null;
    nationality: string;
    passport_number: string;
    country_id?: string;
}

const form = useForm<FormData>({
    surname: props.user.surname,
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone,
    place_birth: props.user.place_birth,
    date_birth: props.user.date_birth ? new Date(props.user.date_birth) : null,
    nationality: props.user.nationality,
    passport_number: props.user.passport_number,
    country_id: props.user.country?.id,
});
const toast = useToast();
const submit = () => {
    form.patch(route('dashboard.profile.update', {profile: props.user.id}), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Mise à jour', detail: 'Vos modifications ont bien été enregistrer', life: 3000 });
        },
        onError: (errors) => {

        }
    });
};
</script>

<template>
    <AppHead title="Mon profil" />

    <b-card no-body class="border-0 shadow-etatsup">
        <b-card-header class="header-etatsup">
            <h4 class="card-header-title text-white mb-0">Informations personnelles</h4>
        </b-card-header>

        <b-card-body>
            <b-form @submit.prevent="submit" class="row g-4 px-3">
                <b-col md="6">
                    <div class="d-flex flex-column">
                        <label for="surname">Nom *</label>
                        <input-text
                            id="surname"
                            v-model="form.surname"
                            :invalid="Boolean(form.errors.surname)"
                            :disabled="form.processing"
                            name="surname"
                            :size="size"
                        />
                        <Message
                            v-if="form.errors.surname"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ form.errors.surname }}
                        </Message>
                    </div>
                </b-col>
                <b-col md="6">
                    <div class="d-flex flex-column">
                        <label for="name">Prénom *</label>
                        <input-text
                            id="name"
                            v-model="form.name"
                            :invalid="Boolean(form.errors.name)"
                            :disabled="form.processing"
                            name="name"
                            :size="size"
                        />
                        <Message
                            v-if="form.errors.name"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ form.errors.name }}
                        </Message>
                    </div>
                </b-col>
                <b-col md="6">
                    <div class="d-flex flex-column">
                        <label for="email">Adresse email</label>
                        <input-text
                            id="email"
                            :model-value="user.email"
                            :disabled="form.processing"
                            readonly
                            :size="size"
                        />
                    </div>
                </b-col>
                <b-col md="6">
                    <div class="d-flex flex-column">
                        <label for="phone">Numéro de téléphone *</label>
                        <input-text
                            id="phone"
                            v-model="form.phone"
                            :invalid="Boolean(form.errors.phone)"
                            :disabled="form.processing"
                            name="phone"
                            :size="size"
                        />
                        <Message
                            v-if="form.errors.phone"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ form.errors.phone }}
                        </Message>
                    </div>
                </b-col>
                <b-col md="6">
                    <div class="d-flex flex-column">
                        <label for="passport_number">N° de passport *</label>
                        <input-text
                            id="passport_number"
                            v-model="form.passport_number"
                            :invalid="Boolean(form.errors.passport_number)"
                            :disabled="form.processing"
                            name="passport_number"
                            :size="size"
                        />
                        <Message
                            v-if="form.errors.passport_number"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ form.errors.passport_number }}
                        </Message>
                    </div>
                </b-col>
                <b-col md="6">
                    <div class="d-flex flex-column">
                        <label for="nationality">Nationalité *</label>
                        <input-text
                            id="nationality"
                            v-model="form.nationality"
                            :invalid="Boolean(form.errors.nationality)"
                            :disabled="form.processing"
                            name="nationality"
                            :size="size"
                        />
                        <Message
                            v-if="form.errors.nationality"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ form.errors.nationality }}
                        </Message>
                    </div>
                </b-col>

                <b-col md="4">
                    <div class="d-flex flex-column">
                        <label for="date_birth">Date de naissance *</label>
                        <Calendar
                            input-id="date_birth"
                            v-model="form.date_birth"
                            :disabled="form.processing"
                            :invalid="Boolean(form.errors.date_birth)"
                            :size="size"
                            dateFormat="dd/mm/yy"
                            showIcon
                            iconDisplay="input"
                        />
                        <Message
                            v-if="form.errors.date_birth"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ form.errors.date_birth }}
                        </Message>
                    </div>
                </b-col>
                <b-col md="4">
                    <div class="d-flex flex-column">
                        <label for="place_birth">Lieu de naissance *</label>
                        <input-text
                            id="place_birth"
                            v-model="form.place_birth"
                            :invalid="Boolean(form.errors.place_birth)"
                            :disabled="form.processing"
                            name="place_birth"
                            :size="size"
                        />
                        <Message
                            v-if="form.errors.place_birth"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ form.errors.place_birth }}
                        </Message>
                    </div>
                </b-col>
                <b-col md="4">
                    <div class="d-flex flex-column">
                        <label for="country_id">Pays de naissance *</label>
                        <Select
                            input-id="country_id"
                            v-model="form.country_id"
                            :options="countries"
                            :disabled="form.processing"
                            :invalid="Boolean(form.errors.country_id)"
                            :size="size"
                            filter
                            option-label="name"
                            option-value="id"
                        />
                        <Message
                            v-if="form.errors.country_id"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ form.errors.country_id }}
                        </Message>
                    </div>
                </b-col>

                <b-col cols="12" class="mt-5 text-end">
                    <b-button
                        variant="primary"
                        type="submit"
                        class="btn-etapsup"
                        :loading="form.processing"
                        :disabled="form.processing || !form.isDirty"
                        >Enregistrer les modifications
                    </b-button>
                </b-col>
            </b-form>
        </b-card-body>
    </b-card>

    <password-form class="mt-4"/>
</template>

<style scoped>
/* Charte EtatSup - Perfect Pixel Purple Gradient */
.header-etatsup {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    border: none;
    border-radius: 12px 12px 0 0;
    padding: 1.25rem 1.5rem;
}

.shadow-etatsup {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    overflow: hidden;
}

.btn-etapsup {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%) !important;
    border: none !important;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border-radius: 50px;
}

.btn-etapsup:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.btn-etapsup:disabled {
    opacity: 0.6;
}

/* Styling des cartes */
:deep(.card) {
    border-radius: 1rem;
    border: none;
}

:deep(.card-header) {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    color: white;
    border-radius: 1rem 1rem 0 0 !important;
    padding: 1.25rem 1.5rem;
}

:deep(.card-header-title) {
    color: white;
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0;
}

/* Inputs focus */
:deep(.p-inputtext:focus),
:deep(.p-calendar input:focus),
:deep(.p-dropdown:focus) {
    border-color: #1e3a8a !important;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
}

/* Labels */
:deep(label) {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}
</style>
