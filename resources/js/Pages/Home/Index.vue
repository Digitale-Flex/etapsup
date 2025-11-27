<script setup lang="ts">
// Refonte: Page d'accueil inspirée de Diplomeo.com avec charte EtapSup
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { BContainer, BRow, BCol, BButton, BFormInput, BFormSelect } from 'bootstrap-vue-next';

defineOptions({ layout: GuestLayout });

interface Establishment {
    id: number;
    name: string;
    logo?: string;
    image?: string;
    country: string;
    city: string;
    type: string;
    description?: string;
}

interface Country {
    id: number;
    name: string;
}

interface City {
    id: number;
    name: string;
}

interface StudyField {
    id: number;
    name: string;
}

interface EstablishmentType {
    id: number;
    name: string;
}

interface Props {
    establishments?: Establishment[];
    countries?: Country[];
    cities?: City[];
    studyFields?: StudyField[];
    establishmentTypes?: EstablishmentType[];
    stats?: {
        totalEstablishments: number;
        totalStudents: number;
        totalCountries: number;
        totalPrograms: number;
    };
}

const props = withDefaults(defineProps<Props>(), {
    establishments: () => [],
    countries: () => [],
    cities: () => [],
    studyFields: () => [],
    establishmentTypes: () => [],
    stats: () => ({
        totalEstablishments: 150,
        totalStudents: 2000,
        totalCountries: 15,
        totalPrograms: 500
    })
});

// Search form
const searchForm = ref({
    country: '',
    city: '',
    study_field: ''
});

// Popular establishments (données fictives pour l'instant)
const popularEstablishments = computed(() => {
    // Si on a des établissements du backend, on les utilise
    if (props.establishments && props.establishments.length > 0) {
        return props.establishments.slice(0, 6);
    }

    // Sinon, données fictives
    return [
        {
            id: 1,
            name: 'Université Cheikh Anta Diop',
            country: 'Sénégal',
            city: 'Dakar',
            type: 'Université publique',
            logo: '/images/establishments/ucad-logo.png',
            image: '/images/establishments/ucad.jpg',
            description: 'La plus grande université du Sénégal'
        },
        {
            id: 2,
            name: 'Université du Ghana',
            country: 'Ghana',
            city: 'Accra',
            type: 'Université publique',
            logo: '/images/establishments/ug-logo.png',
            image: '/images/establishments/ug.jpg',
            description: 'Excellence académique en Afrique de l\'Ouest'
        },
        {
            id: 3,
            name: 'Université Mohammed V',
            country: 'Maroc',
            city: 'Rabat',
            type: 'Université publique',
            logo: '/images/establishments/um5-logo.png',
            image: '/images/establishments/um5.jpg',
            description: 'Enseignement supérieur de qualité au Maroc'
        },
        {
            id: 4,
            name: 'Université de Lagos',
            country: 'Nigeria',
            city: 'Lagos',
            type: 'Université publique',
            logo: '/images/establishments/unilag-logo.png',
            image: '/images/establishments/unilag.jpg',
            description: 'Premier établissement nigérian'
        },
        {
            id: 5,
            name: 'Université de Nairobi',
            country: 'Kenya',
            city: 'Nairobi',
            type: 'Université publique',
            logo: '/images/establishments/uon-logo.png',
            image: '/images/establishments/uon.jpg',
            description: 'Leadership en Afrique de l\'Est'
        },
        {
            id: 6,
            name: 'Université du Cap',
            country: 'Afrique du Sud',
            city: 'Le Cap',
            type: 'Université publique',
            logo: '/images/establishments/uct-logo.png',
            image: '/images/establishments/uct.jpg',
            description: 'Recherche et innovation de pointe'
        }
    ];
});

// How it works steps
const howItWorksSteps = [
    {
        number: 1,
        icon: '🔍',
        title: 'Recherchez',
        description: 'Explorez notre base de données d\'établissements d\'enseignement supérieur pour étudier à l\'étranger'
    },
    {
        number: 2,
        icon: '🔄',
        title: 'Comparez',
        description: 'Comparez les programmes, frais de scolarité, et opportunités offertes par nos partenaires'
    },
    {
        number: 3,
        icon: '📝',
        title: 'Candidatez',
        description: 'Soumettez vos candidatures directement via notre plateforme sécurisée et suivez leur statut'
    },
    {
        number: 4,
        icon: '✅',
        title: 'Réussissez',
        description: 'Préparez votre parcours académique à l\'étranger avec notre accompagnement personnalisé'
    }
];

// Testimonials
const testimonials = [
    {
        name: 'Aminata Diallo',
        country: 'Sénégal',
        university: 'Université Cheikh Anta Diop',
        quote: 'Grâce à EtapSup, j\'ai pu découvrir et postuler facilement dans plusieurs universités à l\'étranger. Le processus était simple et transparent.',
        rating: 5,
        avatar: 'A'
    },
    {
        name: 'Kwame Asante',
        country: 'Ghana',
        university: 'Université du Ghana',
        quote: 'La plateforme m\'a permis de comparer différents programmes et de faire le meilleur choix pour étudier à l\'étranger.',
        rating: 5,
        avatar: 'K'
    },
    {
        name: 'Fatima El Mansouri',
        country: 'Maroc',
        university: 'Université Mohammed V',
        quote: 'Interface intuitive et support client excellent. Je recommande vivement EtapSup à tous les futurs étudiants.',
        rating: 5,
        avatar: 'F'
    }
];

// Features
const features = [
    {
        icon: '🛡️',
        title: 'Sécurisé et fiable',
        description: 'Vos données sont protégées et vos candidatures sécurisées avec les meilleurs standards'
    },
    {
        icon: '🏆',
        title: 'Établissements certifiés',
        description: 'Tous nos partenaires sont vérifiés et accrédités pour étudier à l\'étranger'
    },
    {
        icon: '🌍',
        title: 'Couverture internationale',
        description: 'Plus de 15 pays représentés pour votre projet d\'études à l\'étranger'
    },
    {
        icon: '📖',
        title: 'Suivi personnalisé',
        description: 'Accompagnement tout au long de votre parcours vers l\'étranger'
    }
];

// Handle search
const handleSearch = () => {
    console.log('Search:', searchForm.value);
    // La recherche sera gérée par Inertia.js plus tard
};
</script>

<template>
    <Head title="EtapSup - Trouvez votre établissement pour étudier à l'étranger">
        <meta head-key="description" name="description" content="Découvrez, postulez et suivez vos candidatures dans les meilleurs établissements pour étudier à l'étranger. Plus de 150 établissements partenaires." />
        <meta head-key="keywords" name="keywords" content="études à l'étranger, enseignement supérieur, université internationale, candidature, étudiant africain" />
    </Head>

    <div class="home-page">
        <!-- Navigation Bar -->
        <nav class="main-navigation">
            <BContainer>
                <div class="nav-content">
                    <Link href="/" class="nav-logo">
                        <span class="logo-text">EtapSup</span>
                    </Link>

                    <div class="nav-menu">
                        <Link href="/" class="nav-link">Accueil</Link>
                        <Link href="/establishments" class="nav-link">Établissements</Link>
                        <Link href="/events" class="nav-link">Événements</Link>
                    </div>

                    <div class="nav-actions">
                        <Link href="/login" class="btn-nav btn-nav-login">Connexion</Link>
                        <Link href="/register" class="btn-nav btn-nav-register">Inscription</Link>
                    </div>
                </div>
            </BContainer>
        </nav>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-overlay"></div>
            <BContainer class="hero-content">
                <BRow class="align-items-center min-vh-80">
                    <BCol lg="7" class="text-white position-relative z-index-2">
                        <div class="hero-badge">
                            <span class="badge-icon">⭐</span>
                            <span class="badge-text">Plus de {{ stats.totalEstablishments }}+ établissements partenaires</span>
                        </div>

                        <h1 class="hero-title">
                            Trouvez votre <span class="text-gradient">établissement idéal</span> pour étudier à l'étranger
                        </h1>

                        <p class="hero-subtitle">
                            Découvrez, comparez et postulez dans les meilleurs établissements d'enseignement supérieur
                            à travers le monde. Un processus simplifié pour réaliser votre rêve d'étudier à l'étranger.
                        </p>

                        <!-- Search Bar intégré dans le hero -->
                        <div class="hero-search-card">
                            <form @submit.prevent="handleSearch" class="search-form">
                                <BRow class="g-3">
                                    <BCol md="4">
                                        <BFormSelect
                                            v-model="searchForm.country"
                                            :options="[
                                                { value: '', text: 'Pays de destination' },
                                                ...countries.map(c => ({ value: c.id, text: c.name }))
                                            ]"
                                            class="form-input"
                                        />
                                    </BCol>
                                    <BCol md="3">
                                        <BFormInput
                                            v-model="searchForm.city"
                                            placeholder="Ville"
                                            class="form-input"
                                        />
                                    </BCol>
                                    <BCol md="3">
                                        <BFormSelect
                                            v-model="searchForm.study_field"
                                            :options="[
                                                { value: '', text: 'Domaine d\'études' },
                                                ...studyFields.map(f => ({ value: f.id, text: f.name }))
                                            ]"
                                            class="form-input"
                                        />
                                    </BCol>
                                    <BCol md="2">
                                        <BButton
                                            type="submit"
                                            variant="primary"
                                            size="lg"
                                            class="search-button w-100"
                                        >
                                            🔍
                                        </BButton>
                                    </BCol>
                                </BRow>
                            </form>
                        </div>

                        <!-- Quick Stats -->
                        <div class="hero-stats">
                            <div class="stat-item">
                                <div class="stat-number">{{ stats.totalStudents.toLocaleString() }}+</div>
                                <div class="stat-label">Étudiants accompagnés</div>
                            </div>
                            <div class="stat-divider"></div>
                            <div class="stat-item">
                                <div class="stat-number">{{ stats.totalEstablishments }}+</div>
                                <div class="stat-label">Établissements</div>
                            </div>
                            <div class="stat-divider"></div>
                            <div class="stat-item">
                                <div class="stat-number">{{ stats.totalCountries }}+</div>
                                <div class="stat-label">Pays</div>
                            </div>
                        </div>
                    </BCol>

                    <BCol lg="5" class="d-none d-lg-block">
                        <div class="hero-illustration">
                            <div class="illustration-placeholder">
                                <span class="illustration-icon">🎓</span>
                            </div>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>

        <!-- Popular Establishments Section -->
        <section class="establishments-section">
            <BContainer>
                <div class="section-header text-center">
                    <h2 class="section-title">Établissements populaires</h2>
                    <p class="section-subtitle">
                        Découvrez les établissements les plus recherchés pour étudier à l'étranger
                    </p>
                </div>

                <BRow class="establishments-grid">
                    <BCol
                        v-for="establishment in popularEstablishments"
                        :key="establishment.id"
                        md="6"
                        lg="4"
                        class="mb-4"
                    >
                        <div class="establishment-card">
                            <div class="establishment-image">
                                <div class="image-placeholder">
                                    <span class="placeholder-icon">🏛️</span>
                                </div>
                            </div>
                            <div class="establishment-content">
                                <h3 class="establishment-name">{{ establishment.name }}</h3>
                                <div class="establishment-meta">
                                    <span class="meta-item">
                                        <span class="meta-icon">📍</span>
                                        {{ establishment.city }}, {{ establishment.country }}
                                    </span>
                                    <span class="meta-item">
                                        <span class="meta-icon">🎓</span>
                                        {{ establishment.type }}
                                    </span>
                                </div>
                                <p class="establishment-description">
                                    {{ establishment.description || 'Excellence académique pour étudier à l\'étranger' }}
                                </p>
                                <BButton variant="outline-primary" size="sm" class="establishment-cta">
                                    Voir les détails →
                                </BButton>
                            </div>
                        </div>
                    </BCol>
                </BRow>

                <div class="text-center mt-5">
                    <Link
                        href="/establishments"
                        class="cta-button cta-primary"
                    >
                        Voir tous les établissements
                        <span class="button-arrow">→</span>
                    </Link>
                </div>
            </BContainer>
        </section>

        <!-- How It Works Section -->
        <section class="how-it-works-section">
            <BContainer>
                <div class="section-header text-center">
                    <h2 class="section-title">Comment ça marche ?</h2>
                    <p class="section-subtitle">
                        Un processus simple en 4 étapes pour réaliser votre rêve d'étudier à l'étranger
                    </p>
                </div>

                <BRow class="steps-grid">
                    <BCol
                        v-for="step in howItWorksSteps"
                        :key="step.number"
                        md="6"
                        lg="3"
                        class="mb-4"
                    >
                        <div class="step-card">
                            <div class="step-number">{{ step.number }}</div>
                            <div class="step-icon">{{ step.icon }}</div>
                            <h3 class="step-title">{{ step.title }}</h3>
                            <p class="step-description">{{ step.description }}</p>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>

        <!-- Testimonials Section -->
        <section class="testimonials-section">
            <BContainer>
                <div class="section-header text-center">
                    <h2 class="section-title">Ils nous ont fait confiance</h2>
                    <p class="section-subtitle">
                        Découvrez les témoignages de ceux qui étudient maintenant à l'étranger grâce à EtapSup
                    </p>
                </div>

                <BRow>
                    <BCol
                        v-for="testimonial in testimonials"
                        :key="testimonial.name"
                        md="4"
                        class="mb-4"
                    >
                        <div class="testimonial-card">
                            <div class="testimonial-rating">
                                <span v-for="i in testimonial.rating" :key="i" class="star">⭐</span>
                            </div>
                            <p class="testimonial-quote">"{{ testimonial.quote }}"</p>
                            <div class="testimonial-author">
                                <div class="author-avatar">{{ testimonial.avatar }}</div>
                                <div class="author-info">
                                    <div class="author-name">{{ testimonial.name }}</div>
                                    <div class="author-details">
                                        {{ testimonial.country }} → {{ testimonial.university }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <BContainer>
                <div class="section-header text-center">
                    <h2 class="section-title">Pourquoi choisir EtapSup ?</h2>
                    <p class="section-subtitle">
                        Une plateforme conçue pour simplifier votre parcours vers l'enseignement supérieur à l'étranger
                    </p>
                </div>

                <BRow class="features-grid">
                    <BCol
                        v-for="feature in features"
                        :key="feature.title"
                        md="6"
                        lg="3"
                        class="mb-4"
                    >
                        <div class="feature-card">
                            <div class="feature-icon">{{ feature.icon }}</div>
                            <h3 class="feature-title">{{ feature.title }}</h3>
                            <p class="feature-description">{{ feature.description }}</p>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>

        <!-- Footer CTA Section -->
        <section class="footer-cta">
            <BContainer>
                <div class="cta-content text-center">
                    <h2 class="cta-title">Prêt à commencer votre parcours ?</h2>
                    <p class="cta-text">
                        Rejoignez les milliers d'étudiants qui réalisent leur rêve d'étudier à l'étranger avec EtapSup
                    </p>
                    <div class="cta-buttons">
                        <Link href="/register" class="cta-button cta-primary">
                            Créer mon compte gratuit
                        </Link>
                        <Link href="/events" class="cta-button cta-secondary">
                            Participer à notre prochain événement
                        </Link>
                    </div>
                </div>
            </BContainer>
        </section>

        <!-- Footer -->
        <footer class="main-footer">
            <BContainer>
                <div class="footer-content text-center">
                    <div class="footer-logo">
                        <h4>EtapSup</h4>
                    </div>
                    <p class="footer-description">
                        Votre partenaire de confiance pour réussir vos études à l'étranger.
                        Nous accompagnons les étudiants dans leur parcours d'excellence académique.
                    </p>
                    <div class="footer-links">
                        <a href="/about">À propos</a>
                        <span>•</span>
                        <a href="/events">Événements</a>
                        <span>•</span>
                        <a href="/privacy">Confidentialité</a>
                        <span>•</span>
                        <a href="/contact">Contact</a>
                    </div>
                    <div class="footer-copyright">
                        <p>&copy; 2025 EtapSup. Tous droits réservés.</p>
                    </div>
                </div>
            </BContainer>
        </footer>
    </div>
</template>

<style scoped>
/* Refonte: Page d'accueil inspirée Diplomeo avec charte EtapSup */

/* Variables couleurs EtapSup */
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --accent-gradient: linear-gradient(45deg, #ed2939, #cc1f2d);
    --bg-light: #f8fafc;
    --text-dark: #1a202c;
    --text-gray: #64748b;
}

.home-page {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    overflow-x: hidden;
}

/* Main Navigation */
.main-navigation {
    background: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 0;
    z-index: 1000;
    padding: 1rem 0;
}

.nav-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.nav-logo {
    text-decoration: none;
}

.nav-logo .logo-text {
    font-size: 1.75rem;
    font-weight: 800;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.nav-menu {
    display: flex;
    gap: 2rem;
    align-items: center;
}

.nav-link {
    color: #374151;
    text-decoration: none;
    font-weight: 500;
    font-size: 1rem;
    transition: color 0.2s ease;
}

.nav-link:hover {
    color: #667eea;
}

.nav-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.btn-nav {
    padding: 0.625rem 1.5rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.btn-nav-login {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-nav-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    color: white;
}

.btn-nav-register {
    background: linear-gradient(45deg, #ed2939, #cc1f2d);
    color: white;
}

.btn-nav-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(237, 41, 57, 0.3);
    color: white;
}

@media (max-width: 991px) {
    .nav-menu {
        display: none;
    }

    .nav-actions {
        gap: 0.5rem;
    }

    .btn-nav {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    min-height: 85vh;
    display: flex;
    align-items: center;
    padding: 4rem 0;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.min-vh-80 {
    min-height: 80vh;
}

.z-index-2 {
    z-index: 2;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 0.75rem 1.25rem;
    border-radius: 50px;
    margin-bottom: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.badge-icon {
    font-size: 1rem;
}

.badge-text {
    color: white;
    font-size: 0.875rem;
    font-weight: 600;
}

.hero-title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 1.5rem;
    color: white;
}

.text-gradient {
    background: linear-gradient(45deg, #ffd700, #ffed4e);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: 1.25rem;
    line-height: 1.6;
    margin-bottom: 2.5rem;
    opacity: 0.95;
    color: white;
    max-width: 600px;
}

/* Hero Search Card */
.hero-search-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    margin-bottom: 2.5rem;
}

.form-input {
    padding: 0.875rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 1rem;
    transition: all 0.2s ease;
}

.form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.search-button {
    background: linear-gradient(45deg, #ed2939, #cc1f2d);
    border: none;
    padding: 0.875rem;
    font-size: 1.25rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.search-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(237, 41, 57, 0.3);
}

/* Hero Stats */
.hero-stats {
    display: flex;
    align-items: center;
    gap: 2rem;
    flex-wrap: wrap;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 1.75rem;
    font-weight: 700;
    color: #ffd700;
}

.stat-label {
    font-size: 0.875rem;
    color: white;
    opacity: 0.9;
}

.stat-divider {
    width: 1px;
    height: 40px;
    background: rgba(255, 255, 255, 0.3);
}

/* Hero Illustration */
.hero-illustration {
    position: relative;
}

.illustration-placeholder {
    width: 100%;
    aspect-ratio: 1;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.illustration-icon {
    font-size: 8rem;
    opacity: 0.5;
}

/* Sections */
.establishments-section,
.testimonials-section {
    padding: 5rem 0;
    background: #f8fafc;
}

.how-it-works-section,
.features-section {
    padding: 5rem 0;
    background: white;
}

.section-header {
    margin-bottom: 3rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 1rem;
}

.section-subtitle {
    font-size: 1.1rem;
    color: #64748b;
    max-width: 700px;
    margin: 0 auto;
}

/* Establishment Cards */
.establishment-card {
    background: white;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.establishment-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.establishment-image {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.image-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.placeholder-icon {
    font-size: 4rem;
    opacity: 0.5;
}

.establishment-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.establishment-name {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 0.75rem;
}

.establishment-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.meta-item {
    font-size: 0.875rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.meta-icon {
    font-size: 1rem;
}

.establishment-description {
    font-size: 0.875rem;
    color: #64748b;
    line-height: 1.6;
    margin-bottom: 1rem;
    flex: 1;
}

.establishment-cta {
    background: linear-gradient(45deg, #ed2939, #cc1f2d);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.establishment-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(237, 41, 57, 0.3);
}

/* Steps (How it works) */
.step-card {
    text-align: center;
    padding: 2rem 1rem;
    position: relative;
}

.step-number {
    position: absolute;
    top: 0;
    right: 1rem;
    width: 40px;
    height: 40px;
    background: linear-gradient(45deg, #ed2939, #cc1f2d);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.25rem;
}

.step-icon {
    font-size: 3.5rem;
    margin-bottom: 1rem;
}

.step-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 0.75rem;
}

.step-description {
    font-size: 0.875rem;
    color: #64748b;
    line-height: 1.6;
}

/* Testimonials */
.testimonial-card {
    background: white;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    height: 100%;
    transition: transform 0.3s ease;
}

.testimonial-card:hover {
    transform: translateY(-5px);
}

.testimonial-rating {
    margin-bottom: 1rem;
}

.star {
    font-size: 1rem;
}

.testimonial-quote {
    font-style: italic;
    line-height: 1.6;
    margin-bottom: 1.5rem;
    color: #374151;
    font-size: 0.95rem;
}

.testimonial-author {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.author-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(45deg, #ed2939, #cc1f2d);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1.25rem;
}

.author-name {
    font-weight: 600;
    color: #1a202c;
}

.author-details {
    font-size: 0.875rem;
    color: #64748b;
}

/* Features */
.feature-card {
    text-align: center;
    padding: 2rem 1rem;
}

.feature-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.feature-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 0.75rem;
}

.feature-description {
    color: #64748b;
    line-height: 1.6;
    font-size: 0.875rem;
}

/* Footer CTA */
.footer-cta {
    padding: 5rem 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.cta-content {
    max-width: 700px;
    margin: 0 auto;
}

.cta-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.cta-text {
    font-size: 1.1rem;
    margin-bottom: 2.5rem;
    opacity: 0.95;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.cta-button {
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 50px;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.cta-primary {
    background: linear-gradient(45deg, #ed2939, #cc1f2d);
    color: white;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.cta-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
    color: white;
}

.cta-secondary {
    background: white;
    color: #667eea;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.cta-secondary:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    color: #667eea;
}

.button-arrow {
    transition: transform 0.3s ease;
}

.cta-button:hover .button-arrow {
    transform: translateX(5px);
}

/* Footer */
.main-footer {
    background: #1a202c;
    color: white;
    padding: 3rem 0 1.5rem;
}

.footer-content {
    max-width: 600px;
    margin: 0 auto;
}

.footer-logo h4 {
    font-size: 2rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
}

.footer-description {
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 2rem;
    opacity: 0.8;
}

.footer-links {
    margin-bottom: 2rem;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.footer-links a {
    color: white;
    text-decoration: none;
    font-size: 0.875rem;
    transition: color 0.2s ease;
}

.footer-links a:hover {
    color: #ed2939;
}

.footer-links span {
    opacity: 0.5;
}

.footer-copyright p {
    font-size: 0.875rem;
    opacity: 0.6;
    margin: 0;
}

/* Responsive */
@media (max-width: 991px) {
    .hero-title {
        font-size: 2.5rem;
    }

    .section-title {
        font-size: 2rem;
    }

    .stat-divider {
        display: none;
    }

    .hero-stats {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .hero-section {
        padding: 3rem 0;
        min-height: auto;
    }

    .hero-search-card {
        padding: 1rem;
    }

    .cta-buttons {
        flex-direction: column;
    }

    .cta-button {
        width: 100%;
        justify-content: center;
    }

    .footer-links {
        flex-direction: column;
        gap: 0.5rem;
    }

    .footer-links span {
        display: none;
    }
}
</style>
