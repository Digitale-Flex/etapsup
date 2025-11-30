<script setup lang="ts">
// Refonte: Dashboard Étudiant EtatSup
// Sprint1 Update: Feature 1.1.1 — Espace Étudiant (Connexion & Profil)
// Quick Win: Logo EtapSup
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { BContainer, BRow, BCol, BCard, BCardBody, BCardTitle, BCardText, BBadge, BButton, BProgress } from 'bootstrap-vue-next';
import { computed, ref } from 'vue';
import UserMenu from '@/Layouts/Partials/UserMenu.vue';
import Logo from '@/Components/Logo.vue';
// Refonte Story 1.1.3 - Import EstablishmentCard
import EstablishmentCard from '@/Pages/Applications/Partials/EstablishmentCard.vue';
import axios from 'axios';
import type { User } from '@/Types';

const page = usePage();
const user = computed(() => page.props.auth?.user as User | undefined);

// Sprint1 Update: Upload photo de profil
const photoInput = ref<HTMLInputElement | null>(null);
const isUploadingPhoto = ref(false);

const selectPhoto = () => {
    photoInput.value?.click();
};

const uploadPhoto = async (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (!file) return;

    // Validation côté client
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    const maxSize = 2 * 1024 * 1024; // 2 Mo

    if (!validTypes.includes(file.type)) {
        alert('Format non supporté. Veuillez sélectionner une image JPG ou PNG.');
        return;
    }

    if (file.size > maxSize) {
        alert('La taille maximale est de 2 Mo.');
        return;
    }

    isUploadingPhoto.value = true;

    try {
        // Obtenir le token CSRF
        await axios.get('/sanctum/csrf-cookie');

        // Upload via API
        const formData = new FormData();
        formData.append('photo', file);

        const response = await axios.post('/api/v1/profile/photo', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        if (response.data.success) {
            // Recharger la page pour afficher la nouvelle photo
            router.reload({ only: ['auth'] });
        }
    } catch (error: any) {
        console.error('Erreur upload photo:', error);
        alert(error.response?.data?.message || 'Erreur lors de l\'upload de la photo');
    } finally {
        isUploadingPhoto.value = false;
        if (target) target.value = '';
    }
};

// Sprint1 Update: Feature 1.2.1 — Livret explicatif
const showLivretModal = ref(false);
const livretUrl = ref<string | null>(null);
const isLoadingLivret = ref(false);

const openLivret = async () => {
    isLoadingLivret.value = true;

    try {
        await axios.get('/sanctum/csrf-cookie');

        const response = await axios.get('/api/v1/settings/livret');

        if (response.data.success && response.data.livret_available) {
            livretUrl.value = response.data.livret_url;
            showLivretModal.value = true;
        } else {
            alert('Le livret d\'arrivée n\'est pas encore disponible. Veuillez réessayer plus tard.');
        }
    } catch (error: any) {
        console.error('Erreur chargement livret:', error);
        alert('Impossible de charger le livret pour le moment.');
    } finally {
        isLoadingLivret.value = false;
    }
};

const closeLivretModal = () => {
    showLivretModal.value = false;
};

// Props reçues du backend
interface Props {
    applications?: any[];
    randomEstablishments?: any[];
    profileCompletion?: number;
    documents?: any[]; // Sprint1 Update
    payments?: any[];  // Sprint1 Update
}

const props = withDefaults(defineProps<Props>(), {
    applications: () => [],
    randomEstablishments: () => [],
    profileCompletion: 0,
    documents: () => [], // Sprint1 Update
    payments: () => []   // Sprint1 Update
});

// Refonte Story 1.1.1
const getStatusVariant = (status: string) => {
    switch (status) {
        case 'draft': return 'secondary';
        case 'accepted': return 'success';
        case 'pending': return 'warning';
        case 'submitted': return 'primary';
        case 'in_review': return 'info';
        case 'rejected': return 'danger';
        default: return 'secondary';
    }
};

// Refonte Story 1.1.1 - Mapping statuts en français
const getStatusLabel = (status: string) => {
    switch (status) {
        case 'draft': return 'Brouillon';
        case 'submitted': return 'Soumise';
        case 'in_review': return 'En cours d\'examen';
        case 'accepted': return 'Acceptée';
        case 'rejected': return 'Refusée';
        case 'pending': return 'En attente';
        case 'pending_payment': return 'En attente de paiement';
        default: return status;
    }
};
</script>

<template>
    <Head title="Tableau de bord - EtatSup" />

    <!-- Header with UserMenu -->
    <div class="dashboard-header">
        <BContainer fluid="xl">
            <div class="d-flex justify-content-between align-items-center py-3">
                <Link href="/" class="logo-link">
                    <Logo size="md" variant="gradient" />
                </Link>
                <UserMenu />
            </div>
        </BContainer>
    </div>

    <div class="dashboard-page py-5">
            <BContainer fluid="xl">
                <!-- Welcome Section -->
                <div class="welcome-section mb-5">
                    <BRow>
                        <BCol>
                            <div class="welcome-card">
                                <h1 class="welcome-title">
                                    👋 Bonjour {{ user?.name || 'Étudiant' }} !
                                </h1>
                                <p class="welcome-subtitle">
                                    Bienvenue sur votre espace EtapSup. Suivez vos candidatures, découvrez de nouveaux établissements et réalisez votre rêve d'étudier à l'étranger.
                                </p>
                            </div>
                        </BCol>
                    </BRow>
                </div>

                <!-- Main Content -->
                <BRow class="g-4">
                    <!-- Left Column -->
                    <BCol lg="8">
                        <!-- My Applications -->
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h3 class="section-title">📝 Mes candidatures</h3>
                                <Link :href="route('establishments.index')" class="btn-view-all">
                                    Nouvelle candidature →
                                </Link>
                            </div>
                            <div v-if="props.applications.length === 0" class="empty-state">
                                <p class="text-muted">Vous n'avez pas encore soumis de candidature.</p>
                            </div>
                            <div v-else class="applications-list">
                                <!-- Refonte Story 1.1.2 - Liens cliquables vers /candidatures/{id} -->
                                <Link
                                    v-for="app in props.applications"
                                    :key="app.id"
                                    :href="`/candidatures/${app.id}`"
                                    class="application-item"
                                >
                                    <div class="application-info">
                                        <h4 class="application-title">{{ app.establishment }}</h4>
                                        <p class="application-program">{{ app.program }}</p>
                                        <div class="application-meta">
                                            <span class="meta-item">📍 {{ app.country }}</span>
                                            <span class="meta-item">📅 {{ app.date }}</span>
                                        </div>
                                    </div>
                                    <div class="application-status">
                                        <BBadge :variant="getStatusVariant(app.status)" class="status-badge">
                                            {{ getStatusLabel(app.status) }}
                                        </BBadge>
                                    </div>
                                </Link>
                            </div>
                        </div>


                        <!-- Random Establishments - Refonte Story 1.1.3 -->
                        <div class="section-card mt-4">
                            <div class="section-header">
                                <h3 class="section-title">🔥 Établissements recommandés</h3>
                                <Link :href="route('establishments.index')" class="btn-view-all">
                                    Voir tous →
                                </Link>
                            </div>
                            <BRow class="g-3">
                                <!-- Refonte Story 1.1.3 - Utilisation EstablishmentCard réel -->
                                <BCol
                                    v-for="establishment in props.randomEstablishments"
                                    :key="establishment.id"
                                    md="4"
                                >
                                    <EstablishmentCard :establishment="establishment" />
                                </BCol>
                            </BRow>
                        </div>
                    </BCol>

                    <!-- Right Column -->
                    <BCol lg="4">
                        <!-- Profile Completion -->
                        <div class="section-card mb-4">
                            <h3 class="section-title mb-3">📊 Mon Profil</h3>

                            <!-- Sprint1 Update: Photo de profil avec bouton upload -->
                            <div class="profile-photo-section mb-3">
                                <div class="profile-photo-wrapper">
                                    <div class="profile-photo">
                                        <img
                                            :src="user?.photo_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user?.name || 'U') + '&background=667eea&color=fff&size=128'"
                                            :alt="user?.name || 'Photo de profil'"
                                            class="profile-photo-img"
                                        />
                                        <button
                                            @click="selectPhoto"
                                            :disabled="isUploadingPhoto"
                                            class="profile-photo-btn"
                                            type="button"
                                        >
                                            📷
                                        </button>
                                    </div>
                                    <input
                                        ref="photoInput"
                                        type="file"
                                        accept="image/jpeg,image/jpg,image/png"
                                        @change="uploadPhoto"
                                        style="display: none;"
                                    />
                                </div>
                                <div class="profile-info">
                                    <p class="profile-name">{{ user?.name || 'Étudiant' }}</p>
                                    <p class="profile-email">{{ user?.email }}</p>
                                </div>
                            </div>

                            <div class="profile-completion">
                                <div class="completion-header">
                                    <span class="completion-label">Complétion du profil</span>
                                    <span class="completion-percent">{{ props.profileCompletion }}%</span>
                                </div>
                                <BProgress :value="props.profileCompletion" variant="primary" class="mt-2 mb-3" />
                                <Link :href="route('dashboard.profile')" class="btn-complete-profile">
                                    Compléter mon profil
                                </Link>
                            </div>
                        </div>

                        <!-- Sprint1 Update: Feature 1.1.1 — Mon Dossier (Documents) -->
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h3 class="section-title">📂 Mon dossier</h3>
                                <span class="btn-view-all" style="cursor: pointer;">Voir tout →</span>
                            </div>
                            <div v-if="props.documents.length === 0" class="empty-state">
                                <p class="text-muted">Aucun document téléversé</p>
                            </div>
                            <div v-else class="documents-list">
                                <div
                                    v-for="doc in props.documents"
                                    :key="doc.id"
                                    class="document-item"
                                >
                                    <div class="document-info">
                                        <div class="document-icon">📄</div>
                                        <div>
                                            <p class="document-name">{{ doc.type }}</p>
                                            <p class="document-meta">{{ doc.uploaded_at }}</p>
                                        </div>
                                    </div>
                                    <div class="document-status">
                                        <BBadge :variant="doc.verified ? 'success' : 'secondary'" class="status-badge-sm">
                                            {{ doc.verified ? '✓ Vérifié' : 'En attente' }}
                                        </BBadge>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sprint1 Update: Feature 1.1.1 — Mes Factures (Paiements Stripe) -->
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h3 class="section-title">💳 Mes factures</h3>
                                <span class="btn-view-all" style="cursor: pointer;">Voir tout →</span>
                            </div>
                            <div v-if="props.payments.length === 0" class="empty-state">
                                <p class="text-muted">Aucun paiement effectué</p>
                            </div>
                            <div v-else class="payments-list">
                                <div
                                    v-for="payment in props.payments"
                                    :key="payment.id"
                                    class="payment-item"
                                >
                                    <div class="payment-info">
                                        <p class="payment-description">{{ payment.description }}</p>
                                        <p class="payment-meta">{{ payment.created_at }} • {{ payment.establishment }}</p>
                                    </div>
                                    <div class="payment-amount">
                                        <p class="amount">{{ payment.amount }} {{ payment.currency }}</p>
                                        <BBadge :variant="payment.status === 'succeeded' ? 'success' : 'warning'" class="status-badge-sm">
                                            {{ payment.status_label }}
                                        </BBadge>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aide & Support -->
                        <div class="section-card">
                            <h3 class="section-title mb-3">💬 Besoin d'aide ?</h3>
                            <p class="text-muted mb-3">Notre équipe est là pour vous accompagner dans votre projet d'études.</p>
                            <a href="mailto:contact@etapsup.com" class="btn btn-outline-primary w-100">
                                <i class="bi bi-envelope me-2"></i>
                                Contacter un conseiller
                            </a>
                        </div>
                    </BCol>
                </BRow>

                <!-- Quick Actions -->
                <div class="quick-actions mt-5">
                    <h3 class="section-title mb-4">🚀 Actions rapides</h3>
                    <BRow class="g-3">
                        <BCol md="3" sm="6">
                            <Link :href="route('establishments.index')" class="action-card">
                                <div class="action-icon">🔍</div>
                                <div class="action-label">Rechercher<br/>un établissement</div>
                            </Link>
                        </BCol>
                        <BCol md="3" sm="6">
                            <Link :href="route('events.index')" class="action-card">
                                <div class="action-icon">📅</div>
                                <div class="action-label">Événements<br/>& Salons</div>
                            </Link>
                        </BCol>
                        <!-- Sprint1 Update: Feature 1.2.1 — Livret explicatif -->
                        <BCol md="3" sm="6">
                            <a href="#" @click.prevent="openLivret" class="action-card" :class="{ 'loading': isLoadingLivret }">
                                <div class="action-icon">📘</div>
                                <div class="action-label">Consulter mon<br/>livret d'arrivée</div>
                            </a>
                        </BCol>
                        <BCol md="3" sm="6">
                            <a href="#" @click.prevent="$inertia.visit(route('dashboard.profile'))" class="action-card">
                                <div class="action-icon">📥</div>
                                <div class="action-label">Mon profil<br/>complet</div>
                            </a>
                        </BCol>
                    </BRow>
                </div>
            </BContainer>
    </div>

    <!-- Sprint1 Update: Feature 1.2.1 — Modal Livret explicatif -->
    <div v-if="showLivretModal" class="livret-modal-overlay" @click="closeLivretModal">
        <div class="livret-modal-content" @click.stop>
            <div class="livret-modal-header">
                <h3 class="livret-modal-title">📘 Livret d'arrivée - Parcours A → Z</h3>
                <button @click="closeLivretModal" class="livret-modal-close">✕</button>
            </div>
            <div class="livret-modal-body">
                <p class="livret-description">Consulte ton livret pour connaître chaque étape de ton parcours, de la candidature à ton arrivée.</p>
                <iframe
                    v-if="livretUrl"
                    :src="livretUrl"
                    class="livret-iframe"
                    frameborder="0"
                ></iframe>
            </div>
            <div class="livret-modal-footer">
                <a
                    v-if="livretUrl"
                    :href="livretUrl"
                    download
                    class="btn-download-livret"
                    target="_blank"
                >
                    ⬇️ Télécharger le livret PDF
                </a>
                <button @click="closeLivretModal" class="btn-close-modal">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Header */
.dashboard-header {
    background: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.logo-link {
    text-decoration: none;
}

.logo-text {
    font-size: 1.75rem;
    font-weight: 800;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.dashboard-page {
    background: #f8fafc;
    min-height: 100vh;
}

/* Welcome Section */
.welcome-card {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    padding: 2.5rem;
    border-radius: 1rem;
    color: white;
}

.welcome-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
}

.welcome-subtitle {
    font-size: 1.05rem;
    opacity: 0.95;
    margin-bottom: 0;
    line-height: 1.6;
}

/* Section Card */
.section-card {
    background: white;
    border-radius: 1rem;
    padding: 1.75rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a202c;
    margin: 0;
}

.btn-view-all {
    color: #1e3a8a;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: color 0.2s;
}

.btn-view-all:hover {
    color: #1e3a8a;
}

/* Applications */
.applications-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* Refonte Story 1.1.2 - Liens cliquables */
.application-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem;
    background: #f8fafc;
    border-radius: 0.75rem;
    border: 1px solid #e2e8f0;
    transition: all 0.2s;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
}

.application-item:hover {
    border-color: #1e3a8a;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
    transform: translateX(4px);
}

.application-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.25rem;
}

.application-program {
    font-size: 0.9rem;
    color: #64748b;
    margin-bottom: 0.5rem;
}

.application-meta {
    display: flex;
    gap: 1rem;
}

.meta-item {
    font-size: 0.875rem;
    color: #94a3b8;
}

.status-badge {
    padding: 0.5rem 1rem;
    font-weight: 600;
}

/* Profile Completion */
.profile-completion {
    text-align: center;
}

.completion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.completion-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 600;
}

.completion-percent {
    font-size: 1.25rem;
    color: #1e3a8a;
    font-weight: 700;
}

.btn-complete-profile {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(45deg, #dc2626, #dc2626);
    color: white;
    text-decoration: none;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s;
}

.btn-complete-profile:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(237, 41, 57, 0.3);
}

/* Quick Actions */
.action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 1rem;
    text-decoration: none;
    transition: all 0.3s;
    min-height: 150px;
}

.action-card:hover {
    border-color: #1e3a8a;
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(102, 126, 234, 0.15);
}

.action-icon {
    font-size: 2.5rem;
    margin-bottom: 0.75rem;
}

.action-label {
    font-size: 0.95rem;
    font-weight: 600;
    color: #1a202c;
    text-align: center;
    line-height: 1.4;
}

/* Sprint1 Update: Feature 1.1.1 — Styles Photo de profil */
.profile-photo-section {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid #e2e8f0;
}

.profile-photo-wrapper {
    position: relative;
}

.profile-photo {
    position: relative;
    width: 80px;
    height: 80px;
}

.profile-photo-img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #1e3a8a;
}

.profile-photo-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #1e3a8a;
    border: 2px solid white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 0.875rem;
}

.profile-photo-btn:hover:not(:disabled) {
    background: #1e3a8a;
    transform: scale(1.1);
}

.profile-photo-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.profile-info {
    flex: 1;
}

.profile-name {
    font-size: 1rem;
    font-weight: 700;
    color: #1a202c;
    margin: 0 0 0.25rem 0;
}

.profile-email {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0;
}

/* Sprint1 Update: Feature 1.1.1 — Styles Documents et Factures */
.documents-list,
.payments-list {
    display: flex;
    flex-direction: column;
    gap: 0.875rem;
}

.document-item,
.payment-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
    transition: all 0.2s;
}

.document-item:hover,
.payment-item:hover {
    border-color: #1e3a8a;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
}

.document-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.document-icon {
    font-size: 1.5rem;
}

.document-name,
.payment-description {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 0.25rem;
}

.document-meta,
.payment-meta {
    font-size: 0.8rem;
    color: #94a3b8;
    margin: 0;
}

.document-status,
.payment-amount {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
}

.amount {
    font-size: 1rem;
    font-weight: 700;
    color: #1e3a8a;
    margin: 0 0 0.25rem 0;
}

.status-badge-sm {
    padding: 0.25rem 0.625rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.empty-state {
    text-align: center;
    padding: 1.5rem 1rem;
}

/* Sprint1 Update: Feature 1.2.1 — Styles Modal Livret */
.action-card.loading {
    opacity: 0.6;
    pointer-events: none;
}

.livret-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 1rem;
}

.livret-modal-content {
    background: white;
    border-radius: 1rem;
    width: 100%;
    max-width: 1200px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.livret-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    border-radius: 1rem 1rem 0 0;
}

.livret-modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    margin: 0;
}

.livret-modal-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    font-size: 1.5rem;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.livret-modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.livret-modal-body {
    flex: 1;
    overflow: auto;
    padding: 1.5rem 2rem;
}

.livret-description {
    text-align: center;
    color: #64748b;
    margin-bottom: 1.5rem;
    font-size: 1rem;
}

.livret-iframe {
    width: 100%;
    height: 70vh;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
}

.livret-modal-footer {
    padding: 1.5rem 2rem;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn-download-livret {
    padding: 0.75rem 2rem;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    color: white;
    text-decoration: none;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-download-livret:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
    color: white;
}

.btn-close-modal {
    padding: 0.75rem 2rem;
    background: #e2e8f0;
    color: #64748b;
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-close-modal:hover {
    background: #cbd5e1;
}

@media (max-width: 768px) {
    .welcome-card {
        padding: 1.5rem;
    }

    .welcome-title {
        font-size: 1.5rem;
    }

    .section-card {
        padding: 1.25rem;
    }

    .application-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .document-item,
    .payment-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .document-status,
    .payment-amount {
        align-items: flex-start;
    }

    /* Sprint1 Update: Feature 1.2.1 — Modal responsive */
    .livret-modal-content {
        max-height: 95vh;
    }

    .livret-modal-header,
    .livret-modal-body,
    .livret-modal-footer {
        padding: 1rem 1.5rem;
    }

    .livret-modal-title {
        font-size: 1.25rem;
    }

    .livret-iframe {
        height: 60vh;
    }

    .livret-modal-footer {
        flex-direction: column;
    }

    .btn-download-livret,
    .btn-close-modal {
        width: 100%;
    }
}
</style>
