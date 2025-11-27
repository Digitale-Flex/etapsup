/* eslint-env node */
require("@rushstack/eslint-patch/modern-module-resolution")

module.exports = {
    root: true,
    extends: [
        "plugin:vue/vue3-essential",
        "eslint:recommended",
        "@vue/eslint-config-typescript",
        "@vue/eslint-config-prettier"
    ],
    parserOptions: {
        ecmaVersion: "latest"
    },
    rules: {
        "vue/multi-word-component-names": "off",
        "no-undef": "off",
        // Règles pour détecter et supprimer les variables/imports inutilisés
        "no-unused-vars": "error",
        "@typescript-eslint/no-unused-vars": ["error", {
            "argsIgnorePattern": "^_",
            "varsIgnorePattern": "^_",
            "ignoreRestSiblings": true
        }],
        "vue/no-unused-vars": "error",
        "no-unused-imports": "error",
        // Règle optionnelle pour organiser automatiquement les imports
        "import/order": ["error", {
            "groups": ["builtin", "external", "internal", "parent", "sibling", "index"],
            "newlines-between": "always",
            "alphabetize": { "order": "asc" }
        }]
    }
}
