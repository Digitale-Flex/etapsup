# 💰 RÈGLES MÉTIER FINANCIÈRES - ETAPSUP

**Date validation:** 10/12/2025
**Validé par:** Client
**Statut:** ✅ OFFICIEL

---

## 📊 A02 - CALCUL CA DASHBOARD

### Composition du Chiffre d'Affaires

Le CA affiché dans le dashboard **INCLUT** :

✅ **Frais de dossier** (revenus directs EtapSup)
✅ **Frais de scolarité** (acomptes et paiements)

### Formule
```
CA_Total = Frais_Dossier + Frais_Scolarité
```

### Exemples

#### Cas 1 : Candidature complète
```
Frais dossier : 50 000 XAF
Acompte scolarité : 200 000 XAF
→ CA = 250 000 XAF
```

#### Cas 2 : Plusieurs candidatures
```
Candidature 1:
  - Frais dossier : 50 000 XAF
  - Acompte : 150 000 XAF

Candidature 2:
  - Frais dossier : 50 000 XAF
  - Acompte : 200 000 XAF

→ CA Total = (50k + 150k) + (50k + 200k) = 450 000 XAF
```

### Impact technique

**Widgets dashboard à mettre à jour :**
```php
// app/Filament/Widgets/StatsOverviewWidget.php
$ca_mensuel = DB::table('applications')
    ->whereMonth('created_at', now()->month)
    ->sum(DB::raw('frais_dossier + acompte_scolarite'));

$ca_annuel = DB::table('applications')
    ->whereYear('created_at', now()->year)
    ->sum(DB::raw('frais_dossier + acompte_scolarite'));
```

---

## 💸 A14 - CALCUL COMMISSION ETAPSUP

### Règle de commission

La commission EtapSup s'applique **UNIQUEMENT** sur :

✅ **L'acompte des frais de scolarité**

❌ **PAS sur les frais de dossier** (revenus directs EtapSup)

### Formule
```
Commission_EtapSup = Acompte_Scolarité × Taux_Commission

Revenus_EtapSup = Frais_Dossier + Commission_EtapSup
```

### Exemples

#### Cas 1 : Commission 10%
```
Frais dossier : 50 000 XAF
Acompte scolarité : 200 000 XAF
Taux commission : 10%

Commission = 200 000 × 10% = 20 000 XAF
Revenus EtapSup = 50 000 + 20 000 = 70 000 XAF
Part école = 200 000 - 20 000 = 180 000 XAF
```

#### Cas 2 : Commission 15%
```
Frais dossier : 75 000 XAF
Acompte scolarité : 500 000 XAF
Taux commission : 15%

Commission = 500 000 × 15% = 75 000 XAF
Revenus EtapSup = 75 000 + 75 000 = 150 000 XAF
Part école = 500 000 - 75 000 = 425 000 XAF
```

### Impact technique

**Table properties (établissements) :**
```sql
-- Colonne commission stocke le POURCENTAGE (ex: 10.00 pour 10%)
commission DECIMAL(5,2) -- Ex: 10.00, 15.50
```

**Calculs dans le code :**
```php
// app/Models/Application.php

public function calculateCommission(): float
{
    $establishment = $this->property;
    $commission_rate = $establishment->commission / 100; // 10.00 → 0.10

    return $this->acompte_scolarite * $commission_rate;
}

public function getEtapSupRevenueAttribute(): float
{
    return $this->frais_dossier + $this->calculateCommission();
}

public function getSchoolRevenueAttribute(): float
{
    return $this->acompte_scolarite - $this->calculateCommission();
}
```

**Génération liens paiement Stripe :**
```php
// app/Services/PaymentService.php

public function createApplicationPaymentLinks(Application $application): array
{
    $establishment = $application->property;

    // 1. Lien paiement frais de dossier (100% EtapSup)
    $dossierLink = $this->stripe->paymentLinks->create([
        'amount' => $application->frais_dossier * 100, // centimes
        'currency' => 'xaf',
        'metadata' => [
            'type' => 'frais_dossier',
            'application_id' => $application->id,
        ],
    ]);

    // 2. Lien paiement acompte scolarité
    $scolariteLink = $this->stripe->paymentLinks->create([
        'amount' => $application->acompte_scolarite * 100,
        'currency' => 'xaf',
        'application_fee_amount' => $application->calculateCommission() * 100, // Commission EtapSup
        'on_behalf_of' => $establishment->stripe_account_id, // Compte école
        'metadata' => [
            'type' => 'acompte_scolarite',
            'application_id' => $application->id,
            'commission_rate' => $establishment->commission,
        ],
    ]);

    return [
        'frais_dossier' => $dossierLink->url,
        'acompte_scolarite' => $scolariteLink->url,
    ];
}
```

---

## 🔄 WORKFLOW PAIEMENT COMPLET

### Étape 1 : Création candidature
```
Étudiant soumet candidature
→ Frais dossier : 50 000 XAF
→ Acompte scolarité : 200 000 XAF (commission 10%)
```

### Étape 2 : Génération liens paiement
```
Lien 1 : Frais de dossier
  Montant : 50 000 XAF
  Destination : EtapSup (100%)

Lien 2 : Acompte scolarité
  Montant total : 200 000 XAF
  Commission EtapSup : 20 000 XAF (10%)
  Montant école : 180 000 XAF (90%)
```

### Étape 3 : Paiement étudiant
```
Étudiant paie les deux liens
→ Total : 250 000 XAF
```

### Étape 4 : Distribution fonds
```
EtapSup reçoit :
  - Frais dossier : 50 000 XAF
  - Commission : 20 000 XAF
  → Total EtapSup : 70 000 XAF

École reçoit :
  - Acompte net : 180 000 XAF
  → Total École : 180 000 XAF
```

---

## 📈 RAPPORTS & ANALYTICS

### Dashboard Admin (EtapSup)

**Revenus EtapSup :**
```sql
SELECT
    SUM(frais_dossier) as total_frais_dossier,
    SUM(acompte_scolarite * p.commission / 100) as total_commissions,
    SUM(frais_dossier + (acompte_scolarite * p.commission / 100)) as revenus_total
FROM applications a
JOIN properties p ON a.property_id = p.id
WHERE a.payment_status = 'paid'
```

**Revenus par école :**
```sql
SELECT
    p.title as etablissement,
    COUNT(a.id) as nb_candidatures,
    SUM(a.acompte_scolarite) as montant_brut,
    SUM(a.acompte_scolarite * p.commission / 100) as commission_etapsup,
    SUM(a.acompte_scolarite * (100 - p.commission) / 100) as montant_net_ecole
FROM applications a
JOIN properties p ON a.property_id = p.id
WHERE a.payment_status = 'paid'
GROUP BY p.id
```

---

## ✅ CHECKLIST IMPLÉMENTATION

### Backend
- [ ] Modifier `StatsOverviewWidget.php` (calcul CA)
- [ ] Ajouter méthodes `calculateCommission()` sur Application model
- [ ] Mettre à jour `PaymentService.php` (Stripe Connect)
- [ ] Créer rapport revenus par école
- [ ] Tests unitaires calculs commission

### Frontend
- [ ] Afficher détails commission sur page candidature
- [ ] Afficher répartition fonds dans dashboard école
- [ ] Messages clairs pour étudiant (2 paiements séparés)

### Stripe Configuration
- [ ] Configurer Stripe Connect pour écoles
- [ ] Tester split payment (application fee)
- [ ] Webhooks pour confirmer paiements

---

## 🚨 POINTS D'ATTENTION

### Sécurité
- ✅ Frais de dossier TOUJOURS vers EtapSup (pas modifiable)
- ✅ Taux commission défini par établissement (admin only)
- ⚠️ Vérifier que calculs côté serveur (pas frontend)

### Fiscalité
- 📌 Vérifier règles TVA selon pays
- 📌 Factures conformes législation locale
- 📌 Déclarations fiscales séparées (frais dossier vs commission)

### Communication
- 📌 Conditions générales claires pour étudiants
- 📌 Contrats écoles mentionnant taux commission
- 📌 Emails de confirmation avec détails paiements

---

**Document validé - Prêt pour implémentation**
