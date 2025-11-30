<script setup lang="ts">
// Clone adapté de RealEstate/Reservation/Partials/PropertyCard.vue
// Design EtatSup - Perfect Pixel Diplomeo
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { BCard, BCardBody } from 'bootstrap-vue-next';
import { BIconBuilding, BIconGeoAlt } from 'bootstrap-icons-vue';
import type { Establishment } from '@/Types/establishment';

interface Props {
    establishment: Establishment;
}
const props = defineProps<Props>();

// Tronquer la présentation à 80 caractères max pour alignement uniforme
const truncatedPresentation = computed(() => {
    if (!props.establishment.sectionPresentation) return '';
    const text = props.establishment.sectionPresentation;
    return text.length > 80 ? text.substring(0, 77) + '...' : text;
});
</script>

<template>
    <BCard no-body class="border-0 shadow-diplomeo">
        <!-- Header avec logo -->
        <div class="card-header-etatsup">
            <div class="d-flex align-items-center gap-3">
                <div class="establishment-logo">
                    <img
                        v-if="establishment.logo"
                        :src="establishment.logo"
                        :alt="establishment.title"
                        class="logo-img"
                    />
                    <BIconBuilding v-else class="logo-placeholder" />
                </div>
                <div>
                    <h6 class="mb-1 fw-bold text-white">{{ establishment.title }}</h6>
                    <div class="text-white-50 small">
                        <BIconGeoAlt class="me-1" />
                        {{ establishment.city }}, {{ establishment.country }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Body avec infos clés -->
        <BCardBody class="p-3">
            <!-- Présentation établissement (80 caractères max pour alignement) -->
            <p v-if="truncatedPresentation" class="establishment-presentation mb-3">
                {{ truncatedPresentation }}
            </p>

            <div class="establishment-info">
                <div v-if="establishment.type" class="info-item">
                    <span class="info-label">Type</span>
                    <span class="info-value">{{ establishment.type }}</span>
                </div>
                <div v-if="establishment.ranking" class="info-item">
                    <span class="info-label">Classement</span>
                    <span class="info-value">#{{ establishment.ranking }}</span>
                </div>
                <div v-if="establishment.studentCount" class="info-item">
                    <span class="info-label">Étudiants</span>
                    <span class="info-value">{{ establishment.studentCount.toLocaleString('fr-FR') }}</span>
                </div>
            </div>

            <!-- Lien vers fiche établissement -->
            <div class="mt-3 pt-3 border-top">
                <Link
                    :href="`/properties/${establishment.slug}`"
                    class="btn btn-sm btn-outline-primary w-100"
                    prefetch
                >
                    <i class="bi bi-eye me-2"></i>
                    Voir la fiche
                </Link>
            </div>
        </BCardBody>
    </BCard>
</template>

<style scoped>
/* Design Perfect Pixel inspiré Diplomeo */
.shadow-diplomeo {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    overflow: hidden;
}

.card-header-etatsup {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    padding: 1.25rem;
    border: none;
}

.establishment-logo {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.logo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.logo-placeholder {
    font-size: 2rem;
    color: white;
}

.establishment-presentation {
    font-size: 0.875rem;
    color: #475569;
    line-height: 1.5;
    font-style: italic;
    border-left: 3px solid #1e3a8a;
    padding-left: 0.75rem;
    min-height: 3rem;
    display: flex;
    align-items: center;
}

.establishment-info {
    display: grid;
    gap: 0.75rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
}

.info-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

.info-value {
    font-size: 0.875rem;
    color: #1a202c;
    font-weight: 600;
}

.btn-outline-primary {
    border-color: #1e3a8a;
    color: #1e3a8a;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    border-color: #1e3a8a;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}
</style>
