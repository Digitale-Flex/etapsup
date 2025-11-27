<?php

namespace App\Http\Controllers;

use App\Enums\ReservationType;
use App\Http\Requests\MonthlyReservationRequest;
use App\Http\Resources\PropertyResource;
use App\Mail\RealEstate\ReservationConfirmed;
use App\Models\RealEstate\Property;
use App\Models\RealEstate\Reservation;
use App\Settings\RealEstateSettings;
use App\States\Reservation\Pending;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Period\Period;
use Throwable;

class MonthlyReservationController extends Controller
{
    public function __construct(
        protected readonly Property $property,
        protected readonly RealEstateSettings $settings,
        protected readonly Reservation $reservation,
    ) {}

    public function store(MonthlyReservationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        try {
            DB::beginTransaction();

            $user->update($request->only([
                'surname', 'name', 'phone',
                'nationality', 'place_birth', 'date_birth'
            ]));

            $property = $this->property->findByHashidOrFail($validated['property_id']);
            $period = Period::make(
                Carbon::parse($validated['period'][0]),
                Carbon::parse($validated['period'][1])
            );

            if (!$property->getAvailabilityForPeriod($period)) {
                return back()->withErrors([
                    'period' => "La période choisie n'est pas disponible"
                ]);
            }

            // Création de la réservation
            $reservation = $this->createReservation($validated, $property, $user, $period);

            // Gestion des fichiers
            $this->handleFileUpload($request, $reservation);

            DB::commit();

            // Envoi d'email
            Mail::to($user)->queue(new ReservationConfirmed($reservation, $property));

            return to_route('dashboard.reservations.show', ['reservation' => $reservation->hashid])->with('success', 'Réservation créée avec succès');

        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Erreur lors de la création de la réservation');
        }
    }

    public function show(Request $request, string $slug): Response
    {
        $property = $this->property
            ->whereSlug($slug)
            ->with(['propertyType', 'regulations', 'ratings', 'category', 'subCategory'])
            ->firstOrFail();

        return Inertia::render('RealEstate/Reservation/MonthlyReservation', [
            'property' => new PropertyResource($property),
            'settings' => $this->settings,
            'blockedDates' => $property->getBlockedDates(now(), now()->addYear()),
            'startDate' => $request->date('start_date'),
            'endDate' => $request->date('end_date'),
            'month' => (int)$request->query('month', 1),
            'requireFiles' => optional($property->category)->id === $this->settings->category_supporting_documents,
        ]);
    }

    protected function createReservation(array $data, Property $property, $user, Period $period): Reservation
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
            'type' => ReservationType::Monthly()
        ]);
    }

    protected function handleFileUpload($request, Reservation $reservation): void
    {
        if ($request->hasFile('files')) {
            $reservation
                ->addAllMediaFromRequest('files')
                ->each(fn ($file) => $file->toMediaCollection('files'));
        }
    }
}
