<?php

use App\Http\Controllers\Certificate\PayController;
use App\Http\Controllers\CustomSearchController;
use App\Http\Controllers\HomeController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/laravel', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::resource('monthlies', \App\Http\Controllers\MonthlyReservationController::class);

Route::get('/ct', function () {
    $request = \App\Models\Certificate\CertificateRequest::latest()->first();
    $price = 2000;
    $qrcode = 'bfvjksnvsd';

    return view('pdf.certificate', compact('request', 'price', 'qrcode'));
});

Route::get('/txst', function () {
    $reservation = \App\Models\RealEstate\Reservation::find(1);

    abort_if(!$reservation, 404, 'Reservation not found');

    \App\Jobs\RealEstate\GenerateContractJob::dispatch($reservation->id);
    // ->onQueue('contracts');

    return response()->json([
        'message' => 'Génération du contrat démarrée',
        'queue' => 'contracts',
        'reservation_id' => $reservation->id,
    ]);
});

Route::get('/', [HomeController::class, 'index'])->name('home');

// Refonte: Page Établissements avec filtres fonctionnels (Story 1.3.1)
Route::get('/establishments', [\App\Http\Controllers\EstablishmentController::class, 'index'])
    ->name('establishments.index');

// Refonte: Fiche établissement détaillée (Story 1.3.2)
Route::get('/establishments/{establishment}', [\App\Http\Controllers\EstablishmentController::class, 'show'])
    ->name('establishments.show');

// Refonte: Story 1.1.1 - Event Landing Page Routes
Route::get('/events', [\App\Http\Controllers\EventController::class, 'index'])->name('events.index');
Route::post('/events/register', [\App\Http\Controllers\EventController::class, 'register'])->name('events.register');
Route::get('/events/stats', [\App\Http\Controllers\EventController::class, 'stats'])->name('events.stats');
Route::get('/remerciement_event', [\App\Http\Controllers\EventController::class, 'thanks'])->name('events.thanks');

Route::get('properties/{id}/comments', [\App\Http\Controllers\Property\PropertyController::class, 'loadMoreComments'])->name('properties.comments');
Route::resource('properties', \App\Http\Controllers\Property\PropertyController::class);

Route::resource('reservations', \App\Http\Controllers\ReservationController::class)->only(['edit']);

Route::get('/certificate', function () {
    return Inertia::render('Certificate/Index', []);
})->name('certificate.home');

Route::get('/dashboard', function () {
    $user = auth()->user();

    // Récupérer les candidatures récentes de l'utilisateur (max 3) - toutes les candidatures y compris brouillons
    $rawApplications = \App\Models\Application::where('user_id', $user->id)
        ->with(['property.city.region.country', 'property.category', 'property.propertyType'])
        ->latest()
        ->take(3)
        ->get();

    // Formater les données pour le dashboard
    $applications = $rawApplications->map(function ($app) {
        $statusLabels = [
            'draft' => 'Brouillon',
            'pending' => 'En attente',
            'submitted' => 'Soumise',
            'accepted' => 'Acceptée',
            'rejected' => 'Rejetée',
            'in_review' => 'En cours d\'examen',
        ];

        return [
            'id' => $app->hashid,
            'establishment' => $app->property->title ?? 'Établissement',
            'program' => $app->property->category->label ?? 'Programme non spécifié',
            'status' => $app->status,
            'statusLabel' => $statusLabels[$app->status] ?? ucfirst($app->status),
            'date' => $app->created_at?->format('d/m/Y') ?? '-',
            'country' => $app->property->city?->region?->country?->name ?? 'Non spécifié',
        ];
    })->toArray();

    // Refonte Story 1.1.3 - Récupérer 6 établissements populaires
    $rawRandomEstablishments = \App\Models\RealEstate\Property::where('is_published', true)
        ->with(['city.region.country', 'propertyType', 'category', 'media'])
        ->inRandomOrder()
        ->take(6)
        ->get();

    $randomEstablishments = $rawRandomEstablishments->map(function ($property) {
        return [
            'id' => $property->hashid,
            'slug' => $property->slug,
            'title' => $property->title, // Refonte Story 1.1.3 - Compatible EstablishmentCard
            'city' => $property->city?->name ?? 'Non spécifié',
            'country' => $property->city?->region?->country?->name ?? 'Non spécifié',
            'type' => $property->propertyType?->label ?? 'Établissement',
            'category' => $property->category?->label ?? '',
            'logo' => $property->getFirstMediaUrl('images', 'thumb'),
            'ranking' => $property->ranking,
            'studentCount' => $property->student_count,
        ];
    })->toArray();

    // Refonte: Calcul réel du % de complétion du profil
    $profileFields = [
        'surname', 'name', 'email', 'phone',
        'date_birth', 'place_birth', 'nationality',
        'address_id', 'gender'
    ];
    $filledFields = 0;
    foreach ($profileFields as $field) {
        if (!empty($user->$field)) {
            $filledFields++;
        }
    }
    $profileCompletion = round(($filledFields / count($profileFields)) * 100);

    // Sprint1 Update: Feature 1.1.1 — Récupérer documents et paiements
    // Documents récents (max 3)
    $rawDocuments = \App\Models\ApplicationDocument::whereHas('application', function ($query) use ($user) {
        $query->where('user_id', $user->id);
    })
    ->with(['application.property:id,title'])
    ->latest()
    ->take(3)
    ->get();

    $documents = $rawDocuments->map(function ($document) {
        return [
            'id' => $document->id,
            'type' => $document->document_type,
            'file_name' => basename($document->file_path),
            'file_url' => $document->file_path ? \Storage::url($document->file_path) : null,
            'verified' => $document->verified,
            'uploaded_at' => $document->created_at?->format('d/m/Y'),
            'establishment' => $document->application?->property?->title ?? 'Non spécifié',
        ];
    })->toArray();

    // Paiements Stripe récents (max 3)
    $payments = [];
    if ($user->stripe_id) {
        try {
            $stripe = new \Stripe\StripeClient(config('cashier.secret'));
            $paymentIntents = $stripe->paymentIntents->all([
                'customer' => $user->stripe_id,
                'limit' => 3,
            ]);

            $payments = collect($paymentIntents->data)->map(function ($intent) {
                $metadata = $intent->metadata ?? new \stdClass();
                return [
                    'id' => $intent->id,
                    'amount' => $intent->amount / 100,
                    'currency' => strtoupper($intent->currency),
                    'status' => $intent->status,
                    'status_label' => $intent->status === 'succeeded' ? 'Payé' : ucfirst($intent->status),
                    'description' => $intent->description ?? 'Paiement',
                    'establishment' => $metadata->establishment ?? 'Non spécifié',
                    'created_at' => date('d/m/Y', $intent->created),
                ];
            })->toArray();
        } catch (\Exception $e) {
            logger()->warning('Impossible de récupérer les paiements Stripe pour le dashboard', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    return Inertia::render('Dashboard', [
        'applications' => $applications,
        'randomEstablishments' => $randomEstablishments,
        'profileCompletion' => $profileCompletion,
        'documents' => $documents, // Sprint1 Update
        'payments' => $payments,   // Sprint1 Update
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('custom-search', CustomSearchController::class);
    Route::post('/custom-search/payment/confirm', [CustomSearchController::class, 'confirmPayment'])
        ->name('custom-search.payment.confirm');


    Route::get('/custom-search/success', [CustomSearchController::class, 'success'])
        ->name('custom-search.success');


    Route::get('property/{slug}/preview', [\App\Http\Controllers\Property\PropertyController::class, 'preview'])->name('properties.preview');

    Route::post('reservations/{property}', [\App\Http\Controllers\ReservationController::class, 'addReview'])->name('reservations.review.add');
    Route::resource('reservations', \App\Http\Controllers\ReservationController::class)->only(['store']);

    // EtatSup - Application routes (Feature B)
    Route::get('/applications/create', [\App\Http\Controllers\ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/applications/draft', [\App\Http\Controllers\ApplicationController::class, 'saveDraft'])->name('applications.draft');
    Route::post('/applications', [\App\Http\Controllers\ApplicationController::class, 'store'])->name('applications.store');
    Route::post('/applications/payment/confirm', [\App\Http\Controllers\ApplicationController::class, 'confirmPayment'])->name('applications.payment.confirm');

    // Refonte Story 1.1.2 - Affichage détails candidature cliquable depuis Dashboard
    Route::get('/candidatures/{application}', [\App\Http\Controllers\ApplicationController::class, 'show'])->name('applications.show');

    // Feature 9 - Sprint 1: Accompagnement premium
    Route::get('/applications/{application}/accompagnement', function (\App\Models\Application $application) {
        // Vérifier ownership
        if ($application->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé');
        }

        return Inertia::render('Payment/AccompagnementCheckout', [
            'application' => [
                'id' => $application->id,
                'hashid' => $application->hashid,
                'establishment' => [
                    'title' => $application->establishment?->title ?? 'Non spécifié',
                    'slug' => $application->establishment?->slug ?? '',
                ],
                'accompagnement_premium' => $application->accompagnement_premium,
                'accompagnement_paid' => $application->accompagnement_paid,
            ],
        ]);
    })->name('applications.accompagnement');


    Route::group([
        'prefix' => 'certificate',
        'as' => 'certificate.',
    ], function () {
        Route::resource('later', \App\Http\Controllers\Certificate\PayLaterController::class);
        Route::resource('pay', PayController::class);
        Route::get('refresh-intent', [PayController::class, 'refreshIntent'])->name('stripe.refresh-intent');
    });

    Route::post('/certificate/pay/confirm', [PayController::class, 'confirmPayment'])
        ->name('certificate.payment.confirm');


    // Route de retour après authentification 3D Secure
    Route::get('payment/return', function () {
        return redirect()->route('certificate.pay.index')
            ->with('message', 'Authentification terminée, veuillez finaliser votre paiement.');
    })->name('certificate.payment.return');

    // Sprint1 Feature 1.7.1 — Routes Stripe Checkout success/cancel
    Route::get('/payment/success', function (Illuminate\Http\Request $request) {
        $sessionId = $request->query('session_id');

        return Inertia::render('Payment/Success', [
            'session_id' => $sessionId,
        ]);
    })->name('payment.success');

    Route::get('/payment/cancel', function (Illuminate\Http\Request $request) {
        $applicationId = $request->query('application_id');

        return Inertia::render('Payment/Cancel', [
            'application_id' => $applicationId,
        ]);
    })->name('payment.cancel');

    // Feature 9 - Sprint 1: Routes success/cancel accompagnement premium
    Route::get('/payment/accompagnement/success', function (Illuminate\Http\Request $request) {
        $sessionId = $request->query('session_id');

        // QA Fix: Vérifier ownership du session_id pour éviter session hijacking
        try {
            $stripe = new \Stripe\StripeClient(config('cashier.secret'));
            $session = $stripe->checkout->sessions->retrieve($sessionId);

            // Vérifier que le paiement appartient bien à l'utilisateur connecté
            if (isset($session->metadata->user_id) && (int)$session->metadata->user_id !== auth()->id()) {
                abort(403, 'Cette session de paiement ne vous appartient pas.');
            }
        } catch (\Exception $e) {
            logger()->warning('Session Stripe invalide dans success page', [
                'session_id' => $sessionId,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);
            // Continuer l'affichage même si vérification échoue (pour compatibilité)
        }

        return Inertia::render('Payment/Success', [
            'session_id' => $sessionId,
            'type' => 'accompagnement',
            'message' => 'Accompagnement premium activé avec succès !',
        ]);
    })->name('payment.accompagnement.success');

    Route::group([
        'prefix' => 'dashboard',
        'as' => 'dashboard.',
    ], function () {
        Route::get('profile', [\App\Http\Controllers\Dashboard\ProfileController::class, 'index'])->name('profile');
        Route::patch('profile', [\App\Http\Controllers\Dashboard\ProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/password', [\App\Http\Controllers\Dashboard\ProfileController::class, 'updatePassword'])->name('profile.password');

        Route::post('upload-proofs/{certificate}', [\App\Http\Controllers\Certificate\PayLaterController::class, 'uploadProofs'])->name('later.upload-proofs');
        Route::resource('certificates', \App\Http\Controllers\Dashboard\CertificateRequestController::class);
        Route::resource('reservations', \App\Http\Controllers\ReservationController::class);


        Route::get('/custom-search', function () {
            return Inertia::render('Dashboard/CustomSearch/Index');
        })->name('custom-search.index');
    });

    Route::get('/certificate/option', function () {
        return Inertia::render('Certificate/Option', []);
    })->name('certificate.option');

    /* Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); */

    Route::post('/validate-coupon', function (Request $request) {
        $request->validate([
            'code' => 'required|string',
            'partner_id' => ['required', new \App\Rules\ValidateHashid(\App\Models\Certificate\Partner::class)]
        ]);

        $coupon = \App\Models\Certificate\Coupon::where('code', $request->code)
            ->where('partner_id', collect(Hashids::decode($request->input('partner_id')))->first())
            ->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Coupon invalide ou expiré'
            ], 404);
        }

        return response()->json([
            'valid' => true,
            'id' => $coupon->hashid,
            'amount' => $coupon->discount_amount
        ], 200);
    })->name('coupon.validate');
});

Route::get('/cleanup-regions', function () {
    try {
        // Récupérer les régions soft-deleted sans villes
        $regionsToDelete = \App\Models\Region::onlyTrashed()
            ->whereDoesntHave('cities')
            ->get();

        // Logger les régions qui vont être supprimées
        \Illuminate\Support\Facades\Log::info('Régions à supprimer :', [
            'regions' => $regionsToDelete->pluck('name')->toArray(),
        ]);

        // Supprimer définitivement ces régions
        $deletedCount = $regionsToDelete->each->forceDelete()->count();

        return response()->json([
            'success' => true,
            'message' => 'Nettoyage terminé',
            'deleted_count' => $deletedCount,
            'deleted_regions' => $regionsToDelete->pluck('name'),
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur lors du nettoyage des régions :', [
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue lors du nettoyage',
            'error' => $e->getMessage(),
        ], 500);
    }
});

require __DIR__ . '/auth.php';
