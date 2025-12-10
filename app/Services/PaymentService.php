<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;

class PaymentService
{
    protected ?StripeClient $stripe = null;

    /**
     * Lazy initialization du client Stripe
     * Évite l'erreur si STRIPE_SECRET n'est pas configuré
     */
    protected function getStripeClient(): StripeClient
    {
        if ($this->stripe === null) {
            $secret = config('cashier.secret');

            if (empty($secret)) {
                throw new \RuntimeException(
                    'Stripe non configuré: STRIPE_SECRET manquant dans .env'
                );
            }

            $this->stripe = new StripeClient($secret);
        }

        return $this->stripe;
    }

    public function createPaymentIntent(
        User $user,
        float $amount,
        string $currency = 'eur',
        array $metadata = [],
        string $description = ''
    ): array {
        try {
            DB::beginTransaction();

            // Create/get Stripe customer
            $stripeCustomer = $user->createOrGetStripeCustomer();
            $user->refresh();

            // Create PaymentIntent
            $paymentIntent = $this->getStripeClient()->paymentIntents->create([
                'amount' => $this->convertToCents($amount),
                'currency' => $currency,
                'customer' => $user->stripe_id,
                'description' => $description,
                'metadata' => $metadata,
                'confirmation_method' => 'automatic',
                'confirm' => false,
            ]);

            DB::commit();

            return [
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $amount,
                'currency' => $currency,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function confirmPayment(string $paymentIntentId): array
    {
        $paymentIntent = $this->getStripeClient()->paymentIntents->retrieve($paymentIntentId);

        if ($paymentIntent->status === 'succeeded') {
            return [
                'success' => true,
                'payment_intent' => $paymentIntent,
            ];
        }

        throw new \Exception('Payment not in "succeeded" state. Current status: ' . $paymentIntent->status);
    }

    /**
     * Create a setup intent for frontend
     * Retourne null si Stripe n'est pas configuré
     */
    public function createSetupIntent(User $user): ?string
    {
        if (empty(config('cashier.secret'))) {
            return null;
        }

        $intent = $user->createSetupIntent([
            'payment_method_types' => ['card'],
        ]);

        return $intent->client_secret;
    }

    /**
     * Vérifie si Stripe est configuré
     */
    public function isConfigured(): bool
    {
        return !empty(config('cashier.secret')) && !empty(config('cashier.key'));
    }

    /**
     * Refund a payment
     */
    public function refundPayment(string $paymentIntentId, ?float $amount = null): array
    {
        $params = ['payment_intent' => $paymentIntentId];
        if ($amount !== null) {
            $params['amount'] = $this->convertToCents($amount);
        }

        $refund = $this->getStripeClient()->refunds->create($params);

        return [
            'refund_id' => $refund->id,
            'status' => $refund->status,
            'amount' => $refund->amount / 100,
        ];
    }

    protected function convertToCents(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
