<script setup lang="ts">
// Landing Page Moderne EtapSup - Inspirée de Diplomeo.com
// Design pixel-perfect avec animations et micro-interactions
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { BContainer, BRow, BCol, BButton, BFormInput, BFormSelect, BCard } from 'bootstrap-vue-next';

defineOptions({ layout: GuestLayout });

interface Establishment {
    id: string;
    slug: string;
    title: string;
    logo?: string;
    thumb?: string;
    country: string;
    city: string;
    type: string;
    ranking?: number;
    studentCount?: number;
}

interface Props {
    establishments?: Establishment[];
    stats?: {
        totalEstablishments: number;
        totalStudents: number;
        totalCountries: number;
        totalPrograms: number;
    };
    countries?: Array<{ id: string; name: string }>;
    studyFields?: Array<{ id: string; name: string }>;
}

const props = withDefaults(defineProps<Props>(), {
    establishments: () => [],
    stats: () => ({
        totalEstablishments: 150,
        totalStudents: 2500,
        totalCountries: 15,
        totalPrograms: 520
    }),
    countries: () => [],
    studyFields: () => []
});

// Search form
const searchForm = ref({
    country: '',
    study_field: '',
    keywords: ''
});

const handleSearch = () => {
    router.get('/establishments', searchForm.value);
};

// Animation on scroll
const isVisible = ref(false);
onMounted(() => {
    setTimeout(() => {
        isVisible.value = true;
    }, 100);
});

// Popular study fields
const popularFields = [
    { icon: '💼', name: 'Commerce & Gestion', slug: 'commerce' },
    { icon: '⚕️', name: 'Santé & Médecine', slug: 'sante' },
    { icon: '💻', name: 'Informatique & Tech', slug: 'informatique' },
    { icon: '🎨', name: 'Arts & Design', slug: 'arts' },
    { icon: '⚖️', name: 'Droit & Sciences Po', slug: 'droit' },
    { icon: '🔬', name: 'Sciences & Ingénierie', slug: 'sciences' },
    { icon: '🌍', name: 'Environnement', slug: 'environnement' },
    { icon: '🎓', name: 'Éducation & Formation', slug: 'education' }
];

// Why choose EtapSup
const benefits = [
    {
        icon: '🎯',
        title: 'Orientation personnalisée',
        description: 'Un accompagnement sur-mesure pour trouver la formation qui vous correspond'
    },
    {
        icon: '🌍',
        title: 'Réseau international',
        description: 'Plus de 150 établissements partenaires dans 15 pays africains'
    },
    {
        icon: '✅',
        title: 'Candidature simplifiée',
        description: 'Postulez en quelques clics avec un dossier unique pour tous vos choix'
    },
    {
        icon: '🤝',
        title: 'Suivi complet',
        description: 'De la recherche jusqu\'à votre inscription, nous vous accompagnons'
    }
];

// Testimonials
const testimonials = [
    {
        name: 'Aminata D.',
        country: 'Sénégal',
        photo: 'https://ui-avatars.com/api/?name=Aminata+D&background=1e3a8a&color=fff&size=128',
        text: 'Grâce à EtapSup, j\'ai trouvé la formation parfaite au Ghana. Le processus était simple et rapide !',
        rating: 5
    },
    {
        name: 'Kofi M.',
        country: 'Ghana',
        photo: 'https://ui-avatars.com/api/?name=Kofi+M&background=dc2626&color=fff&size=128',
        text: 'L\'accompagnement personnalisé m\'a aidé à choisir parmi plusieurs options. Merci EtapSup !',
        rating: 5
    },
    {
        name: 'Fatima B.',
        country: 'Côte d\'Ivoire',
        photo: 'https://ui-avatars.com/api/?name=Fatima+B&background=1e3a8a&color=fff&size=128',
        text: 'Service excellent, réponses rapides. Je recommande vivement pour les études à l\'étranger.',
        rating: 5
    }
];
</script>

<template>
    <Head title="EtapSup - Votre passerelle vers les études supérieures à l'étranger" />

    <div class="landing-page">
        <!-- Hero Section - Inspiré Diplomeo avec effet gradient moderne -->
        <section class="hero-section" :class="{ 'is-visible': isVisible }">
            <BContainer>
                <BRow class="align-items-center min-vh-75">
                    <BCol lg="7" class="hero-content">
                        <div class="badge-new mb-3">
                            <span class="badge-pulse"></span>
                            Nouveau : Accompagnement Premium disponible
                        </div>

                        <h1 class="hero-title">
                            Trouvez votre formation
                            <span class="text-gradient">à l'étranger</span>
                        </h1>

                        <p class="hero-subtitle">
                            Plus de 150 établissements partenaires dans 15 pays africains.
                            <br class="d-none d-md-block">
                            Postulez en quelques clics avec un accompagnement personnalisé.
                        </p>

                        <!-- Search Form Modern -->
                        <div class="search-card">
                            <form @submit.prevent="handleSearch" class="search-form">
                                <div class="search-field">
                                    <label class="search-label">
                                        <i class="bi bi-flag"></i>
                                        Pays
                                    </label>
                                    <BFormSelect
                                        v-model="searchForm.country"
                                        :options="[
                                            { value: '', text: 'Tous les pays' },
                                            ...countries.map(c => ({ value: c.id, text: c.name }))
                                        ]"
                                        class="search-select"
                                    />
                                </div>

                                <div class="search-field">
                                    <label class="search-label">
                                        <i class="bi bi-book"></i>
                                        Domaine d'études
                                    </label>
                                    <BFormSelect
                                        v-model="searchForm.study_field"
                                        :options="[
                                            { value: '', text: 'Tous les domaines' },
                                            ...studyFields.map(f => ({ value: f.id, text: f.name }))
                                        ]"
                                        class="search-select"
                                    />
                                </div>

                                <div class="search-field flex-grow-1">
                                    <label class="search-label">
                                        <i class="bi bi-search"></i>
                                        Mots-clés
                                    </label>
                                    <BFormInput
                                        v-model="searchForm.keywords"
                                        placeholder="Ex: Médecine, Commerce, Informatique..."
                                        class="search-input"
                                    />
                                </div>

                                <BButton type="submit" class="btn-search">
                                    <i class="bi bi-search me-2"></i>
                                    Rechercher
                                </BButton>
                            </form>
                        </div>

                        <!-- Stats -->
                        <div class="stats-row">
                            <div class="stat-item">
                                <div class="stat-value">{{ stats.totalEstablishments }}+</div>
                                <div class="stat-label">Établissements</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">{{ stats.totalStudents }}+</div>
                                <div class="stat-label">Étudiants accompagnés</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">{{ stats.totalCountries }}</div>
                                <div class="stat-label">Pays</div>
                            </div>
                        </div>
                    </BCol>

                    <BCol lg="5" class="d-none d-lg-block">
                        <div class="hero-illustration">
                            <div class="floating-card card-1">
                                <i class="bi bi-mortarboard-fill"></i>
                                <span>Licence</span>
                            </div>
                            <div class="floating-card card-2">
                                <i class="bi bi-book-fill"></i>
                                <span>Master</span>
                            </div>
                            <div class="floating-card card-3">
                                <i class="bi bi-award-fill"></i>
                                <span>Doctorat</span>
                            </div>
                            <div class="hero-blob"></div>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>

        <!-- Popular Study Fields -->
        <section class="study-fields-section">
            <BContainer>
                <div class="section-header text-center">
                    <h2 class="section-title">Explorez les domaines d'études</h2>
                    <p class="section-subtitle">Découvrez les formations les plus recherchées</p>
                </div>

                <div class="fields-grid">
                    <Link
                        v-for="field in popularFields"
                        :key="field.slug"
                        :href="`/establishments?field=${field.slug}`"
                        class="field-card"
                    >
                        <div class="field-icon">{{ field.icon }}</div>
                        <div class="field-name">{{ field.name }}</div>
                        <i class="bi bi-arrow-right field-arrow"></i>
                    </Link>
                </div>
            </BContainer>
        </section>

        <!-- Featured Establishments -->
        <section class="establishments-section" v-if="establishments.length > 0">
            <BContainer>
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Établissements à la une</h2>
                        <p class="section-subtitle">Les meilleures universités et écoles partenaires</p>
                    </div>
                    <Link href="/establishments" class="btn-view-all">
                        Voir tout
                        <i class="bi bi-arrow-right ms-2"></i>
                    </Link>
                </div>

                <BRow class="g-4">
                    <BCol
                        v-for="establishment in establishments.slice(0, 6)"
                        :key="establishment.id"
                        md="6"
                        lg="4"
                    >
                        <Link
                            :href="`/establishments/${establishment.slug}`"
                            class="establishment-card"
                        >
                            <div class="establishment-image">
                                <img
                                    :src="establishment.thumb || establishment.logo || 'https://via.placeholder.com/400x240?text=EtapSup'"
                                    :alt="establishment.title"
                                />
                                <div class="establishment-badge" v-if="establishment.ranking">
                                    <i class="bi bi-star-fill"></i>
                                    Top {{ establishment.ranking }}
                                </div>
                            </div>
                            <div class="establishment-body">
                                <h3 class="establishment-title">{{ establishment.title }}</h3>
                                <div class="establishment-meta">
                                    <span class="meta-item">
                                        <i class="bi bi-geo-alt-fill"></i>
                                        {{ establishment.city }}, {{ establishment.country }}
                                    </span>
                                    <span class="meta-item" v-if="establishment.studentCount">
                                        <i class="bi bi-people-fill"></i>
                                        {{ establishment.studentCount }} étudiants
                                    </span>
                                </div>
                                <div class="establishment-type">{{ establishment.type }}</div>
                            </div>
                        </Link>
                    </BCol>
                </BRow>
            </BContainer>
        </section>

        <!-- Why EtapSup -->
        <section class="benefits-section">
            <BContainer>
                <div class="section-header text-center">
                    <h2 class="section-title">Pourquoi choisir EtapSup ?</h2>
                    <p class="section-subtitle">Un accompagnement complet pour réussir vos études à l'étranger</p>
                </div>

                <BRow class="g-4">
                    <BCol
                        v-for="(benefit, index) in benefits"
                        :key="index"
                        md="6"
                        lg="3"
                    >
                        <div class="benefit-card">
                            <div class="benefit-icon">{{ benefit.icon }}</div>
                            <h3 class="benefit-title">{{ benefit.title }}</h3>
                            <p class="benefit-description">{{ benefit.description }}</p>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>

        <!-- Testimonials -->
        <section class="testimonials-section">
            <BContainer>
                <div class="section-header text-center">
                    <h2 class="section-title">Ils nous font confiance</h2>
                    <p class="section-subtitle">Découvrez les témoignages de nos étudiants</p>
                </div>

                <BRow class="g-4">
                    <BCol
                        v-for="(testimonial, index) in testimonials"
                        :key="index"
                        md="4"
                    >
                        <div class="testimonial-card">
                            <div class="testimonial-rating">
                                <i v-for="i in 5" :key="i" class="bi bi-star-fill"></i>
                            </div>
                            <p class="testimonial-text">"{{ testimonial.text }}"</p>
                            <div class="testimonial-author">
                                <img :src="testimonial.photo" :alt="testimonial.name" class="author-photo" />
                                <div>
                                    <div class="author-name">{{ testimonial.name }}</div>
                                    <div class="author-country">{{ testimonial.country }}</div>
                                </div>
                            </div>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <BContainer>
                <div class="cta-card">
                    <BRow class="align-items-center">
                        <BCol lg="8">
                            <h2 class="cta-title">Prêt à commencer votre aventure ?</h2>
                            <p class="cta-subtitle">
                                Créez votre compte gratuitement et découvrez toutes les opportunités qui s'offrent à vous
                            </p>
                        </BCol>
                        <BCol lg="4" class="text-lg-end">
                            <Link href="/register" class="btn-cta">
                                Créer mon compte
                                <i class="bi bi-arrow-right ms-2"></i>
                            </Link>
                        </BCol>
                    </BRow>
                </div>
            </BContainer>
        </section>
    </div>
</template>

<style scoped>
/* Hero Section - Gradient moderne */
.hero-section {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
    position: relative;
    overflow: hidden;
    padding: 80px 0 60px;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.hero-section.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background:
        radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    pointer-events: none;
}

.min-vh-75 {
    min-height: 75vh;
}

.hero-content {
    position: relative;
    z-index: 2;
}

/* Badge New */
.badge-new {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 8px 16px;
    border-radius: 50px;
    color: white;
    font-size: 0.875rem;
    font-weight: 500;
    animation: slideInDown 0.6s ease-out;
}

.badge-pulse {
    width: 8px;
    height: 8px;
    background: #dc2626;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(1.2);
    }
}

/* Hero Title */
.hero-title {
    font-size: clamp(2.5rem, 5vw, 3.75rem);
    font-weight: 800;
    color: white;
    margin-bottom: 1.5rem;
    line-height: 1.2;
    animation: slideInUp 0.8s ease-out 0.2s both;
}

.text-gradient {
    background: linear-gradient(to right, #fbbf24, #f59e0b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: 1.125rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 2.5rem;
    line-height: 1.6;
    animation: slideInUp 0.8s ease-out 0.4s both;
}

/* Search Card - Modern Design */
.search-card {
    background: white;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    margin-bottom: 2rem;
    animation: slideInUp 0.8s ease-out 0.6s both;
}

.search-form {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.search-field {
    flex: 1;
    min-width: 200px;
}

.search-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.search-label i {
    color: #1e3a8a;
}

.search-select,
.search-input {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 1rem;
    transition: all 0.2s;
}

.search-select:focus,
.search-input:focus {
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    outline: none;
}

.btn-search {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    border: none;
    border-radius: 12px;
    padding: 12px 32px;
    font-weight: 600;
    font-size: 1rem;
    color: white;
    transition: all 0.3s;
    white-space: nowrap;
    align-self: flex-end;
}

.btn-search:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.4);
}

/* Stats Row */
.stats-row {
    display: flex;
    gap: 48px;
    animation: slideInUp 0.8s ease-out 0.8s both;
}

.stat-item {
    text-align: left;
}

.stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: white;
    line-height: 1;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.8);
}

/* Hero Illustration */
.hero-illustration {
    position: relative;
    height: 500px;
}

.floating-card {
    position: absolute;
    background: white;
    border-radius: 16px;
    padding: 16px 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.3);
    animation: float 3s ease-in-out infinite;
}

.floating-card i {
    font-size: 1.5rem;
    color: #1e3a8a;
}

.floating-card span {
    font-weight: 600;
    color: #374151;
}

.card-1 {
    top: 20%;
    right: 10%;
    animation-delay: 0s;
}

.card-2 {
    top: 50%;
    right: 30%;
    animation-delay: 1s;
}

.card-3 {
    top: 70%;
    right: 5%;
    animation-delay: 2s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}

.hero-blob {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
    animation: morph 8s ease-in-out infinite;
}

@keyframes morph {
    0%, 100% {
        border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
    }
    50% {
        border-radius: 70% 30% 50% 50% / 30% 60% 70% 40%;
    }
}

/* Study Fields Section */
.study-fields-section {
    padding: 80px 0;
    background: #f9fafb;
}

.section-header {
    margin-bottom: 48px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}

.section-title {
    font-size: 2.25rem;
    font-weight: 800;
    color: #111827;
    margin-bottom: 8px;
}

.section-subtitle {
    font-size: 1.125rem;
    color: #6b7280;
}

.fields-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.field-card {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 16px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
}

.field-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    opacity: 0;
    transition: opacity 0.3s;
}

.field-card:hover {
    border-color: #1e3a8a;
    transform: translateY(-4px);
    box-shadow: 0 10px 30px -10px rgba(30, 58, 138, 0.3);
}

.field-card:hover::before {
    opacity: 1;
}

.field-card:hover .field-icon,
.field-card:hover .field-name,
.field-card:hover .field-arrow {
    position: relative;
    z-index: 1;
    color: white;
}

.field-icon {
    font-size: 2rem;
    flex-shrink: 0;
}

.field-name {
    font-weight: 600;
    font-size: 1rem;
    color: #111827;
    flex: 1;
}

.field-arrow {
    color: #9ca3af;
    transition: transform 0.3s;
}

.field-card:hover .field-arrow {
    transform: translateX(4px);
}

/* Establishments Section */
.establishments-section {
    padding: 80px 0;
}

.btn-view-all {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: transparent;
    border: 2px solid #1e3a8a;
    border-radius: 12px;
    color: #1e3a8a;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-view-all:hover {
    background: #1e3a8a;
    color: white;
    transform: translateY(-2px);
}

.establishment-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    display: block;
    border: 1px solid #e5e7eb;
    transition: all 0.3s;
    height: 100%;
}

.establishment-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15);
    border-color: #1e3a8a;
}

.establishment-image {
    position: relative;
    height: 200px;
    overflow: hidden;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
}

.establishment-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.establishment-card:hover .establishment-image img {
    transform: scale(1.05);
}

.establishment-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 4px;
}

.establishment-body {
    padding: 20px;
}

.establishment-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 12px;
    line-height: 1.3;
}

.establishment-meta {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.875rem;
    color: #6b7280;
}

.meta-item i {
    color: #1e3a8a;
}

.establishment-type {
    display: inline-block;
    background: #eff6ff;
    color: #1e3a8a;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Benefits Section */
.benefits-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
}

.benefits-section .section-title,
.benefits-section .section-subtitle {
    color: white;
}

.benefit-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    padding: 32px;
    text-align: center;
    transition: all 0.3s;
}

.benefit-card:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-8px);
}

.benefit-icon {
    font-size: 3rem;
    margin-bottom: 16px;
}

.benefit-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: white;
    margin-bottom: 12px;
}

.benefit-description {
    color: rgba(255, 255, 255, 0.9);
    line-height: 1.6;
    margin: 0;
}

/* Testimonials Section */
.testimonials-section {
    padding: 80px 0;
    background: #f9fafb;
}

.testimonial-card {
    background: white;
    border-radius: 20px;
    padding: 32px;
    border: 1px solid #e5e7eb;
    transition: all 0.3s;
    height: 100%;
}

.testimonial-card:hover {
    box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
    transform: translateY(-4px);
}

.testimonial-rating {
    color: #fbbf24;
    font-size: 1rem;
    margin-bottom: 16px;
}

.testimonial-text {
    font-size: 1rem;
    color: #374151;
    line-height: 1.6;
    margin-bottom: 24px;
    font-style: italic;
}

.testimonial-author {
    display: flex;
    align-items: center;
    gap: 12px;
}

.author-photo {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: 2px solid #1e3a8a;
}

.author-name {
    font-weight: 700;
    color: #111827;
}

.author-country {
    font-size: 0.875rem;
    color: #6b7280;
}

/* CTA Section */
.cta-section {
    padding: 80px 0;
}

.cta-card {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    border-radius: 24px;
    padding: 48px;
    position: relative;
    overflow: hidden;
}

.cta-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.cta-title {
    font-size: 2rem;
    font-weight: 800;
    color: white;
    margin-bottom: 12px;
}

.cta-subtitle {
    font-size: 1.125rem;
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
}

.btn-cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: white;
    color: #1e3a8a;
    padding: 16px 32px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1.125rem;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px -10px rgba(255, 255, 255, 0.5);
}

/* Animations */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 992px) {
    .search-form {
        flex-direction: column;
    }

    .search-field {
        min-width: 100%;
    }

    .btn-search {
        width: 100%;
    }

    .stats-row {
        gap: 24px;
        flex-wrap: wrap;
    }

    .fields-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }

    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .cta-card {
        padding: 32px;
        text-align: center;
    }

    .btn-cta {
        margin-top: 16px;
    }
}

@media (max-width: 576px) {
    .hero-title {
        font-size: 2rem;
    }

    .section-title {
        font-size: 1.75rem;
    }

    .stats-row {
        justify-content: space-between;
    }

    .stat-value {
        font-size: 1.5rem;
    }
}
</style>
