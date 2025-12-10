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
                    return $query->select('id', 'name', 'country_id'); // A20: region_id → country_id
                },
                'category' => function ($query) {
                    return $query->select('id', 'label');
                },
                'subCategory' => function ($query) {
                    return $query->select('id', 'label');
                },
                'equipments', 'regulations', 'layouts',
                'city.country', // A20: region → country
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
                    return $query->select('id', 'name', 'country_id'); // A20: region_id → country_id
                },
                'category' => function ($query) {
                    return $query->select('id', 'label');
                },
                'subCategory' => function ($query) {
                    return $query->select('id', 'label');
                },
                'equipments', 'regulations', 'layouts',
                'city.country', // A20: region → country
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

    /**
     * Ajouter un avis sur un établissement
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function review(Request $request, $id): RedirectResponse
    {
        $messages = [
            'rating.required' => 'Merci de donner une note à cet établissement',
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
                'general' => 'Vous avez déjà noté cet établissement',
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
     * Retourne la vue de fiche détaillée établissement
     *
     * MAPPING ETATSUP: Property = Establishment
     *
     * @param $model Property (Establishment)
     * @param int $perPage Nombre de commentaires par page
     * @param mixed $page Page actuelle
     * @return \Inertia\Response
     */
    private function returnView($model, int $perPage, mixed $page): \Inertia\Response
    {
        $totalComments = $model->comments()->whereNull('parent_id')->count();
        $hasMoreComments = $totalComments > $perPage;

        // Utiliser EstablishmentResource pour éviter d'exposer les champs immobiliers
        return Inertia::render('Establishments/Show', [
            'establishment' => new \App\Http\Resources\EstablishmentResource($model),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $totalComments,
                'has_more' => $hasMoreComments,
            ],
        ]);
    }
}
