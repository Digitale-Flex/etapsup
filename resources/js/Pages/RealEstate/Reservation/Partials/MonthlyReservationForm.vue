<script setup lang="ts">
import FormError from '@/Components/FormError.vue';
import { Property, User } from '@/Types/index';
import { Link, usePage } from '@inertiajs/vue3';
import { useVuelidate } from '@vuelidate/core';
import { email, maxLength, minLength, required } from '@vuelidate/validators';
import { BIconFilePdf, BIconPeopleFill } from 'bootstrap-icons-vue';
import dayjs from 'dayjs';
import timezone from 'dayjs/plugin/timezone';
import utc from 'dayjs/plugin/utc';
import { computed, ref } from 'vue';

dayjs.extend(utc);
dayjs.extend(timezone);

interface Props {
    form: any;
}

const props = defineProps<Props>();
const page = usePage();
const user = page.props.auth?.user as User;
const property = page.props.property as Property;
const processing = ref(false);
const pondRef = ref(null);

const errors = computed(() => page.props.errors);

const reasons = ref([
    { name: 'Etudes', id: 'Etudes' },
    { name: 'Personnel', id: 'Personnel' },
    { name: 'Professionnel', id: 'Professionnel' },
    { name: 'Tourisme', id: 'Tourisme' },
]);

const blockedDates = computed(() => {
    const dates = page.props.blockedDates as Array<{
        start: string;
        end: string;
    }>;
    const disabledDates: Date[] = [];
    dates.forEach((period) => {
        let currentDate = dayjs(period.start);
        const endDate = dayjs(period.end);
        while (
            currentDate.isBefore(endDate) ||
            currentDate.isSame(endDate, 'day')
        ) {
            disabledDates.push(currentDate.toDate());
            currentDate = currentDate.add(1, 'day');
        }
    });
    return disabledDates;
});

const rules = {
    surname: { required, minLength: minLength(3), maxLength: maxLength(100) },
    name: { required, minLength: minLength(3), maxLength: maxLength(100) },
    email: { required, email },
    status: { required, minLength: minLength(2) },
    phone: { required, minLength: minLength(8) },
    place_birth: { required, minLength: minLength(3) },
    date_birth: { required },
    nationality: { required, minLength: minLength(4) },
    month: { required },
    period: { required },
    reason: { required },
    address: { required, minLength: minLength(3) },
};

const v$ = useVuelidate(rules, props.form);

const handleFileProcessed = () => {};

const onSubmit = async () => {
    const isValid = await v$.value.$validate();
    if (!isValid) return;

    const files = (pondRef.value as any)?.getFiles()?.map((file: any) => file.file) || [];
    props.form.files = files;

    props.form
        .transform((data: any) => ({
            ...data,
            date_birth: dayjs(data.date_birth).format('YYYY-MM-DD'),
            period: [
                dayjs(data.period.start).format('YYYY-MM-DD'),
                dayjs(data.period.end).format('YYYY-MM-DD'),
            ],
        }))
        .post(route('monthlies.store'), {
            preserveScrollX: true,
            forceFormData: true,
            onSuccess: () => {
                // pondRef.value?.removeFiles();
                // props.form.reset('files');
            },
        });
};
</script>

<template>
    <b-form @submit.prevent="onSubmit">
        <b-card no-body class="shadow">
            <b-card-header class="border-bottom p-4">
                <b-card-title class="mb-0" tag="h5">
                    <BIconPeopleFill class="mb-1" />
                    Formulaire de réservation
                </b-card-title>
            </b-card-header>

            <b-card-body class="p-4">
                <div v-if="!user" class="alert alert-info mb-4" role="alert">
                    <Link :href="route('login', { redirect: page.url })" class="alert-heading h6"
                        >Se connecter</Link
                    >
                    pour remplir tous les détails et avoir accès ala réservation
                </div>
                <div class="row g-4">
                    <b-col md="6">
                        <div class="d-flex flex-column">
                            <label for="surname">Nom *</label>
                            <input-text
                                id="surname"
                                v-model="props.form.surname"
                                @blur="v$.surname.$touch()"
                                :invalid="v$.surname.$error"
                                :disabled="form.processing || !user"
                                name="surname"
                                minLength="3"
                            />
                            <Message
                                v-if="v$.surname.$error"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ v$.surname.$errors[0]?.$message }}
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="6">
                        <div class="d-flex flex-column">
                            <label for="name">Prénom *</label>
                            <input-text
                                id="name"
                                v-model="props.form.name"
                                @blur="v$.name.$touch()"
                                :invalid="v$.name.$error"
                                :disabled="form.processing || !user"
                                name="name"
                                minLength="3"
                            />
                            <Message
                                v-if="v$.name.$error"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ v$.name.$errors[0]?.$message }}
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="4">
                        <div class="d-flex flex-column">
                            <label for="phone">Téléphone *</label>
                            <input-text
                                id="phone"
                                v-model="props.form.phone"
                                @blur="v$.phone.$touch()"
                                :invalid="v$.phone.$error"
                                :disabled="form.processing || !user"
                                name="phone"
                                minLength="3"
                            />
                            <Message
                                v-if="v$.phone.$error"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ v$.phone.$errors[0]?.$message }}
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="4">
                        <div class="d-flex flex-column">
                            <label for="date_birth">Date de naissance *</label>
                            <DatePicker
                                input-id="date_birth"
                                v-model="props.form.date_birth"
                                @blur="v$.date_birth.$touch()"
                                :disabled="form.processing || !user"
                                :invalid="v$.date_birth.$error"
                                dateFormat="dd/mm/yy"
                                showIcon
                                iconDisplay="input"
                            />
                            <Message
                                v-if="v$.date_birth.$error"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ v$.date_birth.$errors[0]?.$message }}
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="4">
                        <div class="d-flex flex-column">
                            <label for="place_birth">Lieu de naissance *</label>
                            <input-text
                                id="place_birth"
                                v-model="props.form.place_birth"
                                @blur="v$.place_birth.$touch()"
                                :invalid="v$.place_birth.$error"
                                :disabled="form.processing || !user"
                                name="place_birth"
                                minLength="3"
                            />
                            <Message
                                v-if="v$.place_birth.$error"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ v$.place_birth.$errors[0]?.$message }}
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="4">
                        <div class="d-flex flex-column">
                            <label for="nationality">Nationalité *</label>
                            <input-text
                                id="nationality"
                                v-model="props.form.nationality"
                                @blur="v$.nationality.$touch()"
                                :invalid="v$.nationality.$error"
                                :disabled="form.processing || !user"
                                name="nationality"
                                minLength="3"
                            />
                            <Message
                                v-if="v$.nationality.$error"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ v$.nationality.$errors[0]?.$message }}
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="4">
                        <div class="d-flex flex-column">
                            <label for="status">Statut (A/E) *</label>
                            <input-text
                                id="status"
                                v-model="props.form.status"
                                :disabled="form.processing || !user"
                                @blur="v$.status.$touch()"
                                :invalid="v$.status.$error"
                                name="status"
                                minLength="3"
                            />
                            <Message
                                v-if="v$.status.$error"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ v$.status.$errors[0]?.$message }}
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="4">
                        <div class="d-flex flex-column">
                            <label for="reason">Motif de réservation *</label>
                            <Select
                                input-id="reason"
                                v-model="props.form.reason"
                                @blur="v$.reason.$touch()"
                                :options="reasons"
                                :disabled="form.processing || !user"
                                :invalid="v$.reason.$error"
                                option-label="name"
                                option-value="id"
                                placeholder="Motif de réservation"
                            />
                            <Message
                                v-if="v$.reason.$error"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ v$.reason.$errors[0]?.$message }}
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="12">
                        <div class="d-flex flex-column">
                            <label for="address">Adresse de résidence *</label>
                            <TextArea
                                id="address"
                                v-model="props.form.address"
                                @blur="v$.address.$touch()"
                                :disabled="form.processing || !user"
                                :invalid="v$.address.$error"
                                name="address"
                                minLength="3"
                            />
                            <Message
                                v-if="v$.address.$error"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ v$.address.$errors[0]?.$message }}
                            </Message>
                        </div>
                    </b-col>
                </div>
            </b-card-body>
        </b-card>

        <b-card no-body class="mt-3 shadow" v-show="props.form.requireFiles">
            <b-card-header class="border-bottom p-4">
                <b-card-title class="mb-0" tag="h5">
                    <BIconFilePdf class="mb-1" />
                    informations supplémentaires
                </b-card-title>
            </b-card-header>

            <b-card-body class="p-4">
                <div class="alert alert-info mb-4 text-sm" role="alert">
                    <p class="alert-heading h6">Justificatifs requis</p>
                    Pièce d’identité + Attestation d’admission + Justificatif
                    des ressources : revenu salarial ou solde bancaire, AVI
                </div>
                <file-pond
                    ref="pondRef"
                    max-total-file-size="10MB"
                    name="files"
                    label-max-total-file-size-exceeded="Taille totale maximale dépassée"
                    label-max-total-file-size="La taille totale maximale du fichier est de {filesize}"
                    allow-file-encode="true"
                    accepted-file-types="application/pdf,image/png,image/jpeg,image/jpg"
                    label-idle="Glissez & déposez vos justificatis ou <span class='filepond--label-action'>Parcourir</span>"
                    credits=""
                    allow-multiple
                    @processfile="handleFileProcessed"
                />
                <Message
                    v-if="form.errors.files"
                    severity="error"
                    size="small"
                    variant="simple"
                >
                    {{ form.errors.files }}
                </Message>
            </b-card-body>
        </b-card>

        <FormError :errors="errors" />

        <b-card v-if="user" no-body class="mt-4 shadow">
            <b-card-body class="d-flex justify-content-center p-4 pb-0">
                <b-button
                    type="submit"
                    variant="primary"
                    class="mb-0"
                    :loading="form.processing || processing"
                    :disabled="processing || v$.$invalid"
                >
                    Réserver maintenant
                </b-button>
            </b-card-body>
            <div class="card-footer">
                <p class="small mb-0 text-center">
                    En procédant au traitement, vous acceptez les
                    <a href="#">conditions de service</a> et la
                    <a href="#">politique de réservation</a>
                </p>
            </div>
        </b-card>
    </b-form>
</template>
