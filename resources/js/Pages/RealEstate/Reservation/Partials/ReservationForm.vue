<script setup lang="ts">
import { Property, User } from '@/Types/index';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { loadStripe, Stripe, StripeCardElement } from '@stripe/stripe-js';
import { useVuelidate } from '@vuelidate/core';
import { email, maxLength, minLength, required } from '@vuelidate/validators';
import axios from 'axios';
import { BIconPeopleFill, BIconWalletFill } from 'bootstrap-icons-vue';
import dayjs from 'dayjs';
import timezone from 'dayjs/plugin/timezone';
import utc from 'dayjs/plugin/utc';
import { computed, onMounted, ref, watch } from 'vue';

dayjs.extend(utc);
dayjs.extend(timezone);

const page = usePage();
const user = page.props.auth?.user as User;
const property = page.props.property as Property;
const size = ref<'small' | 'large' | undefined>(undefined);
const errorMessage = ref<null | string>(null);

interface PropsPage {
    period: { start: Date | null; end: Date | null };
    guests: number;
    amount: number;
    fees: any;
}

const props = defineProps<PropsPage>();

const stripeKey = page.props.stripeKey as string;
const intent = ref(page.props.intent as string);
const cardElement = ref<HTMLElement | null>(null);
const stripe = ref<Stripe | null>(null);
const card = ref<StripeCardElement | null>(null);
const processing = ref(false);

interface FormData {
    property_id: string;
    surname: string;
    name: string;
    email: string;
    phone: string;
    place_birth: string;
    date_birth: Date | undefined;
    nationality: string;
    guests: number;
    status: string;
    address: string;
    period: { start: Date | null; end: Date | null };
    reason: string;
    amount: number;
    fees: any;
}

const form = useForm<FormData>({
    property_id: property.id,
    surname: user?.surname,
    name: user?.name,
    email: user?.email,
    phone: user?.phone,
    place_birth: user?.place_birth,
    date_birth: user?.date_birth
        ? dayjs(user?.date_birth).utc().toDate()
        : undefined,
    nationality: user?.nationality,
    status: '',
    address: '',
    reason: '',
    guests: props.guests,
    period: props.period,
    fees: props.fees,
    amount: props.amount,
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
    guests: { required },
    period: {
        required,
    },
    reason: { required },
    address: { required, minLength: minLength(3) },
};
const v$ = useVuelidate(rules, form);

// Error display disabled to avoid type instantiation issues

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

const isDateBlocked = (date: Date) => {
    return blockedDates.value.some(
        (blockedDate) =>
            dayjs(blockedDate).format('YYYY-MM-DD') ===
            dayjs(date).format('YYYY-MM-DD'),
    );
};

onMounted(async () => {
    if (user) {
        stripe.value = await loadStripe(stripeKey);
        if (!stripe.value) return;
        const elements = stripe.value.elements();

        card.value = elements.create('card', {
            hidePostalCode: true, // Cela va masquer le champ ZIP/Code postal
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    '::placeholder': {
                        color: '#aab7c4',
                    },
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a',
                },
            },
        });
        if (card.value && cardElement.value) {
            card.value.mount(cardElement.value);
        }
    }
});

const refreshSetupIntent = async () => {
    try {
        const response = await axios.get(
            route('certificate.stripe.refresh-intent'),
        );
        console.log(response.data.intent);
        intent.value = response.data.intent;
    } catch (error) {
        console.error('Erreur lors du rafraîchissement du setupIntent:', error);
    }
};

const onSubmit = async () => {
    errorMessage.value = null;

    try {
        const result = await v$.value.$validate();
        if (!result) {
            return;
        }

        processing.value = true;

        if (!stripe.value || !card.value) {
            throw new Error('Stripe not initialized');
        }

        const { setupIntent, error } = await stripe.value.confirmCardSetup(
            intent.value,
            {
                payment_method: {
                    card: card.value,
                    billing_details: {
                        name: `${form.surname} ${form.name}`,
                        email: form.email,
                    },
                },
            },
        );

        if (error) {
            errorMessage.value = error.message || 'An error occurred';
            throw new Error(error.message);
        }

        form.transform((data) => ({
            ...data,
            date_birth: dayjs(data.date_birth).format('YYYY-MM-DD'),
            period: data.period
                ? [
                      dayjs(data.period.start).format('YYYY-MM-DD'),
                      dayjs(data.period.end).format('YYYY-MM-DD'),
                  ]
                : undefined,
            payment_method_id: setupIntent.payment_method,
        })).post(route('reservations.store'), {
            onSuccess: (response) => {},
            onError: async (errors) => {
                await refreshSetupIntent();
            },
            onFinish: () => {
                processing.value = false;
            },
        });
    } catch (error) {
        // console.error(error);
        processing.value = false;
    }
};

watch(
    () => props.period,
    (newVal) => {
        form.period = newVal;
    },
    { deep: true },
);

watch(
    () => props.guests,
    (newVal) => {
        form.guests = newVal;
    },
);
watch(
    () => props.amount,
    (newValue) => {
        form.amount = newValue;
    },
);
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
                    <Link
                        :href="route('login', { redirect: page.url })"
                        class="alert-heading h6"
                        >Se connecter
                    </Link>
                    pour remplir tous les détails et avoir accès ala réservation
                </div>
                <div class="row g-4">
                    <b-col md="6">
                        <div class="d-flex flex-column">
                            <label for="surname">Nom *</label>
                            <input-text
                                id="surname"
                                v-model="form.surname"
                                @blur="v$.surname.$touch()"
                                :invalid="v$.surname.$error"
                                :disabled="form.processing || !user"
                                name="surname"
                                :size="size"
                                minLength="3"
                            />
                            <Message
                                v-if="false"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                <!-- Error display disabled -->
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="6">
                        <div class="d-flex flex-column">
                            <label for="name">Prénom *</label>
                            <input-text
                                id="name"
                                v-model="form.name"
                                @blur="v$.name.$touch()"
                                :invalid="v$.name.$error"
                                :disabled="form.processing || !user"
                                name="name"
                                :size="size"
                                minLength="3"
                            />
                            <Message
                                v-if="false"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                <!-- Error:name -->
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="4">
                        <div class="d-flex flex-column">
                            <label for="phone">Téléphone *</label>
                            <input-text
                                id="phone"
                                v-model="form.phone"
                                @blur="v$.phone.$touch()"
                                :invalid="v$.phone.$error"
                                :disabled="form.processing || !user"
                                name="phone"
                                :size="size"
                                minLength="3"
                            />
                            <Message
                                v-if="false"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                <!-- Error:phone -->
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="4">
                        <div class="d-flex flex-column">
                            <label for="date_birth">Date de naissance *</label>
                            <DatePicker
                                input-id="date_birth"
                                v-model="form.date_birth"
                                @blur="v$.date_birth.$touch()"
                                :disabled="form.processing || !user"
                                :invalid="v$.date_birth.$error"
                                :size="size"
                                dateFormat="dd/mm/yy"
                                showIcon
                                iconDisplay="input"
                            />
                            <Message
                                v-if="false"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                <!-- Error:date_birth -->
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="4">
                        <div class="d-flex flex-column">
                            <label for="place_birth">Lieu de naissance *</label>
                            <input-text
                                id="place_birth"
                                v-model="form.place_birth"
                                @blur="v$.place_birth.$touch()"
                                :invalid="v$.place_birth.$error"
                                :disabled="form.processing || !user"
                                name="place_birth"
                                :size="size"
                                minLength="3"
                            />
                            <Message
                                v-if="false"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                <!-- Error:place_birth -->
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="4">
                        <div class="d-flex flex-column">
                            <label for="nationality">Nationalité *</label>
                            <input-text
                                id="nationality"
                                v-model="form.nationality"
                                @blur="v$.nationality.$touch()"
                                :invalid="v$.nationality.$error"
                                :disabled="form.processing || !user"
                                name="nationality"
                                :size="size"
                                minLength="3"
                            />
                            <Message
                                v-if="false"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                <!-- Error:nationality -->
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="4">
                        <div class="d-flex flex-column">
                            <label for="status">Statut (A/E) *</label>
                            <input-text
                                id="status"
                                v-model="form.status"
                                :disabled="form.processing || !user"
                                @blur="v$.status.$touch()"
                                :invalid="v$.status.$error"
                                name="status"
                                :size="size"
                                minLength="3"
                            />
                            <Message
                                v-if="false"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                <!-- Error:status -->
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="4">
                        <div class="d-flex flex-column">
                            <label for="reason">Motif de réservation *</label>
                            <Select
                                input-id="reason"
                                v-model="form.reason"
                                @blur="v$.reason.$touch()"
                                :options="reasons"
                                :disabled="form.processing || !user"
                                :invalid="v$.reason.$error"
                                :size="size"
                                option-label="name"
                                option-value="id"
                                placeholder="Motif de réservation"
                            />
                            <Message
                                v-if="false"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                <!-- Error:reason -->
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="12">
                        <div class="d-flex flex-column">
                            <label for="address">Adresse de résidence *</label>
                            <TextArea
                                id="address"
                                v-model="form.address"
                                @blur="v$.address.$touch()"
                                :disabled="form.processing || !user"
                                :invalid="v$.address.$error"
                                name="address"
                                :size="size"
                                minLength="3"
                            />
                            <Message
                                v-if="false"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                <!-- Error:address -->
                            </Message>
                        </div>
                    </b-col>
                </div>
            </b-card-body>
        </b-card>

        <BAlert
            :model-value="true"
            variant="danger"
            class="mt-2"
            v-if="false"
        >
            <!-- Error:period -->
        </BAlert>
        <b-card v-if="user" no-body class="mt-4 shadow">
            <b-card-header class="border-bottom p-3 pb-2">
                <b-card-title class="mb-0" tag="h5">
                    <BIconWalletFill class="mb-2 me-1" />
                    Paiement
                </b-card-title>
            </b-card-header>

            <b-card-body class="p-4 pb-0">
                <div class="d-sm-flex justify-content-sm-between my-3">
                    <h6 class="mb-sm-0 mb-2">Nous acceptons :</h6>
                    <ul class="list-inline d-flex my-0 gap-1">
                        <li class="list-inline-item">
                            <a href="#">
                                <img
                                    src="/images/front/element/visa.svg"
                                    class="h-30px"
                                    alt=""
                                />
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#">
                                <img
                                    src="/images/front/element/mastercard.svg"
                                    class="h-30px"
                                    alt=""
                                />
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#">
                                <img
                                    src="/images/front/element/expresscard.svg"
                                    class="h-30px"
                                    alt=""
                                />
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="my-3 mt-3">
                    <div ref="cardElement" />
                </div>
                <Message v-if="errorMessage" severity="error">
                    {{ errorMessage }}
                </Message>

                <b-col cols="12 mt-5">
                    <div
                        class="d-sm-flex justify-content-sm-between align-items-center"
                    >
                        <h4>
                            {{
                                new Intl.NumberFormat('fr-FR', {
                                    style: 'currency',
                                    currency: 'EUR',
                                }).format(form.amount)
                            }}
                            <span class="small fs-6">A Payer</span>
                        </h4>
                        <b-button
                            type="submit"
                            variant="primary"
                            class="mb-0"
                            :loading="form.processing || processing"
                            :disabled="processing || v$.$invalid"
                            >Payer & Réserver
                        </b-button>
                    </div>
                </b-col>
            </b-card-body>

            <div class="card-footer p-4">
                <p class="small mb-0 text-center">
                    En procédant au traitement, vous acceptez les
                    <a href="#">conditions de service</a> et la
                    <a href="#">politique de réservation</a>
                </p>
            </div>
        </b-card>
    </b-form>
</template>

<style scoped>
.StripeElement {
    box-sizing: border-box;
    height: 40px;
    padding: 10px 12px;
    border: 1px solid transparent;
    border-radius: 4px;
    background-color: white;
    box-shadow: 0 1px 3px 0 #e6ebf1;
    -webkit-transition: box-shadow 150ms ease;
    transition: box-shadow 150ms ease;
}
</style>
