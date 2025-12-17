<script setup lang="ts">
import AppHead from '@/Components/AppHead.vue';
import PartnerCoupon from '@/Components/PartnerCoupon.vue';
import CustomerSearchInfo from '@/Pages/CustomSearch/components/CustomerSearchInfo.vue';
import CustomSearchPersonalInfo from '@/Pages/CustomSearch/components/CustomSearchPersonalInfo.vue';
import CustomSearchSelectBar from '@/Pages/CustomSearch/components/CustomSearchSelectBar.vue';
import { useCustomSearchStore } from '@/Stores/customSearch';
import {
    Category,
    City,
    Country,
    Layout,
    Partner,
    PropertyType,
    RentalDeposit,
    User,
} from '@/Types/index';
import { Link, router, useForm } from '@inertiajs/vue3';
import { loadStripe } from '@stripe/stripe-js';
import axios from 'axios';
import { BIconHouse } from 'bootstrap-icons-vue';
import dayjs from 'dayjs';
import timezone from 'dayjs/plugin/timezone';
import utc from 'dayjs/plugin/utc';
import { storeToRefs } from 'pinia';
import { computed, onMounted, ref, watch } from 'vue';

dayjs.extend(utc);
dayjs.extend(timezone);

const content = {
    title: 'Demande d\'accompagnement',
    description: 'Demande d\'accompagnement personnalisé pour vos études',
};

interface Props {
    user: User;
    types: PropertyType[];
    categories: Category[];
    layouts: Layout[];
    cities?: City[];
    countries: Country[];
    partners: Partner[];
    rentalDeposits: RentalDeposit[];
    stripeKey: string;
    intent: string;
}

const props = defineProps<Props>();

const store = useCustomSearchStore();
const { r$ } = storeToRefs(store);
const formData = computed(() => r$.value.$value);

const stripeKey = props.stripeKey;
const intent = ref(props.intent);
const cardElement = ref<HTMLElement | null>(null);
const stripe = ref<any>(null);
const card = ref<any>(null);

// Création du formulaire Inertia avec les données initiales du store
const form = useForm({
    ...formData.value,
});

// Watch pour synchroniser le formulaire avec les changements du store
watch(
    formData,
    (newData) => {
        // Mettre à jour toutes les propriétés du formulaire
        Object.keys(newData).forEach((key) => {
            if (form.hasOwnProperty(key)) {
                (form as any)[key] = (newData as any)[key];
            }
        });
    },
    { deep: true, immediate: true },
);

onMounted(async () => {
    if (props.user) {
        store.prefillUserData(props.user);
    }

    // Vérifier que Stripe est configuré
    if (!stripeKey) {
        console.error('Stripe key is not configured');
        errorMessage.value = 'Le système de paiement n\'est pas configuré. Veuillez contacter l\'administration.';
        return;
    }

    try {
        stripe.value = await loadStripe(stripeKey);
        if (!stripe.value) {
            console.error('Failed to load Stripe');
            errorMessage.value = 'Impossible de charger le système de paiement. Vérifiez votre connexion.';
            return;
        }

        const elements = stripe.value.elements();
        card.value = elements.create('card', {
            hidePostalCode: true,
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    '::placeholder': { color: '#aab7c4' },
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a',
                },
            },
        });

        if (cardElement.value) {
            card.value.mount(cardElement.value);
        } else {
            console.error('Card element container not found');
        }
    } catch (e) {
        console.error('Stripe initialization error:', e);
        errorMessage.value = 'Erreur lors de l\'initialisation du paiement.';
    }
});
const onSubmit = async () => {
    store.clearExternalErrors();

    // Validation côté client
    const validation = await store.validateForm();

    if (!validation.isValid) {
        console.log('Erreurs de validation côté client:', validation.errors);
        return;
    }

    // Soumission du formulaire
    form.post(route('custom-search.store'), {
        onError: (errors) => {
            console.log('Erreurs du serveur:', errors);

            // Définir les erreurs externes pour Regle
            store.setExternalErrors(errors);
        },
        onSuccess: (response) => {
            console.log('Formulaire soumis avec succès:', response);

            // Optionnel : réinitialiser le formulaire après succès
            // store.resetForm();
        },
    });
};

const processing = ref(false);
const errorMessage = ref<string | null>(null);
const validationErrors = ref<string[]>([]);
const couponError = ref('');
const couponSuccess = ref('');
const discountedAmount = ref('');
const couponId = ref<number | null>(null);

// Labels des champs pour messages d'erreur
const fieldLabels: Record<string, string> = {
    property_type_ids: "Type d'établissement",
    layout_ids: "Niveau d'études",
    category_id: "Domaine d'études",
    city_id: "Ville",
    name: "Prénom",
    surname: "Nom",
    phone: "Téléphone",
    place_birth: "Lieu de naissance",
    date_birth: "Date de naissance",
    nationality: "Nationalité",
    passport_number: "N° de passeport",
    partner_id: "Partenaire",
    rental_deposit_ids: "Services souhaités",
    budget: "Budget",
    rental_start: "Rentrée souhaitée",
    duration: "Durée souhaitée",
};

// Vérifie si le formulaire est valide
const isFormValid = computed(() => !r$.value.$invalid);

// Liste des champs invalides pour affichage
const invalidFields = computed(() => {
    const fields: string[] = [];
    if (r$.value.$invalid) {
        Object.keys(r$.value.$fields).forEach((key) => {
            const field = (r$.value.$fields as any)[key];
            if (field.$invalid) {
                fields.push(fieldLabels[key] || key);
            }
        });
    }
    return fields;
});

const submit = async () => {
    errorMessage.value = null;
    validationErrors.value = [];

    // Vérifier que Stripe est initialisé
    if (!stripe.value || !card.value) {
        errorMessage.value = 'Le système de paiement n\'est pas prêt. Veuillez rafraîchir la page.';
        return;
    }

    // Valider le formulaire avant soumission
    await r$.value.$validate();

    if (r$.value.$invalid) {
        validationErrors.value = invalidFields.value;
        errorMessage.value = `Veuillez remplir tous les champs obligatoires (${invalidFields.value.length} champ(s) manquant(s))`;
        return;
    }

    try {
        processing.value = true;

        const payload = {
            ...form.data(),
        };

        // 3. Appel au backend pour créer le PaymentIntent (et le modèle CertificateRequest)
        const createIntentResponse = await axios.post(
            route('custom-search.store'),
            payload,
        );
        // On attend l'objet { client_secret, certificate_id } envoyé par /store

        const clientSecret = createIntentResponse.data.client_secret as string;
        const hashId = createIntentResponse.data.id as string;

        // 4. On appelle Stripe.js pour confirmer le PaymentIntent (et déclencher le 3D Secure)
        const { error, paymentIntent } = await stripe.value.confirmCardPayment(
            clientSecret,
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
            // Si l'utilisateur annule ou que l'authentification 3D échoue
            errorMessage.value = error.message!;
            return;
        }

        // 5. Si la confirmation Stripe.js renvoie "succeeded"
        if (paymentIntent!.status === 'succeeded') {
            // On appelle alors confirmPayment pour marquer en BDD / générer le certificat
            await axios.post(route('custom-search.payment.confirm'), {
                search_id: hashId,
            });

            // 6. Puis on redirige vers la page de succès (ou vers la liste des certificats)
            router.get(route('dashboard.custom-search.index'));
            //router.visit(route('custom-search.index', hashId));
            return;
        }

        // — En théorie, on ne passe jamais ici si status === 'succeeded'.
        //    Si Stripe.js renvoie "requires_action", confirmCardPayment()
        //    a déjà refait le 3D Secure. Donc youpi, on ne devrait pas revenir ici.
    } catch (err: any) {
        console.error(err);
        errorMessage.value =
            err.response?.data?.message || 'Une erreur inattendue est survenue';
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <AppHead :title="content.title">
        <meta
            head-key="description"
            name="description"
            :content="content.description"
        />
    </AppHead>
    <section>
        <b-container>
            <b-card no-body class="bg-light px-sm-5 overflow-hidden">
                <b-row class="align-items-center g-4">
                    <b-col sm="9">
                        <b-card-body>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-dots mb-0">
                                    <li class="breadcrumb-item">
                                        <Link href="/">
                                            <BIconHouse class="mb-1 me-1" />
                                            Accueil
                                        </Link>
                                    </li>
                                    <li class="breadcrumb-item active">
                                        Demande d'accompagnement
                                    </li>
                                </ol>
                            </nav>
                            <h1 class="h3 card-title m-0">
                                Demande d'accompagnement
                            </h1>
                        </b-card-body>
                    </b-col>
                    <b-col sm="3" class="d-none d-sm-block text-end">
                        <img
                            src="/images/front/element/17.svg"
                            class="mb-n4"
                            alt=""
                        />
                    </b-col>
                </b-row>
            </b-card>

            <CustomSearchSelectBar :types :categories :layouts :cities />
            <CustomerSearchInfo
                :partners
                :rentalDeposits
                :countries
                class="mb-4"
            />
            <CustomSearchPersonalInfo
                :user
                :countries
                :partners
                :rentalDeposits
            />

            <b-card no-body class="payment-card mt-4 shadow">
                <b-card-header class="payment-header p-4">
                    <h4 class="mb-0">Paiement sécurisé</h4>
                </b-card-header>

                <b-card-body class="p-4">
                    <!-- Section éléments de paiement -->
                    <div class="payment-elements mb-4">
                        <div class="row g-4 align-items-end">
                            <!-- Carte bancaire -->
                            <b-col sm="12" md="6" lg="6" class="pt-3">
                                <label class="form-label"
                                    >Informations de carte</label
                                >
                                <div
                                    ref="cardElement"
                                    class="stripe-card-element"
                                ></div>
                            </b-col>

                            <!-- Coupon -->
                            <b-col sm="12" md="6" lg="6">
                                <PartnerCoupon />
                            </b-col>
                        </div>
                    </div>

                    <!-- Message d'erreur -->
                    <Message v-if="errorMessage" severity="error" class="mb-4">
                        {{ errorMessage }}
                    </Message>

                    <!-- Liste des champs manquants -->
                    <div v-if="validationErrors.length > 0" class="alert alert-warning mb-4">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Champs obligatoires manquants :</strong>
                        <ul class="mb-0 mt-2">
                            <li v-for="field in validationErrors" :key="field">{{ field }}</li>
                        </ul>
                    </div>

                    <!-- Indicateur de formulaire incomplet -->
                    <div v-if="!isFormValid && validationErrors.length === 0" class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Veuillez remplir tous les champs obligatoires marqués d'un <strong>*</strong> pour procéder au paiement.
                    </div>

                    <!-- Section total et paiement -->
                    <div
                        class="payment-footer d-flex flex-column flex-md-row align-items-center justify-content-between border-top pt-4"
                    >
                        <!-- Prix -->
                        <div class="price-section mb-md-0 mb-3">
                            <h4 class="mb-1">
                                <span
                                    :class="{
                                        'text-decoration-line-through text-muted me-2':
                                            form.coupon_id,
                                        'fw-normal': true,
                                    }"
                                >
                                    100 €
                                </span>
                                <span
                                    class="text-success fw-bold"
                                    v-if="form.coupon_id"
                                >
                                    {{ form.paid }} €
                                </span>
                            </h4>
                            <span class="text-muted small">Total à payer</span>
                        </div>

                        <!-- Bouton de paiement -->
                        <div class="d-flex flex-column align-items-center align-items-md-end">
                            <b-button
                                :variant="isFormValid ? 'primary' : 'secondary'"
                                size="lg"
                                @click="submit"
                                :loading="processing || form.processing"
                                :disabled="processing || form.processing"
                                class="payment-button"
                            >
                                <span v-if="!(processing || form.processing)">
                                    <i class="fas fa-lock me-2"></i>Payer et envoyer ma demande
                                </span>
                                <span v-else> Traitement en cours... </span>
                            </b-button>
                            <small v-if="!isFormValid" class="text-muted mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Complétez le formulaire pour activer le paiement
                            </small>
                        </div>
                    </div>
                </b-card-body>
            </b-card>
        </b-container>
    </section>
</template>

<style scoped>
:deep(h4) {
    font-size: 1.2rem;
}
.payment-card {
    border-radius: 12px;
    overflow: hidden;
}

.payment-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #eaeaea !important;
}

.stripe-card-element {
    padding: 12px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    background: white;
}

.payment-footer {
    padding-top: 1.5rem;
}

.payment-button {
    padding: 0.75rem 2rem;
    font-weight: 500;
    border-radius: 8px;
}

.price-section {
    text-align: center;
}

@media (min-width: 768px) {
    .price-section {
        text-align: left;
    }
}
</style>
