<script setup lang="ts">
// Formulaire de candidature multi-étapes EtatSup - Inspiré Diplomeo
// Sprint1 Feature 1.8.1 - Ajout upload documents (étape 5)
import { router, useForm } from '@inertiajs/vue3';
import { useVuelidate } from '@vuelidate/core';
import { email, maxLength, minLength, required } from '@vuelidate/validators';
import { loadStripe } from '@stripe/stripe-js';
import axios from 'axios';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import DocumentUploader from '@/Components/DocumentUploader.vue';
import type { Establishment } from '@/Types/establishment';

interface Props {
    establishment: Establishment;
    applicationId?: string; // Sprint1 Feature 1.8.1 - Pour DocumentUploader
    user?: any;
    draftData?: any;
    stripeKey: string;
    intent: string;
}

const props = defineProps<Props>();

// Stripe setup
const stripe = ref<any>(null);
const card = ref<any>(null);
const cardElement = ref<HTMLElement | null>(null);
const errorMessage = ref<string | null>(null);
const cardMounted = ref(false);
const currentStep = ref(props.draftData?.current_step || 1);
const totalSteps = 6; // Sprint1 Feature 1.8.1 - 6 étapes (ajout Documents)
const processing = computed(() => form.processing);

// Sprint1 Feature 1.8.1 - Référence DocumentUploader pour validation
const documentUploaderRef = ref<InstanceType<typeof DocumentUploader> | null>(null);
const documentsComplete = ref(false);
// Sprint1 Feature 1.8.1 - ApplicationId local (mis à jour après saveDraft)
const localApplicationId = ref<string | undefined>(props.applicationId);

// Formulaire complet (toutes sections) - avec chargement draft si existant
const form = useForm({
    property_id: props.establishment.id,
    // Section 1: Informations personnelles
    surname: props.draftData?.surname || props.user?.surname || '',
    name: props.draftData?.name || props.user?.name || '',
    gender: props.draftData?.gender || '',
    date_of_birth: props.draftData?.date_of_birth || props.user?.date_birth || '',
    nationality: props.draftData?.nationality || props.user?.nationality || '',
    country_of_birth: props.draftData?.country_of_birth || '',
    city_of_birth: props.draftData?.city_of_birth || props.user?.place_birth || '',
    address: props.draftData?.address || '',
    postal_code: props.draftData?.postal_code || '',
    city: props.draftData?.city || '',
    country: props.draftData?.country || '',
    email: props.draftData?.email || props.user?.email || '',
    phone: props.draftData?.phone || props.user?.phone || '',
    parent_email: props.draftData?.parent_email || '',

    // Section 2: Formation
    current_diploma: props.draftData?.current_diploma || '',
    diploma_year: props.draftData?.diploma_year || new Date().getFullYear(),
    years_validated: props.draftData?.years_validated || '',
    previous_institution: props.draftData?.previous_institution || '',
    institution_city: props.draftData?.institution_city || '',
    institution_country: props.draftData?.institution_country || '',

    // Section 3: Compétences linguistiques
    mother_tongue: props.draftData?.mother_tongue || '',
    english_test: props.draftData?.english_test || '',
    english_level: props.draftData?.english_level || '',
    french_test: props.draftData?.french_test || '',
    french_level: props.draftData?.french_level || '',

    // Section 4: Motivation
    motivation: props.draftData?.motivation || '',
    application_location: props.draftData?.application_location || '',
    application_date: props.draftData?.application_date || new Date().toISOString().split('T')[0],

    // Section 5: Documents (à implémenter avec upload)
    documents: [],
});

// Validation par section
const section1Rules = {
    surname: { required, minLength: minLength(2) },
    name: { required, minLength: minLength(2) },
    gender: { required },
    date_of_birth: { required },
    nationality: { required },
    country_of_birth: { required },
    city_of_birth: { required },
    address: { required, minLength: minLength(5) },
    postal_code: { required },
    // city: optionnel selon PRD Sprint 1
    country: { required },
    email: { required, email },
    phone: { required },
};

const section2Rules = {
    current_diploma: { required, minLength: minLength(3) },
    diploma_year: { required },
    years_validated: { required },
    previous_institution: { required, minLength: minLength(3) },
    institution_city: { required },
    institution_country: { required },
};

const section3Rules = {
    mother_tongue: { required },
    // Tests de langue conditionnels (à affiner selon nationalité)
};

const section4Rules = {
    motivation: { required, minLength: minLength(100), maxLength: maxLength(2000) },
    application_location: { required },
};

// Sprint1 Feature 1.8.1 - Pas de règles vuelidate pour étape 5 (documents)
// La validation se fait via documentsComplete ref

// Règles de validation réactives selon l'étape
const validationRules = computed(() => {
    switch (currentStep.value) {
        case 1: return section1Rules;
        case 2: return section2Rules;
        case 3: return section3Rules;
        case 4: return section4Rules;
        case 5: return {}; // Sprint1 Feature 1.8.1 - Documents (validation custom)
        default: return {};
    }
});

const v$ = useVuelidate(validationRules, form);

// Monter Stripe.js
onMounted(async () => {
    try {
        stripe.value = await loadStripe(props.stripeKey);
        if (!stripe.value) {
            console.error('Stripe failed to load');
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

        console.log('Stripe initialized');
    } catch (error) {
        console.error('Error initializing Stripe:', error);
    }
});

// Sprint1 Feature 1.8.1 - Monter/démonter carte Stripe à l'étape 6 (au lieu de 5)
watch(currentStep, async (newStep) => {
    if (newStep === 6 && card.value) {
        await nextTick(); // Attendre que le DOM soit mis à jour

        // Refonte Story 1.1.4 FIX - Guard contre race condition (clic ultra-rapide)
        if (currentStep.value !== 6) {
            console.log('Step changed during nextTick, aborting mount');
            return;
        }

        if (cardElement.value) {
            try {
                // Si déjà montée, démonter d'abord (cas retour arrière)
                if (cardMounted.value) {
                    card.value.unmount();
                    cardMounted.value = false;
                    console.log('Stripe card unmounted for remounting');
                }
                // Monter ou remonter la carte
                card.value.mount(cardElement.value);
                cardMounted.value = true;
                console.log('Stripe card mounted');
            } catch (error) {
                console.error('Error mounting Stripe card:', error);
            }
        }
    } else if (newStep !== 6 && cardMounted.value) {
        // Sprint1 Feature 1.8.1 - Démonter proprement quand on quitte step 6
        try {
            card.value.unmount();
            cardMounted.value = false;
            console.log('Stripe card unmounted');
        } catch (error) {
            console.error('Error unmounting Stripe card:', error);
        }
    }
}, { immediate: true }); // S'exécute immédiatement au montage

// Auto-save draft
const saveDraft = async () => {
    try {
        const response = await axios.post('/applications/draft', {
            ...form.data(),
            current_step: currentStep.value,
        });
        // Sprint1 Feature 1.8.1 - Mettre à jour applicationId après création
        if (response.data.application_id) {
            localApplicationId.value = response.data.application_id;
        }
    } catch (error) {
        console.error('Erreur sauvegarde:', error);
    }
};

// Sprint1 Feature 1.8.1 - Navigation entre étapes avec validation documents
const nextStep = async () => {
    // Étape 5 (Documents) : validation custom
    if (currentStep.value === 5) {
        if (!documentUploaderRef.value?.canProceed) {
            errorMessage.value = 'Veuillez téléverser tous les documents obligatoires avant de continuer.';
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }
        documentsComplete.value = true;
    } else {
        // Autres étapes : validation vuelidate
        const isValid = await v$.value.$validate();
        if (!isValid) {
            return;
        }
    }

    // Auto-save avant de passer à l'étape suivante
    await saveDraft();

    if (currentStep.value < totalSteps) {
        currentStep.value++;
        v$.value.$reset();
        errorMessage.value = null;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

// Finaliser plus tard
const finalizeLater = async () => {
    await saveDraft();
    window.location.href = '/dashboard/reservations';
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
        v$.value.$reset();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const goToStep = (step: number) => {
    currentStep.value = step;
    v$.value.$reset();
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const submit = async () => {
    const isValid = await v$.value.$validate();
    if (!isValid) return;

    errorMessage.value = null;
    form.processing = true;

    try {
        // 1. Créer le PaymentIntent via le backend
        const response = await axios.post('/applications', form.data());
        const { client_secret, application_id } = response.data;

        // 2. Confirmer le paiement avec Stripe.js (gère le 3D Secure)
        const { error, paymentIntent } = await stripe.value!.confirmCardPayment(
            client_secret,
            {
                payment_method: {
                    card: card.value!,
                    billing_details: {
                        name: `${form.surname} ${form.name}`,
                        email: form.email,
                    },
                },
            },
        );

        if (error) {
            errorMessage.value = error.message!;
            form.processing = false;
            return;
        }

        // 3. Vérifier que le paiement est réussi
        if (paymentIntent!.status === 'succeeded') {
            // 4. Confirmer côté backend
            await axios.post(route('applications.payment.confirm'), {
                application_id: application_id,
            });

            // 5. Rediriger vers le dashboard
            router.get(route('dashboard'));
        }
    } catch (err: any) {
        console.error(err);
        errorMessage.value = err.response?.data?.message || 'Une erreur est survenue lors de la soumission';
        form.processing = false;
    }
};

// Options pour les selects
const genders = [
    { value: '', text: '-- Sélectionnez --' },
    { value: 'M', text: 'Masculin' },
    { value: 'F', text: 'Féminin' },
];

const yearsValidated = [
    { value: '', text: '-- Sélectionnez --' },
    { value: '1', text: '1 année' },
    { value: '2', text: '2 années' },
    { value: '3', text: '3 années' },
    { value: '4', text: '4 années' },
    { value: '5+', text: '5 années ou plus' },
];

const languages = [
    { value: '', text: '-- Sélectionnez --' },
    { value: 'Français', text: 'Français' },
    { value: 'Anglais', text: 'Anglais' },
    { value: 'Espagnol', text: 'Espagnol' },
    { value: 'Arabe', text: 'Arabe' },
    { value: 'Wolof', text: 'Wolof' },
    { value: 'Autre', text: 'Autre' },
];

const englishTests = [
    { value: '', text: '-- Sélectionnez --' },
    { value: 'IELTS', text: 'IELTS' },
    { value: 'TOEFL', text: 'TOEFL' },
];

const frenchTests = [
    { value: '', text: '-- Sélectionnez --' },
    { value: 'TCF', text: 'TCF' },
    { value: 'DELF', text: 'DELF' },
    { value: 'DALF', text: 'DALF' },
];

const progressPercentage = computed(() => (currentStep.value / totalSteps) * 100);

// Sprint1 Feature 1.7.1 - Montant dynamique formaté
const formattedAmount = computed(() => {
    const amount = props.establishment.frais_dossier ?? 0;
    return amount.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
});
</script>

<template>
    <BCard no-body class="border-0 shadow-diplomeo">
        <div class="card-header-etatsup">
            <h5 class="mb-0 text-white">
                <i class="bi bi-file-earmark-text me-2"></i>
                Formulaire de candidature - {{ establishment.title }}
            </h5>
        </div>

        <BCardBody class="p-0">
            <!-- Stepper Progress -->
            <div class="stepper-container">
                <div class="stepper-progress">
                    <div class="stepper-progress-bar" :style="{ width: progressPercentage + '%' }"></div>
                </div>

                <div class="stepper-steps">
                    <div
                        v-for="step in totalSteps"
                        :key="step"
                        class="stepper-step"
                        :class="{
                            'active': currentStep === step,
                            'completed': currentStep > step
                        }"
                        @click="goToStep(step)"
                    >
                        <div class="stepper-step-circle">
                            <span>{{ step }}</span>
                        </div>
                        <div class="stepper-step-label">
                            <span v-if="step === 1">Infos personnelles</span>
                            <span v-if="step === 2">Formation</span>
                            <span v-if="step === 3">Langues</span>
                            <span v-if="step === 4">Motivation</span>
                            <span v-if="step === 5">Documents</span>
                            <span v-if="step === 6">Paiement</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="form-content">
                <form @submit.prevent="currentStep === totalSteps ? submit() : nextStep()">

                    <!-- SECTION 1: Informations personnelles -->
                    <div v-if="currentStep === 1" class="form-section">
                        <h6 class="section-title">
                            <i class="bi bi-person-circle me-2"></i>
                            1. Informations personnelles
                        </h6>

                        <BRow class="g-3">
                            <BCol md="6">
                                <label class="form-label-etatsup required">Nom</label>
                                <BFormInput
                                    v-model="form.surname"
                                    :state="v$.surname?.$error ? false : null"
                                    class="form-control-etatsup"
                                    @blur="v$.surname?.$touch"
                                />
                                <div v-if="v$.surname?.$error" class="invalid-feedback d-block">
                                    Le nom est requis (min. 2 caractères)
                                </div>
                            </BCol>

                            <BCol md="6">
                                <label class="form-label-etatsup required">Prénom</label>
                                <BFormInput
                                    v-model="form.name"
                                    :state="v$.name?.$error ? false : null"
                                    class="form-control-etatsup"
                                    @blur="v$.name?.$touch"
                                />
                                <div v-if="v$.name?.$error" class="invalid-feedback d-block">
                                    Le prénom est requis (min. 2 caractères)
                                </div>
                            </BCol>

                            <BCol md="4">
                                <label class="form-label-etatsup required">Sexe</label>
                                <BFormSelect
                                    v-model="form.gender"
                                    :options="genders"
                                    :state="v$.gender?.$error ? false : null"
                                    class="form-control-etatsup"
                                    @blur="v$.gender?.$touch"
                                />
                            </BCol>

                            <BCol md="4">
                                <label class="form-label-etatsup required">Date de naissance</label>
                                <BFormInput
                                    v-model="form.date_of_birth"
                                    type="date"
                                    :state="v$.date_of_birth?.$error ? false : null"
                                    class="form-control-etatsup"
                                    @blur="v$.date_of_birth?.$touch"
                                />
                            </BCol>

                            <BCol md="4">
                                <label class="form-label-etatsup required">Nationalité</label>
                                <BFormInput
                                    v-model="form.nationality"
                                    :state="v$.nationality?.$error ? false : null"
                                    class="form-control-etatsup"
                                    placeholder="Ex: Sénégalaise"
                                    @blur="v$.nationality?.$touch"
                                />
                            </BCol>

                            <BCol md="6">
                                <label class="form-label-etatsup required">Pays de naissance</label>
                                <BFormInput
                                    v-model="form.country_of_birth"
                                    :state="v$.country_of_birth?.$error ? false : null"
                                    class="form-control-etatsup"
                                    @blur="v$.country_of_birth?.$touch"
                                />
                            </BCol>

                            <BCol md="6">
                                <label class="form-label-etatsup required">Ville de naissance</label>
                                <BFormInput
                                    v-model="form.city_of_birth"
                                    :state="v$.city_of_birth?.$error ? false : null"
                                    class="form-control-etatsup"
                                    @blur="v$.city_of_birth?.$touch"
                                />
                            </BCol>

                            <BCol cols="12">
                                <label class="form-label-etatsup required">Adresse actuelle</label>
                                <BFormInput
                                    v-model="form.address"
                                    :state="v$.address?.$error ? false : null"
                                    class="form-control-etatsup"
                                    @blur="v$.address?.$touch"
                                />
                            </BCol>

                            <BCol md="4">
                                <label class="form-label-etatsup required">Code postal</label>
                                <BFormInput
                                    v-model="form.postal_code"
                                    :state="v$.postal_code?.$error ? false : null"
                                    class="form-control-etatsup"
                                    @blur="v$.postal_code?.$touch"
                                />
                            </BCol>

                            <BCol md="4">
                                <label class="form-label-etatsup">Ville</label>
                                <BFormInput
                                    v-model="form.city"
                                    class="form-control-etatsup"
                                    placeholder="Optionnel"
                                />
                            </BCol>

                            <BCol md="4">
                                <label class="form-label-etatsup required">Pays</label>
                                <BFormInput
                                    v-model="form.country"
                                    :state="v$.country?.$error ? false : null"
                                    class="form-control-etatsup"
                                    @blur="v$.country?.$touch"
                                />
                            </BCol>

                            <BCol md="6">
                                <label class="form-label-etatsup required">Email</label>
                                <BFormInput
                                    v-model="form.email"
                                    type="email"
                                    :state="v$.email?.$error ? false : null"
                                    class="form-control-etatsup"
                                    @blur="v$.email?.$touch"
                                />
                            </BCol>

                            <BCol md="6">
                                <label class="form-label-etatsup required">Portable</label>
                                <BFormInput
                                    v-model="form.phone"
                                    type="tel"
                                    :state="v$.phone?.$error ? false : null"
                                    class="form-control-etatsup"
                                    placeholder="+221 77 123 45 67"
                                    @blur="v$.phone?.$touch"
                                />
                            </BCol>

                            <BCol cols="12">
                                <label class="form-label-etatsup">Courriel des parents (optionnel)</label>
                                <BFormInput
                                    v-model="form.parent_email"
                                    type="email"
                                    class="form-control-etatsup"
                                />
                            </BCol>
                        </BRow>
                    </div>

                    <!-- SECTION 2: Formation -->
                    <div v-if="currentStep === 2" class="form-section">
                        <h6 class="section-title">
                            <i class="bi bi-mortarboard me-2"></i>
                            2. Formation
                        </h6>

                        <BRow class="g-3">
                            <BCol md="8">
                                <label class="form-label-etatsup required">Diplôme en cours d'obtention (ou dernier diplôme obtenu)</label>
                                <BFormInput
                                    v-model="form.current_diploma"
                                    :state="v$.current_diploma?.$error ? false : null"
                                    class="form-control-etatsup"
                                    placeholder="Ex: Baccalauréat série S"
                                    @blur="v$.current_diploma?.$touch"
                                />
                            </BCol>

                            <BCol md="4">
                                <label class="form-label-etatsup required">Année du diplôme</label>
                                <BFormInput
                                    v-model="form.diploma_year"
                                    type="number"
                                    :state="v$.diploma_year?.$error ? false : null"
                                    class="form-control-etatsup"
                                    :min="1990"
                                    :max="new Date().getFullYear() + 5"
                                    @blur="v$.diploma_year?.$touch"
                                />
                            </BCol>

                            <BCol cols="12">
                                <label class="form-label-etatsup required">Nombre d'années de premier cycle validé</label>
                                <BFormSelect
                                    v-model="form.years_validated"
                                    :options="yearsValidated"
                                    :state="v$.years_validated?.$error ? false : null"
                                    class="form-control-etatsup"
                                    @blur="v$.years_validated?.$touch"
                                />
                            </BCol>

                            <BCol md="6">
                                <label class="form-label-etatsup required">Établissement antérieur</label>
                                <BFormInput
                                    v-model="form.previous_institution"
                                    :state="v$.previous_institution?.$error ? false : null"
                                    class="form-control-etatsup"
                                    placeholder="Nom de l'établissement"
                                    @blur="v$.previous_institution?.$touch"
                                />
                            </BCol>

                            <BCol md="3">
                                <label class="form-label-etatsup required">Ville</label>
                                <BFormInput
                                    v-model="form.institution_city"
                                    :state="v$.institution_city?.$error ? false : null"
                                    class="form-control-etatsup"
                                    @blur="v$.institution_city?.$touch"
                                />
                            </BCol>

                            <BCol md="3">
                                <label class="form-label-etatsup required">Pays</label>
                                <BFormInput
                                    v-model="form.institution_country"
                                    :state="v$.institution_country?.$error ? false : null"
                                    class="form-control-etatsup"
                                    @blur="v$.institution_country?.$touch"
                                />
                            </BCol>
                        </BRow>
                    </div>

                    <!-- SECTION 3: Compétences linguistiques -->
                    <div v-if="currentStep === 3" class="form-section">
                        <h6 class="section-title">
                            <i class="bi bi-translate me-2"></i>
                            3. Compétences linguistiques
                        </h6>

                        <BRow class="g-3">
                            <BCol cols="12">
                                <label class="form-label-etatsup required">Langue maternelle</label>
                                <BFormSelect
                                    v-model="form.mother_tongue"
                                    :options="languages"
                                    :state="v$.mother_tongue?.$error ? false : null"
                                    class="form-control-etatsup"
                                    @blur="v$.mother_tongue?.$touch"
                                />
                            </BCol>

                            <BCol cols="12" class="mt-4">
                                <div class="info-box">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Pour les non-anglophones :</strong> Indiquez votre niveau d'anglais
                                </div>
                            </BCol>

                            <BCol md="6">
                                <label class="form-label-etatsup">Test d'anglais</label>
                                <BFormSelect
                                    v-model="form.english_test"
                                    :options="englishTests"
                                    class="form-control-etatsup"
                                />
                            </BCol>

                            <BCol md="6">
                                <label class="form-label-etatsup">Niveau obtenu</label>
                                <BFormInput
                                    v-model="form.english_level"
                                    class="form-control-etatsup"
                                    placeholder="Ex: 6.5 (IELTS) ou 90 (TOEFL)"
                                />
                            </BCol>

                            <BCol cols="12" class="mt-4">
                                <div class="info-box">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Pour les non-francophones :</strong> Indiquez votre niveau de français
                                </div>
                            </BCol>

                            <BCol md="6">
                                <label class="form-label-etatsup">Test de français</label>
                                <BFormSelect
                                    v-model="form.french_test"
                                    :options="frenchTests"
                                    class="form-control-etatsup"
                                />
                            </BCol>

                            <BCol md="6">
                                <label class="form-label-etatsup">Niveau obtenu</label>
                                <BFormInput
                                    v-model="form.french_level"
                                    class="form-control-etatsup"
                                    placeholder="Ex: B2, C1"
                                />
                            </BCol>
                        </BRow>
                    </div>

                    <!-- SECTION 4: Motivation -->
                    <div v-if="currentStep === 4" class="form-section">
                        <h6 class="section-title">
                            <i class="bi bi-heart me-2"></i>
                            4. Lettre de motivation
                        </h6>

                        <BRow class="g-3">
                            <BCol cols="12">
                                <label class="form-label-etatsup required">
                                    Pourquoi souhaitez-vous intégrer cet établissement ?
                                </label>
                                <BFormTextarea
                                    v-model="form.motivation"
                                    rows="10"
                                    :state="v$.motivation?.$error ? false : null"
                                    class="form-control-etatsup"
                                    placeholder="Expliquez vos motivations, vos objectifs professionnels, et pourquoi vous avez choisi cet établissement..."
                                    @blur="v$.motivation?.$touch"
                                />
                                <small class="text-muted">
                                    {{ form.motivation.length }} / 2000 caractères (minimum 100)
                                </small>
                                <div v-if="v$.motivation?.$error" class="invalid-feedback d-block">
                                    La motivation doit contenir entre 100 et 2000 caractères
                                </div>
                            </BCol>

                            <BCol md="6">
                                <label class="form-label-etatsup required">Lieu</label>
                                <BFormInput
                                    v-model="form.application_location"
                                    :state="v$.application_location?.$error ? false : null"
                                    class="form-control-etatsup"
                                    placeholder="Ville"
                                    @blur="v$.application_location?.$touch"
                                />
                            </BCol>

                            <BCol md="6">
                                <label class="form-label-etatsup">Date</label>
                                <BFormInput
                                    v-model="form.application_date"
                                    type="date"
                                    class="form-control-etatsup"
                                    readonly
                                />
                            </BCol>
                        </BRow>
                    </div>

                    <!-- SECTION 5: Documents (Sprint1 Feature 1.8.1) -->
                    <div v-if="currentStep === 5" class="form-section">
                        <h6 class="section-title">
                            <i class="bi bi-file-earmark-arrow-up me-2"></i>
                            5. Pièces justificatives
                        </h6>

                        <!-- Sprint1 Feature 1.8.1 - Vérifier que applicationId existe -->
                        <BAlert v-if="!localApplicationId" variant="warning" :model-value="true">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Veuillez d'abord sauvegarder votre candidature (étapes 1-4) avant de téléverser des documents.
                        </BAlert>
                        <DocumentUploader
                            v-else
                            ref="documentUploaderRef"
                            :application-id="localApplicationId"
                            :existing-documents="[]"
                            @documents-updated="documentsComplete = true"
                        />
                    </div>

                    <!-- SECTION 6: Paiement (déplacé de 5 à 6) -->
                    <div v-if="currentStep === 6" class="form-section">
                        <h6 class="section-title">
                            <i class="bi bi-credit-card me-2"></i>
                            6. Paiement des frais de dossier
                        </h6>

                        <BAlert variant="info" :model-value="true" class="mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Frais de dossier :</strong> {{ formattedAmount }} € (paiement sécurisé via Stripe)
                        </BAlert>

                        <BRow class="g-3">
                            <BCol cols="12">
                                <label class="form-label-etatsup required">Carte bancaire</label>
                                <div ref="cardElement" class="stripe-card-element"></div>
                                <small class="text-muted mt-2 d-block">
                                    <i class="bi bi-lock-fill me-1"></i>
                                    Paiement 100% sécurisé avec chiffrement SSL
                                </small>
                            </BCol>

                            <BCol cols="12" v-if="errorMessage">
                                <BAlert variant="danger" :model-value="true">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    {{ errorMessage }}
                                </BAlert>
                            </BCol>

                            <BCol cols="12">
                                <div class="payment-summary p-3 bg-light rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">Total à payer</span>
                                        <span class="h4 mb-0 text-primary fw-bold">{{ formattedAmount }} €</span>
                                    </div>
                                </div>
                            </BCol>
                        </BRow>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="form-navigation">
                        <div class="left-buttons">
                            <BButton
                                v-if="currentStep > 1"
                                type="button"
                                variant="outline-secondary"
                                @click="prevStep"
                                class="btn-nav"
                            >
                                <i class="bi bi-arrow-left me-2"></i>
                                Précédent
                            </BButton>
                        </div>

                        <div class="right-buttons">
                            <BButton
                                type="button"
                                variant="outline-secondary"
                                @click="finalizeLater"
                                class="btn-save-draft"
                            >
                                <i class="bi bi-bookmark me-2"></i>
                                Finaliser plus tard
                            </BButton>

                            <!-- Étapes 1-4 : Bouton Suivant -->
                            <BButton
                                v-if="currentStep < totalSteps"
                                type="submit"
                                variant="primary"
                                class="btn-nav btn-next"
                            >
                                Suivant
                                <i class="bi bi-arrow-right ms-2"></i>
                            </BButton>

                            <!-- Étape finale : Bouton de soumission -->
                            <template v-if="currentStep === totalSteps">
                                <BButton
                                    type="submit"
                                    variant="primary"
                                    :disabled="processing"
                                    class="btn-submit-etatsup"
                                >
                                    <span v-if="processing">
                                        <span class="spinner-border spinner-border-sm me-2"></span>
                                        Envoi en cours...
                                    </span>
                                    <span v-else>
                                        <i class="bi bi-send me-2"></i>
                                        Soumettre ma candidature
                                    </span>
                                </BButton>

                                <!-- V2: Paiement en ligne (désactivé pour V1)
                                <BButton
                                    type="button"
                                    variant="outline-secondary"
                                    disabled
                                    class="btn-payment-disabled"
                                    v-b-tooltip.hover
                                    title="Fonctionnalité de paiement disponible prochainement"
                                >
                                    <i class="bi bi-credit-card me-2"></i>
                                    Payer maintenant
                                    <span class="badge bg-warning ms-2">V2</span>
                                </BButton>
                                -->
                            </template>
                        </div>
                    </div>
                </form>
            </div>
        </BCardBody>
    </BCard>
</template>

<style scoped>
/* Design Perfect Pixel EtatSup - Multi-étapes */
.shadow-diplomeo {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    overflow: hidden;
}

.card-header-etatsup {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    padding: 1.25rem 1.5rem;
    border: none;
}

/* Stepper */
.stepper-container {
    padding: 2rem 1.5rem 1rem;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}

.stepper-progress {
    height: 4px;
    background: #e5e7eb;
    border-radius: 2px;
    margin-bottom: 2rem;
    overflow: hidden;
}

.stepper-progress-bar {
    height: 100%;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    transition: width 0.3s ease;
}

.stepper-steps {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.stepper-step {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.stepper-step.completed {
    cursor: pointer;
}

.stepper-step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5e7eb;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.stepper-step.active .stepper-step-circle {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    color: white;
    transform: scale(1.1);
}

.stepper-step.completed .stepper-step-circle {
    background: #10b981;
    color: white;
}

.stepper-step-label {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 600;
    text-align: center;
}

.stepper-step.active .stepper-step-label {
    color: #1e3a8a;
}

.stepper-step.completed .stepper-step-label {
    color: #10b981;
}

/* Form Content */
.form-content {
    padding: 2rem 1.5rem;
}

.form-section {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e5e7eb;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.form-label-etatsup {
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-label-etatsup.required::after {
    content: " *";
    color: #dc2626;
}

.form-control-etatsup {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.2s ease;
}

.form-control-etatsup:focus {
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.info-box {
    background: #e8f4fd;
    border-left: 4px solid #1e3a8a;
    padding: 1rem;
    border-radius: 8px;
    color: #1a202c;
}

/* Documents */
.documents-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.document-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}

.document-item i {
    font-size: 1.5rem;
}

.document-item span:nth-child(2) {
    flex: 1;
    font-weight: 500;
    color: #1a202c;
}

.document-item.disabled-item {
    opacity: 0.6;
    background: #fafafa;
}

.document-item.disabled-item span:nth-child(2) {
    color: #94a3b8;
}

.upload-placeholder {
    text-align: center;
    padding: 3rem 2rem;
    background: #f8fafc;
    border: 2px dashed #e5e7eb;
    border-radius: 12px;
}

.upload-placeholder-disabled {
    text-align: center;
    padding: 3rem 2rem;
    background: #fafafa;
    border: 2px dashed #e5e7eb;
    border-radius: 12px;
    opacity: 0.7;
}

/* Navigation */
.form-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

.left-buttons,
.right-buttons {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.final-buttons {
    display: flex;
    gap: 1rem;
    flex: 1;
    justify-content: flex-end;
}

/* Uniformisation des boutons */
.btn-nav,
.btn-next,
.btn-submit-etatsup,
.btn-save-draft {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-next {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    border: none;
}

.btn-next:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-submit-etatsup {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    border: none;
    font-size: 1.05rem;
}

.btn-submit-etatsup:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.btn-submit-etatsup:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.btn-save-draft {
    border: 2px solid #64748b;
    color: #64748b;
    background: white;
}

.btn-save-draft:hover:not(:disabled) {
    background: #f8fafc;
    border-color: #1e3a8a;
    color: #1e3a8a;
}

.btn-submit-payment {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    border: none;
    border-radius: 50px;
    padding: 0.875rem 2rem;
    font-weight: 600;
    font-size: 1.05rem;
    transition: all 0.3s ease;
}

.btn-submit-payment:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.btn-payment-disabled {
    padding: 0.875rem 1.5rem;
    font-weight: 600;
    border-radius: 8px;
    opacity: 0.5;
    cursor: not-allowed;
}

/* Stripe Card Element */
.stripe-card-element {
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    transition: all 0.2s ease;
}

.stripe-card-element:focus-within {
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.payment-summary {
    border: 2px solid #1e3a8a;
}

/* Responsive */
@media (max-width: 768px) {
    .stepper-steps {
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }

    .stepper-step {
        flex: 0 0 80px;
    }

    .stepper-step-label {
        font-size: 0.65rem;
    }

    .stepper-step-circle {
        width: 35px;
        height: 35px;
        font-size: 0.9rem;
    }

    .form-content {
        padding: 1.5rem 1rem;
    }

    /* Responsive pour les boutons de navigation */
    .form-navigation {
        flex-direction: column;
        gap: 1rem;
        padding: 1.5rem 1rem;
    }

    .left-buttons,
    .right-buttons {
        width: 100%;
        flex-direction: column;
        gap: 0.75rem;
    }

    .btn-nav,
    .btn-next,
    .btn-save-draft,
    .btn-submit-etatsup {
        width: 100%;
        text-align: center;
        justify-content: center;
    }

    /* Ordre des boutons sur mobile */
    .right-buttons {
        display: flex;
        flex-direction: column-reverse; /* Bouton principal en premier visuellement */
    }
}
</style>
