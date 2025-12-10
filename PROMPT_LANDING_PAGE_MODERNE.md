# Prompt optimisé : Landing Page Moderne EtapSup inspirée de Diplomeo

## Contexte du projet

Tu es un expert Vue.js senior spécialisé dans le développement de landing pages modernes et pixel-perfect. Tu travailles sur **EtapSup**, une plateforme Laravel 11 + Vue 3 + Inertia.js + TypeScript pour accompagner les étudiants africains dans leurs études supérieures à l'étranger.

## Objectif

Créer une **landing page moderne alternative** accessible sur la route `/accueil`, inspirée à 90% du design de **Diplomeo.com**, avec :
- Design moderne, animations fluides, micro-interactions
- Charte graphique EtapSup
- Contenu adapté au contexte EtapSup
- Tous les boutons et liens fonctionnels
- Responsive design parfait
- Code propre et maintenable

## Charte graphique EtapSup

### Couleurs principales
- **Bleu marine** : `#1e3a8a` (couleur primaire, fond hero, boutons primaires)
- **Rouge accent** : `#dc2626` (CTA, badges, éléments d'attention)
- **Jaune/Or** : `#fbbf24` (étoiles, badges premium, highlights)
- **Blanc** : `#ffffff`
- **Gris clair** : `#f3f4f6` (fonds de sections alternées)
- **Gris texte** : `#6b7280`

### Gradients
- Hero gradient : `linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%)`
- CTA gradient : `linear-gradient(135deg, #dc2626 0%, #ef4444 100%)`

### Typographie
- Police principale : `'Figtree', sans-serif` (déjà importée via Bunny Fonts)
- Titres : font-weight 600-700
- Texte : font-weight 400-500

## Structure de la page

### 1. Hero Section (Section d'accueil)
**Éléments obligatoires :**
- Badge animé en haut : "Nouveau : Accompagnement Premium disponible" avec animation pulse
- Titre principal H1 : "Trouvez votre formation" + texte gradient "à l'étranger"
- Sous-titre : Description courte (2-3 lignes)
- **Formulaire de recherche moderne** avec glassmorphism :
  - Select "Pays de destination" (liaison avec données `countries`)
  - Select "Domaine d'études" (liaison avec données `studyFields`)
  - Input "Mots-clés" (placeholder: "Ex: Commerce, Ingénierie...")
  - **Bouton "Rechercher" fonctionnel** → redirection vers `/establishments?country=X&studyField=Y&keyword=Z`
- Statistiques animées (4 colonnes) :
  - X+ Établissements partenaires
  - X+ Étudiants accompagnés
  - X Pays couverts
  - X+ Formations disponibles
- Cartes flottantes illustratives (Licence, Master, Doctorat) avec animation `float`
- Blob décoratif animé avec keyframe `morph`

**Animations :**
- Entrée progressive : `slideInUp`, `slideInDown`
- Badge pulse animé
- Float sur les cartes
- Morph sur le blob

### 2. Section Domaines d'études populaires
**Éléments obligatoires :**
- Titre H2 : "Explorez les domaines d'études"
- Grille de 8 cartes cliquables (grid 4 colonnes desktop, 2 tablette, 1 mobile)
- **Chaque carte doit être un lien fonctionnel** :
  - Composant : `<Link :href="route('establishments.index', { studyField: field.id })">`
  - Emoji/icône représentatif
  - Nom du domaine
  - Flèche `bi-arrow-right` animée au hover
- Effet hover : gradient overlay, flèche slide-right, scale 1.02

**Domaines suggérés :**
- 💼 Commerce & Gestion
- ⚕️ Santé & Médecine
- 💻 Informatique & Tech
- 🏗️ Ingénierie
- 🎨 Arts & Design
- ⚖️ Droit & Sciences Politiques
- 📚 Lettres & Sciences Humaines
- 🔬 Sciences & Recherche

### 3. Section Établissements mis en avant
**Éléments obligatoires :**
- Titre H2 : "Établissements partenaires recommandés"
- Grille de 6 cartes d'établissements (3 colonnes desktop, 2 tablette, 1 mobile)
- **Chaque carte doit être un lien fonctionnel** :
  - Composant : `<Link :href="route('establishments.show', establishment.slug)">`
  - Image de l'établissement (fallback Unsplash si pas d'image)
  - Badge "Top classement" si `ranking <= 10`
  - Titre de l'établissement
  - Ville, Pays
  - Type d'établissement (badge)
  - Nombre d'étudiants (si disponible)
- Effet hover : image zoom 1.1, shadow-xl

### 4. Section Avantages
**Éléments obligatoires :**
- Titre H2 : "Pourquoi choisir EtapSup ?"
- Fond gradient (même que hero)
- Grille de 4 cartes avantages avec glassmorphism
- Chaque carte :
  - Icône Bootstrap Icons (grande taille)
  - Titre H3
  - Description (2-3 lignes)

**Avantages suggérés :**
1. 🎯 **Accompagnement personnalisé** - Un conseiller dédié vous guide à chaque étape
2. 🌍 **Réseau international** - Plus de 150 établissements partenaires dans 15 pays
3. ⚡ **Traitement rapide** - Réponse sous 48h pour vos candidatures
4. 💰 **Pas de frais cachés** - Transparence totale sur les coûts

### 5. Section Témoignages
**Éléments obligatoires :**
- Titre H2 : "Ils nous font confiance"
- Grille de 3 témoignages (3 colonnes desktop, 1 mobile)
- Chaque témoignage :
  - Photo (avatar API avec initiales si pas de photo)
  - 5 étoiles (icônes Bootstrap `bi-star-fill`)
  - Citation en italique
  - Nom de l'étudiant
  - Pays d'origine

**Témoignages (données du controller) :**
- Sophie Martin (France)
- Mohamed Diallo (Sénégal)
- Amina Kouassi (Côte d'Ivoire)

### 6. Section CTA finale
**Éléments obligatoires :**
- Fond gradient rouge (#dc2626 → #ef4444)
- Blob décoratif
- Titre H2 : "Prêt à concrétiser votre projet d'études ?"
- Description courte
- **Bouton CTA fonctionnel** :
  - Composant : `<Link :href="route('register')" class="btn btn-white">`
  - Texte : "Créer mon compte gratuitement"
  - Style : fond blanc, texte rouge, hover shadow-xl

## Props Inertia attendues

Le contrôleur `HomeController::accueil()` passera ces props :

```typescript
interface AccueilProps {
  featuredEstablishments: Array<{
    id: string;
    slug: string;
    title: string;
    city: string;
    country: string;
    type: string;
    ranking: number | null;
    studentCount: number | null;
    image: string;
  }>;

  countries: Array<{
    id: number;
    name: string;
  }>;

  studyFields: Array<{
    id: number;
    name: string;
  }>;

  stats: {
    totalEstablishments: number;
    totalStudents: number;
    totalCountries: number;
    totalPrograms: number;
  };

  testimonials: Array<{
    name: string;
    country: string;
    photo: string;
    rating: number;
    text: string;
  }>;
}
```

## Fonctionnalités des boutons et liens

### ✅ Vérifications obligatoires
1. **Formulaire de recherche Hero** → Redirection vers `/establishments` avec query params
2. **Cartes domaines d'études** → Liens vers `/establishments?studyField=ID`
3. **Cartes établissements** → Liens vers `/establishments/SLUG`
4. **Bouton CTA "Créer mon compte"** → Lien vers `/register`
5. **Logo dans header** → Lien vers `/` (déjà dans AppHeader)

### Code des liens fonctionnels

```vue
<!-- Formulaire recherche -->
<form @submit.prevent="handleSearch">
  <select v-model="searchForm.country">...</select>
  <select v-model="searchForm.studyField">...</select>
  <input v-model="searchForm.keyword" />
  <button type="submit">Rechercher</button>
</form>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';

const searchForm = ref({
  country: '',
  studyField: '',
  keyword: ''
});

const handleSearch = () => {
  router.get(route('establishments.index'), {
    country: searchForm.value.country,
    studyField: searchForm.value.studyField,
    keyword: searchForm.value.keyword
  });
};
</script>

<!-- Carte domaine d'études -->
<Link
  :href="route('establishments.index', { studyField: field.id })"
  class="field-card"
>
  <div class="field-icon">{{ field.emoji }}</div>
  <div class="field-name">{{ field.name }}</div>
  <i class="bi bi-arrow-right field-arrow"></i>
</Link>

<!-- Carte établissement -->
<Link
  :href="route('establishments.show', establishment.slug)"
  class="establishment-card"
>
  <img :src="establishment.image" />
  <h3>{{ establishment.title }}</h3>
  ...
</Link>

<!-- CTA final -->
<Link
  :href="route('register')"
  class="btn btn-white btn-lg"
>
  Créer mon compte gratuitement
</Link>
```

## Animations CSS

### Keyframes obligatoires
```css
@keyframes slideInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slideInDown {
  from { opacity: 0; transform: translateY(-30px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}

@keyframes morph {
  0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
  50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; }
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
```

### Classes d'animation
```css
.is-visible .hero-badge { animation: slideInDown 0.6s ease-out; }
.is-visible .hero-title { animation: slideInUp 0.8s ease-out 0.2s both; }
.is-visible .search-card { animation: slideInUp 0.8s ease-out 0.4s both; }
.illustration-card { animation: float 3s ease-in-out infinite; }
.blob { animation: morph 8s ease-in-out infinite; }
.badge-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
```

## Responsive breakpoints

```css
/* Desktop */
@media (min-width: 992px) {
  .fields-grid { grid-template-columns: repeat(4, 1fr); }
  .establishments-grid { grid-template-columns: repeat(3, 1fr); }
  .benefits-grid { grid-template-columns: repeat(4, 1fr); }
  .testimonials-grid { grid-template-columns: repeat(3, 1fr); }
}

/* Tablet */
@media (max-width: 991px) {
  .fields-grid { grid-template-columns: repeat(2, 1fr); }
  .establishments-grid { grid-template-columns: repeat(2, 1fr); }
  .benefits-grid { grid-template-columns: repeat(2, 1fr); }
}

/* Mobile */
@media (max-width: 576px) {
  .fields-grid { grid-template-columns: 1fr; }
  .establishments-grid { grid-template-columns: 1fr; }
  .benefits-grid { grid-template-columns: 1fr; }
  .testimonials-grid { grid-template-columns: 1fr; }
}
```

## Route et contrôleur

### Route (routes/web.php)
```php
Route::get('/accueil', [HomeController::class, 'accueil'])->name('accueil');
```

### Méthode contrôleur (app/Http/Controllers/HomeController.php)
```php
public function accueil(): \Inertia\Response
{
    // Récupération 6 établissements populaires/récents
    $featuredEstablishments = Cache::remember('accueil_establishments', 3600, function() {
        return Property::with(['propertyType', 'city.region.country', 'ratings', 'media'])
            ->where('is_published', true)
            ->orderBy('ranking', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function($property) {
                return [
                    'id' => $property->hashid,
                    'slug' => $property->slug,
                    'title' => $property->title,
                    'city' => $property->city?->name ?? 'Non spécifié',
                    'country' => $property->city?->region?->country?->name ?? 'Non spécifié',
                    'type' => $property->propertyType?->label ?? 'Établissement',
                    'ranking' => $property->ranking,
                    'studentCount' => $property->student_count ?? rand(100, 500),
                    'image' => $property->getFirstMediaUrl('images', 'thumb') ?: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800',
                ];
            });
    });

    // Pays
    $countries = Country::select('id', 'name')
        ->whereHas('regions.cities.properties', fn($q) => $q->where('is_published', true))
        ->get();

    // Domaines d'études populaires (top 8)
    $studyFields = Category::select('id', 'label as name')
        ->where('is_published', true)
        ->withCount(['properties' => fn($q) => $q->where('is_published', true)])
        ->orderBy('properties_count', 'desc')
        ->limit(8)
        ->get();

    // Statistiques
    $stats = [
        'totalEstablishments' => Property::where('is_published', true)->count(),
        'totalStudents' => 2500,
        'totalCountries' => Country::whereHas('regions.cities.properties', fn($q) => $q->where('is_published', true))->count(),
        'totalPrograms' => Program::count(),
    ];

    // Témoignages
    $testimonials = [
        [
            'name' => 'Sophie Martin',
            'country' => 'France',
            'photo' => 'https://ui-avatars.com/api/?name=Sophie+Martin&background=1e3a8a&color=fff&size=128',
            'rating' => 5,
            'text' => 'EtapSup m\'a aidé à trouver l\'université parfaite pour mes études en gestion. Le processus était simple et l\'accompagnement excellent !',
        ],
        // ... 2 autres témoignages
    ];

    return Inertia::render('Home/Accueil', [
        'featuredEstablishments' => $featuredEstablishments,
        'countries' => $countries,
        'studyFields' => $studyFields,
        'stats' => $stats,
        'testimonials' => $testimonials,
    ]);
}
```

## Checklist de validation finale

### ✅ Design
- [ ] Charte graphique EtapSup respectée (#1e3a8a, #dc2626, #fbbf24)
- [ ] Gradients appliqués (hero, CTA)
- [ ] Glassmorphism sur formulaire et cartes avantages
- [ ] Responsive parfait (desktop, tablet, mobile)
- [ ] Animations fluides sans lag

### ✅ Fonctionnalités
- [ ] Formulaire recherche → redirection `/establishments` avec params
- [ ] Cartes domaines → liens vers `/establishments?studyField=X`
- [ ] Cartes établissements → liens vers `/establishments/SLUG`
- [ ] Bouton CTA → lien vers `/register`
- [ ] Stats affichent les vraies données (props)
- [ ] Images avec fallback Unsplash

### ✅ Code
- [ ] TypeScript strict (aucun `any`)
- [ ] Imports Bootstrap Icons utilisés
- [ ] Composant `Link` d'Inertia pour navigation
- [ ] Props typées avec interface
- [ ] Code commenté (sections principales)
- [ ] Performance optimisée (pas de re-render inutiles)

### ✅ Test manuel
- [ ] Tester la page à `http://127.0.0.1:8000/accueil`
- [ ] Cliquer sur le formulaire recherche → vérifier redirection
- [ ] Cliquer sur chaque domaine d'études → vérifier redirection
- [ ] Cliquer sur chaque établissement → vérifier redirection
- [ ] Cliquer sur "Créer mon compte" → vérifier redirection
- [ ] Tester responsive sur mobile (DevTools)
- [ ] Vérifier toutes les animations

## Instructions finales

1. Crée le fichier `resources/js/Pages/Home/Accueil.vue`
2. Ajoute la méthode `accueil()` dans `HomeController.php`
3. Ajoute la route dans `routes/web.php`
4. Teste TOUS les boutons et liens
5. Vérifie le responsive
6. Fais un commit clair avec description détaillée

**Ne fais PAS de `git push` sans demande explicite de l'utilisateur.**

---

## Prompt court à copier-coller

```
Tu es expert Vue.js senior. Crée une landing page moderne /accueil pour EtapSup (Laravel 11 + Vue 3 + Inertia.js + TypeScript) inspirée à 90% de Diplomeo.com.

Charte: #1e3a8a (bleu), #dc2626 (rouge), #fbbf24 (or)

Sections:
1. Hero avec formulaire recherche FONCTIONNEL (pays, domaine, keywords) → redirection /establishments
2. 8 domaines d'études CLIQUABLES → /establishments?studyField=X
3. 6 établissements CLIQUABLES → /establishments/SLUG
4. 4 avantages avec glassmorphism
5. 3 témoignages
6. CTA avec bouton FONCTIONNEL → /register

Animations: slideInUp, float, morph, pulse
Responsive: 992px, 576px
TOUS les boutons/liens doivent être fonctionnels avec Link d'Inertia

Crée Accueil.vue + méthode HomeController::accueil() + route
Teste TOUS les clics avant de valider
```
