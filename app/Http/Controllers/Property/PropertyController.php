<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PropertyResource;
use App\Models\RealEstate\Property;
use App\Settings\RealEstateSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PropertyController extends Controller
{
    public function __construct(
        protected readonly Property $property,
        protected readonly RealEstateSettings $settings,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): \Inertia\Response
    {
        $page = $request->input('page', 1);
        $perPage = 3;

        $model = $this->property->whereSlug($id)
            ->with([
                'propertyType' => function ($query) {
                    return $query->select('id', 'label');
                },
                'city' => function ($query) {
                    return $query->select('id', 'name', 'region_id');
                },
                'category' => function ($query) {
                    return $query->select('id', 'label');
                },
                'subCategory' => function ($query) {
                    return $query->select('id', 'label');
                },
                'equipments', 'regulations', 'layouts',
                'city.region',
                'comments' => function ($query) use ($perPage) {
                    $query->with('user')
                        ->whereNull('parent_id')
                        ->latest()
                        ->take($perPage); // Charger seulement les 4 premiers
                },
                'comments.replies.user', // Charger les réponses si nécessaire
                'ratings',
            ])
            ->firstOrFail();

        return $this->returnView($model, $perPage, $page);
    }

    public function preview(Request $request, string $id): \Inertia\Response
    {
        $page = $request->input('page', 1);
        $perPage = 3;

        $model = $this->property->whereSlug($id)->withoutPublished()
            ->with([
                'propertyType' => function ($query) {
                    return $query->select('id', 'label');
                },
                'city' => function ($query) {
                    return $query->select('id', 'name', 'region_id');
                },
                'category' => function ($query) {
                    return $query->select('id', 'label');
                },
                'subCategory' => function ($query) {
                    return $query->select('id', 'label');
                },
                'equipments', 'regulations', 'layouts',
                'city.region',
                'comments' => function ($query) use ($perPage) {
                    $query->with('user')
                        ->whereNull('parent_id')
                        ->latest()
                        ->take($perPage); // Charger seulement les 4 premiers
                },
                'comments.replies.user', // Charger les réponses si nécessaire
                'ratings',
            ])
            ->firstOrFail();

        return $this->returnView($model, $perPage, $page);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
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

    public function review(Request $request, $id): RedirectResponse
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

        $model = $this->property->findByHashidOrFail($id);

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

        return to_route('properties.show', $model->slug);
    }

    public function loadMoreComments(Request $request, string $id): JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = 3;

        $comments = $this->property->whereSlug($id)
            ->firstOrFail()
            ->comments()
            ->with('user')
            ->whereNull('parent_id')
            ->latest()
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'comments' => CommentResource::collection($comments),
            'has_more' => $comments->count() === $perPage,
        ]);
    }

    /**
     * @param $model
     * @param int $perPage
     * @param mixed $page
     * @return \Inertia\Response
     */
    private function returnView($model, int $perPage, mixed $page): \Inertia\Response
    {
        $rental_monthly_billing = false;

        if (isset($model->category)) {
            if ($model->category->id === $this->settings->rental_monthly_billing) {
                $rental_monthly_billing = true;
            }
        }

        $totalComments = $model->comments()->whereNull('parent_id')->count();
        $hasMoreComments = $totalComments > $perPage;

        return Inertia::render('RealEstate/Show', [
            'property' => new PropertyResource($model),
            'settings' => $this->settings,
            'rentalMonthlyBilling' => $rental_monthly_billing,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $totalComments,
                'has_more' => $hasMoreComments,
            ],
        ]);
    }
}
