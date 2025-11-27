<?php

namespace App\Http\Controllers\Certificate;

use App\Http\Controllers\Controller;
use App\Http\Requests\PayLaterRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\GenreResource;
use App\Http\Resources\PartnerResource;
use App\Http\Resources\RentalDepositesource;
use App\Http\Resources\UserResource;
use App\Models\Certificate\CertificateRequest;
use App\Models\Certificate\Coupon;
use App\Models\Certificate\Genre;
use App\Models\Certificate\Partner;
use App\Models\Certificate\RentalDeposit;
use App\Models\City;
use App\Models\Country;
use App\States\CertificateRequest\PaymentVerification;
use App\Traits\HandlesBase64Files;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;
use Vinkla\Hashids\Facades\Hashids;

class PayLaterController extends Controller
{
    use HandlesBase64Files;

    public function __construct(
        protected readonly Country            $country,
        protected readonly Partner            $partner,
        protected readonly City               $city,
        protected readonly Genre              $genre,
        protected readonly RentalDeposit      $rentalDeposit,
        protected readonly CertificateRequest $certificateRequest,
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $user = auth()->user()->load('country');

        /* $cities = Cache::remember('cities', now()->addDay(), function () {
             return CityResource::collection(
                 $this->city->query()
                     ->select('id', 'name', 'budget')
                     ->orderBy('name')
                     ->get()
             );
         }); */
        return Inertia::render('Certificate/Later', [
            'user' => new UserResource($user),
            'countries' => fn() => CountryResource::collection(
                $this->country->query()->select('id', 'name')->get()
            ),
            'partners' => fn() => PartnerResource::collection(
                $this->partner->select('id', 'label')->get()
            ),
            'cities' => Inertia::defer(fn() => CityResource::collection(
                $this->city->query()
                    ->select('id', 'name', 'budget')
                    ->orderBy('name')
                    ->get()
            )),
            'genres' => fn() => GenreResource::collection(
                $this->genre->query()->select('id', 'name')->get()
            ),
            'rental_deposits' => fn() => RentalDepositesource::collection(
                $this->rentalDeposit->query()->select('id', 'name')->get()
            ),
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
    public function store(PayLaterRequest $request): RedirectResponse
    {
        $coupon = null;
        $paid = 399;

        // 1. Gestion du coupon
        if ($couponHashId = $request->input('coupon_id')) {
            try {
                $coupon = Coupon::findByHashidOrFail($couponHashId);
                $paid = $coupon->discount_amount;

            } catch (ModelNotFoundException $e) {
                \Log::error("Coupon non trouvé: $couponHashId");
            }
        }

        $rentalDepositIds = collect($request->input('rental_deposit_ids', []))
            ->map(function ($hashId) {
                return collect(Hashids::decode($hashId))->first();
            })
            ->filter();


        $certificateRequest = CertificateRequest::create(array_merge(
            $request->validated(),
            [
                'user_id' => auth()->id(),
                'coupon_id' => $coupon->id ?? null,
                'paid' => $paid
            ]
        ));

        $certificateRequest->rentalDeposits()->attach($rentalDepositIds);

        auth()->user()->update([
            'surname' => $request->input('surname'),
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'nationality' => $request->input('nationality'),
            'passport_number' => $request->input('passport_number'),
            'place_birth' => $request->input('place_birth'),
            'date_birth' => $request->input('date_birth'),
            'country_id' => collect(Hashids::decode($request->input('country_birth_id')))->first(),
        ]);

        return to_route('dashboard.certificates.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function uploadProofs(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!$this->isValidBase64($value)) {
                    $fail('Le fichier fourni est invalide.');
                }

                $fileInfo = $this->getFileInfo($value);
                if (!in_array($fileInfo['mime'], ['image/jpeg', 'image/png', 'application/pdf'])) {
                    $fail('Le type de fichier doit être JPEG, PNG ou PDF.');
                }
            }],
            'note' => ['nullable', 'string', 'max:1000'],
        ], ['file.required' => 'Merci de joindre votre preuve de paiement'], ['file' => 'Le justificatif']);

        $fileInfo = $this->getFileInfo($request->input('file'));
        $fileName = $this->generateFileName('proof', $fileInfo['extension']);

        $certificate = $this->certificateRequest->findByHashidOrFail($id);

        $proof = $certificate->paymentProofs()->create([
            'note' => $request->input('note'),
            'user_id' => auth()->id(),
        ]);

        $proof->addMediaFromBase64($request->input('file'))
            ->usingFileName($fileName)
            ->usingName(pathinfo($fileName, PATHINFO_FILENAME))
            ->toMediaCollection('proof');

        $certificate->state->transitionTo(PaymentVerification::class);

        return to_route('dashboard.certificates.show', $id);
    }
}
