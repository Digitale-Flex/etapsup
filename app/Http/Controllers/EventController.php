<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\EventRegistrationRequest;

// Refonte: Story 1.1.1 - Event Landing Page Controller
class EventController extends Controller
{
    /**
     * Display the event landing page
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('refonte/EventLanding', [
            'event' => [
                'title' => 'Webinaire: Réalisez Votre Rêve d\'Étudier en France',
                'date' => '2024-10-05 19:00:00',
                'description' => 'Découvrez les secrets pour intégrer les meilleures universités françaises',
                'benefits' => [
                    'Orientation personnalisée gratuite',
                    'Accès à notre réseau d\'universités partenaires',
                    'Simplification des démarches administratives',
                    'Solutions de financement et bourses'
                ],
                'testimonials' => [
                    [
                        'name' => 'Amina M.',
                        'country' => 'Cameroun',
                        'university' => 'ESSEC Business School',
                        'quote' => 'Grâce à EtapSup, j\'ai pu intégrer l\'école de mes rêves en seulement 3 mois !',
                        'rating' => 5
                    ],
                    [
                        'name' => 'Omar S.',
                        'country' => 'Sénégal',
                        'university' => 'Université Paris-Dauphine',
                        'quote' => 'L\'accompagnement personnalisé m\'a permis de naviguer facilement dans les démarches.',
                        'rating' => 5
                    ],
                    [
                        'name' => 'Fatou D.',
                        'country' => 'Côte d\'Ivoire',
                        'university' => 'Sciences Po Paris',
                        'quote' => 'Une équipe exceptionnelle qui m\'a aidée à réaliser mon rêve d\'étudier en France.',
                        'rating' => 5
                    ]
                ]
            ]
        ]);
    }

    /**
     * Handle event registration
     *
     * @param EventRegistrationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(EventRegistrationRequest $request)
    {
        // Get validated data from Form Request
        $validated = $request->validated();

        try {
            // Store the registration (you can create a EventRegistration model)
            $registration = \App\Models\EventRegistration::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'country' => $validated['country'],
                'phone' => $validated['phone'] ?? null,
                'study_level' => $validated['study_level'] ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'registered_at' => now()
            ]);

            // Send confirmation email directement (sans job pour debug)
            try {
                \Illuminate\Support\Facades\Mail::to($validated['email'])
                    ->send(new \App\Mail\EventConfirmationMail($registration));

                \Illuminate\Support\Facades\Log::info('Event email sent successfully', [
                    'to' => $validated['email'],
                    'registration_id' => $registration->id
                ]);
            } catch (\Exception $mailError) {
                \Illuminate\Support\Facades\Log::error('Event email FAILED', [
                    'to' => $validated['email'],
                    'error' => $mailError->getMessage(),
                    'trace' => $mailError->getTraceAsString()
                ]);
                // Continue même si email échoue - l'inscription est réussie
            }

            // Track conversion in analytics
            \Illuminate\Support\Facades\Log::info('Event registration successful', [
                'registration_id' => $registration->id,
                'email' => $validated['email'],
                'country' => $validated['country']
            ]);

            // Rediriger vers la page de remerciement
            return redirect()->route('events.thanks')->with([
                'success' => true,
                'message' => 'Inscription réussie ! Vous recevrez bientôt un email de confirmation.',
                'registration_id' => $registration->id
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Event registration failed', [
                'error' => $e->getMessage(),
                'email' => $validated['email'] ?? 'unknown'
            ]);

            return back()->withErrors([
                'form' => 'Une erreur est survenue. Veuillez réessayer.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * Get event statistics for the landing page
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        $stats = [
            'total_registrations' => \App\Models\EventRegistration::count(),
            'countries_represented' => \App\Models\EventRegistration::distinct('country')->count(),
            'available_spots' => max(0, 200 - \App\Models\EventRegistration::count()),
            'success_stories' => 95 // Static for now
        ];

        return response()->json($stats);
    }

    /**
     * Display the thank you page after registration
     *
     * @return \Inertia\Response
     */
    public function thanks()
    {
        return Inertia::render('refonte/RemerciementEvent', [
            'registration' => session('registration')
        ]);
    }
}