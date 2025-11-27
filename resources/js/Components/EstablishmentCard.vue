<script setup lang="ts">
import { Establishment } from '@/Types/establishment';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { BIconGeoAlt, BIconPeople, BIconAward, BIconCheckCircleFill } from 'bootstrap-icons-vue';

interface Props {
    establishment: Establishment;
    showFullDescription?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showFullDescription: false,
});

const displayStars = computed(() => {
    const average = props.establishment.ratings?.average ?? 0;
    const fullStars = Math.floor(average);
    const hasHalfStar = average % 1 >= 0.5;
    return {
        full: fullStars,
        half: hasHalfStar,
        empty: 5 - fullStars - (hasHalfStar ? 1 : 0),
    };
});

const truncatedDescription = computed(() => {
    if (props.showFullDescription) return props.establishment.description;
    return props.establishment.description.length > 120 
        ? props.establishment.description.substring(0, 120) + '...'
        : props.establishment.description;
});

const formatStudentCount = (count?: number): string => {
    if (!count) return 'Non spécifié';
    if (count >= 1000) {
        return `${(count / 1000).toFixed(1)}k étudiants`;
    }
    return `${count} étudiants`;
};

const getMainStudyFields = computed(() => {
    return props.establishment.study_fields.slice(0, 3);
});
</script>

<template>
    <BCard 
        no-body 
        class="card-hover-shadow h-100 overflow-hidden"
    >
        <!-- Image de l'établissement -->
        <div class="card-img-scale-wrapper position-relative">
            <BImg 
                :src="establishment.thumb" 
                :alt="establishment.name"
                class="card-img-top"
                style="height: 200px; object-fit: cover;"
            />
            
            <!-- Badge vérifié -->
            <div 
                v-if="establishment.is_verified" 
                class="position-absolute top-0 end-0 m-2"
            >
                <span class="badge bg-success d-flex align-items-center">
                    <BIconCheckCircleFill class="me-1" size="12" />
                    Vérifié
                </span>
            </div>
            
            <!-- Badge établissement vedette -->
            <div 
                v-if="establishment.is_featured" 
                class="position-absolute top-0 start-0 m-2"
            >
                <span class="badge bg-warning text-dark">
                    <BIconAward class="me-1" size="12" />
                    Recommandé
                </span>
            </div>
        </div>

        <BCardBody class="d-flex flex-column">
            <!-- En-tête avec nom et type -->
            <div class="mb-2">
                <BCardTitle tag="h5" class="mb-1">
                    <Link
                        :href="route('establishments.show', { establishment: establishment.slug })"
                        class="text-decoration-none text-dark stretched-link"
                        prefetch
                    >
                        {{ establishment.name }}
                    </Link>
                </BCardTitle>
                
                <small class="text-primary fw-medium">
                    {{ establishment.establishment_type.name }}
                </small>
            </div>

            <!-- Localisation -->
            <div class="mb-2">
                <small class="text-muted d-flex align-items-center">
                    <BIconGeoAlt class="me-1" size="14" />
                    {{ establishment.city.name }}, {{ establishment.country.name }}
                </small>
            </div>

            <!-- Description -->
            <BCardText class="text-muted small mb-3 flex-grow-1">
                {{ truncatedDescription }}
            </BCardText>

            <!-- Domaines d'études principaux -->
            <div class="mb-3">
                <div class="d-flex flex-wrap gap-1">
                    <span 
                        v-for="field in getMainStudyFields" 
                        :key="field.id"
                        class="badge bg-light text-dark border"
                    >
                        {{ field.name }}
                    </span>
                    <span 
                        v-if="establishment.study_fields.length > 3"
                        class="badge bg-light text-muted border"
                    >
                        +{{ establishment.study_fields.length - 3 }} autres
                    </span>
                </div>
            </div>

            <!-- Informations complémentaires -->
            <div class="d-flex justify-content-between align-items-center text-muted small">
                <div class="d-flex align-items-center">
                    <BIconPeople class="me-1" size="14" />
                    {{ formatStudentCount(establishment.student_count) }}
                </div>
                
                <div v-if="establishment.founded_year" class="text-end">
                    Fondé en {{ establishment.founded_year }}
                </div>
            </div>

            <!-- Ratings -->
            <div 
                v-if="establishment.ratings && establishment.ratings.count > 0" 
                class="mt-2 pt-2 border-top"
            >
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <ul class="list-inline mb-0 me-2">
                            <!-- Étoiles pleines -->
                            <template v-for="n in displayStars.full" :key="`full-${n}`">
                                <li class="list-inline-item me-0">
                                    <i class="fas fa-star text-warning"></i>
                                </li>
                            </template>
                            <!-- Demi-étoile -->
                            <li v-if="displayStars.half" class="list-inline-item me-0">
                                <i class="fas fa-star-half-alt text-warning"></i>
                            </li>
                            <!-- Étoiles vides -->
                            <template v-for="n in displayStars.empty" :key="`empty-${n}`">
                                <li class="list-inline-item me-0">
                                    <i class="far fa-star text-warning"></i>
                                </li>
                            </template>
                        </ul>
                        <span class="small text-muted">
                            {{ establishment.ratings.average.toFixed(1) }}
                        </span>
                    </div>
                    <small class="text-muted">
                        {{ establishment.ratings.count }} avis
                    </small>
                </div>
            </div>
        </BCardBody>
    </BCard>
</template>

<style scoped>
.card-hover-shadow {
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.08);
}

.card-hover-shadow:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

.card-img-scale-wrapper {
    overflow: hidden;
}

.card-img-scale-wrapper img {
    transition: transform 0.3s ease;
}

.card-hover-shadow:hover .card-img-scale-wrapper img {
    transform: scale(1.05);
}

.stretched-link::after {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1;
    content: "";
}
</style>