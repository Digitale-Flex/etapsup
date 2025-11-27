# Features — EtapSup (Sprint 1)

> **Vision** : Offrir un parcours fluide de découverte → candidature → accompagnement, centré sur l’étudiant africain.

## 1. Page Événementielle (`/events`)
- Landing page autonome, responsive (mobile-first)
- Formulaire d’inscription (nom, email, pays)
- Intégration Google Analytics
- Design inspiré de Diplomeo.com : clarté, hiérarchie visuelle, CTA percutants

## 2. Authentification & Livret PDF
- Connexion sécurisée (email + mot de passe)
- Réinitialisation de mot de passe (lien temporaire par email)
- Téléchargement du livret `livret-etudiant-etapsup.pdf` (stocké sur AWS S3)
- Accès réservé aux utilisateurs connectés

## 3. Visualisation des Établissements
- Grille de résultats avec filtres dynamiques : **pays (select)**, **ville (text)**, **type de formation (select)**
- Fiche détaillée par établissement : nom, description, frais, commission, localisation
- Carte Google Maps intégrée (coordonnées en BDD)
- UI **strictement inspirée de Diplomeo.com** : espacement, typo, boutons, micro-interactions

## 4. Backoffice Admin (`/admin`)
- Interface protégée (auth requise)
- CRUD complet sur les établissements
- Upload de logo (AWS S3)
- Saisie de la commission (%) par établissement
- Tableau de bord basique (stats candidatures)

## 5. Paiement via Stripe Connect
- Bouton “Payer les frais” sur la fiche établissement
- Redirection vers Stripe Checkout (mode marketplace)
- Répartition automatique : **10% EtapSup / 90% établissement**
- Envoi automatique d’un **reçu par email** après succès
- Historique visible dans le profil étudiant

## 6. Suivi des Candidatures
- Section “Mes candidatures” dans le tableau de bord
- Statuts clairs : **En attente / Acceptée / Refusée**
- Mise à jour en temps réel (via polling léger ou WebSocket si nécessaire)
- Liens vers les actions manquantes (“Téléchargez votre passeport”)

## 7. Upload Pièces Justificatives
- Formulaire dans la fiche candidature
- Formats acceptés : **PDF, JPG, PNG**
- Taille max : **10 Mo**
- Stockage : **AWS S3**
- Prévisualisation + suppression possible
- Liste des documents déjà uploadés (icônes, noms, dates)

> 💡 Chaque feature est conçue pour **réduire la friction d’Amina** et **accélérer sa candidature**.