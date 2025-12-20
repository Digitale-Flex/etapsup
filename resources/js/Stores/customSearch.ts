import { useCustomRegle } from '@/Plugins/regle.config';
import { RegleExternalErrorTree } from '@regle/core';
import { email, minLength, required } from '@regle/rules';
import { defineStore, skipHydrate } from 'pinia';
import { ref } from 'vue';

// Interface pour typer notre état de formulaire
interface FormState {
    property_type_ids: string[];
    layout_ids: string[];
    category_id: string;
    city_id: string;
    destination_country_ids: string[]; // Pays visés (multi-sélection)
    name: string;
    surname: string;
    email: string;
    phone: string;
    place_birth: string;
    date_birth: Date | null;
    nationality: string;
    passport_number: string;
    country_birth_id: string;
    country_id: string;
    partner_id: string;
    coupon_id: string;
    paid: number;
    rental_deposit_ids: string[];
    budget: number | null;
    rental_start: Date | null;
    duration: number | null;
    note: string;
    // 10 nouveaux champs questionnaire
    gender: string;
    passport_expiry_date: Date | null;
    address: string;
    current_level_id: string;
    preferred_language: string;
    has_campus_france_experience: boolean;
    has_diploma: boolean;
    has_transcript: boolean;
    has_cv: boolean;
    has_motivation_letter: boolean;
    has_conduct_certificate: boolean;
}

export const useCustomSearchStore = defineStore('customSearchStore', () => {
    const processing = ref(false);

    // État initial du formulaire
    const initialFormState: FormState = {
        property_type_ids: [],
        layout_ids: [],
        category_id: '',
        city_id: '',
        destination_country_ids: [], // Pays visés (multi-sélection)
        name: '',
        surname: '',
        email: '',
        phone: '',
        place_birth: '',
        date_birth: null,
        nationality: '',
        passport_number: '',
        country_birth_id: '',
        country_id: '',
        partner_id: '',
        coupon_id: '',
        paid: 100,
        rental_deposit_ids: [],
        budget: null,
        rental_start: null,
        duration: null,
        note: '',
        // 10 nouveaux champs questionnaire
        gender: '',
        passport_expiry_date: null,
        address: '',
        current_level_id: '',
        preferred_language: '',
        has_campus_france_experience: false,
        has_diploma: false,
        has_transcript: false,
        has_cv: false,
        has_motivation_letter: false,
        has_conduct_certificate: false,
    };

    // Définition des erreurs externes avec le bon typage
    const externalErrors = ref<RegleExternalErrorTree<FormState>>({});

    const { r$ } = useCustomRegle(
        initialFormState,
        {
            // Sélection critères (requis)
            property_type_ids: { required },
            layout_ids: { required },
            category_id: { required },
            destination_country_ids: { required }, // Pays ciblés (remplace Ville)

            // Informations personnelles (requis)
            name: { required, minLength: minLength(2) },
            surname: { required, minLength: minLength(2) },
            phone: { required, minLength: minLength(8) },
            place_birth: { required, minLength: minLength(3) },
            date_birth: { required },
            nationality: { required, minLength: minLength(4) },
            passport_number: { required, minLength: minLength(5) },
            budget: { required },
            rental_start: { required },
            duration: { required },

            // Informations demande (requis)
            partner_id: { required },
            rental_deposit_ids: { required },

            // Champs optionnels (pas de validation required)
            email: { email }, // optionnel mais doit être valide si rempli
            country_birth_id: {}, // optionnel
            country_id: {}, // optionnel
            city_id: {}, // optionnel (remplacé par destination_country_ids)
            paid: {}, // optionnel

            // 10 nouveaux champs questionnaire (tous optionnels sauf gender)
            gender: {}, // optionnel - M ou F
            passport_expiry_date: {}, // optionnel
            address: {}, // optionnel
            current_level_id: {}, // optionnel
            preferred_language: {}, // optionnel - FR ou EN
            has_campus_france_experience: {}, // optionnel
            has_diploma: {}, // optionnel
            has_transcript: {}, // optionnel
            has_cv: {}, // optionnel
            has_motivation_letter: {}, // optionnel
            has_conduct_certificate: {}, // optionnel
        },
        {
            externalErrors, // Passage des erreurs externes
            // rewardEarly: true, // Optionnel : validation en temps réel
        },
    );

    // Méthode pour définir les erreurs externes (typiquement après un échec de soumission)
    const setExternalErrors = (errors: Record<string, string | string[]>) => {
        // Conversion des erreurs du serveur au format Regle
        const formattedErrors: RegleExternalErrorTree<FormState> = {};

        Object.entries(errors).forEach(([field, messages]) => {
            const errorMessages = Array.isArray(messages)
                ? messages
                : [messages];
            // @ts-ignore - Assignation dynamique des erreurs
            formattedErrors[field as keyof FormState] = errorMessages;
        });

        externalErrors.value = formattedErrors;
    };

    // Méthode pour effacer les erreurs externes
    const clearExternalErrors = () => {
        externalErrors.value = {};
        r$.$clearExternalErrors();
    };

    // Méthode pour effacer une erreur externe spécifique
    const clearFieldError = (fieldName: keyof FormState) => {
        if (externalErrors.value[fieldName]) {
            delete externalErrors.value[fieldName];
            // Force la réactivité
            externalErrors.value = { ...externalErrors.value };
        }
    };

    // Méthode pour réinitialiser le formulaire
    const resetForm = () => {
        Object.assign(r$.$value, initialFormState);
        clearExternalErrors();
        processing.value = false;
    };

    // Méthode pour pré-remplir avec des données utilisateur
    const prefillUserData = (user: any) => {
        if (user) {
            r$.$value.name = user.name || '';
            r$.$value.surname = user.surname || '';
            r$.$value.email = user.email || '';
            r$.$value.phone = user.phone || '';
            r$.$value.place_birth = user.place_birth || '';
            r$.$value.date_birth = user.date_birth ? new Date(user.date_birth) : null;
            r$.$value.nationality = user.nationality || '';
            r$.$value.passport_number = user.passport_number || '';
            r$.$value.country_birth_id = user.country_birth_id || '';
            r$.$value.country_id = user.country_id || '';
            // Nouveaux champs
            r$.$value.gender = user.gender || '';
            r$.$value.passport_expiry_date = user.passport_expiry_date ? new Date(user.passport_expiry_date) : null;
            r$.$value.address = user.address || '';
        }
    };

    // Méthode pour valider et obtenir les erreurs
    const validateForm = async () => {
        await r$.$validate();
        return {
            isValid: !r$.$invalid,
            errors: r$.$errors,
            externalErrors: externalErrors.value,
        };
    };

    return {
        r$: skipHydrate(r$),
        processing,
        externalErrors,
        setExternalErrors,
        clearExternalErrors,
        clearFieldError,
        resetForm,
        prefillUserData,
        validateForm,
    };
});
