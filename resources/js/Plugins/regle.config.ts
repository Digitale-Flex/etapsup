import { createRule, defineRegleConfig } from '@regle/core';
import { email, minLength, required, sameAs, withMessage } from '@regle/rules';

export const { useRegle: useCustomRegle } = defineRegleConfig({
    rules: () => {
        return {
            required: withMessage(required, 'Ce champ est obligatoire'),
            email: withMessage(email, 'Veuillez entrer un email valide'),
            minLength: withMessage(minLength, ({ $params: [min] }) => `Doit contenir au moins ${min} caractères`),
            sameAs: withMessage(sameAs, ({ $params: [other] }) => `Doit correspondre au champ "${other}"`),

            // Ajout de règles personnalisées (exemple)
            passwordStrength: createRule({
                validator: (value) => /^(?=.*[A-Z])(?=.*\d).{8,}$/.test(value),
                message: 'Le mot de passe doit contenir 8 caractères, une majuscule et un chiffre',
            }),
        };
    },

    // Configuration des modificateurs globaux
    modifiers: {
        autoDirty: true,
        lazy: true,
        rewardEarly: false,
    },
});
