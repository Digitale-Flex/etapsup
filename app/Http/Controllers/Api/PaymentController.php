<?php

namespace App\Http\Controllers\Api;

// Sprint1 Update: Feature 1.1.1 — Espace Étudiant (Connexion & Profil)
// Sprint1 Update: Feature 1.7.1 — Module Paiement (Stripe + frais_dossier)

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\StripeClient;
use Stripe\Checkout\Session;

/**
 * Contrôleur pour les paiements/factures Stripe de l'étudiant
 *
 * Feature 1.1.1 — Espace Étudiant (Connexion & Profil)
 * Endpoint: GET /api/v1/payments
 */
class PaymentController extends Controller
{
    /**
     * Liste des factures Stripe de l'étudiant connecté
     *
     * Critères d'acceptation :
     * - Afficher ID, établissement, formation, statut, date
     * - Lien vers receipt_url Stripe si disponible
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Vérifier si l'utilisateur a un compte Stripe
            if (!$user->stripe_id) {
                return response()->json([
                    'success' => true,
                    'payments' => [],
                ], 200);
            }

            // Récupérer les PaymentIntents Stripe via l'API
            $stripe = new StripeClient(config('cashier.secret'));

            $paymentIntents = $stripe->paymentIntents->all([
                'customer' => $user->stripe_id,
                'limit' => 50,
            ]);

            // Mapper les paiements pour le dashboard
            $payments = collect($paymentIntents->data)->map(function ($intent) {
                $metadata = $intent->metadata ?? new \stdClass();

                return [
                    'id' => $intent->id,
                    'amount' => $intent->amount / 100, // Convertir centimes en euros
                    'currency' => strtoupper($intent->currency),
                    'status' => $intent->status,
                    'status_label' => $this->getStatusLabel($intent->status),
                    'description' => $intent->description ?? 'Paiement',
                    'establishment' => $metadata->establishment ?? 'Non spécifié',
                    'created_at' => date('d/m/Y à H:i', $intent->created),
                    'receipt_url' => $this->getReceiptUrl($intent),
                ];
            });

            return response()->json([
                'success' => true,
                'payments' => $payments,
            ], 200);

        } catch (\Exception $e) {
            logger()->error('Erreur récupération paiements Stripe', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des paiements',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Obtenir le label français du statut de paiement
     *
     * @param string $status
     * @return string
     */
    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'succeeded' => 'Payé',
            'pending' => 'En attente',
            'processing' => 'En cours',
            'requires_payment_method' => 'Méthode de paiement requise',
            'requires_confirmation' => 'Confirmation requise',
            'requires_action' => 'Action requise',
            'canceled' => 'Annulé',
            'failed' => 'Échec',
            default => ucfirst($status),
        };
    }

    /**
     * Obtenir l'URL du reçu Stripe si disponible
     *
     * @param object $intent
     * @return string|null
     */
    private function getReceiptUrl(object $intent): ?string
    {
        // Si le PaymentIntent a une charge (charge), récupérer le receipt_url
        if (isset($intent->latest_charge) && !empty($intent->latest_charge)) {
            try {
                $stripe = new StripeClient(config('cashier.secret'));
                $charge = $stripe->charges->retrieve($intent->latest_charge);

                return $charge->receipt_url ?? null;
            } catch (\Exception $e) {
                logger()->warning('Impossible de récupérer le receipt_url', [
                    'charge_id' => $intent->latest_charge,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return null;
    }

    /**
     * Créer une session Stripe Checkout pour frais de dossier
     *
     * Feature 1.7.1 — Module Paiement (Stripe + frais_dossier)
     *
     * Critères d'acceptation :
     * - Paiement via Stripe Checkout (mode test)
     * - Montant = frais_dossier de l'établissement
     * - Après paiement → statut = "paid"
     * - Reçu Stripe accessible via receipt_url
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createCheckoutSession(Request $request): JsonResponse
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'application_id' => 'required|exists:reservations,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = $request->user();
            $application = Application::with('establishment')->findOrFail($request->application_id);

            // Vérifier que l'application appartient bien à l'utilisateur
            if ($application->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé à cette candidature',
                ], 403);
            }

            // Vérifier que l'établissement a des frais de dossier
            $establishment = $application->establishment;
            if (!$establishment || !$establishment->frais_dossier || $establishment->frais_dossier <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun frais de dossier configuré pour cet établissement',
                ], 400);
            }

            // Vérifier si déjà payé
            if ($application->stripe_payment_intent_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Les frais de dossier ont déjà été payés',
                ], 400);
            }

            // Créer ou récupérer le customer Stripe
            if (!$user->stripe_id) {
                $user->createAsStripeCustomer([
                    'name' => $user->name . ' ' . $user->surname,
                    'email' => $user->email,
                ]);
            }

            // Créer la session Stripe Checkout
            $stripe = new StripeClient(config('cashier.secret'));

            $checkoutSession = $stripe->checkout->sessions->create([
                'customer' => $user->stripe_id,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Frais de dossier - ' . $establishment->title,
                            'description' => 'Frais de candidature pour ' . $establishment->title,
                        ],
                        'unit_amount' => (int) ($establishment->frais_dossier * 100), // Convertir en centimes
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => url('/payment/success?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => url('/payment/cancel?application_id=' . $application->hashid),
                'metadata' => [
                    'application_id' => $application->id,
                    'establishment_id' => $establishment->id,
                    'establishment_name' => $establishment->title,
                    'user_id' => $user->id,
                    'fee_type' => 'frais_dossier',
                ],
            ]);

            logger()->info('Session Stripe Checkout créée', [
                'user_id' => $user->id,
                'application_id' => $application->id,
                'session_id' => $checkoutSession->id,
                'amount' => $establishment->frais_dossier,
            ]);

            return response()->json([
                'success' => true,
                'session_id' => $checkoutSession->id,
                'session_url' => $checkoutSession->url,
            ], 200);

        } catch (\Exception $e) {
            logger()->error('Erreur création session Stripe Checkout', [
                'user_id' => $request->user()?->id,
                'application_id' => $request->application_id ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la session de paiement',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Créer une session Stripe Checkout pour accompagnement premium
     *
     * Feature 9 — Accompagnement premium personnalisé (299€)
     *
     * Critères d'acceptation :
     * - Montant fixe : 299€
     * - Paiement via Stripe Checkout
     * - Vérification ownership de la candidature
     * - Empêcher paiement si déjà payé
     *
     * @param Request $request
     * @param Application $application
     * @return JsonResponse
     */
    public function createAccompagnementCheckout(Request $request, Application $application): JsonResponse
    {
        try {
            $user = $request->user();

            // Vérifier ownership
            if ($application->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé à cette candidature',
                ], 403);
            }

            // Vérifier si déjà payé
            if ($application->isAccompagnementPaid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'L\'accompagnement premium a déjà été payé',
                ], 400);
            }

            // Créer ou récupérer le customer Stripe
            if (!$user->stripe_id) {
                $user->createAsStripeCustomer([
                    'name' => $user->name . ' ' . $user->surname,
                    'email' => $user->email,
                ]);
            }

            // Constante : montant accompagnement premium
            $accompagnementPrice = 299; // €

            // Créer la session Stripe Checkout
            $stripe = new StripeClient(config('cashier.secret'));
            $establishment = $application->establishment;

            $checkoutSession = $stripe->checkout->sessions->create([
                'customer' => $user->stripe_id,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Accompagnement Premium - ' . ($establishment?->title ?? 'Candidature'),
                            'description' => 'Accompagnement personnalisé pour votre candidature : conseils CV, lettre motivation, préparation entretiens',
                        ],
                        'unit_amount' => (int) ($accompagnementPrice * 100), // Convertir en centimes
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => url('/payment/accompagnement/success?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => url('/applications/' . $application->hashid . '?accompagnement=cancel'),
                'metadata' => [
                    'application_id' => $application->id,
                    'establishment_id' => $establishment?->id,
                    'establishment_name' => $establishment?->title ?? 'Non spécifié',
                    'user_id' => $user->id,
                    'fee_type' => 'accompagnement_premium', // 🔑 Identifier le type de paiement
                ],
            ]);

            logger()->info('Session Stripe Checkout accompagnement créée', [
                'user_id' => $user->id,
                'application_id' => $application->id,
                'session_id' => $checkoutSession->id,
                'amount' => $accompagnementPrice,
            ]);

            return response()->json([
                'success' => true,
                'session_id' => $checkoutSession->id,
                'session_url' => $checkoutSession->url,
            ], 200);

        } catch (\Exception $e) {
            logger()->error('Erreur création session Stripe Checkout accompagnement', [
                'user_id' => $request->user()?->id,
                'application_id' => $application->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la session de paiement',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Activer/désactiver l'accompagnement premium pour une candidature
     *
     * Feature 9 — Accompagnement premium
     * Permet à l'étudiant de demander l'accompagnement sans payer immédiatement
     *
     * @param Request $request
     * @param Application $application
     * @return JsonResponse
     */
    public function toggleAccompagnement(Request $request, Application $application): JsonResponse
    {
        try {
            $user = $request->user();

            // Vérifier ownership
            if ($application->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé',
                ], 403);
            }

            // Empêcher désactivation si déjà payé
            if ($application->isAccompagnementPaid() && $application->hasAccompagnement()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de désactiver un accompagnement déjà payé',
                ], 422);
            }

            // Toggle l'accompagnement
            $newValue = !$application->accompagnement_premium;
            $application->update(['accompagnement_premium' => $newValue]);

            logger()->info('Accompagnement togglé', [
                'user_id' => $user->id,
                'application_id' => $application->id,
                'accompagnement_premium' => $newValue,
            ]);

            return response()->json([
                'success' => true,
                'accompagnement_premium' => $newValue,
                'message' => $newValue
                    ? 'Accompagnement premium activé. Procédez au paiement pour confirmer.'
                    : 'Accompagnement premium désactivé.',
            ], 200);

        } catch (\Exception $e) {
            logger()->error('Erreur toggle accompagnement', [
                'user_id' => $request->user()?->id,
                'application_id' => $application->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
            ], 500);
        }
    }
}
