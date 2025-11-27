<script setup lang="ts">
import {
    City,
    Country,
    Genre,
    Partner,
    RentalDeposit,
    User,
} from '@/Types/index';
import {router, useForm, usePage } from '@inertiajs/vue3';
import { loadStripe, Stripe, StripeCardElement } from '@stripe/stripe-js';
import { useVuelidate } from '@vuelidate/core';
import { email, maxLength, minLength, required } from '@vuelidate/validators';
import axios from 'axios';
import { BIconPencil, BIconPerson } from 'bootstrap-icons-vue';
import { computed, onMounted, ref, toRefs } from 'vue';
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc';
import timezone from 'dayjs/plugin/timezone';

dayjs.extend(utc);
dayjs.extend(timezone);

interface Props {
    countries: Country[];
    partners: Partner[];
    cities: City[];
    genres: Genre[];
    rental_deposits: RentalDeposit[];
}

interface FormData {
    surname: string;
    name: string;
    email: string;
    phone: string;
    place_birth: string;
    date_birth: Date | undefined;
    nationality: string;
    passport_number: string;
    country_birth_id?: string;
    partner_id: string;
    coupon: string;
    is_coupon_valid: boolean;
    city_id: string;
    genre_id: string;
    rental_deposit_ids: string;
    country_id: string;
    budget: number | undefined;
    rental_start: Date | undefined;
    duration: number | undefined;
    further_information: string;
    solo: boolean;
}

defineProps<Props>();
const page = usePage();
const user = page.props.user as User;
const size = ref<'small' | 'large' | undefined>(undefined);
const errorMessage = ref<null | string>(null);

const form = useForm<FormData>({
    surname: user.surname,
    name: user.name,
    email: user.email,
    phone: user.phone,
    place_birth: user.place_birth,
    date_birth: user.date_birth ? dayjs(user.date_birth).utc().toDate() : undefined,
    nationality: user.nationality,
    passport_number: user.passport_number,
    country_birth_id: user.country?.id,
    partner_id: '',
    coupon: '',
    is_coupon_valid: false,
    city_id: '',
    genre_id: '',
    rental_deposit_ids: '',
    country_id: '',
    budget: undefined,
    rental_start: undefined,
    duration: undefined,
    further_information: '',
    solo: true,
});

const rules = {
    surname: { required, minLength: minLength(3), maxLength: maxLength(100) },
    name: { required, minLength: minLength(3), maxLength: maxLength(100) },
    phone: { required, minLength: minLength(8) },
    place_birth: { required, minLength: minLength(3) },
    date_birth: { required },
    nationality: { required, minLength: minLength(4) },
    passport_number: { required, minLength: minLength(5) },
    country_birth_id: { required },
    partner_id: {
        required: computed(() => !form.solo),
    },
    country_id: { required },
    city_id: { required },
    genre_id: { required },
    rental_deposit_ids: { required },
    budget: { required, minLength: minLength(3) },
    rental_start: { required },
    duration: { required },
    solo: {},
};
// Create a computed object that contains only the form data for validation
const formData = computed(() => ({
    surname: form.surname,
    name: form.name,
    phone: form.phone,
    place_birth: form.place_birth,
    date_birth: form.date_birth,
    nationality: form.nationality,
    passport_number: form.passport_number,
    country_birth_id: form.country_birth_id || '',
    partner_id: form.partner_id,
    country_id: form.country_id,
    city_id: form.city_id,
    genre_id: form.genre_id,
    rental_deposit_ids: form.rental_deposit_ids,
    budget: form.budget,
    rental_start: form.rental_start,
    duration: form.duration,
    solo: form.solo
}));

const v$ = useVuelidate(rules, formData);

const stripeKey = page.props.stripeKey as string;
const intent = ref(page.props.intent as string);
const cardElement = ref<HTMLElement | null>(null);
const stripe = ref<Stripe | null>(null);
const card = ref<StripeCardElement | null>(null);
const processing = ref(false);

onMounted(async () => {
    stripe.value = await loadStripe(stripeKey);
    if (!stripe.value) return;
    const elements = stripe.value.elements();
    card.value = elements.create('card', {
        hidePostalCode: true,
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
                '::placeholder': { color: '#aab7c4' }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        }
    });
    if (card.value && cardElement.value) {
        card.value.mount(cardElement.value);
    }
});

const refreshSetupIntent = async () => {
    try {
        const response = await axios.get(
            route('certificate.stripe.refresh-intent'),
        );
        intent.value = response.data.intent;
    } catch (error) {

    }
};



const submit = async () => {
    errorMessage.value = null;
    try {
        processing.value = true;
        // 1. Valider votre formulaire avec Vuelidate (ou autre). S’il n’est pas valide, on arrête.
        // if (!await v$.value.$validate()) return;

        // 2. Construire les données “payload” que vous envoyez au serveur pour créer le PaymentIntent
        //    On envoie TOUT le formulaire, exactement comme avant.
        const payload = {
            ...form,
            coupon_id: couponId.value,
            date_birth: dayjs(form.date_birth).format('YYYY-MM-DD'),
            rental_start: dayjs(form.rental_start).format('YYYY-MM-DD'),
            partner_id: form.solo ? undefined : form.partner_id,
            // (on n’envoie plus payment_method_id ici)
        };

        // 3. Appel au backend pour créer le PaymentIntent (et le modèle CertificateRequest)
        const createIntentResponse = await axios.post(route('certificate.pay.store'), payload);
        // On attend l’objet { client_secret, certificate_id } envoyé par /store

        const clientSecret  = createIntentResponse.data.client_secret as string;
        const certificateId = createIntentResponse.data.certificate_id as string;

        // 4. On appelle Stripe.js pour confirmer le PaymentIntent (et déclencher le 3D Secure)
        if (!stripe.value || !card.value) {
            throw new Error('Stripe not initialized');
        }

        const { error, paymentIntent } = await stripe.value.confirmCardPayment(clientSecret, {
            payment_method: {
                card: card.value,
                billing_details: {
                    name: `${form.surname} ${form.name}`,
                    email: form.email
                }
            }
        });

        if (error) {
            // Si l’utilisateur annule ou que l’authentification 3D échoue
            errorMessage.value = error.message!;
            return;
        }

        // 5. Si la confirmation Stripe.js renvoie “succeeded”
        if (paymentIntent!.status === 'succeeded') {
            // On appelle alors confirmPayment pour marquer en BDD / générer le certificat
            await axios.post(route('certificate.payment.confirm'), {
                certificate_id: certificateId
            });

            // 6. Puis on redirige vers la page de succès (ou vers la liste des certificats)
            router.visit(route('dashboard.certificates.show', certificateId));
            return;
        }

        // — En théorie, on ne passe jamais ici si status === 'succeeded'.
        //    Si Stripe.js renvoie “requires_action”, confirmCardPayment()
        //    a déjà refait le 3D Secure. Donc youpi, on ne devrait pas revenir ici.

    } catch (err: any) {
        console.error(err);
        errorMessage.value = err.response?.data?.message || 'Une erreur inattendue est survenue';
    } finally {
        processing.value = false;
    }
};


const isValidating = ref(false);
const couponError = ref('');
const couponSuccess = ref('');
const discountedAmount = ref('');
const couponId = ref<number | null>(null);
const onValidateCoupon = async () => {
    isValidating.value = true;
    couponError.value = '';
    couponSuccess.value = '';

    try {
        if (!form.solo && !form.partner_id) {
            couponError.value = 'Veuillez sélectionner un partenaire';
            return;
        }

        const response = await axios.post(route('coupon.validate'), {
            code: form.coupon,
            partner_id: form.partner_id,
        });

        if (response.data.valid) {
            couponId.value = response.data.id;
            discountedAmount.value = response.data.amount;
            form.is_coupon_valid = true;
            couponSuccess.value = `Coupon valide! Montant a payer: ${response.data.amount}€`;
        } else {
            couponError.value = response.data.message || 'Coupon invalide';
        }
    } catch (e: any) {
        form.is_coupon_valid = false;
        if (e.response?.status === 422) {
            couponError.value = e.response?.data?.message || 'Erreur de validation';
        } else {
            couponError.value = e.response?.data?.message || 'Erreur inconnue';
        }
    } finally {
        isValidating.value = false;
    }
};
</script>

<template>
    <b-form @submit.prevent="submit" class="vstack gap-5">
        <b-card no-body class="shadow">
            <b-card-header class="border-bottom p-4">
                <b-card-title class="mb-0 text-xl">
                    <BIconPerson class="mb-1" />
                    Informations personnelles
                </b-card-title>
            </b-card-header>
            <b-card-body class="p-4">
                <div class="row g-4">
                    <b-col md="6">
                        <div class="d-flex flex-column">
                            <label for="surname">Nom *</label>
                            <input-text
                                id="surname"
                                v-model="form.surname"
                                :invalid="v$.surname.$error"
                                :disabled="form.processing"
                                name="surname"
                                :size="size"
                                minLength="3"
                                @blur="v$.surname.$touch()"
                            />
                            <Message
                                v-if="v$.surname.$error"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                <span v-if="v$.surname.required.$invalid"
                                >Le nom est requis.</span
                                >
                                <span v-else-if="v$.surname.minLength.$invalid"
                                >Le nom doit contenir au moins 3
                                    caractères.</span
                                >
                                <span v-else-if="v$.surname.maxLength.$invalid"
                                >Le nom ne doit pas dépasser 100
                                    caractères.</span
                                >
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="6">
                        <div class="d-flex flex-column">
                            <label for="name">Prénom *</label>
                            <input-text
                                id="name"
                                v-model="form.name"
                                :invalid="v$.name.$error"
                                :disabled="form.processing"
                                @blur="v$.name.$touch()"
                                name="name"
                                :size="size"
                                minLength="3"
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
                            <label for="name">Adresse e-mail *</label>
                            <input-text
                                id="name"
                                v-model="form.email"
                                :disabled="form.processing"
                                readonly
                                name="email"
                                type="email"
                                :size="size"
                                minLength="3"
                            />
                        </div>
                    </b-col>
                    <b-col md="6">
                        <div class="d-flex flex-column">
                            <label for="phone">Téléphone (Whatsapp)*</label>
                            <input-text
                                id="phone"
                                v-model="form.phone"
                                :invalid="v$.phone.$error"
                                :disabled="form.processing"
                                @blur="v$.phone.$touch()"
                                name="phone"
                                :size="size"
                                minLength="3"
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
                    <b-col md="4">
                        <div class="d-flex flex-column">
                            <label for="date_birth">Date de naissance *</label>
                            <Calendar
                                input-id="date_birth"
                                v-model="form.date_birth"
                                :disabled="form.processing"
                                :invalid="v$.date_birth.$error"
                                @blur="v$.date_birth.$touch()"
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
                                :invalid="v$.place_birth.$error"
                                :disabled="form.processing"
                                name="place_birth"
                                @blurlav$irth.$touch("
                            :size="size"
                            minLength="3"
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
                            <label for="country_birth_id"
                            >Pays de naissance *</label
                            >
                            <Select
                                input-id="country_birth_id"
                                v-model="form.country_birth_id"
                                :options="countries"
                                :disabled="form.processing"
                                :invalid="v$.country_birth_id.$error"
                                @blur="v$.country_birth_id.$touch()"
                                :size="size"
                                filter
                                option-label="name"
                                option-value="id"
                            />
                            <Message
                                v-if="form.errors.country_birth_id"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ form.errors.country_birth_id }}
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="6">
                        <div class="d-flex flex-column">
                            <label for="nationality">Nationalité *</label>
                            <input-text
                                id="nationality"
                                v-model="form.nationality"
                                :invalid="v$.nationality.$error"
                                :disabled="form.processing"
                                name="nationality"
                                @bluratv$lity.$touch("
                            :size="size"
                            minLength="3"
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
                    <b-col md="6">
                        <div class="d-flex flex-column">
                            <label for="passport_number"
                            >N° de passport *</label
                            >
                            <input-text
                                id="passport_number"
                                v-model="form.passport_number"
                                :invalid="v$.passport_number.$error"
                                :disabled="form.processing"
                                name="passport_number"
                                @blurasv$t_number.$touch("
                            :size="size"
                            minLength="3"
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
                </div>
            </b-card-body>
        </b-card>

        <b-card no-body class="shadow">
            <b-card-header class="border-bottom p-4">
                <b-card-title class="mb-0">
                    <BIconPencil class="mb-2 me-1" />
                    Informations sur la demande
                </b-card-title>
            </b-card-header>

            <b-card-body class="p-4 pb-0">
                <div class="row g-4">
                    <b-col md="12" class="d-flex items-center">
                        <toggle-switch v-model="form.solo" class="me-3" />
                        {{
                            form.solo
                                ? 'Je prendrai en charge le suivi de mon dossier'
                                : 'Mon dossier est suivi par un partenaire de Mareza'
                        }}
                    </b-col>
                    <b-col md="6">
                        <transition>
                            <div v-if="!form.solo" class="d-flex flex-column">
                                <label for="on_partner"
                                >Quel partenaire suit votre dossier *</label
                                >
                                <Select
                                    input-id="on_partner"
                                    v-model="form.partner_id"
                                    :options="partners"
                                    :disabled="form.processing"
                                    :invalid="v$.partner_id.$error"
                                    @blur="v$.partner_id.$touch()"
                                    :size="size"
                                    option-label="name"
                                    option-value="id"
                                    placeholder="Partenaire"
                                    required
                                />
                                <Message
                                    v-if="form.errors.partner_id"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ form.errors.partner_id }}
                                </Message>
                            </div>
                        </transition>
                    </b-col>
                    <b-col md="6">
                        <transition>
                            <div class="d-flex flex-column" v-if="!form.solo">
                                <label for="code">Code promo *</label>
                                <div class="flex items-center">
                                    <input-text
                                        id="code"
                                        v-model="form.coupon"
                                        :disabled="
                                            form.processing || isValidating
                                        "
                                        name="code"
                                        :size="size"
                                        minLength="3"
                                        fluid
                                    />
                                    <Button
                                        icon="pi pi-check"
                                        aria-label="validate"
                                        severity="contrast"
                                        isabled="form.partner_id == ''"
                                        :loading="isValidating"
                                        @click="onValidateCoupon"
                                    />
                                </div>
                                <Message
                                    v-if="!!couponError"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ couponError }}
                                </Message>
                                <Message
                                    v-if="couponSuccess"
                                    severity="success"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ couponSuccess }}
                                </Message>
                            </div>
                        </transition>
                    </b-col>
                    <b-col md="6">
                        <div class="d-flex flex-column">
                            <label for="on_country">Pays de residence *</label>
                            <Select
                                input-id="on_country"
                                v-model="form.country_id"
                                :options="countries"
                                :disabled="form.processing"
                                :invalid="v$.country_id.$error"
                                filter
                                @blur="v$.country_id.$touch()"
                                :size="size"
                                option-label="name"
                                option-value="id"
                                placeholder="Depuis quel pays faites-vous la demande ?"
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
                    <b-col md="6">
                        <div class="d-flex flex-column">
                            <label for="on_city"
                            >Ville souhaitée (France)</label
                            >
                            <Select
                                input-id="on_city"
                                v-model="form.city_id"
                                :options="cities"
                                :disabled="form.processing"
                                :invalid="v$.city_id.$error"
                                :virtualScrollerOptions="{ itemSize: 38 }"
                                @blur="v$.city_id.$touch()"
                                :size="size"
                                filter
                                option-label="name"
                                option-value="id"
                                placeholder="Ville souhaitée pour la location"
                            />
                            <Message
                                v-if="form.errors.city_id"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ form.errors.city_id }}
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="6">
                        <div class="d-flex flex-column">
                            <label for="on_genre">Type de logement</label>
                            <Select
                                input-id="on_genre"
                                v-model="form.genre_id"
                                :options="genres"
                                :disabled="form.processing"
                                :invalid="v$.genre_id.$error"
                                @blur="v$.genre_id.$touch()"
                                :size="size"
                                option-label="name"
                                option-value="id"
                                placeholder="Quel type de logement souhaitez vous louer ?"
                            />
                            <Message
                                v-if="form.errors.genre_id"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ form.errors.genre_id }}
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="6">
                        <div class="d-flex flex-column">
                            <label for="on_rental">Cautionnement locatif</label>
                            <MultiSelect
                                input-id="on_rental"
                                v-model="form.rental_deposit_ids"
                                :options="rental_deposits"
                                :disabled="form.processing"
                                :invalid="v$.rental_deposit_ids.$error"
                                @blur="v$.rental_deposit_ids.$touch()"
                                :size="size"
                                option-label="name"
                                option-value="id"
                                placeholder="Cautionnement locatif"
                            />
                            <Message
                                v-if="form.errors.rental_deposit_ids"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ form.errors.rental_deposit_ids }}
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="4">
                        <div class="d-flex flex-column">
                            <label for="on_budget">Budget maximal</label>
                            <input-number
                                input-id="on_budget"
                                v-model="form.budget"
                                @blur="v$.budget.$touch()"
                                :size="size"
                                :disabled="form.processing"
                                :invalid="v$.budget.$error"
                                mode="currency"
                                currency="EUR"
                                locale="fr-FR"
                                class="w-full"
                                placeholder="Budget maximal"
                            />
                            <Message
                                v-if="form.errors.budget"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ form.errors.budget }}
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="4">
                        <div class="d-flex flex-column">
                            <label for="on_start">Debut de location</label>
                            <Calendar
                                input-id="on_start"
                                v-model="form.rental_start"
                                :disabled="form.processing"
                                :invalid="v$.rental_start.$error"
                                @blur="v$.rental_start.$touch()"
                                :size="size"
                                showIcon
                                iconDisplay="input"
                            />
                            <Message
                                v-if="form.errors.rental_start"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ form.errors.rental_start }}
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="4">
                        <div class="d-flex flex-column">
                            <label for="on_duration">Durée de location</label>
                            <input-number
                                input-id="on_duration"
                                v-model="form.duration"
                                @blur="v$.duration.$touch()"
                                :size="size"
                                :disabled="form.processing"
                                :invalid="v$.duration.$error"
                                suffix=" mois"
                                placeholder="A partir de 6 mois"
                            />
                            <Message
                                v-if="form.errors.duration"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ form.errors.duration }}
                            </Message>
                        </div>
                    </b-col>
                    <b-col md="12">
                        <div class="d-flex flex-column">
                            <label for="further_information"
                            >Informations complémentaires
                                (facultatif)</label
                            >
                            <Textarea
                                input-id="further_information"
                                v-model="form.further_information"
                                :size="size"
                                :disabled="form.processing"
                            />
                            <Message
                                v-if="form.errors.further_information"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ form.errors.further_information }}
                            </Message>
                        </div>
                    </b-col>
                </div>

                <b-col cols="12" class="mt-5">
                    <div class="bg-light rounded-2 px-4 py-3">
                        <h6 class="mb-0">Paiement</h6>
                    </div>

                    <div class="mt-3 px-3">
                        <div ref="cardElement" />
                    </div>
                </b-col>

                <Message v-if="errorMessage" severity="error">
                    {{ errorMessage }}
                </Message>

                <b-col cols="12" class="mt-5 pb-4">
                    <div
                        class="d-sm-flex justify-content-sm-between align-items-center"
                    >
                        <div>
                            <h4 v-if="!form.is_coupon_valid">
                                399 €
                                <span class="small fs-6">À payer</span>
                            </h4>
                            <h4 v-else>
                                <span
                                    class="text-decoration-line-through text-muted me-2"
                                >
                                    399 €
                                </span>
                                <span class="text-success">
                                    {{ discountedAmount }} €
                                </span>
                                <span class="small fs-6">À payer</span>
                            </h4>
                        </div>
                        <b-button
                            variant="primary"
                            type="submit"
                            class="mb-0"
                            :loading="form.processing || processing"
                            :disabled="processing || v$.$invalid"
                        >Payer et valider ma demande
                        </b-button>
                    </div>
                </b-col>
            </b-card-body>
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
