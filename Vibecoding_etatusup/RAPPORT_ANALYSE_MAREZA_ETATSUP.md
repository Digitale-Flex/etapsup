# RAPPORT D'ANALYSE : ADAPTATION MAREZA → ETATSUP

## 1. ANALYSE COMPARATIVE MAREZA vs ETATSUP

### 1.1 Architecture Existante Mareza

**Stack Technique Identifié :**
- **Backend :** Laravel 10 avec Inertia.js
- **Frontend :** Vue.js 3 avec TypeScript
- **UI Framework :** Bootstrap Vue Next + PrimeVue
- **Base de données :** MySQL (modèles Eloquent)
- **Authentification :** Laravel Breeze
- **Médias :** Spatie Media Library
- **Admin :** Filament PHP

**Modèles Métier Principaux :**
- `Property` : Logements immobiliers
- `PropertyType` : Types de propriétés
- `Category/SubCategory` : Catégorisation
- `Reservation` : Réservations
- `CertificateRequest` : Demandes de certificats
- `User` : Utilisateurs
- `City/Country` : Géolocalisation

### 1.2 Écarts Fonctionnels Identifiés

| **Domaine** | **Mareza (Existant)** | **EtapSup (Cible)** | **Écart** |
|-------------|----------------------|---------------------|-----------|
| **Métier** | Immobilier/Location | Éducation/Établissements | **MAJEUR** - Changement complet du domaine |
| **Entités principales** | Property, Reservation | Establishment, Application | **MAJEUR** - Nouveaux modèles requis |
| **Processus de paiement** | Réservation logement | Frais de candidature (10/90%) | **MINEUR** - Stripe Connect déjà présent |
| **Géolocalisation** | Villes/Régions | Pays africains focus | **MINEUR** - Structure similaire |
| **Authentification** | Standard Laravel | Même système | **AUCUN** - Réutilisable |
| **Admin** | Filament pour propriétés | Filament pour établissements | **MINEUR** - Adaptation des ressources |
| **Upload fichiers** | Images propriétés | Documents candidature | **MINEUR** - AWS S3 à configurer |

### 1.3 Composants Réutilisables

**✅ RÉUTILISABLES DIRECTEMENT :**
- Système d'authentification complet
- Architecture Inertia.js + Vue 3
- Composants UI de base (forms, layouts)
- Système de paiement Stripe
- Structure admin Filament
- Gestion des médias/uploads

**🔄 ADAPTABLES :**
- Modèles de géolocalisation (City → Country focus Afrique)
- Système de filtres (PropertyFilter → EstablishmentFilter)
- Contrôleurs de base (structure similaire)
- Pages Vue.js (templates réutilisables)

**❌ À CRÉER ENTIÈREMENT :**
- Modèles métier éducation (Establishment, Program, Application)
- Logique de candidature et suivi
- Pages spécifiques EtapSup
- Intégration Google Maps pour établissements

## 2. PLAN D'ADAPTATION TECHNIQUE

### 2.1 Phase 1 : Adaptation des Modèles (Sprint 1 - Semaine 1)

**Nouveaux Modèles à Créer :**

```php
// app/Models/Education/Establishment.php
class Establishment extends Model {
    protected $fillable = [
        'name', 'description', 'country_id', 'city', 
        'address', 'phone', 'email', 'website',
        'logo_path', 'cover_image_path', 'is_verified',
        'stripe_account_id' // Pour Stripe Connect
    ];
}

// app/Models/Education/Program.php  
class Program extends Model {
    protected $fillable = [
        'establishment_id', 'name', 'description',
        'duration', 'level', 'application_fee',
        'requirements', 'is_active'
    ];
}

// app/Models/Education/Application.php
class Application extends Model {
    protected $fillable = [
        'user_id', 'program_id', 'status',
        'application_date', 'documents_path',
        'payment_status', 'stripe_payment_intent_id'
    ];
}
```

**Migrations à Créer :**
- `create_establishments_table.php`
- `create_programs_table.php` 
- `create_applications_table.php`
- `create_establishment_program_pivot.php`

### 2.2 Phase 2 : Adaptation des Contrôleurs (Sprint 1 - Semaine 2)

**Contrôleurs à Créer/Adapter :**

```php
// app/Http/Controllers/EstablishmentController.php
// Adaptation de PropertyController.php
class EstablishmentController extends Controller {
    public function index() // Liste avec filtres
    public function show($id) // Fiche établissement
    public function programs($id) // Programmes par établissement
}

// app/Http/Controllers/ApplicationController.php  
// Nouveau contrôleur inspiré de ReservationController
class ApplicationController extends Controller {
    public function store() // Créer candidature
    public function payment() // Processus paiement
    public function track() // Suivi candidature
}
```

**Routes à Adapter :**
```php
// routes/web.php - Remplacer routes immobilières
Route::get('/', [EstablishmentController::class, 'index'])->name('home');
Route::get('/establishment/{id}', [EstablishmentController::class, 'show'])->name('establishment.show');
Route::post('/application', [ApplicationController::class, 'store'])->name('application.store');
Route::get('/dashboard/applications', [DashboardController::class, 'applications'])->name('dashboard.applications');
```

### 2.3 Phase 3 : Adaptation Frontend (Sprint 1 - Semaine 3)

**Pages Vue.js à Créer/Adapter :**

```
resources/js/Pages/
├── Home/
│   └── Index.vue (adapter RealEstate/Index.vue)
├── Establishment/
│   ├── Index.vue (liste établissements)
│   ├── Show.vue (fiche établissement)
│   └── Programs.vue (programmes)
├── Application/
│   ├── Create.vue (formulaire candidature)
│   └── Track.vue (suivi)
└── Dashboard/
    ├── Applications.vue (mes candidatures)
    └── Documents.vue (mes documents)
```

**Composants à Adapter :**
- `EstablishmentCard.vue` (adapter PropertyCard)
- `EstablishmentFilter.vue` (adapter PropertyFilter)
- `ApplicationForm.vue` (nouveau, inspiré CustomSearch)
- `PaymentForm.vue` (réutiliser existant)

### 2.4 Phase 4 : Configuration & Intégrations (Sprint 1 - Semaine 4)

**AWS S3 Configuration :**
```php
// config/filesystems.php - Déjà configuré
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
]
```

**Stripe Connect Setup :**
```php
// app/Services/StripeConnectService.php
class StripeConnectService {
    public function createConnectedAccount($establishment) {
        // 90% vers établissement, 10% vers EtapSup
    }
}
```

**Google Maps Integration :**
```javascript
// resources/js/Components/EstablishmentMap.vue
// Intégration Google Maps API pour localisation
```

## 3. MAQUETTES TEXTUELLES DES PAGES CLÉS

### 3.1 Page d'Accueil (Landing)

```
=== HEADER ===
[LOGO EtapSup] [Accueil] [Établissements] [Comment ça marche] [Connexion] [Inscription]

=== HERO SECTION ===
"Trouvez votre établissement d'enseignement supérieur en Afrique"
"Découvrez, postulez et suivez vos candidatures en un seul endroit"
[Bouton CTA: "Découvrir les établissements"]

=== BARRE DE RECHERCHE ===
[Pays ▼] [Ville] [Domaine d'études ▼] [Niveau ▼] [RECHERCHER]

=== ÉTABLISSEMENTS POPULAIRES ===
Grille 3x2 d'établissements avec :
- Photo de couverture
- Logo établissement  
- Nom + Ville, Pays
- "X programmes disponibles"
- Note/étoiles si disponible
- [Bouton "Voir les programmes"]

=== COMMENT ÇA MARCHE ===
1. "Découvrez" - Explorez les établissements
2. "Postulez" - Soumettez votre candidature  
3. "Suivez" - Trackez vos demandes

=== FOOTER ===
[Liens légaux] [Contact] [Réseaux sociaux]
```

### 3.2 Fiche Établissement

```
=== BREADCRUMB ===
Accueil > Établissements > [Pays] > [Nom Établissement]

=== HEADER ÉTABLISSEMENT ===
[Photo couverture en arrière-plan]
[Logo établissement] 
[Nom] [Badge "Vérifié" si applicable]
[Ville, Pays] [Site web] [Téléphone]
[Bouton "Voir sur la carte"]

=== NAVIGATION ONGLETS ===
[Présentation] [Programmes] [Admission] [Contact]

=== SECTION PRÉSENTATION ===
- Description établissement
- Galerie photos
- Informations pratiques

=== SECTION PROGRAMMES ===
Liste des programmes avec :
- Nom du programme
- Durée
- Niveau (Licence, Master, etc.)
- Frais de candidature
- [Bouton "Postuler"]

=== CARTE GOOGLE MAPS ===
Localisation de l'établissement

=== SIDEBAR ===
- Informations de contact
- Bouton "Postuler maintenant"
- Programmes populaires
```

### 3.3 Dashboard Utilisateur

```
=== NAVIGATION DASHBOARD ===
[Mes candidatures] [Mes documents] [Profil] [Paramètres]

=== MES CANDIDATURES ===
Tableau avec colonnes :
- Établissement
- Programme  
- Date candidature
- Statut (En attente, Acceptée, Refusée)
- Actions (Voir détails, Télécharger reçu)

=== FILTRES ===
[Tous les statuts ▼] [Tous les établissements ▼] [Période ▼]

=== DÉTAIL CANDIDATURE (Modal/Page) ===
- Informations programme
- Documents soumis
- Historique des statuts
- Paiement effectué
- Actions possibles

=== MES DOCUMENTS ===
- CV
- Diplômes
- Lettres de motivation
- Autres documents
[Bouton "Ajouter un document"]

=== STATISTIQUES ===
- Nombre total de candidatures
- Candidatures en attente
- Taux d'acceptation
```

## 4. MAPPING USER STORIES → TÂCHES TECHNIQUES

### 4.1 Story 1 : "Amina découvre la plateforme"

**User Story :** En tant qu'Amina, je veux découvrir les établissements disponibles pour explorer mes options d'études.

**Tâches Techniques :**
1. **Créer le modèle Establishment** 
   - Migration + Model + Factory
   - Relations avec Country/City
   - Scope pour établissements actifs

2. **Développer EstablishmentController@index**
   - Logique de filtrage (pays, ville, domaine)
   - Pagination
   - Ressource API pour Vue.js

3. **Créer la page Home/Index.vue**
   - Composant EstablishmentCard
   - Barre de recherche avec filtres
   - Intégration avec backend

4. **Implémenter EstablishmentFilter**
   - Filtres par pays, ville, domaine d'études
   - Recherche textuelle
   - Tri par popularité/nom

### 4.2 Story 2 : "Amina consulte une fiche établissement"

**User Story :** En tant qu'Amina, je veux consulter les détails d'un établissement pour évaluer s'il correspond à mes attentes.

**Tâches Techniques :**
1. **Créer EstablishmentController@show**
   - Chargement établissement avec relations
   - Gestion des programmes associés
   - Intégration Google Maps

2. **Développer Establishment/Show.vue**
   - Affichage informations établissement
   - Galerie photos (Spatie Media Library)
   - Onglets navigation (présentation, programmes)

3. **Créer le modèle Program**
   - Migration + Model
   - Relation avec Establishment
   - Champs spécifiques éducation

4. **Intégrer Google Maps**
   - Composant EstablishmentMap.vue
   - API Google Maps
   - Géolocalisation établissement

### 4.3 Story 3 : "Amina s'authentifie"

**User Story :** En tant qu'Amina, je veux créer un compte pour pouvoir postuler aux établissements.

**Tâches Techniques :**
1. **Adapter les pages d'authentification existantes**
   - Personnaliser Auth/Login.vue
   - Personnaliser Auth/Register.vue
   - Adapter les textes pour le contexte éducatif

2. **Configurer les redirections**
   - Redirection post-login vers dashboard
   - Middleware auth pour candidatures
   - Gestion des rôles (étudiant, établissement, admin)

### 4.4 Story 4 : "Amina télécharge le livret PDF"

**User Story :** En tant qu'Amina, je veux télécharger un livret PDF avec les informations de l'établissement.

**Tâches Techniques :**
1. **Créer PdfGenerationService**
   - Service de génération PDF (DomPDF/Snappy)
   - Template PDF pour établissement
   - Intégration avec données établissement

2. **Ajouter route de téléchargement**
   - Route protégée par authentification
   - Contrôleur pour génération/téléchargement
   - Gestion des erreurs

3. **Intégrer bouton téléchargement**
   - Bouton dans fiche établissement
   - Feedback utilisateur (loading, succès)
   - Tracking des téléchargements

### 4.5 Story 5 : "Amina visualise les établissements sur une carte"

**User Story :** En tant qu'Amina, je veux voir les établissements sur une carte pour comprendre leur localisation.

**Tâches Techniques :**
1. **Intégrer Google Maps API**
   - Configuration API key
   - Composant MapView.vue
   - Markers pour établissements

2. **Créer endpoint géolocalisation**
   - API pour établissements avec coordonnées
   - Filtrage par zone géographique
   - Optimisation requêtes

3. **Développer interface carte**
   - Vue carte/liste toggle
   - InfoWindow pour établissements
   - Filtres géographiques

### 4.6 Story 6 : "Amina gère son backoffice admin"

**User Story :** En tant qu'admin, je veux gérer les établissements, utilisateurs et candidatures.

**Tâches Techniques :**
1. **Adapter Filament Resources**
   - EstablishmentResource (CRUD établissements)
   - ProgramResource (CRUD programmes)
   - ApplicationResource (gestion candidatures)
   - UserResource (gestion utilisateurs)

2. **Configurer permissions**
   - Policies pour chaque ressource
   - Rôles admin/établissement/étudiant
   - Middleware de protection

3. **Créer widgets dashboard**
   - Statistiques candidatures
   - Graphiques établissements
   - Métriques utilisateurs

### 4.7 Story 7 : "Amina intègre Stripe Connect"

**User Story :** En tant qu'établissement, je veux recevoir 90% des frais de candidature via Stripe Connect.

**Tâches Techniques :**
1. **Configurer Stripe Connect**
   - Service StripeConnectService
   - Onboarding établissements
   - Gestion comptes connectés

2. **Implémenter split payment**
   - 10% EtapSup / 90% établissement
   - Gestion des transferts
   - Webhooks Stripe

3. **Créer interface paiement**
   - Formulaire paiement candidature
   - Confirmation paiement
   - Historique transactions

### 4.8 Story 8 : "Amina suit ses candidatures"

**User Story :** En tant qu'Amina, je veux suivre le statut de mes candidatures depuis mon dashboard.

**Tâches Techniques :**
1. **Créer le modèle Application**
   - Migration avec statuts
   - Relations User/Program
   - Timestamps pour tracking

2. **Développer ApplicationController**
   - CRUD candidatures
   - Changement de statut
   - Notifications utilisateur

3. **Créer Dashboard/Applications.vue**
   - Liste candidatures utilisateur
   - Filtres par statut
   - Actions sur candidatures

### 4.9 Story 9 : "Amina upload ses documents"

**User Story :** En tant qu'Amina, je veux uploader mes documents de candidature sur AWS S3.

**Tâches Techniques :**
1. **Configurer AWS S3**
   - Configuration Laravel Filesystem
   - Buckets pour documents
   - Permissions et sécurité

2. **Créer DocumentUploadService**
   - Service upload S3
   - Validation types fichiers
   - Gestion erreurs upload

3. **Développer interface upload**
   - Composant FileUpload.vue
   - Drag & drop
   - Progress bar upload

## 5. ESTIMATION TEMPORELLE

### Sprint 1 (4 semaines) - MVP
- **Semaine 1 :** Modèles + Migrations + Seeders
- **Semaine 2 :** Contrôleurs + Routes + API
- **Semaine 3 :** Pages Vue.js + Composants
- **Semaine 4 :** Intégrations (Stripe, AWS, Maps) + Tests

### Effort Total Estimé : **120-150 heures**
- Backend (modèles, contrôleurs, API) : 40h
- Frontend (Vue.js, composants) : 50h  
- Intégrations (Stripe, AWS, Maps) : 30h
- Tests + Debug + Documentation : 20-40h

## 6. RISQUES ET RECOMMANDATIONS

### Risques Identifiés
1. **Complexité Stripe Connect** - Gestion des comptes établissements
2. **Performance Google Maps** - Optimisation chargement cartes
3. **Sécurité uploads AWS** - Validation fichiers malveillants
4. **Migration données** - Si données existantes à préserver

### Recommandations
1. **Commencer par le MVP** - Fonctionnalités core d'abord
2. **Tests automatisés** - PHPUnit + Pest pour backend
3. **Documentation API** - Swagger/OpenAPI
4. **Monitoring** - Logs applicatifs + métriques business
5. **Backup stratégie** - Sauvegarde base + fichiers S3

---

**Rapport généré le :** {{ date('Y-m-d H:i:s') }}  
**Auteur :** Assistant IA VibeCoding  
**Version :** 1.0  
**Statut :** Prêt pour implémentation Sprint 1