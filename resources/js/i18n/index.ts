// resources/js/i18n/index.ts
export interface I18nInstance {
  global: {
    t: (key: string, params?: any) => string;
  };
}

// Simple mock i18n for now - you can replace this with vue-i18n later
const i18n: I18nInstance = {
  global: {
    t: (key: string, params?: any) => {
      // Simple fallback translations
      const translations: Record<string, string> = {
        'validation.required': 'Ce champ est requis',
        'validation.minLength': 'Ce champ doit contenir au moins {length} caractères',
        'validation.maxLength': 'Ce champ ne peut pas dépasser {length} caractères',
        'validation.email': 'Veuillez saisir une adresse email valide',
      };

      let message = translations[key] || key;

      // Simple parameter replacement
      if (params) {
        Object.keys(params).forEach(param => {
          message = message.replace(`{${param}}`, params[param]);
        });
      }

      return message;
    }
  }
};

export default i18n;