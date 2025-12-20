<script setup lang="ts">
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({ layout: DashboardLayout });

interface ApplicationData {
    id: string;
    status: string;
    state: string;
    created_at: string;
    updated_at: string;
    establishment: {
        id: string;
        title: string;
        slug: string;
        logo: string;
    };
    formData: {
        current_step?: number;
        // Section 1
        surname?: string;
        name?: string;
        gender?: string;
        date_of_birth?: string;
        nationality?: string;
        country_of_birth?: string;
        city_of_birth?: string;
        address?: string;
        postal_code?: string;
        city?: string;
        country?: string;
        email?: string;
        phone?: string;
        parent_email?: string;
        // Section 2
        current_diploma?: string;
        diploma_year?: number;
        years_validated?: string;
        previous_institution?: string;
        institution_city?: string;
        institution_country?: string;
        // Section 3
        mother_tongue?: string;
        english_test?: string;
        english_level?: string;
        french_test?: string;
        french_level?: string;
        // Section 4
        motivation?: string;
        application_location?: string;
        application_date?: string;
    };
}

const props = defineProps<{
    application: ApplicationData;
}>();

const statusBadge = computed(() => {
    const badges: Record<string, { variant: string; label: string }> = {
        draft: { variant: 'secondary', label: 'Brouillon' },
        submitted: { variant: 'info', label: 'Soumise' },
        accepted: { variant: 'success', label: 'Acceptée' },
        rejected: { variant: 'danger', label: 'Refusée' },
    };
    return badges[props.application.status] || { variant: 'secondary', label: props.application.status };
});

const progressPercentage = computed(() => {
    const step = props.application.formData.current_step || 5;
    return (step / 5) * 100;
});
</script>

<template>
    <Head title="Détails de la candidature" />

    <BRow class="g-4">
        <!-- Header -->
        <BCol cols="12">
            <BCard no-body class="border-0 shadow-etatsup">
                <BCardHeader class="header-etatsup">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 text-white">
                                <i class="bi bi-file-earmark-text me-2"></i>
                                Candidature #{{ application.id.substring(0, 8) }}
                            </h4>
                            <p class="mb-0 text-white-50 small">
                                Créée le {{ application.created_at }} · Mise à jour le {{ application.updated_at }}
                            </p>
                        </div>
                        <div>
                            <span
                                class="badge px-3 py-2"
                                :class="`bg-${statusBadge.variant}`"
                                style="font-size: 1rem;"
                            >
                                {{ statusBadge.label }}
                            </span>
                        </div>
                    </div>
                </BCardHeader>

                <BCardBody class="p-4">
                    <!-- Établissement -->
                    <div class="mb-4 pb-4 border-bottom">
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-building me-2"></i>
                            Établissement
                        </h6>
                        <div class="d-flex align-items-center">
                            <img
                                v-if="application.establishment.logo"
                                :src="application.establishment.logo"
                                class="rounded me-3"
                                style="width: 60px; height: 60px; object-fit: cover;"
                                alt="Logo"
                            />
                            <div>
                                <Link
                                    :href="route('establishments.show', application.establishment.slug)"
                                    class="h5 mb-0 text-decoration-none"
                                >
                                    {{ application.establishment.title }}
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Progression -->
                    <div v-if="application.status === 'draft'" class="mb-4 pb-4 border-bottom">
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-hourglass-split me-2"></i>
                            Progression
                        </h6>
                        <div class="progress" style="height: 25px;">
                            <div
                                class="progress-bar bg-gradient-etatsup"
                                :style="{ width: progressPercentage + '%' }"
                            >
                                {{ progressPercentage.toFixed(0) }}% (Étape {{ application.formData.current_step || 5 }}/5)
                            </div>
                        </div>
                        <div class="mt-3">
                            <Link
                                :href="`/applications/create?application_id=${application.id}`"
                                class="btn btn-etapsup"
                            >
                                <i class="bi bi-pencil-square me-2"></i>
                                Reprendre ma candidature
                            </Link>
                        </div>
                    </div>

                    <!-- Données du formulaire -->
                    <div>
                        <h6 class="text-muted mb-4">
                            <i class="bi bi-file-earmark-person me-2"></i>
                            Informations de candidature
                        </h6>

                        <!-- Section 1: Informations personnelles -->
                        <div class="section-title mb-3">
                            <i class="bi bi-person-circle me-2 text-primary"></i>
                            <strong>Informations personnelles</strong>
                        </div>

                        <BRow class="g-3 mb-4">
                            <!-- Identité -->
                            <BCol md="6">
                                <div class="info-card">
                                    <div class="info-card-header">
                                        <i class="bi bi-person-badge text-primary"></i>
                                        <span>Identité</span>
                                    </div>
                                    <div class="info-card-body">
                                        <div class="info-item">
                                            <span class="info-label">Prénom</span>
                                            <span class="info-value">{{ application.formData.name || '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Nom</span>
                                            <span class="info-value">{{ application.formData.surname || '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Genre</span>
                                            <span class="info-value">
                                                {{ application.formData.gender === 'M' ? 'Masculin' : application.formData.gender === 'F' ? 'Féminin' : '-' }}
                                            </span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Date de naissance</span>
                                            <span class="info-value">{{ application.formData.date_of_birth || '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Nationalité</span>
                                            <span class="info-value">{{ application.formData.nationality || '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </BCol>

                            <!-- Lieu de naissance -->
                            <BCol md="6">
                                <div class="info-card">
                                    <div class="info-card-header">
                                        <i class="bi bi-geo-alt text-primary"></i>
                                        <span>Lieu de naissance</span>
                                    </div>
                                    <div class="info-card-body">
                                        <div class="info-item">
                                            <span class="info-label">Pays</span>
                                            <span class="info-value">{{ application.formData.country_of_birth || '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Ville</span>
                                            <span class="info-value">{{ application.formData.city_of_birth || '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </BCol>

                            <!-- Contact -->
                            <BCol md="6">
                                <div class="info-card">
                                    <div class="info-card-header">
                                        <i class="bi bi-envelope text-primary"></i>
                                        <span>Contact</span>
                                    </div>
                                    <div class="info-card-body">
                                        <div class="info-item">
                                            <span class="info-label">Email</span>
                                            <span class="info-value">{{ application.formData.email || '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Téléphone</span>
                                            <span class="info-value">{{ application.formData.phone || '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Email parent</span>
                                            <span class="info-value">{{ application.formData.parent_email || '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </BCol>

                            <!-- Adresse -->
                            <BCol md="6">
                                <div class="info-card">
                                    <div class="info-card-header">
                                        <i class="bi bi-house text-primary"></i>
                                        <span>Adresse</span>
                                    </div>
                                    <div class="info-card-body">
                                        <div class="info-item">
                                            <span class="info-label">Rue</span>
                                            <span class="info-value">{{ application.formData.address || '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Code postal</span>
                                            <span class="info-value">{{ application.formData.postal_code || '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Ville</span>
                                            <span class="info-value">{{ application.formData.city || '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Pays</span>
                                            <span class="info-value">{{ application.formData.country || '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </BCol>
                        </BRow>

                        <!-- Section 2: Formation -->
                        <div class="section-title mb-3">
                            <i class="bi bi-mortarboard me-2 text-primary"></i>
                            <strong>Formation académique</strong>
                        </div>

                        <BRow class="g-3 mb-4">
                            <!-- Diplôme actuel -->
                            <BCol md="6">
                                <div class="info-card">
                                    <div class="info-card-header">
                                        <i class="bi bi-award text-primary"></i>
                                        <span>Diplôme actuel</span>
                                    </div>
                                    <div class="info-card-body">
                                        <div class="info-item">
                                            <span class="info-label">Diplôme</span>
                                            <span class="info-value">{{ application.formData.current_diploma || '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Année d'obtention</span>
                                            <span class="info-value">{{ application.formData.diploma_year || '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Années validées</span>
                                            <span class="info-value">{{ application.formData.years_validated || '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </BCol>

                            <!-- Établissement précédent -->
                            <BCol md="6">
                                <div class="info-card">
                                    <div class="info-card-header">
                                        <i class="bi bi-building text-primary"></i>
                                        <span>Établissement précédent</span>
                                    </div>
                                    <div class="info-card-body">
                                        <div class="info-item">
                                            <span class="info-label">Établissement</span>
                                            <span class="info-value">{{ application.formData.previous_institution || '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Ville</span>
                                            <span class="info-value">{{ application.formData.institution_city || '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Pays</span>
                                            <span class="info-value">{{ application.formData.institution_country || '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </BCol>
                        </BRow>

                        <!-- Section 3: Langues -->
                        <div class="section-title mb-3">
                            <i class="bi bi-translate me-2 text-primary"></i>
                            <strong>Compétences linguistiques</strong>
                        </div>

                        <BRow class="g-3 mb-4">
                            <!-- Langue maternelle -->
                            <BCol md="4">
                                <div class="info-card">
                                    <div class="info-card-header">
                                        <i class="bi bi-chat-square-text text-primary"></i>
                                        <span>Langue maternelle</span>
                                    </div>
                                    <div class="info-card-body">
                                        <div class="info-item">
                                            <span class="info-label">Langue</span>
                                            <span class="info-value">{{ application.formData.mother_tongue || '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </BCol>

                            <!-- Anglais -->
                            <BCol md="4">
                                <div class="info-card">
                                    <div class="info-card-header">
                                        <i class="bi bi-flag text-primary"></i>
                                        <span>Anglais</span>
                                    </div>
                                    <div class="info-card-body">
                                        <div class="info-item">
                                            <span class="info-label">Test passé</span>
                                            <span class="info-value">{{ application.formData.english_test || '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Niveau</span>
                                            <span class="info-value">{{ application.formData.english_level || '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </BCol>

                            <!-- Français -->
                            <BCol md="4">
                                <div class="info-card">
                                    <div class="info-card-header">
                                        <i class="bi bi-flag-fill text-primary"></i>
                                        <span>Français</span>
                                    </div>
                                    <div class="info-card-body">
                                        <div class="info-item">
                                            <span class="info-label">Test passé</span>
                                            <span class="info-value">{{ application.formData.french_test || '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Niveau</span>
                                            <span class="info-value">{{ application.formData.french_level || '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </BCol>
                        </BRow>

                        <!-- Section 4: Motivation -->
                        <div class="section-title mb-3">
                            <i class="bi bi-heart me-2 text-primary"></i>
                            <strong>Motivation et projet</strong>
                        </div>

                        <BRow class="g-3">
                            <!-- Motivation -->
                            <BCol cols="12">
                                <div class="info-card">
                                    <div class="info-card-header">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                        <span>Lettre de motivation</span>
                                    </div>
                                    <div class="info-card-body">
                                        <div class="info-item-full">
                                            <p class="motivation-text">{{ application.formData.motivation || '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </BCol>

                            <!-- Informations complémentaires -->
                            <BCol md="6">
                                <div class="info-card">
                                    <div class="info-card-header">
                                        <i class="bi bi-calendar-event text-primary"></i>
                                        <span>Informations complémentaires</span>
                                    </div>
                                    <div class="info-card-body">
                                        <div class="info-item">
                                            <span class="info-label">Lieu de candidature</span>
                                            <span class="info-value">{{ application.formData.application_location || '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Date de candidature</span>
                                            <span class="info-value">{{ application.formData.application_date || '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </BCol>
                        </BRow>
                    </div>

                    <!-- UI-Fix-5.1: Section Documents - Upload après soumission ou paiement -->
                    <div v-if="['submitted', 'in_review', 'paid', 'pending_payment'].includes(application.status)" class="mt-4 pt-4 border-top">
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-files me-2"></i>
                            Documents justificatifs
                        </h6>

                        <BAlert variant="info" :model-value="true" class="mb-3">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-info-circle me-3 mt-1" style="font-size: 1.5rem;"></i>
                                <div>
                                    <p class="mb-2 fw-semibold">
                                        Vous pouvez mettre à jour vos documents tant que votre dossier n'est pas traité par l'équipe EtapSup.
                                    </p>
                                    <p class="mb-0 small text-muted">
                                        Les documents modifiés seront revérifiés par nos équipes avant la décision finale.
                                    </p>
                                </div>
                            </div>
                        </BAlert>

                        <Link
                            :href="`/applications/create?application_id=${application.id}`"
                            class="btn btn-etapsup"
                        >
                            <i class="bi bi-upload me-2"></i>
                            Mettre à jour mes documents
                        </Link>
                    </div>
                </BCardBody>
            </BCard>
        </BCol>
    </BRow>
</template>

<style scoped>
/* Charte EtatSup - Purple Gradient */
.header-etatsup {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    padding: 1.5rem;
    border: none;
}

.shadow-etatsup {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
}

.bg-gradient-etatsup {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
}

.btn-etapsup {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%) !important;
    border: none !important;
    color: white !important;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-etapsup:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

/* Section Title */
.section-title {
    font-size: 1.1rem;
    color: #1a202c;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #1e3a8a;
}

/* Info Card */
.info-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.info-card:hover {
    border-color: #1e3a8a;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
    transform: translateY(-2px);
}

.info-card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 700;
    font-size: 0.95rem;
    color: #1a202c;
}

.info-card-header i {
    font-size: 1.1rem;
}

.info-card-body {
    padding: 1rem;
}

/* Info Item */
.info-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
    flex-shrink: 0;
    width: 45%;
}

.info-value {
    font-size: 0.875rem;
    color: #1a202c;
    font-weight: 600;
    text-align: right;
    width: 55%;
    word-break: break-word;
}

/* Info Item Full Width */
.info-item-full {
    padding: 0.5rem 0;
}

.motivation-text {
    font-size: 0.9rem;
    color: #1a202c;
    line-height: 1.6;
    margin: 0;
    white-space: pre-wrap;
    word-wrap: break-word;
}

/* Responsive */
@media (max-width: 768px) {
    .info-item {
        flex-direction: column;
        gap: 0.25rem;
    }

    .info-label,
    .info-value {
        width: 100%;
        text-align: left;
    }

    .info-card-header {
        font-size: 0.875rem;
    }
}
</style>
