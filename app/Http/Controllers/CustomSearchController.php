<?php

namespace App\Http\Controllers;

use App\Helpers\ArrayHelper;
use App\Http\Requests\CustomSearchRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\EquipmentResource;
use App\Http\Resources\LayoutResource;
use App\Http\Resources\PartnerResource;
use App\Http\Resources\PropertyTypeResource;
use App\Http\Resources\RentalDepositesource;
use App\Http\Resources\SubCategoryResource;
use App\Http\Resources\UserResource;
use App\Models\Certificate\Coupon;
use App\Models\Certificate\Partner;
use App\Models\Certificate\RentalDeposit;
use App\Models\City;
use App\Models\Country;
use App\Models\CustomSearch;
use App\Models\RealEstate\Category;
use App\Models\RealEstate\Equipment;
use App\Models\RealEstate\Layout;
use App\Models\RealEstate\PropertyType;
use App\Models\RealEstate\Regulation;
use App\Services\PaymentService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Stripe\StripeClient;

class CustomSearchController extends Controller
{
    public function __construct(protected UserService $userService, protected CustomSearch $customSearch, protected PaymentService $paymentService)
    {
    }

    public function index()
    {
        $user = auth()->user()->load('country');

        return Inertia::render('CustomSearch/Index', [
            'user' => new UserResource($user),
            'countries' => fn() => CountryResource::collection(
                Country::select('id', 'name')->get()
            ),
            'types' => fn() => PropertyTypeResource::collection(PropertyType::minimal()->get()),
            'categories' => fn() => SubCategoryResource::collection(Category::minimal()->get()),
            'layouts' => fn() => LayoutResource::collection(Layout::minimal()->get()),
            'cities' => Inertia::defer(fn() => CityResource::collection(City::minimal()->get())),
            'partners' => fn() => PartnerResource::collection(Partner::minimal()->get()),
            'rentalDeposits' => fn() => RentalDepositesource::collection(RentalDeposit::minimal()->get()),
            'intent' => $this->paymentService->createSetupIntent($user),
            'stripeKey' => config('cashier.key'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomSearchRequest $request)
    {
        try {
            DB::beginTransaction();

            $validatedData = ArrayHelper::decodeHashIds([
                'category_id',
                'city_id',
                'partner_id',
                'property_type_ids',
                'layout_ids',
                'rental_deposit_ids',
                'country_birth_id',
                'coupon_id'
            ], $request->validated());

            $updateResult = $this->userService->update($validatedData);

            if (!$updateResult['success']) {
                throw new \Exception($updateResult['message']);
            }

            $user = $updateResult['user'];

            $amount = 100;
            $coupon = null;

            if ($request->coupon_id) {
                $coupon = Coupon::findByHashidOrFail($request->coupon_id);
                if ($coupon->discount_amount) {
                    $amount = $coupon->discount_amount;
                }
            }

            $validatedData['paid'] = $amount;
            $validatedData['user_id'] = auth()->id();

            $model = $this->customSearch->create($validatedData);

            $model->rentalDeposits()->attach($validatedData['rental_deposit_ids']);
            $model->layouts()->attach($validatedData['layout_ids']);
            $model->propertyTypes()->attach($validatedData['property_type_ids']);

            $user->createOrGetStripeCustomer();
            $user->refresh();

            $stripe = new \Stripe\StripeClient(config('cashier.secret'));
            $payInCents = $amount * 100;

            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $payInCents,
                'currency' => 'eur',
                'customer' => $user->stripe_id,
                'description' => 'Demande d\'accompagnement EtapSup #' . $model->id,
                'confirmation_method' => 'automatic',
                'confirm' => false, // on ne confirme pas encore : on attend Stripe.js
            ]);

            // 6) Sauvegarde de l’ID du PaymentIntent dans la base
            $model->stripe_payment_intent = $paymentIntent->id;
            $model->save();

            DB::commit();

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
                'id' => $model->hashid(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error($e);
            return response()->json([
                'error' => 'Erreur lors de la création du PaymentIntent.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function confirmPayment(Request $request): JsonResponse
    {
        try {
            $hashId = $request->input('search_id');
            $model = CustomSearch::findByHashidOrFail($hashId);

            // Récupérer le PaymentIntent (pour s'assurer que son statut est bien “succeeded”)
            $stripe = new StripeClient(config('cashier.secret'));
            $paymentIntent = $stripe->paymentIntents->retrieve($model->stripe_payment_intent);

            if ($paymentIntent->status === 'succeeded') {
                $model->update(['state' => 'payment_validated']);
                return response()->json([
                    'success' => true,
                    'search_id' => $hashId,
                ]);
            }

            $model->delete();
            // Si le statut n’est pas encore “succeeded”, on peut renvoyer une erreur ou une instruction
            return response()->json([
                'error' => 'Le paiement n’est pas dans l’état “succeeded”.',
                'message' => 'Status actuel : ' . $paymentIntent->status,
            ], 422);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la confirmation du paiement.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function refreshIntent(): JsonResponse
    {
        $user = auth()->user();
        $intent = $this->paymentService->createSetupIntent($user);

        return response()->json([
            'intent' => $intent,
        ]);
    }
}
