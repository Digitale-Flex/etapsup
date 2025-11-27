<?php

namespace App\Http\Controllers;

use App\Enums\ReservationType;
use App\Http\Requests\ReservationRequest;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\ReservationResource;
use App\Mail\RealEstate\ReservationConfirmed;
use App\Models\RealEstate\Property;
use App\Models\RealEstate\Reservation;
use App\Services\ReservationService;
use App\Settings\RealEstateSettings;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Spatie\Period\Period;
use Stripe\Exception\CardException;

class ReservationController extends Controller
{
    public function __construct(
        protected readonly Property $property,
        protected readonly Reservation $reservation,
        protected readonly RealEstateSettings $settings,
        protected ReservationService $reservationService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): \Inertia\Response
    {
        $reservations = $this->reservation
            ->author()
            ->with([
                'property' => function ($query) {
                    return $query->select(['id', 'title', 'price', 'address', 'slug'])
                        ->with('ratings');
                },
            ])
            ->latest()
            ->paginate(3);

        return Inertia::render('Dashboard/RealEstate/Index', [
            'reservations' => ReservationResource::collection($reservations),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReservationRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $this->updateUserDetails($user, $validated);

        try {
            DB::beginTransaction();

            // Vérification de la disponibilité
            $property = $this->property->findByHashidOrFail($validated['property_id']);
            $period = Period::make(
                Carbon::parse($validated['period'][0]),
                Carbon::parse($validated['period'][1])
            );

            if (!$property->getAvailabilityForPeriod($period)) {
                return back()->withErrors(['period' => "La période choisie n'est pas disponible"]);
            }

            // Paiement Stripe
            $payment = $this->reservationService->processPayment($user, $validated);

            // Création de la réservation
            $reservation = $this->reservationService->createReservation($validated, $property, $user, $period, $payment);

            DB::commit();

          //  Mail::to($user)->queue(new ReservationConfirmed($reservation, $property));

            return to_route('dashboard.reservations.show', $reservation->hashid)
                ->with('success', 'Réservation créée avec succès');

        } catch (CardException|IncompletePayment $e) {
            DB::rollBack();
            Log::error('Erreur de paiement : ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du traitement du paiement');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::debug($e);
            Log::error('Erreur lors de la création de la réservation : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la création de la réservation');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): \Inertia\Response
    {
        $model = $this->reservation
            ->author()
            ->with([
                'property' => function ($query) {
                    return $query->with(['propertyType']);
                },
                'comments' => function ($query) {
                    $query->with('user')
                        ->forAuthor()
                        ->latest()
                        ->get();
                },
                'ratings',
            ])
            ->findByHashidOrFail($id);

        return Inertia::render('Dashboard/RealEstate/Show', [
            'reservation' => fn () => new ReservationResource($model),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id): \Inertia\Response
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $nights = $request->query('nights');
        $guests = $request->query('guests');
        $month = $request->query('month');
        $user = auth()->user()?->load('country');

        $intent = '';

        if ($user) {
            $intent = $user->createSetupIntent([
                'payment_method_types' => ['card'],
                'automatic_payment_methods' => [
                    'enabled' => false,
                ],
            ]);
        }

        $model = $this->property
            ->whereSlug($id)
            ->with(['propertyType', 'regulations', 'ratings', 'category', 'subCategory'])
            ->firstOrFail();

        $rental_monthly_billing = false;

        if (isset($model->category)) {
            if ($model->category->id === $this->settings->rental_monthly_billing) {
                $rental_monthly_billing = true;
            }
        }

        $blockedDates = $model->getBlockedDates(
            Carbon::now(),
            Carbon::now()->addYear()
        );

        return Inertia::render('RealEstate/Reservation/Index', array_merge(
            [
                'property' => new PropertyResource($model),
                'settings' => $this->settings,
                'blockedDates' => $blockedDates,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'nights' => intval($nights),
                'guests' => intval($guests),
            ],
            $user ? [
                'intent' => $intent?->client_secret,
                'stripeKey' => config('cashier.key'),
            ] : [],
        ));
    }

    protected function createReservation(array $data, Property $property, $user, Period $period, array $payment): Reservation
    {
        return $this->reservation->create([
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
            'type' => ReservationType::PayPerNight(),
            'stripe_payment_intent_id' => $payment->id,
        ]);
    }

    private function handleStripePayment($user, $amount, $validated)
    {
        Log::debug($amount);
        $user->createOrGetStripeCustomer();
        $user->addPaymentMethod($validated['payment_method_id']);

        return $user->charge($amount, $validated['payment_method_id'], [
            'currency' => 'eur',
            'confirm' => true,
            'payment_method_types' => ['card'],
            'description' => 'Réservation',
        ]);
    }


    public function checkAvailability(Request $request)
    {
        $property = Property::findOrFail($request->property_id);
        $period = Period::make(
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'available' => $property->getAvailabilityForPeriod($period),
            'blocked_dates' => $property->getBlockedDates(
                $period->start(),
                $period->end()
            ),
        ]);
    }

    public function addReview(Request $request, $id): RedirectResponse
    {
        $messages = [
            'rating.required' => 'Merci de donner une note à cette propriété',
            'rating.integer' => 'La note doit être un nombre entier',
            'rating.min' => 'La note minimum est de 1 étoile',
            'rating.max' => 'La note maximum est de 5 étoiles',
            'comment.required' => 'Le commentaire est obligatoire',
            'comment.string' => 'Le format du commentaire est invalide',
            'comment.min' => 'Votre commentaire doit faire au moins 3 caractères',
        ];

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3',
        ], $messages);

        $model = $this->reservation->findByHashidOrFail($id);

        if ($model->ratings()->where('user_id', auth()->id())->exists()) {
            return back()->withErrors([
                'general' => 'Vous avez déjà noté cette propriété',
            ]);
        }

        $model->ratings()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['score' => $validated['rating']]
        );

        $model->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['comment'],
            'score' => $validated['rating'],
        ]);

        return to_route('dashboard.reservations.show', $id);
    }

    private function updateUserDetails($user, array $data): void
    {
        $user->update([
            'surname' => $data['surname'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'nationality' => $data['nationality'],
            'place_birth' => $data['place_birth'],
            'date_birth' => $data['date_birth'],
        ]);
    }
}
