<?php

namespace App\Services;

use App\Enums\ReservationType;
use App\Jobs\RealEstate\GenerateContractJob;
use App\Models\RealEstate\Property;
use App\Models\RealEstate\Reservation;
use App\Models\User;
use App\States\Reservation\Confirmed;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Money\Money;
use Spatie\Period\Period;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;

class ReservationService
{
    /**
     * Traite le paiement Stripe.
     */
    public function processPayment(User $user, array $data): PaymentIntent
    {
        try {
            $user->createOrGetStripeCustomer();
            $user->addPaymentMethod($data['payment_method_id']);

            $amount = Money::EUR((int)($data['amount'] * 100));
            $amountInCents = $amount->getAmount();

            $payment = $user->charge($amountInCents, $data['payment_method_id'], [
                'currency' => 'eur',
                'confirm' => true,
                'payment_method_types' => ['card'],
                'description' => 'Réservation',
            ]);

            return $payment->asStripePaymentIntent();
        } catch (CardException|IncompletePayment $e) {
            Log::error('Erreur de paiement : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Crée une nouvelle réservation.
     */
    public function createReservation(array $data, Property $property, User $user, Period $period, PaymentIntent $payment): Reservation
    {
        $reservation =  Reservation::create([
            'property_id' => $property->id,
            'user_id' => $user->id,
            'start_date' => $period->start(),
            'end_date' => $period->end(),
            'guests' => $data['guests'],
            'reason' => $data['reason'],
            'status' => $data['status'],
            'price' => $data['amount'],
            'fees' => $data['fees'],
            'address' => $data['address'],
            'type' => ReservationType::Stay(),
            'stripe_payment_intent_id' => $payment->id,
        ]);

        $reservation->state->transitionTo(Confirmed::class);

        if (app()->environment('production')) {
            GenerateContractJob::dispatch($reservation->id)->onQueue('contracts');
        }

        return $reservation;
    }

    public function createPendingReservation(array $data, Property $property, User $user, Period $period): Reservation
    {
       return Reservation::create([
            'property_id' => $property->id,
            'user_id' => $user->id,
            'start_date' => $period->start(),
            'end_date' => $period->end(),
            'guests' => $data['month'],
            'reason' => $data['reason'],
            'status' => $data['status'],
            'price' => $data['amount'],
            'fees' => $data['fees'],
            'address' => $data['address'],
            'type' => ReservationType::Monthly(),
        ]);
    }
}
