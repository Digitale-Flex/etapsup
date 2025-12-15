<script setup lang="ts">
import { useCustomSearchStore } from '@/Stores/customSearch';
import { Country, Partner, RentalDeposit } from '@/Types/index';
import axios from 'axios';
import { BIconPencil } from 'bootstrap-icons-vue';
import { storeToRefs } from 'pinia';
import { computed, ref } from 'vue';

interface Props {
    partners: Partner[];
    rentalDeposits: RentalDeposit[];
    countries: Country[];
}

defineProps<Props>();
const store = useCustomSearchStore();

const { r$ } = storeToRefs(store);
const form = computed(() => r$.value.$value);

const isValidating = ref(false);
const coupon = ref('');
const couponError = ref('');
const couponSuccess = ref('');
const discountedAmount = ref('');
const couponId = ref<number | null>(null);
const onSubmit = async () => {
    isValidating.value = true;
    couponError.value = '';
    couponSuccess.value = '';
    try {
        if (!form.value.partner_id) {
            couponError.value = 'Veuillez sélectionner un partenaire';
            return;
        }

        const response = await axios.post(route('coupon.validate'), {
            code: coupon,
            partner_id: form.value.partner_id,
        });

        if (response.data.valid) {
            couponId.value = response.data.id;
            discountedAmount.value = response.data.amount;
            // form.coupon_id = true;
            couponSuccess.value = `Coupon valide! Montant a payer: ${response.data.amount}€`;
        } else {
            couponError.value = response.data.message || 'Coupon invalide';
        }
    } catch (e: any) {
        console.log(e);
        // form.is_coupon_valid = false;
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
    <b-card no-body class="shadow">
        <b-card-header class="border-bottom">
            <b-card-title class="d-flex mb-0">
                <BIconPencil class="mb-2 me-1" />
                Informations sur la demande
            </b-card-title>
        </b-card-header>

        <b-card-body class="p-4">
            <div class="row g-4">
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex flex-column">
                        <label for="country_birth_id"
                            >Quel partenaire ? *</label
                        >
                        <Select
                            input-id="country_birth_id"
                            v-model="r$.$value.partner_id"
                            :options="partners"
                            :disabled="store.processing"
                            :invalid="r$.$fields.partner_id.$error"
                            filter
                            option-label="name"
                            option-value="id"
                            placeholder="Choisissez un partenaire"
                        />
                        <Message
                            v-if="r$.$fields.partner_id.$error"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ r$.$fields.partner_id.$errors[0] }}
                        </Message>
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex flex-column">
                        <label for="country_id">Pays de residence *</label>
                        <Select
                            input-id="country_id"
                            v-model="r$.$value.country_id"
                            :options="countries"
                            :disabled="store.processing"
                            :invalid="r$.$fields.country_id.$error"
                            filter
                            option-label="name"
                            option-value="id"
                            placeholder="Pays de residence"
                        />
                        <Message
                            v-if="r$.$fields.country_id.$error"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ r$.$fields.country_id.$errors[0] }}
                        </Message>
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex flex-column">
                        <label for="rental_deposit_ids"
                            >Frais de dossier *</label
                        >
                        <MultiSelect
                            input-id="country_id"
                            v-model="r$.$value.rental_deposit_ids"
                            :options="rentalDeposits"
                            :disabled="store.processing"
                            :invalid="r$.$fields.rental_deposit_ids.$error"
                            :max-selected-labels="2"
                            option-label="name"
                            option-value="id"
                            placeholder="Frais de dossier"
                        />
                        <Message
                            v-if="r$.$fields.rental_deposit_ids.$error"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ r$.$fields.rental_deposit_ids.$errors[0] }}
                        </Message>
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex flex-column">
                        <label for="budget">Budget maximal *</label>
                        <div class="flex items-center">
                            <input-number
                                id="budget"
                                v-model="r$.$value.budget"
                                :disabled="store.processing"
                                :invalid="r$.$fields.budget.$error"
                                ode="currency"
                                currency="EUR"
                                locale="fr-FR"
                                placeholder="Budget maximal"
                                fluid
                            />
                        </div>
                        <Message
                            v-if="r$.$fields.budget.$error"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ r$.$fields.budget.$errors[0] }}
                        </Message>
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex flex-column">
                        <label for="on_start">Rentrée souhaitée</label>
                        <Calendar
                            input-id="on_start"
                            v-model="r$.$value.rental_start"
                            :disabled="store.processing"
                            :invalid="r$.$fields.rental_start.$error"
                            showIcon
                            iconDisplay="input"
                        />
                        <Message
                            v-if="r$.$fields.rental_start.$error"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ r$.$fields.rental_start.$errors[0] }}
                        </Message>
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex flex-column">
                        <label for="on_duration">Durée du programme</label>
                        <input-number
                            input-id="on_duration"
                            v-model="r$.$value.duration"
                            :disabled="store.processing"
                            :invalid="r$.$fields.duration.$error"
                            suffix=" mois"
                            placeholder="A partir de 6 mois"
                        />
                        <Message
                            v-if="r$.$fields.duration.$error"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ r$.$fields.duration.$errors[0] }}
                        </Message>
                    </div>
                </b-col>
                <b-col lg="6">
                    <div class="d-flex flex-column">
                        <label for="note">Note complémentaire</label>
                        <input-text
                            id="note"
                            v-model="r$.$value.note"
                            :disabled="store.processing"
                            fluid
                        />
                    </div>
                </b-col>
            </div>
        </b-card-body>
    </b-card>
</template>

<style scoped></style>
