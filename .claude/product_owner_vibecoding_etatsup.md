# PRD — EtapSup (Sprint 1)

## 🌍 Vision Produit
> **Devenir la plateforme tout-en-un pour l’orientation, la formation et l’accompagnement des étudiants africains vers l’étranger — de la découverte de formations à l’installation sur place.**

EtapSup résout deux problèmes critiques :
- **Pour les étudiants africains** : perte de temps, stress administratif, manque de clarté sur les démarches (visa, logement, inscription).
- **Pour les établissements européens** : difficulté à attirer des candidats internationaux qualifiés, préparés, et engagés.

Notre MVP Sprint 1 pose les fondations de cette vision en offrant un parcours fluide, sécurisé et inspirant — **du premier clic à la soumission de candidature**.

## 🎯 Objectif du Sprint
Lancer un MVP fonctionnel permettant à **Amina (22 ans, Dakar → Paris)** de :
1. Découvrir des formations européennes via une landing événementielle,
2. Créer un compte sécurisé,
3. Télécharger un guide administratif,
4. Explorer des établissements (style Diplomeo.com),
5. Postuler à une formation,
6. Payer les frais via Stripe,
7. Uploader ses pièces justificatives,
8. Suivre l’état de sa candidature en temps réel.

## 🧩 Portée (In / Out)

### ✅ Inclus (MVP Sprint 1)
- **Page événementielle** (`/events`) – landing autonome avec formulaire
- **Authentification étudiante** – email + mot de passe + reset
- **Livret PDF** – guide des démarches (stocké sur AWS S3)
- **Visualisation des établissements** – filtres (pays, ville, formation), fiche détaillée, carte Google Maps
- **Backoffice admin** – CRUD établissements, upload logo, saisie commission (%)
- **Paiement Stripe Connect** – 10% EtapSup / 90% établissement
- **Tableau de bord candidature** – statuts, historique, upload pièces
- **Stockage sécurisé** – AWS S3 pour documents (PDF, JPG, PNG ≤ 10 Mo)

### ❌ Hors périmètre (Sprint 1)
- Notifications push ou email automatisées (sauf reçu Stripe)
- Comparateur de formations
- Chat en direct
- Traduction multilingue
- Intégration API avec systèmes d’admission universitaires
- Application mobile native (seulement PWA via Vue.js)

## ⚙️ Stack Technologique Validée
| Couche | Technologie | Raison |
|--------|-------------|--------|
| **Frontend** | Vue.js 3 (Composition API) | Remplace Node.js/EJS — plus moderne, réactif, adapté au PWA |
| **Backend** | Laravel 10 | Logique métier, API REST, authentification |
| **Base de données** | MySQL | Stockage relationnel sécurisé, compatible Hostinger |
| **Stockage fichiers** | AWS S3 | Pièces justificatives et livret PDF |
| **Paiements** | Stripe Connect | Marketplace avec répartition automatique |
| **Hébergement** | Hostinger VPS | Infrastructure cloud déjà provisionnée |
| **UI/UX** | Inspirée de Diplomeo.com | Référence UX validée pour la découverte de formations |

## 📊 Indicateurs de Succès (KPIs)
- **Taux de conversion** : landing → inscription > **15%**
- **Taux de réussite upload** : > **95%**
- **Temps moyen traitement candidature** : < **48h**
- **NPS utilisateur** : > **7/10**
- **Taux de paiement réussi** : > **90%**

> ✅ Ce PRD est la source de vérité exécutable. Toute déviation doit être validée par le PO.