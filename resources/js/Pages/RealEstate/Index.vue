<script setup lang="ts">
import AppHead from '@/Components/AppHead.vue';
import Pagination from '@/Components/Pagination.vue';
import PropertyCard from '@/Components/PropertyCard.vue';
import SearchBar from '@/Pages/RealEstate/Partials/SearchBar.vue';
import {
    Category,
    City,
    Equipment,
    Layout,
    PaginationMeta,
    Property,
    PropertyType,
    Regulation,
    SubCategory,
} from '@/Types/index';
import { router } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

interface PropertyPaginated {
    data: Property[];
    meta: PaginationMeta;
}

interface Props {
    properties: PropertyPaginated;
    propertyTypes: PropertyType[];
    categories: Category[];
    subCategories: SubCategory[];
    regulations: Regulation[];
    layouts: Layout[];
    equipments: Equipment[];
    cities: City[];
    filters?: {
        types?: string[];
        category?: string;
        regulations?: string[];
        price_min?: number;
        price_max?: number;
        rooms?: number;
        bathrooms?: number;
        city?: string;
    };
}

const props = withDefaults(defineProps<Props>(), {
    filters: () => ({ city: '' }),
});

const isLoading = ref(false);

// Computed property to show loading state or no results message
const showNoResults = computed(() => {
    return !isLoading.value && props.properties.data.length === 0;
});

// Computed property for SEO description based on active filters
const seoDescription = computed(() => {
    const parts = ['Trouvez un bien'];

    if (props.filters?.types?.length) {
        const typeLabels = props.propertyTypes
            .filter((type) =>
                props.filters?.types?.includes(type.id.toString()),
            )
            .map((type) => type.label)
            .join(', ');
        parts.push(`de type ${typeLabels}`);
    }

    if (props.filters?.category) {
        const category = props.categories.find(
            (cat) => cat.id.toString() === props.filters?.category,
        );
        if (category) {
            parts.push(`dans la catégorie ${category.label}`);
        }
    }

    return parts.join(' ');
});

// Watch route changes for loading state

onMounted(() => {
    router.on('start', (event) => {
        if ((event.detail.visit as any).type === 'get' && event.detail.visit.data) {
            isLoading.value = true;
        }
    });

    router.on('finish', (event) => {
        if ((event.detail.visit as any).type === 'get' && event.detail.visit.data) {
            isLoading.value = false;
        }
    });
});

const seo = {
    title: 'Trouvez votre hébergement idéal',
    description: "Explorez une variété d'options pour vos séjours",
};
</script>

<template>
    <AppHead :title="seo.title">
        <meta
            head-key="description"
            name="description"
            :content="seo.description"
        />
    </AppHead>
    <div>
        <SearchBar
            :property-types="propertyTypes"
            :categories="categories"
            :sub-categories="subCategories"
            :regulations="regulations"
            :filters="{ ...filters, city: filters?.city || '' }"
            :layouts="layouts"
            :cities
            :equipments
        />

        <section class="pt-0">
            <b-container>
                <div v-if="isLoading" class="py-5 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>

                <template v-else>
                    <b-row class="g-4">
                        <b-col
                            md="6"
                            xl="4"
                            v-for="(property, idx) in properties.data"
                            :key="idx"
                        >
                            <PropertyCard :property />
                        </b-col>
                    </b-row>

                    <b-row>
                        <b-col cols="12" class="mt-4">
                            <Pagination :pagination="properties.meta" />
                        </b-col>
                    </b-row>
                    <b-container class="mt-5" v-if="showNoResults">
                        <b-alert
                            :model-value="true"
                            variant="light"
                            class="rounded-3 mb-4 pe-2"
                        >
                            <div class="text-center">
                                <p class="mb-0">
                                    Aucune propriété ne correspond à vos
                                    critères de recherche
                                </p>
                                <small class="text-muted">
                                    Essayez de modifier vos filtres pour voir
                                    plus de résultats
                                </small>
                            </div>
                        </b-alert>
                    </b-container>
                </template>
            </b-container>
        </section>
    </div>
</template>

<style scoped>
.property-grid-enter-active,
.property-grid-leave-active {
    transition: opacity 0.3s ease;
}

.property-grid-enter-from,
.property-grid-leave-to {
    opacity: 0;
}
</style>
