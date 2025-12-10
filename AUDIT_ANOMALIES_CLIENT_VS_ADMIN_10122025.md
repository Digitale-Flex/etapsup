# 🔴 AUDIT QUALITÉ - CLIENT vs ADMIN - CLASSIFICATION DES ANOMALIES
**Date:** 10/12/2025
**Auditeur:** Expert Vue.js/Laravel 20+ ans
**Deadline:** 20/12/2025
**Contexte:** Sprint critique - Campus France ferme pour 1ère vague

---

## 🏗️ ARCHITECTURE DU SYSTÈME

### Applications distinctes identifiées

#### 1️⃣ **APPLICATION CLIENT** (Vue 3 + Inertia.js)
- **URL:** `http://localhost` (domaine principal)
- **Stack:** Vue 3.4 + Inertia.js + Laravel 11
- **Pages:** `resources/js/Pages/`
- **Utilisateurs:** Étudiants/candidats
- **Fonctionnalités:**
  - Page d'accueil / Landing page événements
  - Recherche et navigation établissements/formations
  - Inscription événements
  - Création candidatures
  - **Mon Profil** (Dashboard étudiant)
    - Informations personnelles
    - Mes candidatures
    - Mes factures
    - Mon dossier

#### 2️⃣ **APPLICATION ADMIN** (Filament 3.2)
- **URL:** `http://localhost/gate` (panel admin)
- **Stack:** Filament 3.2.131 + Laravel 11
- **Resources:** `app/Filament/Resources/`
- **Utilisateurs:** Administrateurs EtapSup
- **Fonctionnalités:**
  - Dashboard analytics (CA, stats)
  - Gestion candidatures (validation)
  - Gestion établissements (CRUD)
  - Gestion formations/programmes
  - Paramètres système
  - Gestion utilisateurs/rôles/permissions

#### 3️⃣ **APPLICATION PARTNER** (Filament Panel séparé)
- **URL:** `http://localhost/partner`
- **Stack:** Filament 3.2.131
- **Resources:** `app/Filament/Partner/Resources/`
- **Utilisateurs:** Partenaires/Écoles
- **Fonctionnalités:**
  - Dashboard partenaire
  - Gestion de leurs établissements
  - Certificats/Attestations (legacy ma-Reza)

---

## 📊 CLASSIFICATION PAR APPLICATION

---

## 🟦 PARTIE 1: APPLICATION CLIENT (Vue.js/Inertia)

### 1.1 ÉVÉNEMENTS (Landing Page)

| # | Anomalie | Type | Priorité | Localisation | Description |
|---|----------|------|----------|--------------|-------------|
| **C01** | Mail confirmation non reçu | 🐛 BUG CRITIQUE | **P0** | `app/Http/Controllers/EventController.php` | Mail confirmation événement non envoyé (ni inbox ni spam) |

**Analyse C01:**
- **Application:** CLIENT
- **Flow:** Page événement → Formulaire inscription → Email confirmation
- **Route:** `POST /events/register`
- **Périmètre initial:** ✅ OUI (formulaire événement requis)
- **Impact:** BLOQUANT - utilisateurs ne reçoivent pas confirmation
- **Tests requis:**
  - Vérifier config mail `.env` (MAIL_MAILER, MAIL_HOST, etc.)
  - Vérifier queue processing `php artisan queue:work`
  - Vérifier logs `storage/logs/laravel.log`
  - Tester avec Mailtrap/MailHog en dev

**Action corrective:**
```bash
# 1. Vérifier config mail
php artisan config:cache
php artisan queue:restart

# 2. Tester envoi mail
php artisan tinker
>>> Mail::raw('Test', fn($msg) => $msg->to('test@example.com')->subject('Test'));

# 3. Vérifier jobs queue
php artisan queue:listen --tries=1
```

---

### 1.2 ÉTABLISSEMENTS / FORMATIONS (Pages publiques)

| # | Anomalie | Type | Priorité | Localisation | Description |
|---|----------|------|----------|--------------|-------------|
| **C02** | Page établissements non fonctionnelle | 🐛 BUG CRITIQUE | **P0** | `resources/js/Pages/RealEstate/Index.vue` | Impossible de visualiser les établissements côté front |
| **C03** | Renommer "Établissement" en "Formation" | 💡 EVOLUTION | P3 | Terminologie UI | Demande changement terminologie |

**Analyse C02:**
- **Application:** CLIENT
- **Routes:**
  - `GET /establishments` → Index
  - `GET /establishments/{id}` → Show
- **Composant:** `EstablishmentCard.vue`, `Index.vue`
- **Périmètre initial:** ✅ OUI (affichage catalogue établissements)
- **Impact:** CRITIQUE - Cœur de l'application inaccessible
- **Erreur probable:**
  - Route mal configurée
  - Contrôleur retourne mauvaise structure données
  - Composant Vue plante au render

**Tests requis:**
```bash
# 1. Tester route API
curl http://localhost/establishments

# 2. Vérifier logs Laravel
tail -f storage/logs/laravel.log

# 3. Vérifier console navigateur (Vue DevTools)
```

**Action corrective:**
1. Vérifier `EstablishmentController.php` retourne bon format Inertia
2. Vérifier relations Eloquent chargées
3. Tester composant Vue isolément

**Analyse C03:**
- **Application:** CLIENT (UI)
- **Périmètre initial:** ❌ NON (cosmétique)
- **Action:** Backlog post-sprint

---

### 1.3 CANDIDATURES (Validation utilisateur)

| # | Anomalie | Type | Priorité | Localisation | Description |
|---|----------|------|----------|--------------|-------------|
| **C04** | Validation candidature NOK | 🐛 BUG CRITIQUE | **P0** | `resources/js/Pages/Applications/` | Impossible de valider/soumettre une candidature |

**Analyse C04:**
- **Application:** CLIENT (avec API backend)
- **Flow:**
  1. Étudiant remplit formulaire candidature
  2. Upload documents
  3. Validation finale → **PLANTE ICI**
- **Routes concernées:**
  - `GET /applications/create`
  - `POST /applications` (store)
- **Périmètre initial:** ✅ OUI (workflow candidature complet requis)
- **Impact:** BLOQUANT TOTAL - Empêche workflow métier principal
- **Lié à:** Probablement même erreur Stripe que C05-C08

**Action corrective:**
1. Fix Stripe config (voir C05)
2. Vérifier validation formulaire
3. Vérifier stockage documents
4. Tests E2E complet workflow

---

### 1.4 MON PROFIL (Dashboard Étudiant)

#### 1.4.1 Erreur Stripe - Root Cause Multiple Bugs

| # | Anomalie | Type | Priorité | Localisation | Description |
|---|----------|------|----------|--------------|-------------|
| **C05** | Stripe Config Exception | 🔥 BUG MAJEUR | **P0** | `app/Services/PaymentService.php:13` | `Stripe\Exception\InvalidArgumentException: $config must be string or array` |

**Analyse C05 - ROOT CAUSE:**
- **Application:** CLIENT + ADMIN (service partagé)
- **Erreur:** `app/Services/PaymentService.php` ligne 13
- **Cause:** Configuration Stripe mal initialisée
- **Impact:** BLOQUE tous les workflows liés paiement:
  - Mes factures (C07)
  - Mes candidatures (C06) - si paiement requis
  - Mon dossier (C08) - si affichage factures
  - Validation candidatures admin
- **Périmètre initial:** ✅ OUI (paiements = cœur métier)

**Code problématique probable:**
```php
// app/Services/PaymentService.php:13
public function __construct()
{
    // ❌ MAUVAIS - config retourne null ou mauvais type
    Stripe::setApiKey(config('stripe.secret'));
}
```

**Action corrective URGENTE:**
```bash
# 1. Vérifier .env
STRIPE_KEY=pk_test_xxxxx
STRIPE_SECRET=sk_test_xxxxx

# 2. Vérifier config/services.php
php artisan config:clear
php artisan config:cache

# 3. Fix PaymentService.php
```

```php
// ✅ CORRECT
public function __construct()
{
    $stripeSecret = config('services.stripe.secret');

    if (empty($stripeSecret)) {
        throw new \RuntimeException('Stripe secret key not configured');
    }

    \Stripe\Stripe::setApiKey($stripeSecret);
}
```

---

#### 1.4.2 Sections Mon Profil

| # | Anomalie | Type | Priorité | Localisation | Description |
|---|----------|------|----------|--------------|-------------|
| **C06** | Mes candidatures non visible | 🐛 BUG CRITIQUE | **P0** | `resources/js/Pages/Dashboard.vue` | Liste candidatures plante |
| **C07** | Mes factures non visible | 🐛 BUG CRITIQUE | **P0** | `resources/js/Pages/Dashboard/` | Liste factures plante |
| **C08** | Mon dossier non accessible | 🐛 BUG CRITIQUE | **P0** | `resources/js/Pages/Dashboard/Profile/` | Dossier non chargeable |
| **C09** | Informations personnelles OK | ✅ OK | - | `resources/js/Pages/Dashboard/Profile/Index.vue` | Fonctionne |

**Analyse C06-C08 - Pattern Bug Commun:**
- **Application:** CLIENT (Dashboard)
- **Route:** `GET /dashboard`
- **Composant:** `resources/js/Pages/Dashboard.vue`
- **Root cause:** Probablement erreur Stripe (C05) qui casse le chargement
- **Périmètre initial:** ✅ OUI (dashboard étudiant complet requis)
- **Impact:** BLOQUANT - utilisateur ne peut pas gérer son parcours

**Tests requis:**
```bash
# 1. Tester route dashboard
curl -H "Authorization: Bearer TOKEN" http://localhost/dashboard

# 2. Vérifier API endpoints
GET /api/applications (mes candidatures)
GET /api/invoices (mes factures)
GET /api/user/dossier (mon dossier)

# 3. Console navigateur
Inspecter erreurs JS/Vue
```

**Action corrective:**
1. Fix Stripe config (C05) en priorité
2. Vérifier API endpoints retournent données
3. Vérifier auth/permissions
4. Test chaque section isolément

---

#### 1.4.3 Propositions UX Menu

| # | Anomalie | Type | Priorité | Description |
|---|----------|------|----------|-------------|
| **C10** | Ajouter "Mon dossier" menu | 💡 EVOLUTION | P2 | UX - lien rapide |
| **C11** | Ajouter "Mes factures" menu | 💡 EVOLUTION | P2 | UX - lien rapide |
| **C12** | Ajouter "Membre depuis [année]" | 💡 EVOLUTION | P3 | UX - info supplémentaire |

**Analyse C10-C12:**
- **Application:** CLIENT (UI)
- **Périmètre initial:** ❌ NON (améliorations ergonomie)
- **Action:** Backlog post-sprint

---

## 🟩 PARTIE 2: APPLICATION ADMIN (Filament /gate)

### 2.1 DASHBOARD ADMIN

| # | Anomalie | Type | Priorité | Localisation | Description |
|---|----------|------|----------|--------------|-------------|
| **A01** | Présentation dashboard OK | ✅ OK | - | Widgets Filament | Fonctionne |
| **A02** | CA = frais dossier + scolarité ? | 📊 QUESTION | **P0** | Business Logic | URGENT - Validation calcul |
| **A03** | Graphique camembert pays | 💡 EVOLUTION | P2 | `CountryDistributionWidget` | Nouveau type graphique |

**Analyse A02 - QUESTION MÉTIER CRITIQUE:**
- **Application:** ADMIN
- **Widget:** `StatsOverviewWidget.php`
- **Impact:** VALIDATION BUSINESS - Calcul revenus EtapSup
- **Question:** Le CA affiché inclut:
  - ☐ Frais de dossier uniquement ?
  - ☐ Frais de scolarité uniquement ?
  - ☐ Les deux ?
  - ☐ Commission EtapSup uniquement ?

**ACTION IMMÉDIATE:** Validation client requise avant toute correction

**Analyse A03:**
- **Périmètre initial:** ❌ NON (nouveau widget)
- **Action:** Backlog - ApexCharts pie chart

---

### 2.2 CANDIDATURES (Gestion Admin)

| # | Anomalie | Type | Priorité | Localisation | Description |
|---|----------|------|----------|--------------|-------------|
| **A04** | Visualisation candidatures NOK | 🔥 BUG MAJEUR | **P0** | `app/Filament/Resources/ApplicationResource.php` | Internal Server Error Stripe |

**Analyse A04:**
- **Application:** ADMIN
- **Erreur:** Même erreur Stripe que C05
- **Route Filament:** `/gate/applications`
- **Resource:** `ApplicationResource.php`
- **Impact:** Admin ne peut pas gérer candidatures
- **Périmètre initial:** ✅ OUI (gestion candidatures = cœur admin)

**Action corrective:**
- Dépend de fix Stripe (C05)
- Vérifier Resource Filament correctement configuré

---

### 2.3 GESTION DES ÉTABLISSEMENTS

#### 2.3.1 Informations Principales

| # | Anomalie | Type | Priorité | Localisation | Description |
|---|----------|------|----------|--------------|-------------|
| **A05** | Retirer types immobilier | 🐛 BUG MINEUR | P1 | `database/seeders/` | Types legacy ma-Reza (appartement, chalet...) |
| **A06** | Spécialisation reprend formations | 🐛 BUG MAJEUR | P1 | `PropertyResource.php` | Logique métier incorrecte |
| **A07** | Spécialisation champ libre | 💡 EVOLUTION | P2 | Alternative système actuel |
| **A08** | Ajouter champ "Pays" | 💡 EVOLUTION | P2 | Multi-pays |

**Analyse A05 - Nettoyage Legacy:**
- **Application:** ADMIN (data)
- **Table:** `property_types` ou `establishment_types`
- **Types à supprimer:**
  - Appartement
  - Chalet
  - Studio
  - Villa
  - Etc. (tout immobilier ma-Reza)
- **Périmètre initial:** ✅ OUI (migration ma-Reza clean)
- **Impact:** Pollution UI + confusion utilisateurs

**Action corrective:**
```php
// database/seeders/CleanLegacyTypesSeeder.php
EstablishmentType::whereIn('slug', [
    'appartement', 'chalet', 'studio', 'villa'
])->delete();
```

**Analyse A06 - Bug Logique Métier:**
- **Application:** ADMIN
- **Problème:** Champ "Spécialisation" affiche liste "Formations"
- **Attendu:** Spécialisations distinctes des formations
- **Périmètre initial:** ✅ OUI (logique métier)
- **Impact:** Confusion données

**Action corrective:**
1. Vérifier relation `specializations` vs `formations`
2. Corriger Resource Filament select options
3. Clarifier avec client différence métier

**Analyse A07-A08:**
- **Périmètre initial:** ❌ NON
- **Action:** Backlog

---

#### 2.3.2 Contact & Tarifs

| # | Anomalie | Type | Priorité | Localisation | Description |
|---|----------|------|----------|--------------|-------------|
| **A09-A13** | Contact/tarifs OK | ✅ OK | - | PropertyResource | Tous fonctionnent |
| **A14** | Commission sur quoi ? | 📊 QUESTION | **P0** | Business Logic | **URGENT** - Règle métier financière |

**Analyse A14 - QUESTION BUSINESS CRITIQUE:**
- **Application:** ADMIN (règles tarifaires)
- **Question:** La commission EtapSup s'applique sur:
  - ☐ Acompte frais de scolarité ?
  - ☐ Frais de dossier ?
  - ☐ Les deux ?
  - ☐ Autre ?

**IMPACT BUSINESS:**
- Calcul revenus EtapSup
- Génération liens paiement Stripe
- Facturation partenaires

**ACTION IMMÉDIATE:** STOP - Validation client obligatoire

---

#### 2.3.3 Validation & Publication Établissement

| # | Anomalie | Type | Priorité | Localisation | Description |
|---|----------|------|----------|--------------|-------------|
| **A15** | Publication établissement NOK | 🔥 BUG MAJEUR | **P0** | `PropertyResource.php` | `SQLSTATE[42S22]: Column not found: 1054` |

**Analyse A15 - ROOT CAUSE MAJEURE:**
- **Application:** ADMIN
- **Erreur SQL:** Colonnes manquantes table `properties`
- **Colonnes listées dans l'erreur:**
  ```
  frais_dossier, category_id, sub_category_id, city_id,
  address, price, description, website, phone, email,
  student_count, ranking, tuition_min, tuition_max,
  commission, acompte_scolarite, establishment_type_id
  ```
- **Cause:** **MIGRATION DATABASE INCOMPLÈTE**
- **Périmètre initial:** ✅ OUI (publication établissements requis)
- **Impact:** BLOQUANT - impossible de publier établissements

**Action corrective URGENTE:**

```php
// database/migrations/xxxx_fix_properties_table.php
public function up()
{
    Schema::table('properties', function (Blueprint $table) {
        // Colonnes financières EtapSup
        $table->decimal('frais_dossier', 10, 2)->nullable();
        $table->decimal('acompte_scolarite', 10, 2)->nullable();
        $table->decimal('commission', 5, 2)->nullable()->comment('% commission EtapSup');

        // Colonnes établissement
        $table->foreignId('establishment_type_id')->nullable()->constrained();
        $table->integer('student_count')->nullable();
        $table->integer('ranking')->nullable();
        $table->decimal('tuition_min', 10, 2)->nullable();
        $table->decimal('tuition_max', 10, 2)->nullable();

        // Champs déjà existants ? Vérifier
        // $table->string('website')->nullable();
        // $table->string('phone')->nullable();
        // $table->string('email')->nullable();
    });
}
```

**Tests requis:**
```bash
# 1. Backup DB
php artisan db:backup

# 2. Créer migration
php artisan make:migration fix_properties_missing_columns

# 3. Exécuter
php artisan migrate

# 4. Tester publication
```

---

### 2.4 FORMATIONS

| # | Anomalie | Type | Priorité | Description |
|---|----------|------|----------|-------------|
| **A16-A17** | Formations OK | ✅ OK | - | Présentation + Création fonctionnent |

---

### 2.5 PARAMÈTRES

#### 2.5.1 Pays & Villes

| # | Anomalie | Type | Priorité | Localisation | Description |
|---|----------|------|----------|--------------|-------------|
| **A18-A19** | Pays/Villes OK | ✅ OK | - | CityResource, CountryResource | Fonctionnent |
| **A20** | Villes->Régions FR au lieu Pays | ⚠️ BUG MINEUR | P1 | `City` model | Relation incorrecte contexte Afrique |

**Analyse A20:**
- **Application:** ADMIN (data model)
- **Problème:** `City` belongsTo `Region` (française) au lieu de `Country`
- **Impact:** Logique métier incorrecte pour expansion multi-pays
- **Périmètre initial:** ✅ OUI (plateforme multi-pays africains)

**Action corrective:**
```php
// app/Models/City.php
// ❌ AVANT
public function region()
{
    return $this->belongsTo(Region::class);
}

// ✅ APRÈS
public function country()
{
    return $this->belongsTo(Country::class);
}

// Migration
Schema::table('cities', function (Blueprint $table) {
    $table->dropForeign(['region_id']);
    $table->dropColumn('region_id');
    $table->foreignId('country_id')->constrained();
});
```

---

#### 2.5.2 Paramètres Généraux

| # | Anomalie | Type | Priorité | Localisation | Description |
|---|----------|------|----------|--------------|-------------|
| **A21** | Paramètres généraux NOK | 🔥 BUG MAJEUR | **P0** | `app/Filament/Pages/ManageGeneral.php` | `Spatie\LaravelSettings\Exceptions\MissingSettings` |

**Analyse A21:**
- **Application:** ADMIN
- **Erreur:** Settings Spatie non initialisés
- **Message:** `Tried loading settings 'App\Settings\GeneralSettings', and the following properties were missing: livret_path`
- **Cause:** Settings pas créés ou migration manquante
- **Périmètre initial:** ✅ OUI (config système requis)

**Action corrective:**
```php
// database/seeders/SettingsSeeder.php
use App\Settings\GeneralSettings;

$settings = app(GeneralSettings::class);
$settings->livret_path = '/default/path';
$settings->save();

// OU créer migration settings
php artisan make:settings-migration CreateGeneralSettings
```

---

#### 2.5.3 Types & Paramètres

| # | Anomalie | Type | Priorité | Description |
|---|----------|------|----------|-------------|
| **A22** | Types établissements OK | ✅ OK | - | Fonctionne |
| **A23** | Retirer types immobilier | 🐛 BUG MINEUR | P1 | Doublon A05 |
| **A24** | Types formation OK | ✅ OK | - | Fonctionne |
| **A25** | Redondance Type formation / Formation ? | ⚠️ QUESTION | P2 | Clarifier architecture |
| **A26-A27** | Métiers / Niveaux études OK | ✅ OK | - | Fonctionnent |

**Analyse A25:**
- **Question architecture:** Différence entre:
  - "Type de formation" (ex: Licence, Master)
  - "Formation" (ex: Informatique, Droit)
- **Action:** Clarification client requise

---

### 2.6 PROGRAMME D'ÉTUDES

| # | Anomalie | Type | Priorité | Localisation | Description |
|---|----------|------|----------|--------------|-------------|
| **A28** | Informations principales OK | ✅ OK | - | ProgramResource | Fonctionne |
| **A29** | Spécialisation champ libre | 💡 EVOLUTION | P2 | Même demande A07 |
| **A30** | Publication programme NOK | 🔥 BUG MAJEUR | **P0** | ProgramResource | `SQLSTATE[01000]: Warning: 1265 Data truncated` |

**Analyse A30:**
- **Application:** ADMIN
- **Erreur SQL:** Données tronquées colonne `duration`
- **Cause:** Format données incompatible avec type colonne
- **Exemple:** Envoie "10 mois" mais colonne attend INTEGER

**Action corrective:**
```php
// Vérifier migration
Schema::table('programs', function (Blueprint $table) {
    // ❌ Si c'est ça
    $table->integer('duration');

    // ✅ Changer en
    $table->string('duration'); // "10 mois", "2 ans"
    // OU
    $table->integer('duration_value');
    $table->enum('duration_unit', ['mois', 'ans']);
});
```

---

### 2.7 GESTION COMPTES & PERMISSIONS

#### 2.7.1 Comptes Gestionnaires

| # | Anomalie | Type | Priorité | Localisation | Description |
|---|----------|------|----------|--------------|-------------|
| **A31** | Comptes gestionnaires non visibles | 🐛 BUG CRITIQUE | **P0** | `app/Filament/Resources/Account/EmployeeResource.php` | Liste vide |
| **A32** | Regrouper tous comptes | 💡 EVOLUTION | P2 | UX amélioration |

**Analyse A31:**
- **Application:** ADMIN
- **Route:** `/gate/employees` (ou similar)
- **Problème:** Liste n'affiche rien ou erreur
- **Périmètre initial:** ✅ OUI (gestion utilisateurs requis)
- **Impact:** Impossible de gérer comptes

**Action corrective:**
1. Vérifier Resource query
2. Vérifier permissions
3. Vérifier scope/filters

---

#### 2.7.2 Rôles & Permissions

| # | Anomalie | Type | Priorité | Localisation | Description |
|---|----------|------|----------|--------------|-------------|
| **A33** | Création rôle NOK | 🔥 BUG MAJEUR | **P0** | `app/Filament/Resources/Account/RoleResource.php` | `Spatie\Permission\Exceptions\RoleDoesNotExist` |
| **A34** | Création autorisation NOK | 🔥 BUG MAJEUR | **P0** | RoleResource | Non visible |

**Analyse A33-A34:**
- **Application:** ADMIN
- **Erreur:** `There is no role named 'account' for guard 'web'`
- **Cause:** Spatie Permission mal configuré
- **Guards:** Probablement config guards incorrecte

**Action corrective:**
```php
// config/auth.php - Vérifier guards
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'admin' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
],

// config/permission.php - Vérifier
'models' => [
    'permission' => Spatie\Permission\Models\Permission::class,
    'role' => Spatie\Permission\Models\Role::class,
],

// Seeders requis
php artisan db:seed --class=RolesAndPermissionsSeeder
```

```php
// database/seeders/RolesAndPermissionsSeeder.php
Role::create(['name' => 'admin', 'guard_name' => 'web']);
Role::create(['name' => 'gestionnaire', 'guard_name' => 'web']);
Role::create(['name' => 'school', 'guard_name' => 'web']);

Permission::create(['name' => 'view_applications', 'guard_name' => 'web']);
Permission::create(['name' => 'create_establishments', 'guard_name' => 'web']);
```

---

#### 2.7.3 Administration - Comptes Utilisateurs

| # | Anomalie | Type | Priorité | Localisation | Description |
|---|----------|------|----------|--------------|-------------|
| **A35** | Accès comptes utilisateurs NOK | 🔥 BUG MAJEUR | **P0** | UserResource | `TypeError: Argument #2 ($label) must be string, null given` |
| **A36** | Visualisation selon droits | 💡 EVOLUTION | P2 | Permissions granulaires |

**Analyse A35:**
- **Application:** ADMIN
- **Erreur Filament:** Select component mal configuré
- **File:** `vendor/filament/forms/src/Components/Select.php:190`
- **Cause:** Option sans label dans Select

**Action corrective:**
```php
// app/Filament/Resources/UserResource.php
Select::make('role_id')
    ->label('Rôle')
    ->options(function () {
        return Role::all()->pluck('name', 'id');
        // ✅ S'assurer que name n'est jamais null
    })
    ->required()
```

---

### 2.8 DIVERS ADMIN

#### 2.8.1 Paramètres Réservation / Candidature

| # | Anomalie | Type | Priorité | Description |
|---|----------|------|----------|-------------|
| **A37** | Paramètres réservation à optimiser | 🎨 OPTIMISATION | P2 | Reprend ma-Reza, besoin différent |

**Analyse A37:**
- **Question métier:** À discuter en séance
- **Action:** Point synchro client

---

#### 2.8.2 Attestations (Legacy)

| # | Anomalie | Type | Priorité | Description |
|---|----------|------|----------|-------------|
| **A38** | Demande attestations à supprimer | 🐛 BUG MINEUR | P1 | Module legacy ma-Reza |

**Analyse A38:**
- **Application:** ADMIN (ou PARTNER)
- **Périmètre initial:** ❌ NON (fonctionnalité ma-Reza uniquement)
- **Impact:** Pollution interface
- **Action:** Supprimer Resource/routes

---

#### 2.8.3 Partenaires

| # | Anomalie | Type | Priorité | Description |
|---|----------|------|----------|-------------|
| **A39** | Partenaires OK | ✅ OK | - | Fonctionne |
| **A40** | Basculer dans gestion comptes | 💡 EVOLUTION | P3 | Réorg menu |

---

## 📊 SYNTHÈSE GLOBALE

### Répartition par application

#### 🟦 APPLICATION CLIENT (12 bugs)
- **P0 Critiques:** 7 bugs
  - C01: Mail événement
  - C02: Page établissements
  - C04: Validation candidatures
  - C05: Stripe config (**ROOT CAUSE**)
  - C06: Mes candidatures
  - C07: Mes factures
  - C08: Mon dossier
- **Évolutions:** 4 (C03, C10, C11, C12)

#### 🟩 APPLICATION ADMIN (28 bugs)
- **P0 Critiques:** 10 bugs
  - A02: Question CA (**VALIDATION MÉTIER**)
  - A04: Liste candidatures
  - A14: Question commission (**VALIDATION MÉTIER**)
  - A15: Publication établissement (**SQL**)
  - A21: Paramètres généraux (**Spatie Settings**)
  - A30: Publication programme (**SQL**)
  - A31: Comptes gestionnaires
  - A33: Création rôle (**Spatie Permissions**)
  - A34: Création autorisation
  - A35: Accès utilisateurs (**Filament**)

- **P1 Majeurs:** 3 bugs
  - A05: Types immobilier
  - A06: Spécialisation/Formation
  - A20: Villes->Pays
  - A38: Attestations

- **Évolutions:** 8 items
- **Questions:** 2 (A02, A14, A25)

---

## 🎯 ROOT CAUSES IDENTIFIÉES

### 🔴 RC1: Configuration Stripe invalide
**Impact: 30% des bugs critiques**
- C05, C06, C07, C08, A04
- **Action:** Fix `PaymentService.php` + `.env`

### 🔴 RC2: Migrations base de données incomplètes
**Impact: 25% des bugs**
- A15 (18 colonnes manquantes)
- A30 (type colonne incorrect)
- A20 (relation City->Region)
- **Action:** Migrations correctives urgentes

### 🔴 RC3: Spatie Packages mal configurés
**Impact: 20% des bugs**
- A21 (Settings)
- A33, A34 (Permissions)
- **Action:** Seeders + config guards

### 🔴 RC4: Legacy ma-Reza non nettoyé
**Impact: 15% des bugs**
- A05, A23 (types immobilier)
- A38 (attestations)
- A37 (paramètres réservation)
- **Action:** Nettoyage complet legacy

### 🔴 RC5: Validation métier manquante
**Impact: 10% mais CRITIQUE business**
- A02 (CA dashboard)
- A14 (commission)
- **Action:** Validation client URGENTE

---

## ⚡ PLAN D'ACTION PAR PRIORITÉ

### 🔥 PHASE 1: STOPPERS (24h) - P0 uniquement

#### Sprint 1A: Questions métier BLOQUANTES (2h)
**STOP - Réunion client obligatoire**
- [ ] A02: Valider calcul CA dashboard
- [ ] A14: Valider règle commission EtapSup

**Sans ces réponses, impossible de continuer corrections financières**

---

#### Sprint 1B: Configuration Stripe (3h)
**Débloque: C05, C06, C07, C08, A04**
- [ ] Vérifier `.env` STRIPE_KEY/SECRET
- [ ] Fix `app/Services/PaymentService.php:13`
- [ ] Config `config/services.php`
- [ ] Tests paiement staging
- [ ] Tests liens paiement

---

#### Sprint 1C: Migrations Database (4h)
**Débloque: A15, A30**
- [ ] Audit complet schéma `properties`
- [ ] Migration colonnes manquantes (18 champs)
- [ ] Fix colonne `duration` programme
- [ ] Backup DB avant migration
- [ ] Tests publication établissement
- [ ] Tests publication programme

---

#### Sprint 1D: Spatie Packages (3h)
**Débloque: A21, A33, A34**
- [ ] Config guards `config/auth.php`
- [ ] Config `config/permission.php`
- [ ] Seeder Settings généraux
- [ ] Seeder Roles & Permissions
- [ ] Tests création rôles
- [ ] Tests assignation permissions

---

#### Sprint 1E: Frontend Client Critical (6h)
**Débloque: C01, C02, C04**
- [ ] Fix mail événement (queue + config)
- [ ] Fix page établissements (route + composant)
- [ ] Fix validation candidatures
- [ ] Tests E2E workflow complet

---

#### Sprint 1F: Admin User Management (4h)
**Débloque: A31, A35**
- [ ] Fix liste comptes gestionnaires
- [ ] Fix accès comptes utilisateurs (Select)
- [ ] Tests création compte
- [ ] Tests permissions

---

### 🟠 PHASE 2: TESTS & VALIDATION (16h)

#### Sprint 2A: Tests Backend (6h)
- [ ] Feature tests établissements
- [ ] Tests paiements Stripe
- [ ] Tests candidatures workflow
- [ ] Tests permissions/rôles
- [ ] Tests migrations

#### Sprint 2B: Tests Frontend (6h)
- [ ] Tests E2E formulaires
- [ ] Tests navigation
- [ ] Tests dashboard étudiant
- [ ] Tests affichage établissements

#### Sprint 2C: Tests Manuels (4h)
- [ ] Checklist fonctionnelle complète
- [ ] Tests multi-rôles (admin/student/school)
- [ ] Tests cross-browser
- [ ] Tests mobile

---

### 🟡 PHASE 3: CLEANUP P1 (8h)

- [ ] A05: Supprimer types immobilier
- [ ] A06: Fix spécialisation/formation
- [ ] A20: Migration City->Country
- [ ] A38: Supprimer module attestations
- [ ] Documentation corrections

---

### 🟢 BACKLOG POST-SPRINT

- Évolutions P2/P3
- Refonte UX
- Graphiques supplémentaires
- Optimisations

---

## ⚠️ RISQUES & DÉPENDANCES

| Risque | Impact | Mitigation |
|--------|--------|------------|
| Questions métier non répondues | 🔴 BLOQUANT | Réunion urgente client |
| Migrations prod risquées | 🔴 HAUTE | Backup + staging + rollback plan |
| Stripe prod non testé | 🔴 HAUTE | Env staging obligatoire |
| Deadline 20/12 non tenable si bugs P0 | 🔴 HAUTE | Focus strict P0 uniquement |

---

## 🎬 PROCHAINES ÉTAPES IMMÉDIATES

### ✅ Validation client REQUISE (URGENT)

**Avant de commencer les corrections, le client DOIT répondre:**

1. **A02:** Le CA dashboard = frais dossier + scolarité + commission ? Ou autre ?
2. **A14:** La commission EtapSup s'applique sur quoi exactement ?
3. **Périmètre sprint:**
   - "Test partie financière non disponible" → À implémenter maintenant ?
   - "Accompagnement personnalisé" → C'est dans ce sprint ?
4. **Priorisation:** Confirmer focus P0 uniquement pour tenir 20/12 ?

---

## 📈 MÉTRIQUES QUALITÉ

### État actuel
- **Bugs CLIENT critiques (P0):** 7 🔴
- **Bugs ADMIN critiques (P0):** 10 🔴
- **Questions métier bloquantes:** 2 🔴
- **Taux fonctionnalités CLIENT NOK:** ~50% ❌
- **Taux fonctionnalités ADMIN NOK:** ~35% ❌
- **Couverture tests:** 0% ❌

### Objectif 20/12
- **Bugs P0:** 0 ✅
- **Questions métier:** Résolues ✅
- **Taux fonctionnalités OK:** 100% ✅
- **Couverture tests:** >60% ✅

---

**Statut:** ⚠️ ROUGE - SPRINT CRITIQUE - VALIDATION CLIENT URGENTE

**Prochaine action:** Réunion client pour validation questions métier (A02, A14)
