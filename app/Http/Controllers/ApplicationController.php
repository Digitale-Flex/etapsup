<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\RealEstate\Property;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Stripe\StripeClient;

/**
 * Contrôleur des candidatures étudiantes (EtatSup)
 * Clone de ReservationController avec simplification V0
 */
class ApplicationController extends Controller
{
    public function __construct(
        protected readonly Property $property,
        protected readonly Application $application,
        protected readonly PaymentService $paymentService,
    ) {}

    /**
     * Afficher le formulaire de candidature
     * GET /applications/create?establishment_id={hashid}
     * GET /applications/create?application_id={hashid} (reprendre brouillon)
     *
     * Clone de ReservationController@edit mais simplifié pour V0
     */
    public function create(Request $request): Response
    {
        $applicationId = $request->query('application_id');
        $establishmentId = $request->query('establishment_id');
        $user = auth()->user()?->load('country');

        $draftData = null;
        $establishment = null;

        // CAS 1: Reprendre une candidature existante
        if ($applicationId) {
            $application = $this->application
                ->with(['property.propertyType', 'property.city.country', 'property.category']) // A20
                ->findByHashidOrFail($applicationId);

            // Sécurité: vérifier que c'est bien la candidature du user connecté
            if ($application->user_id !== $user->id) {
                abort(403, 'Vous n\'êtes pas autorisé à accéder à cette candidature');
            }

            $establishment = $application->property;
            $draftData = $application->fees;
        }
        // CAS 2: Nouvelle candidature
        else {
            if (!$establishmentId) {
                return Inertia::render('Error', [
                    'status' => 400,
                    'message' => 'Veuillez sélectionner un établissement'
                ]);
            }

            // Charger l'établissement (Property) avec relations
            $establishment = $this->property
                ->findByHashidOrFail($establishmentId)
                ->load(['propertyType', 'city.country', 'category']); // A20

            // Vérifier que c'est publié
            if (!$establishment->is_published) {
                abort(404, 'Cet établissement n\'est pas disponible');
            }

            // Chercher un brouillon existant pour cet établissement
            $draftApplication = $this->application
                ->where('user_id', $user->id)
                ->where('property_id', $establishment->id)
                ->where('status', 'draft')
                ->first();

            $draftData = $draftApplication?->fees ?? null;
        }

        return Inertia::render('Applications/Create', [
            'draftData' => $draftData,
            'establishment' => [
                'id' => $establishment->hashid,
                'slug' => $establishment->slug,
                'title' => $establishment->title,
                'description' => $establishment->description,
                'type' => $establishment->propertyType?->label,
                'category' => $establishment->category?->label,
                'city' => $establishment->city?->name,
                'country' => $establishment->city?->country?->name, // A20
                'logo' => $establishment->getFirstMediaUrl('images', 'thumb'),
                // Champs éducatifs EtatSup
                'website' => $establishment->website,
                'phone' => $establishment->phone,
                'ranking' => $establishment->ranking,
                'studentCount' => $establishment->student_count,
                // Sprint1 Feature 1.7.1 - Frais de paiement
                'frais_dossier' => $establishment->frais_dossier ?? 0,
                'acompte_scolarite' => $establishment->acompte_scolarite ?? 0,
            ],
            'user' => $user ? [
                'surname' => $user->surname,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'place_birth' => $user->place_birth,
                'date_birth' => $user->date_birth,
                'nationality' => $user->nationality,
            ] : null,
            // Stripe pour paiement (copié de CustomSearchController)
            'stripeKey' => config('cashier.key'),
            'intent' => $this->paymentService->createSetupIntent($user),
        ]);
    }

    /**
     * Afficher les détails d'une candidature
     * GET /candidatures/{hashid}
     *
     * Refonte Story 1.1.2 - Page détails candidature cliquable depuis Dashboard
     */
    public function show(string $hashid): Response
    {
        $application = $this->application
            ->with(['property.propertyType', 'property.city.country', 'property.category', 'property.media']) // C02
            ->findByHashidOrFail($hashid);

        // Sécurité: vérifier que c'est la candidature du user connecté
        if ($application->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à accéder à cette candidature');
        }

        return Inertia::render('Dashboard/Applications/Show', [
            'application' => [
                'id' => $application->hashid,
                'status' => $application->status,
                'state' => $application->state,
                'created_at' => $application->created_at?->format('d/m/Y à H:i'),
                'updated_at' => $application->updated_at?->format('d/m/Y à H:i'),
                'establishment' => [
                    'id' => $application->property->hashid,
                    'title' => $application->property->title,
                    'slug' => $application->property->slug,
                    'logo' => $application->property->getFirstMediaUrl('images', 'thumb'),
                ],
                'formData' => $application->fees ?? [],
            ],
        ]);
    }

    /**
     * Sauvegarder un brouillon (auto-save)
     * POST /applications/draft
     */
    public function saveDraft(Request $request)
    {
        $validated = $request->validate([
            'property_id' => ['required', 'string'],
            'current_step' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $user = $request->user();
        $property = $this->property->findByHashidOrFail($validated['property_id']);

        // Chercher un brouillon existant
        $application = $this->application
            ->where('user_id', $user->id)
            ->where('property_id', $property->id)
            ->where('status', 'draft')
            ->first();

        $applicationData = [
            'property_id' => $property->id,
            'user_id' => $user->id,
            'status' => 'draft',
            'state' => 'draft',
            'price' => $property->price ?? 0,
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'fees' => array_merge($application?->fees ?? [], [
                'current_step' => $validated['current_step'],
                // All form fields
                'surname' => $request->input('surname'),
                'name' => $request->input('name'),
                'gender' => $request->input('gender'),
                'date_of_birth' => $request->input('date_of_birth'),
                'nationality' => $request->input('nationality'),
                'country_of_birth' => $request->input('country_of_birth'),
                'city_of_birth' => $request->input('city_of_birth'),
                'address' => $request->input('address'),
                'postal_code' => $request->input('postal_code'),
                'city' => $request->input('city'),
                'country' => $request->input('country'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'parent_email' => $request->input('parent_email'),
                'current_diploma' => $request->input('current_diploma'),
                'diploma_year' => $request->input('diploma_year'),
                'years_validated' => $request->input('years_validated'),
                'previous_institution' => $request->input('previous_institution'),
                'institution_city' => $request->input('institution_city'),
                'institution_country' => $request->input('institution_country'),
                'mother_tongue' => $request->input('mother_tongue'),
                'english_test' => $request->input('english_test'),
                'english_level' => $request->input('english_level'),
                'french_test' => $request->input('french_test'),
                'french_level' => $request->input('french_level'),
                'motivation' => $request->input('motivation'),
                'application_location' => $request->input('application_location'),
                'application_date' => $request->input('application_date'),
            ]),
        ];

        if ($application) {
            $application->update($applicationData);
        } else {
            $application = $this->application->create($applicationData);
        }

        return response()->json([
            'success' => true,
            'application_id' => $application->hashid,
        ]);
    }

    /**
     * Soumettre une candidature avec paiement Stripe
     * POST /applications
     *
     * V2: Créer candidature + PaymentIntent (10 EUR frais de dossier)
     * Retourne { client_secret, application_id } pour Stripe.js
     */
    public function store(Request $request): JsonResponse
    {
        // Validation complète (sections 1-4)
        $validated = $request->validate([
            'property_id' => ['required', 'string'],

            // Section 1: Informations personnelles
            'surname' => ['required', 'string', 'min:2'],
            'name' => ['required', 'string', 'min:2'],
            'gender' => ['required', 'string'],
            'date_of_birth' => ['required', 'date'],
            'nationality' => ['required', 'string'],
            'country_of_birth' => ['required', 'string'],
            'city_of_birth' => ['required', 'string'],
            'address' => ['required', 'string', 'min:5'],
            'postal_code' => ['required', 'string'],
            'city' => ['nullable', 'string'], // C04: Optionnel selon PRD Sprint1 (frontend)
            'country' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],

            // Section 2: Formation
            'current_diploma' => ['required', 'string', 'min:3'],
            'diploma_year' => ['required', 'integer'],
            'years_validated' => ['required', 'string'],
            'previous_institution' => ['required', 'string', 'min:3'],
            'institution_city' => ['required', 'string'],
            'institution_country' => ['required', 'string'],

            // Section 3: Langues
            'mother_tongue' => ['required', 'string'],

            // Section 4: Motivation
            'motivation' => ['required', 'string', 'min:100', 'max:2000'],
            'application_location' => ['required', 'string'],
        ]);

        try {
            $user = $request->user();
            $property = $this->property->findByHashidOrFail($validated['property_id']);

            // Refonte Story 1.1.4 FIX - Vérifier AVANT transaction (read-only, pas de race condition)
            $existingApplication = $this->application
                ->where('user_id', $user->id)
                ->where('property_id', $property->id)
                ->where('status', 'pending_payment')
                ->first();

            // Si une session Stripe existe déjà, la relancer (pas de transaction nécessaire)
            if ($existingApplication && $existingApplication->stripe_payment_intent_id) {
                $stripe = new StripeClient(config('cashier.secret'));
                try {
                    $paymentIntent = $stripe->paymentIntents->retrieve($existingApplication->stripe_payment_intent_id);

                    // Refonte Story 1.1.4 FIX - Ajouter 'processing' dans statuts valides
                    if (in_array($paymentIntent->status, ['requires_payment_method', 'requires_confirmation', 'requires_action', 'processing'])) {
                        return response()->json([
                            'client_secret' => $paymentIntent->client_secret,
                            'application_id' => $existingApplication->hashid,
                        ]);
                    }
                } catch (\Exception $e) {
                    // PaymentIntent invalide, continuer pour en créer un nouveau
                    logger()->warning('PaymentIntent invalide, création d\'un nouveau', [
                        'payment_intent_id' => $existingApplication->stripe_payment_intent_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Refonte Story 1.1.4 FIX - Transaction uniquement pour CREATE
            DB::beginTransaction();

            // Sprint1 Feature 1.7.1 - Montant dynamique depuis l'établissement
            $amount = $property->frais_dossier ?? 0;

            // Validation: frais_dossier doit être défini et > 0
            if ($amount <= 0) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Les frais de dossier ne sont pas configurés pour cet établissement.',
                    'message' => 'Veuillez contacter l\'administration.',
                ], 422);
            }

            // Préparer les données complètes (tous champs du formulaire)
            $applicationData = [
                'property_id' => $property->id,
                'user_id' => $user->id,
                'status' => 'pending_payment', // Status initial avant paiement
                'state' => 'pending_payment',
                'notes' => $request->input('motivation', ''),
                'reason' => $request->input('field_of_study', ''),
                'price' => $amount, // 10 EUR
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'address' => $request->input('address', ''),
                'fees' => [
                    // Section 1
                    'surname' => $request->input('surname'),
                    'name' => $request->input('name'),
                    'gender' => $request->input('gender'),
                    'date_of_birth' => $request->input('date_of_birth'),
                    'nationality' => $request->input('nationality'),
                    'country_of_birth' => $request->input('country_of_birth'),
                    'city_of_birth' => $request->input('city_of_birth'),
                    'postal_code' => $request->input('postal_code'),
                    'city' => $request->input('city'),
                    'country' => $request->input('country'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                    'parent_email' => $request->input('parent_email'),

                    // Section 2
                    'current_diploma' => $request->input('current_diploma'),
                    'diploma_year' => $request->input('diploma_year'),
                    'years_validated' => $request->input('years_validated'),
                    'previous_institution' => $request->input('previous_institution'),
                    'institution_city' => $request->input('institution_city'),
                    'institution_country' => $request->input('institution_country'),

                    // Section 3
                    'mother_tongue' => $request->input('mother_tongue'),
                    'english_test' => $request->input('english_test'),
                    'english_level' => $request->input('english_level'),
                    'french_test' => $request->input('french_test'),
                    'french_level' => $request->input('french_level'),

                    // Section 4
                    'motivation' => $request->input('motivation'),
                    'application_location' => $request->input('application_location'),
                    'application_date' => $request->input('application_date'),
                ],
                'guests' => null,
            ];

            $application = $this->application->create($applicationData);

            // Créer PaymentIntent Stripe (copié de CustomSearchController)
            $user->createOrGetStripeCustomer();
            $user->refresh();

            $stripe = new StripeClient(config('cashier.secret'));
            $payInCents = $amount * 100;

            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $payInCents,
                'currency' => 'eur',
                'customer' => $user->stripe_id,
                'description' => 'Frais de dossier - Candidature #' . $application->id,
                'metadata' => [
                    'application_id' => $application->id,
                    'establishment' => $property->title,
                ],
                'confirmation_method' => 'automatic',
                'confirm' => false, // Confirmation côté frontend avec Stripe.js
            ]);

            // Sauvegarder l'ID du PaymentIntent
            $application->stripe_payment_intent_id = $paymentIntent->id;
            $application->save();

            DB::commit();

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
                'application_id' => $application->hashid,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error($e);
            return response()->json([
                'error' => 'Erreur lors de la création de la candidature.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Confirmer le paiement d'une candidature
     * POST /applications/payment/confirm
     *
     * Sprint1 Feature 1.7.1 - Mapping complet des statuts Stripe
     * Vérifie le statut du PaymentIntent et met à jour la candidature en conséquence
     */
    public function confirmPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'application_id' => ['required', 'string'],
        ]);

        try {
            $application = $this->application->findByHashidOrFail($validated['application_id']);

            // Sécurité: vérifier que c'est bien la candidature du user connecté
            if ($application->user_id !== $request->user()->id) {
                return response()->json([
                    'error' => 'Vous n\'êtes pas autorisé à accéder à cette candidature',
                ], 403);
            }

            // Vérifier le statut du PaymentIntent sur Stripe
            $stripe = new StripeClient(config('cashier.secret'));
            $paymentIntent = $stripe->paymentIntents->retrieve($application->stripe_payment_intent_id);

            // Sprint1 Feature 1.7.1 - Mapping complet des statuts Stripe
            switch ($paymentIntent->status) {
                case 'succeeded':
                    // Paiement réussi
                    $application->update([
                        'status' => 'paid',
                        'state' => 'paid',
                        'fees' => array_merge($application->fees ?? [], [
                            'frais_dossier_paid' => true,
                            'paid_at' => now()->toIso8601String(),
                            'payment_status' => 'succeeded',
                        ]),
                    ]);

                    logger()->info('Paiement candidature réussi', [
                        'application_id' => $application->id,
                        'payment_intent_id' => $paymentIntent->id,
                        'amount' => $paymentIntent->amount / 100,
                    ]);

                    return response()->json([
                        'success' => true,
                        'application_id' => $application->hashid,
                        'message' => 'Paiement effectué avec succès',
                    ]);

                case 'canceled':
                    // Paiement annulé par l'utilisateur
                    $application->update([
                        'status' => 'canceled',
                        'state' => 'canceled',
                        'fees' => array_merge($application->fees ?? [], [
                            'payment_status' => 'canceled',
                            'canceled_at' => now()->toIso8601String(),
                        ]),
                    ]);

                    logger()->info('Paiement candidature annulé', [
                        'application_id' => $application->id,
                        'payment_intent_id' => $paymentIntent->id,
                    ]);

                    return response()->json([
                        'error' => 'Le paiement a été annulé.',
                        'message' => 'Vous pouvez réessayer plus tard.',
                    ], 422);

                case 'processing':
                    // Paiement en cours de traitement (paiements différés)
                    $application->update([
                        'status' => 'processing',
                        'state' => 'processing',
                        'fees' => array_merge($application->fees ?? [], [
                            'payment_status' => 'processing',
                        ]),
                    ]);

                    return response()->json([
                        'success' => true,
                        'application_id' => $application->hashid,
                        'message' => 'Paiement en cours de traitement. Vous serez notifié une fois validé.',
                    ]);

                case 'requires_payment_method':
                case 'requires_confirmation':
                case 'requires_action':
                    // Paiement nécessite une action supplémentaire
                    return response()->json([
                        'error' => 'Le paiement nécessite une action supplémentaire.',
                        'message' => 'Veuillez réessayer ou utiliser un autre moyen de paiement.',
                        'status' => $paymentIntent->status,
                    ], 422);

                default:
                    // Paiement échoué (payment_failed, requires_capture, etc.)
                    $errorMessage = $paymentIntent->last_payment_error?->message ?? 'Paiement refusé par votre banque';
                    $declineCode = $paymentIntent->last_payment_error?->decline_code ?? 'unknown';

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

                    logger()->warning('Paiement candidature échoué', [
                        'application_id' => $application->id,
                        'payment_intent_id' => $paymentIntent->id,
                        'status' => $paymentIntent->status,
                        'error' => $errorMessage,
                        'decline_code' => $declineCode,
                    ]);

                    return response()->json([
                        'error' => 'Le paiement a échoué.',
                        'message' => $errorMessage,
                        'decline_code' => $declineCode,
                    ], 422);
            }

        } catch (\Exception $e) {
            logger()->error('Erreur confirmation paiement candidature', [
                'application_id' => $validated['application_id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Erreur lors de la confirmation du paiement.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
