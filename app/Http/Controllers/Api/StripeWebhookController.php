<?php

namespace App\Http\Controllers\Api;

// Sprint1 Feature 1.7.1 — Module Paiement (Stripe + frais_dossier)

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

/**
 * Contrôleur pour les webhooks Stripe
 *
 * Feature 1.7.1 — Module Paiement (Stripe + frais_dossier)
 *
 * Gère les événements Stripe, notamment checkout.session.completed
 * pour mettre à jour automatiquement le statut de paiement des candidatures.
 */
class StripeWebhookController extends Controller
{
    /**
     * Gérer les webhooks Stripe
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('cashier.webhook.secret');

        try {
            // Vérifier la signature Stripe (sécurité)
            if ($webhookSecret) {
                try {
                    $event = Webhook::constructEvent(
                        $payload,
                        $sigHeader,
                        $webhookSecret
                    );
                } catch (SignatureVerificationException $e) {
                    logger()->error('Signature Stripe invalide', [
                        'error' => $e->getMessage(),
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Signature invalide',
                    ], 400);
                }
            } else {
                // Mode développement sans signature (non recommandé en production)
                $event = json_decode($payload, true);
            }

            // Traiter l'événement selon son type
            switch ($event['type']) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event['data']['object']);
                    break;

                case 'payment_intent.succeeded':
                    $this->handlePaymentIntentSucceeded($event['data']['object']);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentIntentFailed($event['data']['object']);
                    break;

                case 'payment_intent.canceled':
                    $this->handlePaymentIntentCanceled($event['data']['object']);
                    break;

                default:
                    logger()->info('Événement Stripe non géré', [
                        'type' => $event['type'],
                    ]);
            }

            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            logger()->error('Erreur traitement webhook Stripe', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
            ], 500);
        }
    }

    /**
     * Gérer l'événement checkout.session.completed
     *
     * Met à jour le statut de la candidature et stocke le payment_intent_id
     *
     * @param array $session
     * @return void
     */
    private function handleCheckoutSessionCompleted(array $session): void
    {
        try {
            $metadata = $session['metadata'] ?? [];
            $applicationId = $metadata['application_id'] ?? null;
            $feeType = $metadata['fee_type'] ?? 'frais_dossier'; // Default: frais dossier

            if (!$applicationId) {
                logger()->warning('Application ID manquant dans metadata Stripe', [
                    'session_id' => $session['id'],
                ]);
                return;
            }

            $application = Application::find($applicationId);

            if (!$application) {
                logger()->error('Application introuvable', [
                    'application_id' => $applicationId,
                    'session_id' => $session['id'],
                ]);
                return;
            }

            // Récupérer le PaymentIntent ID
            $paymentIntentId = $session['payment_intent'] ?? null;

            // Feature 9 - Sprint 1: Différencier frais_dossier et accompagnement_premium
            if ($feeType === 'accompagnement_premium') {
                // Paiement accompagnement premium
                $application->update([
                    'accompagnement_premium' => true,
                    'accompagnement_paid' => true,
                    'accompagnement_stripe_payment_intent_id' => $paymentIntentId,
                ]);

                logger()->info('Accompagnement premium payé', [
                    'application_id' => $applicationId,
                    'payment_intent_id' => $paymentIntentId,
                    'session_id' => $session['id'],
                    'amount' => $session['amount_total'] / 100,
                ]);
            } else {
                // Paiement frais de dossier (comportement existant)
                $application->update([
                    'stripe_payment_intent_id' => $paymentIntentId,
                    'status' => 'paid',
                    'state' => 'paid',
                    'fees' => array_merge($application->fees ?? [], [
                        'frais_dossier_paid' => true,
                        'paid_at' => now()->toIso8601String(),
                        'payment_status' => 'succeeded',
                        'stripe_session_id' => $session['id'],
                        'amount_paid' => $session['amount_total'] / 100,
                    ]),
                ]);

                logger()->info('Frais de dossier payés', [
                    'application_id' => $applicationId,
                    'payment_intent_id' => $paymentIntentId,
                    'session_id' => $session['id'],
                    'amount' => $session['amount_total'] / 100,
                ]);
            }

        } catch (\Exception $e) {
            logger()->error('Erreur mise à jour candidature après paiement', [
                'session_id' => $session['id'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Gérer l'événement payment_intent.succeeded
     *
     * Sprint1 Feature 1.7.1 - Gestion webhook PaymentIntent réussi
     *
     * @param array $paymentIntent
     * @return void
     */
    private function handlePaymentIntentSucceeded(array $paymentIntent): void
    {
        try {
            $metadata = $paymentIntent['metadata'] ?? [];
            $applicationId = $metadata['application_id'] ?? null;

            if (!$applicationId) {
                logger()->info('PaymentIntent réussi sans application_id', [
                    'payment_intent_id' => $paymentIntent['id'],
                ]);
                return;
            }

            $application = Application::find($applicationId);

            if (!$application) {
                logger()->warning('Application introuvable pour PaymentIntent', [
                    'application_id' => $applicationId,
                    'payment_intent_id' => $paymentIntent['id'],
                ]);
                return;
            }

            // Ne mettre à jour que si pas déjà payé (idempotence)
            if ($application->status !== 'paid') {
                $application->update([
                    'status' => 'paid',
                    'state' => 'paid',
                    'fees' => array_merge($application->fees ?? [], [
                        'frais_dossier_paid' => true,
                        'paid_at' => now()->toIso8601String(),
                        'payment_status' => 'succeeded',
                        'amount_paid' => $paymentIntent['amount'] / 100,
                    ]),
                ]);

                logger()->info('Candidature mise à jour après PaymentIntent réussi', [
                    'application_id' => $applicationId,
                    'payment_intent_id' => $paymentIntent['id'],
                    'amount' => $paymentIntent['amount'] / 100,
                ]);
            }

        } catch (\Exception $e) {
            logger()->error('Erreur handlePaymentIntentSucceeded', [
                'payment_intent_id' => $paymentIntent['id'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Gérer l'événement payment_intent.payment_failed
     *
     * Sprint1 Feature 1.7.1 - Gestion webhook PaymentIntent échoué
     *
     * @param array $paymentIntent
     * @return void
     */
    private function handlePaymentIntentFailed(array $paymentIntent): void
    {
        try {
            $metadata = $paymentIntent['metadata'] ?? [];
            $applicationId = $metadata['application_id'] ?? null;

            if (!$applicationId) {
                logger()->warning('PaymentIntent échoué sans application_id', [
                    'payment_intent_id' => $paymentIntent['id'],
                ]);
                return;
            }

            $application = Application::find($applicationId);

            if (!$application) {
                logger()->warning('Application introuvable pour PaymentIntent échoué', [
                    'application_id' => $applicationId,
                    'payment_intent_id' => $paymentIntent['id'],
                ]);
                return;
            }

            $errorMessage = $paymentIntent['last_payment_error']['message'] ?? 'Paiement refusé';
            $declineCode = $paymentIntent['last_payment_error']['decline_code'] ?? 'unknown';

            $application->update([
                'status' => 'rejected',
                'state' => 'rejected',
                'fees' => array_merge($application->fees ?? [], [
                    'payment_status' => 'failed',
                    'rejection_reason' => $errorMessage,
                    'decline_code' => $declineCode,
                    'failed_at' => now()->toIso8601String(),
                ]),
            ]);

            logger()->warning('Candidature marquée comme rejetée (paiement échoué)', [
                'application_id' => $applicationId,
                'payment_intent_id' => $paymentIntent['id'],
                'error' => $errorMessage,
                'decline_code' => $declineCode,
            ]);

        } catch (\Exception $e) {
            logger()->error('Erreur handlePaymentIntentFailed', [
                'payment_intent_id' => $paymentIntent['id'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Gérer l'événement payment_intent.canceled
     *
     * Sprint1 Feature 1.7.1 - Gestion webhook PaymentIntent annulé
     *
     * @param array $paymentIntent
     * @return void
     */
    private function handlePaymentIntentCanceled(array $paymentIntent): void
    {
        try {
            $metadata = $paymentIntent['metadata'] ?? [];
            $applicationId = $metadata['application_id'] ?? null;

            if (!$applicationId) {
                logger()->info('PaymentIntent annulé sans application_id', [
                    'payment_intent_id' => $paymentIntent['id'],
                ]);
                return;
            }

            $application = Application::find($applicationId);

            if (!$application) {
                logger()->warning('Application introuvable pour PaymentIntent annulé', [
                    'application_id' => $applicationId,
                    'payment_intent_id' => $paymentIntent['id'],
                ]);
                return;
            }

            $application->update([
                'status' => 'canceled',
                'state' => 'canceled',
                'fees' => array_merge($application->fees ?? [], [
                    'payment_status' => 'canceled',
                    'canceled_at' => now()->toIso8601String(),
                ]),
            ]);

            logger()->info('Candidature marquée comme annulée', [
                'application_id' => $applicationId,
                'payment_intent_id' => $paymentIntent['id'],
            ]);

        } catch (\Exception $e) {
            logger()->error('Erreur handlePaymentIntentCanceled', [
                'payment_intent_id' => $paymentIntent['id'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
