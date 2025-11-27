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

// Refonte: Page Établissements (frontend uniquement - données fictives)
Route::get('/establishments', function () {
    return Inertia::render('Establishments/Index');
})->name('establishments.index');

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
    return Inertia::render('Dashboard');
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
