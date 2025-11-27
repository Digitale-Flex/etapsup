<script setup lang="ts">
import { useCustomSearchStore } from '@/Stores/customSearch';
import axios from 'axios';
import { storeToRefs } from 'pinia';
import { computed, ref } from 'vue';

// 1. Interface pour typer la réponse API
interface CouponValidationResponse {
    valid: boolean;
    id?: number;
    amount?: string;
    message?: string;
}

const store = useCustomSearchStore();
const { r$ } = storeToRefs(store);
const form = computed(() => r$.value.$value);

const processing = ref(false);
const coupon = ref('');
const couponError = ref('');
const couponSuccess = ref('');
const discountedAmount = ref('');

// 2. Gestion plus robuste des erreurs
const getErrorMessage = (error: unknown): string => {
    if (axios.isAxiosError(error)) {
        if (error.response) {
            return (
                error.response.data.message ||
                `Erreur serveur (${error.response.status})`
            );
        }
        return error.request ? 'Pas de réponse du serveur' : error.message;
    }
    return error instanceof Error ? error.message : 'Erreur inconnue';
};

// 3. Réinitialisation cohérente de l'état
const resetCouponState = () => {
    form.value.coupon_id = '';
    discountedAmount.value = '';
    couponSuccess.value = '';
    couponError.value = '';
    form.value.paid = 100;
};

const onSubmit = async () => {
    // 4. Réinitialisation avant la soumission
    processing.value = true;
    resetCouponState();

    try {
        const response = await axios.post<CouponValidationResponse>(
            route('coupon.validate'),
            {
                code: coupon.value,
                partner_id: form.value.partner_id,
            },
        );

        if (response.data.valid) {
            form.value.coupon_id = response.data.id?.toString() || '';
            discountedAmount.value = (Number(response.data.amount) || 0).toString();
            form.value.paid = Number(response.data.amount) || 0;
            couponSuccess.value = `Coupon valide! Montant à payer: ${response.data.amount}€`;
        } else {
            couponError.value = response.data.message || 'Coupon invalide';
        }
    } catch (error) {
        console.error('Erreur validation coupon:', error);
        couponError.value = getErrorMessage(error);
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <div>
        <div class="form-label d-flex justify-content-between">
            Code promo
            <div v-if="couponError || couponSuccess">
                <Message
                    v-if="couponError"
                    severity="error"
                    size="small"
                    variant="simple"
                    class="animate-fade-in"
                >
                    {{ couponError }}
                </Message>
                <Message
                    v-else-if="couponSuccess"
                    severity="success"
                    size="small"
                    variant="simple"
                    class="animate-fade-in"
                >
                    {{ couponSuccess }}
                </Message>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <input-text
                id="code"
                v-model.trim="coupon"
                :disabled="processing || !form.partner_id"
                name="code"
                minLength="3"
                fluid
                placeholder="Code promo"
                @keyup.enter="onSubmit"
            />
            <Button
                icon="pi pi-check"
                aria-label="validate"
                severity="contrast"
                :disabled="!form.partner_id || !coupon.trim()"
                :loading="processing"
                type="button"
                @click="onSubmit"
                class="px-4"
            />
        </div>
    </div>
</template>

<style scoped>
/* 9. Animation douce pour les messages */
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
