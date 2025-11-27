# User Stories — EtapSup (Sprint 1)

> **Vision** : Amina doit passer de la découverte à la candidature en **moins de 5 minutes**, sans stress, avec confiance.

## 📌 Feature 1 : Page Événementielle
### Story 1.1.1  
*As a* étudiant africain,  
*I want* accéder à une landing page événementielle claire et responsive,  
*so that* je puisse m’inscrire à un webinar.  
**Critères** :  
- Page accessible à `/events`  
- Formulaire (nom, email, pays) → POST `/api/events/register`  
- Confirmation visuelle après soumission  
- Responsive (mobile/tablet/desktop)  
- UI inspirée de Diplomeo.com

## 📌 Feature 2 : Auth & Livret
### Story 1.2.1  
*As a* étudiant,  
*I want* me connecter avec email/mot de passe,  
*so that* j’accède à mon espace.  
**Critères** :  
- Auth via Laravel Sanctum  
- Redirection vers dashboard  
- Erreur claire en cas d’échec  

### Story 1.2.2  
*As a* étudiant connecté,  
*I want* télécharger le livret PDF,  
*so that* je comprenne les démarches.  
**Critères** :  
- Bouton dans le dashboard  
- Fichier `livret-etudiant-etapsup.pdf` depuis AWS S3  

### Story 1.2.3  
*As a* étudiant,  
*I want* réinitialiser mon mot de passe,  
*so that* je récupère mon compte.  
**Critères** :  
- Lien “Mot de passe oublié”  
- Email avec lien valide 1h  

## 📌 Feature 3 : Établissements
### Story 1.3.1  
*As a* étudiant,  
*I want* filtrer les établissements,  
*so that* je trouve ceux qui me correspondent.  
**Critères** :  
- Filtres pays/ville/formation  
- UI inspirée de Diplomeo.com  

### Story 1.3.2  
*As a* étudiant,  
*I want* voir la fiche détaillée,  
*so that* je comprenne les conditions.  
**Critères** :  
- Page `/etablissements/{id}`  
- Infos clés + bouton “Postuler”  

### Story 1.3.3  
*As a* étudiant,  
*I want* voir la localisation sur Google Maps,  
*so that* je visualise le campus.  
**Critères** :  
- Carte intégrée, responsive, avec pin  

## 📌 Feature 4 : Backoffice
### Story 1.4.1  
*As a* admin,  
*I want* ajouter un établissement,  
*so that* il soit visible.  
**Critères** :  
- Formulaire avec upload logo → AWS S3  
- Enregistrement en MySQL  

### Story 1.4.2  
*As a* admin,  
*I want* modifier/supprimer un établissement,  
*so that* je maintienne la base à jour.  

## 📌 Feature 5 : Paiement
### Story 1.5.1  
*As a* étudiant,  
*I want* payer via Stripe,  
*so that* ma candidature soit soumise.  
**Critères** :  
- Redirection vers Stripe Checkout (Connect)  
- Commission 10%/90% appliquée  

### Story 1.5.2  
*As a* étudiant,  
*I want* recevoir un reçu par email,  
*so that* j’aie une preuve.  

## 📌 Feature 6 : Suivi Candidatures
### Story 1.6.1  
*As a* étudiant,  
*I want* voir mes candidatures en temps réel,  
*so that* je sache où j’en suis.  

## 📌 Feature 7 : Upload Pièces
### Story 1.7.1  
*As a* étudiant,  
*I want* uploader mon passeport/relevés,  
*so that* je complète ma candidature.  
**Critères** :  
- Formats PDF/JPG/PNG, max 10 Mo  
- Stockage AWS S3, lien en BDD  

### Story 1.7.2  
*As a* étudiant,  
*I want* voir mes documents uploadés,  
*so that* je ne les renvoie pas.  
**Critères** :  
- Liste avec icônes + prévisualisation  
- Option “Supprimer” (avec confirmation)