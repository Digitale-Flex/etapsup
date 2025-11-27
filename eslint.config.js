import globals from "globals";
import pluginJs from "@eslint/js";
import tseslint from "typescript-eslint";
import pluginVue from "eslint-plugin-vue";
import unusedImports from "eslint-plugin-unused-imports";


/** @type {import('eslint').Linter.Config[]} */
export default [
    {
        files: ["**/*.{js,mjs,cjs,ts,vue}"],
        plugins: {
            'unused-imports': unusedImports
        },
        rules: {
            'no-unused-vars': 'error',        // Pour JavaScript
            '@typescript-eslint/no-unused-vars': 'error',  // Pour TypeScript
            'vue/no-unused-vars': 'error',    // Pour Vue
            'unused-imports/no-unused-imports': 'error',   // Pour les imports
            'unused-imports/no-unused-vars': [
                'error',
                {
                    "vars": "all",
                    "varsIgnorePattern": "^_",
                    "args": "after-used",
                    "argsIgnorePattern": "^_"
                }
            ]
        }
    },
    {files: ["**/*.js"], languageOptions: {sourceType: "commonjs"}},
    {languageOptions: { globals: globals.browser }},
    pluginJs.configs.recommended,
    ...tseslint.configs.recommended,
    ...pluginVue.configs["flat/essential"],
    {
        files: ["**/*.vue"],
        languageOptions: {
            parserOptions: {parser: tseslint.parser}
        }
    }
];
