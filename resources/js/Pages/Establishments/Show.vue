<script setup lang="ts">
// Refonte: Fiche détaillée établissement (adapté de RealEstate/Show)
// MAPPING: Property = Establishment, PropertyType = EstablishmentType, etc.
import AppHead from '@/Components/AppHead.vue';
import ExpandableContent from '@/Components/ExpandableContent.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Link } from '@inertiajs/vue3';
import { BContainer, BRow, BCol, BCard, BCardBody, BCardHeader, BCardTitle, BButton, BBadge, BProgress, BModal } from 'bootstrap-vue-next';
import { computed, ref } from 'vue';

defineOptions({ layout: GuestLayout });

/**
 * Interface TypeScript pour Establishment
 * MAPPING ETATSUP: Property = Establishment
 */
interface EstablishmentType {
    id: string;
    label: string;
}

interface Category {
    id: string;
    label: string;
}

interface City {
    id: string;
    name: string;
    country?: {
        id: string;
        name: string;
    };
    region?: {
        id: string;
        name: string;
    };
}

interface Equipment {
    id: number;
    label: string;
}

interface Regulation {
    id: number;
    label: string;
}

interface Image {
    url: string;
    alt?: string;
}

interface User {
    id: string;
    name: string;
}

interface Comment {
    id: number;
    content: string;
    score: number;
    user?: User;
    created_at: string;
}

interface Rating {
    score: number;
}

interface Program {
    id: string;
    name: string;
    slug: string;
    description?: string;
    degreeLevel?: {
        id: string;
        label: string;
    };
    studyField?: {
        id: string;
        label: string;
    };
    specialization?: {
        id: string;
        label: string;
    };
    duration?: string;
    language?: string;
    tuitionFee?: {
        amount: number;
        currency: string;
        formatted: string;
    };
    isPublished: boolean;
}

interface Establishment {
    id: string;
    title: string;
    slug: string;
    description: string;
    address?: string;
    propertyType?: EstablishmentType;
    category?: Category;
    subCategory?: Category;
    city?: City;
    equipments?: Equipment[];
    regulations?: Regulation[];
    images?: Image[];
    comments?: Comment[];
    ratings?: {
        average: number;
        count: number;
        distribution?: Record<number, { count: number; percentage: number }>;
        user_rating?: number;
    };
    // Phase 2: Champs éducatifs EtatSup
    website?: string;
    phone?: string;
    email?: string;
    studentCount?: number;
    ranking?: number;
    tuitionRange?: {
        min: number;
        max: number;
        currency: string;
    };
    accreditations?: {
        national: boolean;
        international: string[];
        labels: string[];
    };
    // Sprint 1: 5 sections fiche établissement (max 1000 chars)
    sectionPresentation?: string;
    sectionPrerequis?: string;
    sectionConditionsFinancieres?: string;
    sectionSpecialisation?: string;
    sectionCampus?: string;
    // Phase 3: Programmes d'études
    programs?: Program[];
}

const props = defineProps<{
    establishment: Establishment;
    pagination?: {
        current_page: number;
        per_page: number;
        total: number;
        has_more: boolean;
    };
}>();

// Calcul de la note moyenne à partir de la resource
const averageRating = computed(() => {
    return props.establishment.ratings?.average ?? 0;
});

const ratingCount = computed(() => props.establishment.ratings?.count ?? 0);

// DEMANDE 02.03: Partage social
const showShareModal = ref(false);
const shareUrl = computed(() => window.location.href);
const shareText = computed(() => {
    return `Découvrez ${props.establishment.title} - ${props.establishment.city?.name ?? ''}, ${props.establishment.city?.country?.name ?? ''}`;
});

// DEMANDE 02.02: Favoris (localStorage)
const isFavorite = ref(false);

const toggleFavorite = () => {
    const favorites = JSON.parse(localStorage.getItem('etatsup_favorites') || '[]');

    if (isFavorite.value) {
        // Retirer des favoris
        const index = favorites.indexOf(props.establishment.id);
        if (index > -1) {
            favorites.splice(index, 1);
        }
        isFavorite.value = false;
    } else {
        // Ajouter aux favoris
        if (!favorites.includes(props.establishment.id)) {
            favorites.push(props.establishment.id);
        }
        isFavorite.value = true;
    }

    localStorage.setItem('etatsup_favorites', JSON.stringify(favorites));
};

// Vérifier si déjà en favoris au chargement
const checkFavorite = () => {
    const favorites = JSON.parse(localStorage.getItem('etatsup_favorites') || '[]');
    isFavorite.value = favorites.includes(props.establishment.id);
};

checkFavorite();

// UI-Fix-TS: Fonction pour copier le lien de partage
const copyShareLink = () => {
    if (typeof window !== 'undefined' && window.navigator?.clipboard) {
        window.navigator.clipboard.writeText(shareUrl.value);
        window.alert('Lien copié !');
    }
};
</script>

<template>
    <AppHead :title="establishment.title">
        <meta
            head-key="description"
            name="description"
            :content="establishment.description"
        />
    </AppHead>

    <div class="establishment-show-page">
        <!-- Header avec localisation -->
        <section class="pt-4 pb-0 bg-light">
            <BContainer>
                <BRow>
                    <BCol lg="12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-3">
                                <li class="breadcrumb-item"><Link href="/">Accueil</Link></li>
                                <li class="breadcrumb-item"><Link href="/establishments">Établissements</Link></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ establishment.title }}</li>
                            </ol>
                        </nav>

                        <h1 class="h2 mb-2">{{ establishment.title }}</h1>

                        <div class="d-flex align-items-center mb-3 gap-3">
                            <!-- Type d'établissement -->
                            <BBadge variant="primary" pill>
                                {{ establishment.propertyType?.label || 'Établissement' }}
                            </BBadge>

                            <!-- Localisation -->
                            <span class="text-muted">
                                <i class="bi bi-geo-alt me-1"></i>
                                {{ establishment.city?.name }}, {{ establishment.city?.country?.name }}
                            </span>

                            <!-- Note -->
                            <div v-if="ratingCount > 0" class="d-flex align-items-center">
                                <span class="text-warning me-1">★</span>
                                <strong>{{ averageRating }}</strong>
                                <span class="text-muted ms-1">({{ ratingCount }} avis)</span>
                            </div>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>

        <!-- Galerie d'images (réutilise PropertyGallery si images existent) -->
        <section v-if="establishment.images && establishment.images.length > 0" class="py-0">
            <BContainer fluid>
                <BRow>
                    <BCol>
                        <div class="establishment-gallery">
                            <img
                                v-if="establishment.images[0]"
                                :src="establishment.images[0].url"
                                :alt="establishment.title"
                                class="img-fluid w-100"
                                style="max-height: 500px; object-fit: cover;"
                            />
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>

        <!-- Contenu principal -->
        <section class="py-5">
            <BContainer>
                <BRow class="g-4 g-xl-5">
                    <!-- Colonne principale -->
                    <BCol xl="8">
                        <div class="vstack gap-4">
                            <!-- Informations clés (Phase 2: champs éducatifs) -->
                            <BCard v-if="establishment.ranking || establishment.studentCount || establishment.tuitionRange" no-body class="border border-primary">
                                <BCardHeader class="bg-gradient-primary text-white">
                                    <h3 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informations clés</h3>
                                </BCardHeader>
                                <BCardBody>
                                    <BRow class="g-3">
                                        <!-- Classement -->
                                        <BCol v-if="establishment.ranking" md="4" class="text-center">
                                            <div class="p-3 bg-light rounded">
                                                <div class="text-primary h3 mb-1">#{{ establishment.ranking }}</div>
                                                <small class="text-muted">Classement national</small>
                                            </div>
                                        </BCol>

                                        <!-- Nombre d'étudiants -->
                                        <BCol v-if="establishment.studentCount" md="4" class="text-center">
                                            <div class="p-3 bg-light rounded">
                                                <div class="text-primary h3 mb-1">{{ establishment.studentCount.toLocaleString('fr-FR') }}</div>
                                                <small class="text-muted">Étudiants</small>
                                            </div>
                                        </BCol>

                                        <!-- Frais de scolarité -->
                                        <BCol v-if="establishment.tuitionRange" md="4" class="text-center">
                                            <div class="p-3 bg-light rounded">
                                                <div class="text-primary h6 mb-1">
                                                    {{ establishment.tuitionRange.min.toLocaleString('fr-FR', { style: 'currency', currency: establishment.tuitionRange.currency }) }} - {{ establishment.tuitionRange.max.toLocaleString('fr-FR', { style: 'currency', currency: establishment.tuitionRange.currency }) }}
                                                </div>
                                                <small class="text-muted">Frais annuels</small>
                                            </div>
                                        </BCol>
                                    </BRow>
                                </BCardBody>
                            </BCard>

                            <!-- 1. Section Présentation (Sprint 1 Feature 1.3.1) -->
                            <BCard no-body class="border">
                                <BCardHeader class="bg-light">
                                    <h3 class="mb-0">📖 Présentation</h3>
                                </BCardHeader>
                                <BCardBody>
                                    <ExpandableContent
                                        v-if="establishment.sectionPresentation"
                                        :content="establishment.sectionPresentation"
                                        :max-chars="500"
                                    />
                                    <p v-else class="text-muted fst-italic mb-0">
                                        Information à venir
                                    </p>
                                </BCardBody>
                            </BCard>

                            <!-- 2. Section Prérequis (Sprint 1 Feature 1.3.1) -->
                            <BCard no-body class="border">
                                <BCardHeader class="bg-light">
                                    <h3 class="mb-0">📋 Prérequis et admission</h3>
                                </BCardHeader>
                                <BCardBody>
                                    <ExpandableContent
                                        v-if="establishment.sectionPrerequis"
                                        :content="establishment.sectionPrerequis"
                                        :max-chars="500"
                                    />
                                    <p v-else class="text-muted fst-italic mb-0">
                                        Information à venir
                                    </p>
                                </BCardBody>
                            </BCard>

                            <!-- 3. Section Conditions financières (Sprint 1 Feature 1.3.1) -->
                            <BCard no-body class="border">
                                <BCardHeader class="bg-light">
                                    <h3 class="mb-0">💰 Frais de dossier</h3>
                                </BCardHeader>
                                <BCardBody>
                                    <ExpandableContent
                                        v-if="establishment.sectionConditionsFinancieres"
                                        :content="establishment.sectionConditionsFinancieres"
                                        :max-chars="500"
                                    />
                                    <p v-else class="text-muted fst-italic mb-0">
                                        Information à venir
                                    </p>
                                </BCardBody>
                            </BCard>

                            <!-- 4. Section Spécialisation (Sprint 1 Feature 1.3.1) -->
                            <BCard no-body class="border">
                                <BCardHeader class="bg-light">
                                    <h3 class="mb-0">📚 Programme</h3>
                                </BCardHeader>
                                <BCardBody>
                                    <ExpandableContent
                                        v-if="establishment.sectionSpecialisation"
                                        :content="establishment.sectionSpecialisation"
                                        :max-chars="500"
                                    />
                                    <p v-else class="text-muted fst-italic mb-0">
                                        Information à venir
                                    </p>

                                    <!-- Domaine d'études principal -->
                                    <div v-if="establishment.category" class="mt-4">
                                        <h5 class="fw-light mb-2">Domaine d'études principal</h5>
                                        <BBadge variant="primary" class="px-3 py-2">
                                            {{ establishment.category.label }}
                                        </BBadge>
                                    </div>

                                    <!-- Spécialisation -->
                                    <div v-if="establishment.subCategory" class="mt-3">
                                        <h5 class="fw-light mb-2">Spécialisation</h5>
                                        <BBadge variant="secondary" class="px-3 py-2">
                                            {{ establishment.subCategory.label }}
                                        </BBadge>
                                    </div>
                                </BCardBody>
                            </BCard>

                            <!-- 5. Section Campus (Sprint 1 Feature 1.3.1) -->
                            <BCard no-body class="border">
                                <BCardHeader class="bg-light">
                                    <h3 class="mb-0">🏫 Campus & Vie étudiante</h3>
                                </BCardHeader>
                                <BCardBody>
                                    <ExpandableContent
                                        v-if="establishment.sectionCampus"
                                        :content="establishment.sectionCampus"
                                        :max-chars="500"
                                    />
                                    <p v-else class="text-muted fst-italic mb-0">
                                        Information à venir
                                    </p>
                                </BCardBody>
                            </BCard>

                            <!-- Phase 3: Programmes d'études proposés -->
                            <BCard v-if="establishment.programs && establishment.programs.length > 0" no-body class="border border-primary">
                                <BCardHeader class="bg-gradient-primary text-white">
                                    <h3 class="mb-0">
                                        <i class="bi bi-mortarboard-fill me-2"></i>
                                        Programmes d'études proposés
                                    </h3>
                                </BCardHeader>
                                <BCardBody>
                                    <div class="vstack gap-3">
                                        <div
                                            v-for="program in establishment.programs"
                                            :key="program.id"
                                            class="p-3 border rounded bg-light"
                                        >
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1">{{ program.name }}</h5>
                                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                                        <!-- Niveau de diplôme -->
                                                        <BBadge v-if="program.degreeLevel" variant="primary" class="px-2 py-1">
                                                            <i class="bi bi-award me-1"></i>
                                                            {{ program.degreeLevel.label }}
                                                        </BBadge>

                                                        <!-- Durée -->
                                                        <BBadge v-if="program.duration" variant="secondary" class="px-2 py-1">
                                                            <i class="bi bi-clock me-1"></i>
                                                            {{ program.duration }}
                                                        </BBadge>

                                                        <!-- Langue -->
                                                        <BBadge v-if="program.language" variant="info" class="px-2 py-1">
                                                            <i class="bi bi-translate me-1"></i>
                                                            {{ program.language }}
                                                        </BBadge>

                                                        <!-- Frais de scolarité -->
                                                        <BBadge v-if="program.tuitionFee" variant="success" class="px-2 py-1">
                                                            <i class="bi bi-currency-euro me-1"></i>
                                                            {{ program.tuitionFee.formatted }}
                                                        </BBadge>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Description du programme -->
                                            <p v-if="program.description" class="mb-2 text-muted small">
                                                {{ program.description }}
                                            </p>

                                            <!-- Domaine et spécialisation -->
                                            <div v-if="program.studyField || program.specialization" class="d-flex flex-wrap gap-2 mt-2">
                                                <span v-if="program.studyField" class="badge bg-white border text-dark px-2 py-1">
                                                    <i class="bi bi-book me-1"></i>
                                                    {{ program.studyField.label }}
                                                </span>
                                                <span v-if="program.specialization" class="badge bg-white border text-dark px-2 py-1">
                                                    <i class="bi bi-star me-1"></i>
                                                    {{ program.specialization.label }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Message si aucun programme publié -->
                                        <div v-if="establishment.programs.length === 0" class="text-center py-4 text-muted">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Aucun programme publié pour le moment
                                        </div>
                                    </div>
                                </BCardBody>
                            </BCard>

                            <!-- Infrastructures & Équipements -->
                            <BCard v-if="establishment.equipments && establishment.equipments.length > 0" no-body class="border">
                                <BCardHeader class="bg-light">
                                    <h3 class="mb-0">Infrastructures & Équipements</h3>
                                </BCardHeader>
                                <BCardBody>
                                    <BRow class="g-3">
                                        <BCol
                                            v-for="equipment in establishment.equipments"
                                            :key="equipment.id"
                                            sm="6"
                                            md="4"
                                        >
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                                <span>{{ equipment.label }}</span>
                                            </div>
                                        </BCol>
                                    </BRow>
                                </BCardBody>
                            </BCard>

                            <!-- Accréditations & Certifications (Phase 2: enrichi) -->
                            <!-- DEMANDE 02.04: Masqué temporairement -->
                            <BCard v-if="false && (((establishment.regulations?.length ?? 0) > 0) || establishment.accreditations)" no-body class="border">
                                <!-- UI-Fix-TS: TypeScript strict null checks -->
                                <BCardHeader class="bg-light">
                                    <h3 class="mb-0">Accréditations & Certifications</h3>
                                </BCardHeader>
                                <BCardBody>
                                    <!-- Accréditation nationale -->
                                    <div v-if="establishment.accreditations?.national" class="mb-3">
                                        <BBadge variant="success" class="px-3 py-2">
                                            <i class="bi bi-patch-check-fill me-1"></i>
                                            Accréditation Nationale
                                        </BBadge>
                                    </div>

                                    <!-- Accréditations internationales -->
                                    <div v-if="(establishment.accreditations?.international?.length ?? 0) > 0" class="mb-3">
                                        <h5 class="fw-light mb-2 small">Accréditations internationales</h5>
                                        <div class="d-flex flex-wrap gap-2">
                                            <BBadge
                                                v-for="(accred, index) in establishment.accreditations?.international"
                                                :key="index"
                                                variant="primary"
                                                class="px-3 py-2"
                                            >
                                                <i class="bi bi-globe me-1"></i>
                                                {{ accred }}
                                            </BBadge>
                                        </div>
                                    </div>

                                    <!-- Labels qualité -->
                                    <div v-if="(establishment.accreditations?.labels?.length ?? 0) > 0" class="mb-3">
                                        <h5 class="fw-light mb-2 small">Labels qualité</h5>
                                        <div class="d-flex flex-wrap gap-2">
                                            <BBadge
                                                v-for="(label, index) in establishment.accreditations?.labels"
                                                :key="index"
                                                variant="info"
                                                class="px-3 py-2"
                                            >
                                                <i class="bi bi-award me-1"></i>
                                                {{ label }}
                                            </BBadge>
                                        </div>
                                    </div>

                                    <!-- Certifications legacy (regulations) -->
                                    <div v-if="(establishment.regulations?.length ?? 0) > 0">
                                        <h5 class="fw-light mb-2 small">Autres certifications</h5>
                                        <div class="d-flex flex-wrap gap-2">
                                            <BBadge
                                                v-for="regulation in establishment.regulations"
                                                :key="regulation.id"
                                                variant="success"
                                                class="px-3 py-2"
                                            >
                                                <i class="bi bi-patch-check me-1"></i>
                                                {{ regulation.label }}
                                            </BBadge>
                                        </div>
                                    </div>
                                </BCardBody>
                            </BCard>

                            <!-- Localisation -->
                            <BCard no-body class="border">
                                <BCardHeader class="bg-light">
                                    <h3 class="mb-0">
                                        <i class="bi bi-geo-alt me-2"></i>
                                        Localisation
                                    </h3>
                                </BCardHeader>
                                <BCardBody>
                                    <p class="mb-0">
                                        <strong>Adresse:</strong> {{ establishment.address }}<br>
                                        <strong>Ville:</strong> {{ establishment.city?.name }}<br>
                                        <strong>Région:</strong> {{ establishment.city?.region?.name }}<br>
                                        <strong>Pays:</strong> {{ establishment.city?.country?.name }}
                                    </p>
                                </BCardBody>
                            </BCard>

                            <!-- Avis étudiants -->
                            <BCard v-if="establishment.comments && establishment.comments.length > 0" no-body class="border">
                                <BCardHeader class="bg-light">
                                    <h3 class="mb-0">Avis des étudiants</h3>
                                </BCardHeader>
                                <BCardBody>
                                    <div v-for="comment in establishment.comments" :key="comment.id" class="mb-4 pb-3 border-bottom">
                                        <div class="d-flex justify-content-between mb-2">
                                            <div>
                                                <strong>{{ comment.user?.name || 'Étudiant anonyme' }}</strong>
                                                <div class="text-warning small">
                                                    <span v-for="n in 5" :key="n">
                                                        {{ n <= comment.score ? '★' : '☆' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                {{ new Date(comment.created_at).toLocaleDateString('fr-FR') }}
                                            </small>
                                        </div>
                                        <p class="mb-0">{{ comment.content }}</p>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </div>
                    </BCol>

                    <!-- Sidebar avec CTA -->
                    <BCol xl="4">
                        <div class="sticky-top" style="top: 100px;">
                            <!-- Carte action principale -->
                            <BCard class="border shadow-sm">
                                <BCardBody>
                                    <h4 class="mb-3">Intéressé(e) par cet établissement ?</h4>

                                    <!-- Note globale -->
                                    <div v-if="ratingCount > 0" class="mb-4 p-3 bg-light rounded text-center">
                                        <div class="h2 mb-1">{{ averageRating }}/5</div>
                                        <div class="text-warning mb-1">
                                            ★★★★☆
                                        </div>
                                        <small class="text-muted">Basé sur {{ ratingCount }} avis</small>
                                    </div>

                                    <!-- Sprint1 Feature 1.3.1 — Bouton Candidater (Gradient EtapSup #1e3a8a → #1e3a8a) -->
                                    <Link
                                        v-if="$page.props.auth?.user"
                                        :href="`/applications/create?establishment_id=${establishment.id}`"
                                        class="btn btn-lg w-100 mb-3"
                                        style="background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%); border: none; color: white; font-weight: 600;"
                                    >
                                        <i class="bi bi-send-fill me-2"></i>
                                        Candidater
                                    </Link>
                                    <Link
                                        v-else
                                        href="/login"
                                        class="btn btn-lg w-100 mb-3"
                                        style="background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%); border: none; color: white; font-weight: 600;"
                                    >
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        Connectez-vous pour candidater
                                    </Link>

                                    <!-- Infos clés -->
                                    <div class="vstack gap-2">
                                        <div class="d-flex justify-content-between small">
                                            <span class="text-muted">Type:</span>
                                            <strong>{{ establishment.propertyType?.label }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between small">
                                            <span class="text-muted">Ville:</span>
                                            <strong>{{ establishment.city?.name }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between small">
                                            <span class="text-muted">Pays:</span>
                                            <strong>{{ establishment.city?.country?.name }}</strong>
                                        </div>
                                    </div>

                                    <!-- Contact établissement (Phase 2) -->
                                    <div v-if="establishment.website || establishment.phone || establishment.email" class="mt-3">
                                        <hr class="my-3">
                                        <h6 class="mb-2">Contact</h6>
                                        <div class="vstack gap-2 small">
                                            <a v-if="establishment.website" :href="establishment.website" target="_blank" class="text-decoration-none">
                                                <i class="bi bi-globe me-2"></i>
                                                Site web
                                            </a>
                                            <a v-if="establishment.phone" :href="`tel:${establishment.phone}`" class="text-decoration-none">
                                                <i class="bi bi-telephone me-2"></i>
                                                {{ establishment.phone }}
                                            </a>
                                            <a v-if="establishment.email" :href="`mailto:${establishment.email}`" class="text-decoration-none">
                                                <i class="bi bi-envelope me-2"></i>
                                                {{ establishment.email }}
                                            </a>
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    <!-- Boutons secondaires -->
                                    <div class="d-grid gap-2">
                                        <!-- DEMANDE 02.02: Favoris -->
                                        <BButton
                                            :variant="isFavorite ? 'primary' : 'outline-secondary'"
                                            size="sm"
                                            @click="toggleFavorite"
                                        >
                                            <i :class="isFavorite ? 'bi bi-bookmark-fill' : 'bi bi-bookmark'" class="me-2"></i>
                                            {{ isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris' }}
                                        </BButton>
                                        <!-- DEMANDE 02.03: Partage -->
                                        <BButton
                                            variant="outline-secondary"
                                            size="sm"
                                            @click="showShareModal = true"
                                        >
                                            <i class="bi bi-share me-2"></i>
                                            Partager
                                        </BButton>
                                    </div>
                                </BCardBody>
                            </BCard>

                            <!-- Établissements similaires -->
                            <BCard class="border mt-4">
                                <BCardBody>
                                    <h5 class="mb-3">Établissements similaires</h5>
                                    <p class="text-muted small">
                                        Découvrez d'autres établissements qui pourraient vous intéresser
                                    </p>
                                    <Link href="/establishments" class="btn btn-outline-primary btn-sm w-100">
                                        Voir tous les établissements
                                    </Link>
                                </BCardBody>
                            </BCard>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>

        <!-- DEMANDE 02.03: Modal de partage social -->
        <BModal v-model="showShareModal" title="Partager cet établissement" hide-footer>
            <p class="mb-3">{{ shareText }}</p>

            <div class="d-grid gap-2">
                <!-- Facebook -->
                <a
                    :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`"
                    target="_blank"
                    class="btn btn-primary"
                    style="background-color: #1877f2; border-color: #1877f2;"
                >
                    <i class="bi bi-facebook me-2"></i>
                    Partager sur Facebook
                </a>

                <!-- Twitter/X -->
                <a
                    :href="`https://twitter.com/intent/tweet?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(shareText)}`"
                    target="_blank"
                    class="btn btn-dark"
                >
                    <i class="bi bi-twitter-x me-2"></i>
                    Partager sur X (Twitter)
                </a>

                <!-- LinkedIn -->
                <a
                    :href="`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl)}`"
                    target="_blank"
                    class="btn btn-primary"
                    style="background-color: #0a66c2; border-color: #0a66c2;"
                >
                    <i class="bi bi-linkedin me-2"></i>
                    Partager sur LinkedIn
                </a>

                <!-- WhatsApp -->
                <a
                    :href="`https://wa.me/?text=${encodeURIComponent(shareText + ' ' + shareUrl)}`"
                    target="_blank"
                    class="btn btn-success"
                    style="background-color: #25d366; border-color: #25d366;"
                >
                    <i class="bi bi-whatsapp me-2"></i>
                    Partager sur WhatsApp
                </a>

                <!-- Copier le lien -->
                <BButton
                    variant="outline-secondary"
                    @click="copyShareLink"
                >
                    <i class="bi bi-clipboard me-2"></i>
                    Copier le lien
                </BButton>
            </div>
        </BModal>

        <!-- Sprint1 Feature 1.3.1 — Footer sticky mobile pour Candidater -->
        <div class="mobile-footer-cta d-md-none">
            <BContainer>
                <BRow>
                    <BCol>
                        <Link
                            v-if="$page.props.auth?.user"
                            :href="`/applications/create?establishment_id=${establishment.id}`"
                            class="btn btn-lg w-100"
                            style="background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%); border: none; color: white; font-weight: 600;"
                        >
                            <i class="bi bi-send-fill me-2"></i>
                            Candidater
                        </Link>
                        <Link
                            v-else
                            href="/login"
                            class="btn btn-lg w-100"
                            style="background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%); border: none; color: white; font-weight: 600;"
                        >
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Connectez-vous pour candidater
                        </Link>
                    </BCol>
                </BRow>
            </BContainer>
        </div>
    </div>
</template>

<style scoped>
.establishment-show-page {
    min-height: 100vh;
    padding-bottom: 80px; /* Sprint1 Feature 1.3.1 — Espace pour footer sticky mobile */
}

.breadcrumb {
    background: transparent;
    padding: 0;
}

.establishment-gallery img {
    border-radius: 8px;
}

/* Charte EtatSup: Purple gradient */
.bg-gradient-primary {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
}

/* Sprint1 Feature 1.3.1 — Footer sticky mobile pour bouton Candidater */
.mobile-footer-cta {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    padding: 12px 0;
    z-index: 1030;
}
</style>
