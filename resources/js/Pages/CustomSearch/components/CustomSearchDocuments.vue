<script setup lang="ts">
import { useCustomSearchStore } from '@/Stores/customSearch';
import { BIconFileEarmarkCheck } from 'bootstrap-icons-vue';
import { storeToRefs } from 'pinia';

const store = useCustomSearchStore();
const { r$ } = storeToRefs(store);
</script>

<template>
    <b-card no-body class="shadow">
        <b-card-header class="border-bottom">
            <b-card-title class="mb-0">
                <BIconFileEarmarkCheck class="mb-1" />
                Documents disponibles (optionnel)
            </b-card-title>
        </b-card-header>

        <b-card-body class="p-4">
            <p class="text-muted mb-4">
                Indiquez les documents que vous avez déjà en votre possession.
                Ces informations sont optionnelles et nous aideront à mieux vous accompagner.
            </p>
            <div class="row g-4">
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex align-items-center gap-2">
                        <Checkbox
                            input-id="has_diploma"
                            v-model="r$.$value.has_diploma"
                            :disabled="store.processing"
                            :binary="true"
                        />
                        <label for="has_diploma" class="ms-2">
                            Dernier diplôme obtenu
                        </label>
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex align-items-center gap-2">
                        <Checkbox
                            input-id="has_transcript"
                            v-model="r$.$value.has_transcript"
                            :disabled="store.processing"
                            :binary="true"
                        />
                        <label for="has_transcript" class="ms-2">
                            Relevés de notes
                        </label>
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex align-items-center gap-2">
                        <Checkbox
                            input-id="has_cv"
                            v-model="r$.$value.has_cv"
                            :disabled="store.processing"
                            :binary="true"
                        />
                        <label for="has_cv" class="ms-2">
                            CV
                        </label>
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex align-items-center gap-2">
                        <Checkbox
                            input-id="has_motivation_letter"
                            v-model="r$.$value.has_motivation_letter"
                            :disabled="store.processing"
                            :binary="true"
                        />
                        <label for="has_motivation_letter" class="ms-2">
                            Lettre de motivation
                        </label>
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex align-items-center gap-2">
                        <Checkbox
                            input-id="has_conduct_certificate"
                            v-model="r$.$value.has_conduct_certificate"
                            :disabled="store.processing"
                            :binary="true"
                        />
                        <label for="has_conduct_certificate" class="ms-2">
                            Attestation de bonne vie et moeurs
                        </label>
                    </div>
                </b-col>
            </div>
        </b-card-body>
    </b-card>
</template>

<style scoped></style>
