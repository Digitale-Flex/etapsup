<?php

namespace App\Http\Controllers\Api;

// Sprint1 Update: Feature 1.1.1 — Espace Étudiant (Connexion & Profil)
// Sprint1 Update: Feature 1.8.1 — Dossier de candidature & pièces justificatives

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * Contrôleur pour la gestion des documents étudiant
 *
 * Feature 1.1.1 — Espace Étudiant (Connexion & Profil)
 * Feature 1.8.1 — Dossier de candidature & pièces justificatives
 *
 * Endpoints:
 * - GET /api/v1/documents
 * - POST /api/v1/documents
 * - DELETE /api/v1/documents/{document}
 * - GET /api/v1/documents/{document}/download
 */
class DocumentController extends Controller
{
    /**
     * Liste des documents de l'étudiant connecté
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Récupérer tous les documents liés aux candidatures de l'étudiant
            $documents = ApplicationDocument::whereHas('application', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['application.property:id,title,slug']) // Charger relation établissement
            ->latest()
            ->get()
            ->map(function ($document) {
                return [
                    'id' => $document->id,
                    'type' => $document->document_type,
                    'file_name' => basename($document->file_path),
                    'file_url' => $document->file_path ? Storage::url($document->file_path) : null,
                    'verified' => $document->verified,
                    'verified_at' => $document->verified_at?->format('d/m/Y à H:i'),
                    'uploaded_at' => $document->created_at?->format('d/m/Y à H:i'),
                    'establishment' => $document->application?->property?->title ?? 'Non spécifié',
                ];
            });

            return response()->json([
                'success' => true,
                'documents' => $documents,
            ], 200);

        } catch (\Exception $e) {
            logger()->error('Erreur récupération documents étudiant', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des documents',
            ], 500);
        }
    }

    /**
     * Upload d'un nouveau document
     *
     * Critères d'acceptation :
     * - Formats acceptés : pdf, jpg, jpeg, png
     * - Taille max : 5 Mo
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'application_id' => ['required', 'exists:reservations,id'],
            'document_type' => ['required', 'string', 'max:255'],
            'file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120', // 5 Mo max
            ],
        ], [
            'application_id.required' => 'La candidature est requise',
            'application_id.exists' => 'Candidature introuvable',
            'document_type.required' => 'Le type de document est requis',
            'file.required' => 'Veuillez sélectionner un fichier',
            'file.mimes' => 'Formats acceptés : PDF, JPG, JPEG, PNG',
            'file.max' => 'La taille maximale est de 5 Mo',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = $request->user();

            // Vérifier que la candidature appartient bien à l'utilisateur
            $application = \App\Models\Application::where('id', $request->application_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Sprint1 Feature 1.8.1 - Upload vers disk 'private' pour sécurité
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/' . $user->id, $fileName, 'private');

            // Créer l'enregistrement du document
            $document = ApplicationDocument::create([
                'application_id' => $application->id,
                'document_type' => $request->document_type,
                'file_path' => $filePath,
                'verified' => false,
            ]);

            // Sprint1 Feature 1.8.1 - Log audit upload
            logger()->info('Document uploadé', [
                'user_id' => $user->id,
                'application_id' => $application->id,
                'document_id' => $document->id,
                'document_type' => $document->document_type,
                'file_name' => $fileName,
                'file_size' => $file->getSize(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document téléversé avec succès',
                'document' => [
                    'id' => $document->id,
                    'document_type' => $document->document_type,
                    'file_name' => basename($document->file_path),
                    'file_size' => $file->getSize(),
                    'verified' => $document->verified,
                    'created_at' => $document->created_at->format('d/m/Y à H:i'),
                ],
            ], 201);

        } catch (\Exception $e) {
            logger()->error('Erreur upload document étudiant', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload du document',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Supprimer un document
     *
     * Sprint1 Feature 1.8.1 — Dossier de candidature & pièces justificatives
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = auth()->user();
            $document = ApplicationDocument::findOrFail($id);

            // Vérifier ownership
            if ($document->application->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé',
                ], 403);
            }

            // Empêcher suppression si vérifié
            if ($document->verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document déjà vérifié, suppression impossible',
                ], 422);
            }

            // Supprimer fichier
            if (Storage::disk('private')->exists($document->file_path)) {
                Storage::disk('private')->delete($document->file_path);
            }

            logger()->info('Document supprimé', [
                'user_id' => $user->id,
                'document_id' => $document->id,
                'document_type' => $document->document_type,
            ]);

            $document->delete();

            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            logger()->error('Erreur suppression document', [
                'document_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur suppression',
            ], 500);
        }
    }

    /**
     * Télécharger un document
     *
     * Sprint1 Feature 1.8.1 — Accès sécurisé aux documents (disk private)
     *
     * @param string $id
     * @return mixed
     */
    public function download(string $id)
    {
        try {
            $user = auth()->user();
            $document = ApplicationDocument::findOrFail($id);

            // Vérifier ownership
            if ($document->application->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // Vérifier existence fichier
            if (!Storage::disk('private')->exists($document->file_path)) {
                return response()->json(['success' => false, 'message' => 'Fichier introuvable'], 404);
            }

            logger()->info('Document téléchargé', [
                'user_id' => $user->id,
                'document_id' => $document->id,
            ]);

            return Storage::disk('private')->download($document->file_path, basename($document->file_path));

        } catch (\Exception $e) {
            logger()->error('Erreur téléchargement', ['document_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur'], 500);
        }
    }
}
