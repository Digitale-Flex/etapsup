<script setup lang="ts">
// Refonte: Page Établissements style Diplomeo avec charte EtapSup
// UI-Fix-2.5: Connexion avec l'API backend via Inertia props
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import UserMenu from '@/Layouts/Partials/UserMenu.vue';
import { BContainer, BRow, BCol, BFormInput, BFormSelect, BButton } from 'bootstrap-vue-next';

defineOptions({ layout: GuestLayout });

// Props reçues du contrôleur EstablishmentController
interface Country {
    id: string;
    name: string;
}

interface City {
    id: string;
    name: string;
    country_id: string;
}

interface Filter {
    id: string;
    label: string;
}

interface Establishment {
    id: string;
    title: string;
    slug: string;
    description: string;
    city?: {
        name: string;
        country?: {
            name: string;
        };
    };
    propertyType?: {
        label: string;
    };
    category?: {
        label: string;
    };
    ratings?: {
        average: number;
        count: number;
    };
}

const props = defineProps<{
    establishments: {
        data: Establishment[];
        links: any[];
        meta: any;
    };
    filters: {
        countries: Country[];
        cities: City[];
        establishment_types: Filter[];
        training_types: Filter[];
        career_fields: Filter[];
        degree_levels: Filter[];
    };
    currentFilters: {
        country_id?: string;
        city_id?: string;
        establishment_type_id?: string;
        training_type_id?: string;
        career_field_id?: string;
        degree_level_id?: string;
        search?: string;
    };
}>();

// Filtres locaux
const searchFilters = ref({
    country_id: props.currentFilters.country_id || '',
    city_id: props.currentFilters.city_id || '',
    career_field_id: props.currentFilters.career_field_id || '',
    establishment_type_id: props.currentFilters.establishment_type_id || '',
    search: props.currentFilters.search || ''
});

// Filtrer les villes selon le pays sélectionné
const filteredCities = computed(() => {
    if (!searchFilters.value.country_id) return [];
    return props.filters.cities.filter(city => city.country_id == searchFilters.value.country_id);
});

// Réinitialiser city_id quand le pays change
const onCountryChange = () => {
    searchFilters.value.city_id = '';
};

// Appliquer les filtres via Inertia
const applyFilters = () => {
    router.get(route('establishments.index'), searchFilters.value, {
        preserveState: true,
        preserveScroll: true
    });
};
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
                        <!-- Si utilisateur connecté : afficher icône utilisateur -->
                        <template v-if="$page.props.auth?.user">
                            <div class="user-menu-wrapper">
                                <UserMenu />
                            </div>
                        </template>

                        <!-- Si utilisateur NON connecté : afficher boutons -->
                        <template v-else>
                            <Link href="/login" class="btn-nav btn-nav-login">Connexion</Link>
                            <Link href="/register" class="btn-nav btn-nav-register">Inscription</Link>
                        </template>
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

                        <!-- UI-Fix-2.5: Recherche par nom -->
                        <div class="filter-group">
                            <label class="filter-label">Recherche</label>
                            <BFormInput v-model="searchFilters.search" placeholder="Nom de l'établissement..." class="filter-input" @keyup.enter="applyFilters" />
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">Pays</label>
                            <BFormSelect v-model="searchFilters.country_id" class="filter-input" @change="onCountryChange">
                                <option value="">Tous les pays</option>
                                <option v-for="country in filters.countries" :key="country.id" :value="country.id">
                                    {{ country.name }}
                                </option>
                            </BFormSelect>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">Ville</label>
                            <BFormSelect v-model="searchFilters.city_id" class="filter-input" :disabled="!searchFilters.country_id">
                                <option value="">Toutes les villes</option>
                                <option v-for="city in filteredCities" :key="city.id" :value="city.id">
                                    {{ city.name }}
                                </option>
                            </BFormSelect>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">Type d'établissement</label>
                            <BFormSelect v-model="searchFilters.establishment_type_id" class="filter-input">
                                <option value="">Tous les types</option>
                                <option v-for="type in filters.establishment_types" :key="type.id" :value="type.id">
                                    {{ type.label }}
                                </option>
                            </BFormSelect>
                        </div>

                        <!-- UI-Fix-2.5: Filtre Domaine d'études (career_fields) -->
                        <div class="filter-group">
                            <label class="filter-label">Domaine d'études</label>
                            <BFormSelect v-model="searchFilters.career_field_id" class="filter-input">
                                <option value="">Tous les domaines</option>
                                <option v-for="field in filters.career_fields" :key="field.id" :value="field.id">
                                    {{ field.label }}
                                </option>
                            </BFormSelect>
                        </div>

                        <BButton variant="primary" class="w-100 filter-button" @click="applyFilters">
                            Appliquer les filtres
                        </BButton>
                    </div>
                </BCol>

                <!-- Establishments Grid -->
                <BCol lg="9">
                    <div class="results-header">
                        <h2 class="results-count" v-if="establishments?.meta?.total !== undefined">
                            {{ establishments.meta.total }} établissement{{ establishments.meta.total > 1 ? 's' : '' }} trouvé{{ establishments.meta.total > 1 ? 's' : '' }}
                        </h2>
                    </div>

                    <!-- UI-Fix-2.5: Affichage réel des données via props -->
                    <BRow v-if="establishments?.data?.length > 0">
                        <BCol
                            v-for="establishment in establishments.data"
                            :key="establishment.id"
                            md="6"
                            lg="4"
                            class="mb-4"
                        >
                            <div class="establishment-card">
                                <div class="establishment-image">
                                    <img v-if="establishment.logo" :src="establishment.logo" :alt="establishment.title" class="establishment-img" />
                                    <div v-else class="image-placeholder">
                                        <span class="placeholder-icon">🏛️</span>
                                    </div>
                                    <div v-if="establishment.ratings?.average" class="establishment-rating">
                                        <span class="rating-icon">⭐</span>
                                        <span class="rating-value">{{ establishment.ratings.average.toFixed(1) }}</span>
                                    </div>
                                </div>

                                <div class="establishment-content">
                                    <h3 class="establishment-name">{{ establishment.title }}</h3>

                                    <div class="establishment-meta">
                                        <div class="meta-item" v-if="establishment.city">
                                            <span class="meta-icon">📍</span>
                                            <span>{{ establishment.city.name }}{{ establishment.city.country ? ', ' + establishment.city.country.name : '' }}</span>
                                        </div>
                                        <div class="meta-item" v-if="establishment.propertyType">
                                            <span class="meta-icon">🎓</span>
                                            <span>{{ establishment.propertyType.label }}</span>
                                        </div>
                                        <div class="meta-item" v-if="establishment.category">
                                            <span class="meta-icon">📚</span>
                                            <span>{{ establishment.category.label }}</span>
                                        </div>
                                    </div>

                                    <p class="establishment-description">
                                        {{ establishment.description || 'Découvrez cet établissement d\'enseignement supérieur en Afrique.' }}
                                    </p>

                                    <Link :href="`/establishments/${establishment.slug}`" class="btn btn-primary w-100 establishment-cta">
                                        Voir les détails
                                    </Link>
                                </div>
                            </div>
                        </BCol>
                    </BRow>

                    <!-- Message si aucun établissement -->
                    <div v-else class="no-results text-center py-5">
                        <p class="text-muted">Aucun établissement ne correspond à vos critères de recherche.</p>
                        <BButton variant="outline-primary" @click="searchFilters = { country_id: '', career_field_id: '', establishment_type_id: '', search: '' }; applyFilters()">
                            Réinitialiser les filtres
                        </BButton>
                    </div>
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

/* UI-Fix-2.4: Logo charte EtapSup */
.logo-text {
    font-size: 1.75rem;
    font-weight: 800;
    background: #1e3a8a;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    color: #1e3a8a;
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

/* UI-Fix-2.4: Navigation active EtapSup */
.nav-link:hover,
.nav-link.active {
    color: #1e3a8a;
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

/* UI-Fix-2.4: Boutons navigation charte EtapSup */
.btn-nav-login {
    background: #1e3a8a;
    color: white;
}

.btn-nav-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(30, 58, 138, 0.3);
    color: white;
    background: #2b4a9e;
}

.btn-nav-register {
    background: #dc2626;
    color: white;
}

.btn-nav-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 38, 38, 0.3);
    color: white;
    background: #b91c1c;
}

/* UI-Fix-2.4: Header charte EtapSup */
.page-header {
    background: #1e3a8a;
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

/* UI-Fix-2.4: Focus filtres EtapSup */
.filter-input:focus {
    outline: none;
    border-color: #1e3a8a;
}

.filter-button {
    background: #dc2626;
    border: none;
    padding: 0.875rem;
    font-weight: 600;
    border-radius: 0.5rem;
}

.filter-button:hover {
    background: #b91c1c;
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
    overflow: hidden;
}

.establishment-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* UI-Fix-2.4: Image placeholder charte EtapSup */
.image-placeholder {
    width: 100%;
    height: 100%;
    background: #1e3a8a;
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

/* UI-Fix-2.4: Bouton CTA charte EtapSup */
.establishment-cta {
    background: #dc2626;
    border: none;
    padding: 0.75rem;
    font-weight: 600;
    border-radius: 0.5rem;
}

.establishment-cta:hover {
    background: #b91c1c;
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
