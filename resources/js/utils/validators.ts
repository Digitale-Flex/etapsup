// validators.ts
import i18n from '@/i18n'; // Importez l'instance i18n
import { createI18nMessage, helpers, required as vRequired, minLength as vMinLength, maxLength as vMaxLength, email as vEmail } from '@vuelidate/validators';

const { t } = i18n.global; // Accédez à la fonction de traduction

// Créez un wrapper pour les messages traduits
const withI18nMessage = createI18nMessage({ t });

// Définissez les validateurs avec des messages traduits
export const required = withI18nMessage(
    helpers.withMessage(
        t('validation.required'), // Utilisez la traduction
        vRequired,
    ),
);

export const minLength = (min: number) =>
    withI18nMessage(
        helpers.withMessage(
            t('validation.minLength', { length: min }), // Utilisez la traduction avec paramètres
            vMinLength(min),
        ),
    );

export const maxLength = (max: number) =>
    withI18nMessage(
        helpers.withMessage(
            t('validation.maxLength', { length: max }), // Utilisez la traduction avec paramètres
            vMaxLength(max),
        ),
    );

export const email = withI18nMessage(
    helpers.withMessage(
        t('validation.email'), // Utilisez la traduction
        vEmail,
    ),
);

// Validateur personnalisé pour la période
export const validPeriod = withI18nMessage(
    helpers.withMessage(
        t('validation.invalidPeriod'), // Utilisez la traduction
        (value: { start: Date; end: Date }) => {
            return value.start && value.end && value.start < value.end;
        },
    ),
);
