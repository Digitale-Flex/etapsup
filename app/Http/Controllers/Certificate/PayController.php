<?php

namespace App\Http\Controllers\Certificate;

use App\Http\Controllers\Controller;
use App\Http\Requests\PayRequest;
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
use App\Services\CertificateGenerationService;
use App\States\CertificateRequest\CertificateGenerated;
use App\States\CertificateRequest\PaymentValidated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Exception\CardException;
use Stripe\StripeClient;
use Vinkla\Hashids\Facades\Hashids;

class PayController extends Controller
{
    public function __construct(
        protected readonly Country                      $country,
        protected readonly Partner                      $partner,
        protected readonly City                         $city,
        protected readonly Genre                        $genre,
        protected readonly RentalDeposit                $rentalDeposit,
        protected readonly CertificateRequest           $certificateRequest,
        protected readonly CertificateGenerationService $certificateGenerationService,
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $user = auth()->user()->load('country');

        $intent = $user->createSetupIntent([
            'payment_method_types' => ['card'],
        ]);

        return Inertia::render('Certificate/Pay', [
            'user' => new UserResource($user),
            'countries' => fn() => CountryResource::collection(
                $this->country->query()->select('id', 'name')->get()
            ),
            'partners' => fn() => PartnerResource::collection(
                $this->partner->query()->select('id', 'label')->get()
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
            'stripeKey' => config('cashier.key'),
            'intent' => $intent->client_secret,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    public function store(PayRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();
            // 1) Mise à jour des infos utilisateur
            $user->update([
                'surname'        => $request->surname,
                'name'           => $request->name,
                'phone'          => $request->phone,
                'nationality'    => $request->nationality,
                'passport_number'=> $request->passport_number,
                'place_birth'    => $request->place_birth,
                'date_birth'     => $request->date_birth,
                'country_id'     => collect(Hashids::decode($request->country_birth_id))->first(),
            ]);

            // 2) Calcul du montant (par défaut 399 ou montant réduit via coupon)
            $amount = 399;
            $coupon = null;

            if ($request->coupon_id) {
                $coupon = Coupon::findByHashidOrFail($request->coupon_id);
                if ($coupon->discount_amount) {
                    $amount = $coupon->discount_amount;
                }
            }

            // 3) Création du modèle CertificateRequest
            $rentalDepositIds = collect($request->rental_deposit_ids ?? [])
                ->map(fn($hashId) => collect(Hashids::decode($hashId))->first())
                ->filter();

            $model = CertificateRequest::create(array_merge(
                $request->validated(),
                [
                    'user_id'   => auth()->id(),
                    'coupon_id' => $coupon?->id,
                    'paid'      => $amount,
                ]
            ));
            $model->rentalDeposits()->attach($rentalDepositIds);
            $certificateHashId = $model->hashid();

            // 4) Créer/récupérer le client Stripe
            $stripeCustomer = $user->createOrGetStripeCustomer();
            // S’assurer que $user->stripe_id est bien rafraîchi
            $user->refresh();

            // 5) Créer le PaymentIntent SANS "payment_method"
            //    On spécifie juste le customer et le montant,
            //    la confirmation 3DS se fera côté front via confirmCardPayment.
            $stripe     = new \Stripe\StripeClient(config('cashier.secret'));
            $payInCents = $amount * 100;

            $paymentIntent = $stripe->paymentIntents->create([
                'amount'             => $payInCents,
                'currency'           => 'eur',
                'customer'           => $user->stripe_id,
                'description'        => 'Certificat de location #' . $model->id,
                'confirmation_method'=> 'automatic',
                'confirm'            => false, // on ne confirme pas encore : on attend Stripe.js
            ]);

            // 6) Sauvegarde de l’ID du PaymentIntent dans la base
            $model->stripe_payment_intent = $paymentIntent->id;
            $model->save();

            DB::commit();

            // 7) On renvoie uniquement le client_secret ET l’ID du certificat
            return response()->json([
                'client_secret'  => $paymentIntent->client_secret,
                'certificate_id' => $certificateHashId,
            ]);
        }
        catch (\Exception $e) {
            DB::rollBack();
            logger()->error($e);
            return response()->json([
                'error'   => 'Erreur lors de la création du PaymentIntent.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Cette méthode sera appelée _après_ que le front ait confirmé
     * le PaymentIntent (3D Secure terminé, statut = “succeeded”).
     */
    public function confirmPayment(Request $request): JsonResponse
    {
        logger()->info('confirm payment');
        try {
            $certificateHashId = $request->input('certificate_id');
            $model = CertificateRequest::findByHashidOrFail($certificateHashId);

            // Récupérer le PaymentIntent (pour s'assurer que son statut est bien “succeeded”)
            $stripe = new StripeClient(config('cashier.secret'));
            $paymentIntent = $stripe->paymentIntents->retrieve($model->stripe_payment_intent);

            if ($paymentIntent->status === 'succeeded') {
                // On marque la commande comme payée
                $model->state->transitionTo(PaymentValidated::class);
                // On génère le certificat (PDF, email, etc.)
                $this->generateCertificate($model);

                return response()->json([
                    'success'        => true,
                    'certificate_id' => $certificateHashId,
                ]);
            }

            // Si le statut n’est pas encore “succeeded”, on peut renvoyer une erreur ou une instruction
            return response()->json([
                'error'   => 'Le paiement n’est pas dans l’état “succeeded”.',
                'message' => 'Status actuel : ' . $paymentIntent->status,
            ], 422);
        }
        catch (\Exception $e) {
            logger()->error($e->getMessage());
            return response()->json([
                'error'   => 'Erreur lors de la confirmation du paiement.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function refreshIntent(): JsonResponse
    {
        $intent = auth()->user()->createSetupIntent([
            'payment_method_types' => ['card'],
        ]);

        return response()->json([
            'intent' => $intent->client_secret,
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(CertificateRequest $certificateRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CertificateRequest $certificateRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CertificateRequest $certificateRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CertificateRequest $certificateRequest)
    {
        //
    }

    private function generateCertificate(CertificateRequest $model): void
    {
        try {
            $this->certificateGenerationService->generate($model, false);

            if ($model->getFirstMedia('certificate')) {
                $model->state->transitionTo(CertificateGenerated::class);
            }
        } catch (\Exception $e) {
            Log::error('Certificate Generation Error: ' . $e->getMessage());
        }
    }
}
