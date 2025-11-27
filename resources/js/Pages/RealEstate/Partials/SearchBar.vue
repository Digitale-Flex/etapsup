<script setup lang="ts">
import {
    Category,
    City,
    Equipment,
    Layout,
    PropertyType,
    Regulation,
    SubCategory,
} from '@/Types/index';
import { router } from '@inertiajs/vue3';
import {
    BIconHouseCheck,
    BIconPinMap,
    BIconRecord2,
    BIconSliders,
    BIconTag,
} from 'bootstrap-icons-vue';
import debounce from 'lodash/debounce';
import { computed, ref, watch } from 'vue';
import VueSlider from 'vue-3-slider-component';

interface Props {
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
        subCategory?: string;
        regulations?: string[];
        layouts?: string[];
        equipments?: string[];
        price?: number[];
        rooms?: number;
        bathrooms?: number;
        city: string;
    };
}

const props = withDefaults(defineProps<Props>(), {
    filters: () => ({ city: '' }),
});

const form = ref({
    types: props.filters.types || [],
    category: props.filters.category || '',
    subCategory: props.filters.subCategory || '',
    regulations: props.filters.regulations || [],
    price: props.filters.price || null,
    rooms: props.filters.rooms || null,
    bathrooms: props.filters.bathrooms || null,
    layouts: props.filters.layouts || [],
    equipments: props.filters.equipments || [],
    city: props.filters.city || '',
});

// Form processing state
const processing = ref(false);

// Size for form controls
const size = ref<'small' | 'large' | undefined>(undefined);

// Function to perform the search
const performSearch = (replace = true) => {
    processing.value = true;
    router.get(
        route('home'),
        {
            ...form.value,
            page: 1,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace,
            onFinish: () => {
                processing.value = false;
            },
        },
    );
};

// Create debounced search function for automatic updates
const debouncedSearch = debounce(() => performSearch(), 300);

// Function for button click
const handleSearch = () => {
    performSearch(false); // Don't replace the URL when explicitly searching
};

// Watch individual form fields for automatic updates
// watch(() => form.value, debouncedSearch, { deep: true, immediate: true });

watch(
    () => form.value,
    debouncedSearch,
    { deep: true }, // immediate: false par défaut
);

const priceSlider = computed({
    get() {
        return form.value.price || [10, 1000]; // Retourne la valeur actuelle ou la valeur par défaut
    },
    set(value: number[]) {
        form.value.price = value[0] === 10 && value[1] === 1000 ? null : value; // Met à jour form.value.price
    },
});

const dotStyle = {
    backgroundColor: '#489793',
    border: '5px solid #fff',
    boxShadow: '0px 0px 0px 1px #489793',
};

const processStyle = {
    backgroundColor: '#489793',
};

const railStyle = {
    backgroundColor: 'rgb(81, 67, 217, 0.1)',
};
</script>

<template>
    <section class="pt-0">
        <b-container class="position-relative">
            <div
                class="rounded-3 p-sm-5 p-4"
                :style="{
                    backgroundImage: `url('/images/dummy/06.jpg')`,
                    backgroundPosition: 'center center',
                    backgroundRepeat: 'no-repeat',
                    backgroundSize: 'cover',
                }"
            >
                <b-row>
                    <b-col md="12" class="mx-auto my-5">
                        <h1 class="text-dark text-center">
                            Trouvez votre logement idéal
                        </h1>
                        <ul
                            class="nav nav-divider h6 text-dark justify-content-center mb-0"
                        >
                            <li class="nav-item">
                                Explorez une variété d'options pour vos séjours
                            </li>
                        </ul>
                    </b-col>
                </b-row>
            </div>

            <b-row class="mt-n4 mt-sm-n7">
                <b-col cols="11" class="mx-auto">
                    <div class="bg-mode rounded-3 p-4 shadow">
                        <form
                            @submit.prevent="handleSearch"
                            class="form-control-bg-transparent bg-mode rounded-3"
                        >
                            <b-row class="g-4 align-items-center">
                                <b-col xl="12">
                                    <b-row class="g-4">
                                        <b-col md="6" lg="3">
                                            <label class="h6 fw-normal mb-0">
                                                <BIconHouseCheck
                                                    class="text-primary me-1"
                                                />
                                                Hébergement
                                            </label>
                                            <div
                                                class="form-border-bottom form-control-transparent form-fs-lg mt-2"
                                            >
                                                <MultiSelect
                                                    v-model="form.types"
                                                    :options="propertyTypes"
                                                    display="chip"
                                                    :max-selected-labels="3"
                                                    option-label="label"
                                                    option-value="id"
                                                    :size="size"
                                                    class="w-full"
                                                    placeholder="Type d’hébergement"
                                                />
                                            </div>
                                        </b-col>

                                        <b-col md="6" lg="3">
                                            <label class="h6 fw-normal mb-0">
                                                <BIconTag
                                                    class="text-primary me-1"
                                                />
                                                Catégorie
                                            </label>
                                            <div
                                                class="form-border-bottom form-control-transparent form-fs-lg mt-2"
                                            >
                                                <Select
                                                    v-model="form.category"
                                                    :options="categories"
                                                    option-label="label"
                                                    option-value="id"
                                                    showClear
                                                    :size="size"
                                                    class="w-full"
                                                    placeholder="Catégorie"
                                                />
                                            </div>
                                        </b-col>

                                        <b-col md="6" lg="3">
                                            <label class="h6 fw-normal mb-0">
                                                <BIconRecord2
                                                    class="text-primary me-1"
                                                />
                                                Règlement</label
                                            >
                                            <div
                                                class="form-border-bottom form-control-transparent form-fs-lg mt-2"
                                            >
                                                <MultiSelect
                                                    v-model="form.regulations"
                                                    :options="regulations"
                                                    display="chip"
                                                    :max-selected-labels="2"
                                                    option-label="label"
                                                    option-value="id"
                                                    :size="size"
                                                    class="w-full"
                                                    placeholder="Règlement intérieur"
                                                />
                                            </div>
                                        </b-col>

                                        <b-col md="6" lg="3">
                                            <label class="h6 fw-normal mb-0">
                                                <BIconPinMap
                                                    class="text-primary me-1"
                                                />
                                                Ville</label
                                            >
                                            <div
                                                class="form-border-bottom form-control-transparent form-fs-lg mt-2"
                                            >
                                                <Select
                                                    v-model="form.city"
                                                    :options="cities"
                                                    filter
                                                    showClear
                                                    option-label="name"
                                                    option-value="id"
                                                    :size="size"
                                                    class="w-full"
                                                    placeholder="Dans quelle ville ?"
                                                />
                                            </div>
                                        </b-col>
                                    </b-row>
                                </b-col>
                            </b-row>
                        </form>

                        <div class="d-grid mt-4">
                            <input
                                type="checkbox"
                                class="btn-check"
                                id="btn-check-soft"
                            />
                            <label
                                class="btn btn-primary-soft btn-primary-check mb-0"
                                for="btn-check-soft"
                                v-b-toggle.collapseExample
                            >
                                <BIconSliders class="fa-fe me-2" />
                                Filtres avancés
                            </label>
                        </div>

                        <b-collapse class="collapse" id="collapseExample">
                            <b-form class="row g-4 mt-3">
                                <b-col md="6" lg="4">
                                    <div class="mb-5">
                                        <label> Sous catégorie </label>
                                        <div
                                            class="form-border-bottom form-control-transparent form-fs-lg mt-2"
                                        >
                                            <Select
                                                v-model="form.subCategory"
                                                :options="subCategories"
                                                option-label="label"
                                                option-value="id"
                                                showClear
                                                :size="size"
                                                class="w-full"
                                                placeholder="Sous catégorie"
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label"
                                            >Gamme de prix</label
                                        >
                                        <div
                                            class="d-flex justify-content-between"
                                        >
                                            <span>€ {{ priceSlider[0] }}</span>
                                            <span>€ {{ priceSlider[1] }}</span>
                                        </div>
                                        <div class="position-relative">
                                            <VueSlider
                                                v-model="priceSlider"
                                                :min="10"
                                                :max="15000"
                                                :dotSize="19"
                                                :dotStyle="dotStyle"
                                                :processStyle="processStyle"
                                                :railStyle="railStyle"
                                                tooltip="none"
                                            />
                                        </div>
                                    </div>
                                </b-col>

                                <b-col md="4" lg="4">
                                    <label class="form-label">Équipement</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <div
                                            v-for="(item, i) in equipments"
                                            :key="i"
                                            class="flex items-center gap-2"
                                        >
                                            <Checkbox
                                                v-model="form.equipments"
                                                :inputId="`equipment${i}`"
                                                name="equipment"
                                                :value="item.id"
                                            />
                                            <label :for="`equipment${i}`">
                                                {{ item.label }}
                                            </label>
                                        </div>
                                    </div>
                                </b-col>

                                <b-col md="4">
                                    <label class="form-label"
                                        >Aménagement</label
                                    >
                                    <div class="d-flex flex-wrap gap-2">
                                        <div
                                            v-for="(item, i) in layouts"
                                            :key="i"
                                            class="flex items-center gap-2"
                                        >
                                            <Checkbox
                                                v-model="form.layouts"
                                                :inputId="`layout${i}`"
                                                name="layout"
                                                :value="item.id"
                                            />
                                            <label :for="`ingredient${i}`">
                                                {{ item.label }}
                                            </label>
                                        </div>
                                    </div>
                                </b-col>
                            </b-form>
                        </b-collapse>
                    </div>
                </b-col>
            </b-row>
        </b-container>
    </section>
</template>

<style scoped>
:deep(.p-multiselect),
:deep(.p-select),
:deep(.p-dropdown) {
    border: none !important;
}
</style>
