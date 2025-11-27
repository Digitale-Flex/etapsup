<script setup lang="ts">
// Refonte: Page Établissements style Diplomeo avec charte EtapSup (FRONTEND UNIQUEMENT)
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { BContainer, BRow, BCol, BFormInput, BFormSelect, BButton } from 'bootstrap-vue-next';

defineOptions({ layout: GuestLayout });

// Données fictives pour l'instant
const establishments = ref([
    {
        id: 1,
        name: 'Université Cheikh Anta Diop',
        country: 'Sénégal',
        city: 'Dakar',
        type: 'Université publique',
        description: 'La plus grande université du Sénégal offrant des formations de qualité pour étudier à l\'étranger',
        rating: 4.5
    },
    {
        id: 2,
        name: 'Université du Ghana',
        country: 'Ghana',
        city: 'Accra',
        type: 'Université publique',
        description: 'Excellence académique en Afrique de l\'Ouest pour vos études à l\'étranger',
        rating: 4.7
    },
    {
        id: 3,
        name: 'Université Mohammed V',
        country: 'Maroc',
        city: 'Rabat',
        type: 'Université publique',
        description: 'Enseignement supérieur de qualité au Maroc pour étudier à l\'étranger',
        rating: 4.6
    },
    {
        id: 4,
        name: 'Université de Lagos',
        country: 'Nigeria',
        city: 'Lagos',
        type: 'Université publique',
        description: 'Premier établissement nigérian pour vos projets d\'études à l\'étranger',
        rating: 4.4
    },
    {
        id: 5,
        name: 'Université de Nairobi',
        country: 'Kenya',
        city: 'Nairobi',
        type: 'Université publique',
        description: 'Leadership en Afrique de l\'Est pour étudier à l\'étranger',
        rating: 4.8
    },
    {
        id: 6,
        name: 'Université du Cap',
        country: 'Afrique du Sud',
        city: 'Le Cap',
        type: 'Université publique',
        description: 'Recherche et innovation de pointe pour vos études à l\'étranger',
        rating: 4.9
    }
]);

const filters = ref({
    country: '',
    city: '',
    type: ''
});
</script>

<template>
    <Head title="Établissements - EtapSup">
        <meta name="description" content="Découvrez nos établissements partenaires pour étudier à l'étranger. Plus de 150 universités et écoles dans le monde." />
    </Head>

    <div class="establishments-page">
        <!-- Navigation -->
        <nav class="main-navigation">
            <BContainer>
                <div class="nav-content">
                    <Link href="/" class="nav-logo">
                        <span class="logo-text">EtapSup</span>
                    </Link>

                    <div class="nav-menu">
                        <Link href="/" class="nav-link">Accueil</Link>
                        <Link href="/establishments" class="nav-link active">Établissements</Link>
                        <Link href="/events" class="nav-link">Événements</Link>
                    </div>

                    <div class="nav-actions">
                        <Link href="/login" class="btn-nav btn-nav-login">Connexion</Link>
                        <Link href="/register" class="btn-nav btn-nav-register">Inscription</Link>
                    </div>
                </div>
            </BContainer>
        </nav>

        <!-- Page Header -->
        <div class="page-header">
            <BContainer>
                <h1 class="page-title">Trouvez votre établissement idéal</h1>
                <p class="page-subtitle">Plus de 150 établissements partenaires pour réaliser votre rêve d'étudier à l'étranger</p>
            </BContainer>
        </div>

        <!-- Main Content -->
        <BContainer class="main-content py-5">
            <BRow>
                <!-- Filters Sidebar -->
                <BCol lg="3" class="mb-4">
                    <div class="filters-sidebar">
                        <h3 class="filters-title">Filtres</h3>

                        <div class="filter-group">
                            <label class="filter-label">Pays</label>
                            <BFormSelect v-model="filters.country" class="filter-input">
                                <option value="">Tous les pays</option>
                                <option value="senegal">Sénégal</option>
                                <option value="ghana">Ghana</option>
                                <option value="maroc">Maroc</option>
                                <option value="nigeria">Nigeria</option>
                                <option value="kenya">Kenya</option>
                                <option value="south-africa">Afrique du Sud</option>
                            </BFormSelect>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">Ville</label>
                            <BFormInput v-model="filters.city" placeholder="Rechercher une ville" class="filter-input" />
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">Type d'établissement</label>
                            <BFormSelect v-model="filters.type" class="filter-input">
                                <option value="">Tous les types</option>
                                <option value="public">Université publique</option>
                                <option value="private">Université privée</option>
                                <option value="school">École spécialisée</option>
                            </BFormSelect>
                        </div>

                        <BButton variant="primary" class="w-100 filter-button">
                            Appliquer les filtres
                        </BButton>
                    </div>
                </BCol>

                <!-- Establishments Grid -->
                <BCol lg="9">
                    <div class="results-header">
                        <h2 class="results-count">{{ establishments.length }} établissements trouvés</h2>
                    </div>

                    <BRow>
                        <BCol
                            v-for="establishment in establishments"
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
                                    <div class="establishment-rating">
                                        <span class="rating-icon">⭐</span>
                                        <span class="rating-value">{{ establishment.rating }}</span>
                                    </div>
                                </div>

                                <div class="establishment-content">
                                    <h3 class="establishment-name">{{ establishment.name }}</h3>

                                    <div class="establishment-meta">
                                        <div class="meta-item">
                                            <span class="meta-icon">📍</span>
                                            <span>{{ establishment.city }}, {{ establishment.country }}</span>
                                        </div>
                                        <div class="meta-item">
                                            <span class="meta-icon">🎓</span>
                                            <span>{{ establishment.type }}</span>
                                        </div>
                                    </div>

                                    <p class="establishment-description">
                                        {{ establishment.description }}
                                    </p>

                                    <BButton variant="primary" class="w-100 establishment-cta">
                                        Voir les détails
                                    </BButton>
                                </div>
                            </div>
                        </BCol>
                    </BRow>
                </BCol>
            </BRow>
        </BContainer>

        <!-- Footer -->
        <footer class="main-footer">
            <BContainer>
                <div class="footer-content text-center">
                    <p class="mb-0">&copy; 2025 EtapSup. Tous droits réservés.</p>
                </div>
            </BContainer>
        </footer>
    </div>
</template>

<style scoped>
.establishments-page {
    background: #f8fafc;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* Navigation (same as home) */
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

.logo-text {
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
}

.nav-link {
    color: #374151;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.nav-link:hover,
.nav-link.active {
    color: #667eea;
}

.nav-actions {
    display: flex;
    gap: 1rem;
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

/* Page Header */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 3rem 0;
    color: white;
    text-align: center;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
}

/* Main Content */
.main-content {
    flex: 1;
}

/* Filters Sidebar */
.filters-sidebar {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 100px;
}

.filters-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: #1a202c;
}

.filter-group {
    margin-bottom: 1.5rem;
}

.filter-label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #374151;
    font-size: 0.9rem;
}

.filter-input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.95rem;
    transition: all 0.2s ease;
}

.filter-input:focus {
    outline: none;
    border-color: #667eea;
}

.filter-button {
    background: linear-gradient(45deg, #ed2939, #cc1f2d);
    border: none;
    padding: 0.875rem;
    font-weight: 600;
    border-radius: 0.5rem;
}

/* Results */
.results-header {
    margin-bottom: 2rem;
}

.results-count {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a202c;
}

/* Establishment Cards */
.establishment-card {
    background: white;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.establishment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.establishment-image {
    position: relative;
    height: 180px;
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

.establishment-rating {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: white;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-weight: 600;
}

.rating-icon {
    font-size: 1rem;
}

.rating-value {
    color: #1a202c;
}

.establishment-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.establishment-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 1rem;
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
    border: none;
    padding: 0.75rem;
    font-weight: 600;
    border-radius: 0.5rem;
}

/* Footer */
.main-footer {
    background: #1a202c;
    color: white;
    padding: 2rem 0;
    margin-top: auto;
}

.footer-content p {
    color: #94a3b8;
    font-size: 0.875rem;
}

@media (max-width: 991px) {
    .nav-menu {
        display: none;
    }

    .filters-sidebar {
        position: static;
    }

    .page-title {
        font-size: 2rem;
    }
}
</style>
