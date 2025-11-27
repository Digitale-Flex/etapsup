import { loadStripe, Stripe, StripeCardElement } from '@stripe/stripe-js';
import axios from 'axios';
import { onMounted, ref } from 'vue';

export default function useStripePayment(stripeKey: string) {
    const stripe = ref<Stripe | null>(null);
    const cardElement = ref<HTMLElement | null>(null);
    const card = ref<StripeCardElement | null>(null);
    const processing = ref(false);
    const errorMessage = ref<string | null>(null);
    const intent = ref<string>('');

    // Initialisation de Stripe
    const initStripe = async () => {
        stripe.value = await loadStripe(stripeKey);
        if (!stripe.value) return;
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
        if (cardElement.value) {
            card.value.mount(cardElement.value);
        }
    };

    // Rafraîchir l'intent de setup
    const refreshSetupIntent = async () => {
        try {
            const response = await axios.get(
                route('certificate.stripe.refresh-intent'),
            );
            intent.value = response.data.intent;
            return intent.value;
        } catch (error) {
            errorMessage.value =
                'Erreur lors du rafraîchissement du setup intent';
            throw error;
        }
    };

    // Traitement du paiement
    const processPayment = async (
        payload: any,
        createIntentRoute: string,
        confirmRoute: string,
        successRoute: string,
    ) => {
        try {
            processing.value = true;
            errorMessage.value = null;

            // 1. Créer le PaymentIntent côté serveur
            const createIntentResponse = await axios.post(
                createIntentRoute,
                payload,
            );
            const clientSecret = createIntentResponse.data.client_secret;
            const entityId = createIntentResponse.data.id; // Peut être certificate_id, subscription_id, etc.

            // 2. Confirmer le paiement avec Stripe.js
            if (!stripe.value || !card.value) {
                throw new Error('Stripe not initialized');
            }
            const { error, paymentIntent } =
                await stripe.value.confirmCardPayment(clientSecret, {
                    payment_method: {
                        card: card.value,
                        billing_details: {
                            name:
                                payload.name ||
                                `${payload.surname} ${payload.name}`,
                            email: payload.email,
                        },
                    },
                });

            if (error) {
                errorMessage.value = error.message || 'Erreur lors du paiement';
                return { success: false, error: error.message };
            }

            // 3. Si le paiement est réussi, confirmer côté serveur
            if (paymentIntent.status === 'succeeded') {
                await axios.post(confirmRoute, { id: entityId });
                return { success: true, id: entityId, redirect: successRoute };
            }

            return { success: false, error: "Le paiement n'a pas abouti" };
        } catch (error: any) {
            errorMessage.value =
                error.response?.data?.message ||
                'Une erreur inattendue est survenue';
            return { success: false, error: error.message };
        } finally {
            processing.value = false;
        }
    };

    onMounted(initStripe);

    return {
        stripe,
        cardElement,
        card,
        processing,
        errorMessage,
        intent,
        initStripe,
        refreshSetupIntent,
        processPayment,
    };
}
