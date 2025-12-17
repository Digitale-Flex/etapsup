<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;

class PaymentService
{
    protected ?StripeClient $stripe = null;

    /**
     * Get or create the Stripe client (lazy initialization)
     */
    protected function getStripeClient(): StripeClient
    {
        if ($this->stripe === null) {
            $secret = config('cashier.secret');
            if (empty($secret)) {
                throw new \RuntimeException('Stripe secret key is not configured. Please set STRIPE_SECRET in your .env file.');
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
     * Returns null if Stripe is not configured
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
