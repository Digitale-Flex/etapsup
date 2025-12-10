<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\CertificateRequestResource;
use App\Http\Resources\UserResource;
use App\Models\Certificate\CertificateRequest;
use App\Traits\HandlesBase64Files;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CertificateRequestController extends Controller
{
    public function __construct(
        protected readonly CertificateRequest $certificateRequest
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): \Inertia\Response
    {
        return Inertia::render('Dashboard/CertificateRequest/Index', [
            'certificateRequests' => Inertia::defer(
                fn () => CertificateRequestResource::collection(
                    $this->certificateRequest->query()
                        ->with([
                            'city' => function ($query) {
                                $query->select('id', 'name', 'country_id'); // A20
                            }, 'city.country' => function ($query) { // A20
                                $query->select('id', 'name');
                            }, 'genre' => function ($query) {
                                $query->select('id', 'name');
                            }, 'rentalDeposit' => function ($query) {
                                $query->select('id', 'name');
                            }, 'partner' => function ($query) {
                                $query->select('id', 'label');
                            },
                        ])
                        ->owner()
                        ->latest()
                        ->paginate(3)
                )
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): \Inertia\Response
    {
        $query = $this->certificateRequest->query()
            ->with([
                'city' => function ($query) {$query->select('id', 'name', 'country_id');}, // A20
                'city.country' => function ($query) {$query->select('id', 'name');}, // A20
                'genre' => function ($query) {$query->select('id', 'name');},
                'rentalDeposit' => function ($query) {$query->select('id', 'name');},
                'partner' => function ($query) {$query->select('id', 'label');},
                'rentalDeposits'
            ])
            ->owner()
            ->findByHashidOrFail($id);

        return Inertia::render('Dashboard/CertificateRequest/Show', [
            'certificateRequest' => new CertificateRequestResource($query),
            'user' => new UserResource(auth()->user()->load('country')),
        ]);
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
}
