<script setup lang="ts">
import {
    Country,
    City,
    StudyField,
    EstablishmentType,
    EstablishmentFilters,
} from '@/Types/establishment';
import { router } from '@inertiajs/vue3';
import {
    BIconBuilding,
    BIconGeoAlt,
    BIconBook,
    BIconSliders,
    BIconSearch,
    BIconAward,
    BIconFilter,
    BIconX,
    BIconChevronDown,
} from 'bootstrap-icons-vue';
import debounce from 'lodash/debounce';
import { computed, ref, watch } from 'vue';

interface Props {
    countries: Country[];
    cities: City[];
    studyFields: StudyField[];
    establishmentTypes: EstablishmentType[];
    filters?: EstablishmentFilters;
}

const props = withDefaults(defineProps<Props>(), {
    filters: () => ({}),
});

const emit = defineEmits<{
    search: [filters: EstablishmentFilters];
}>();

const form = ref<EstablishmentFilters>({
    query: props.filters.query || '',
    country: props.filters.country || '',
    city: props.filters.city || '',
    studyFields: props.filters.studyFields || [],
    establishmentTypes: props.filters.establishmentTypes || [],
    levels: props.filters.levels || [],
    isPublic: props.filters.isPublic,
    isPrivate: props.filters.isPrivate,
    isVerified: props.filters.isVerified,
    minStudents: props.filters.minStudents || null,
    maxStudents: props.filters.maxStudents || null,
    foundedAfter: props.filters.foundedAfter || null,
});

// Form processing state
const processing = ref(false);
const showAdvancedFilters = ref(false);

// Study levels options
const studyLevels = [
    { label: 'Licence (L1, L2, L3)', value: 'licence' },
    { label: 'Master (M1, M2)', value: 'master' },
    { label: 'Doctorat (PhD)', value: 'doctorat' },
    { label: 'BTS/DUT', value: 'bts_dut' },
    { label: 'École d\'ingénieur', value: 'ingenieur' },
    { label: 'École de commerce', value: 'commerce' },
    { label: 'Formation professionnelle', value: 'professionnel' },
];

// Function to perform the search
const performSearch = (replace = true) => {
    processing.value = true;
    emit('search', form.value);
    
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
const debouncedSearch = debounce(() => performSearch(), 500);

// Function for button click
const handleSearch = () => {
    performSearch(false);
};

// Watch form changes for automatic updates
watch(
    () => form.value,
    debouncedSearch,
    { deep: true }
);

// Computed property for filtered cities based on selected country
const filteredCities = computed(() => {
    if (!form.value.country) return props.cities;
    return props.cities.filter(city => city.country_id === form.value.country);
});

// Reset city when country changes
watch(() => form.value.country, () => {
    form.value.city = '';
});

// Clear all filters
const clearFilters = () => {
    form.value = {
        query: '',
        country: '',
        city: '',
        studyFields: [],
        establishmentTypes: [],
        levels: [],
        isPublic: undefined,
        isPrivate: undefined,
        isVerified: undefined,
        minStudents: null,
        maxStudents: null,
        foundedAfter: null,
    };
};

// Check if any filters are active
const hasActiveFilters = computed(() => {
    return form.value.query ||
           form.value.country ||
           form.value.city ||
           (form.value.studyFields?.length ?? 0) > 0 ||
           (form.value.establishmentTypes?.length ?? 0) > 0 ||
           (form.value.levels?.length ?? 0) > 0 ||
           form.value.isPublic !== undefined ||
           form.value.isPrivate !== undefined ||
           form.value.isVerified !== undefined ||
           form.value.minStudents ||
           form.value.maxStudents ||
           form.value.foundedAfter;
});

// Get selected country name
const selectedCountryName = computed(() => {
    if (!form.value.country) return '';
    const country = props.countries.find(c => c.id === form.value.country);
    return country?.name || '';
});

// Get selected city name
const selectedCityName = computed(() => {
    if (!form.value.city) return '';
    const city = filteredCities.value.find(c => c.id === form.value.city);
    return city?.name || '';
});

// Get selected study fields names
const selectedStudyFieldsNames = computed(() => {
    if (!form.value.studyFields?.length) return [];
    return props.studyFields
        .filter(sf => form.value.studyFields?.includes(sf.id))
        .map(sf => sf.name);
});

// Get selected establishment types names
const selectedEstablishmentTypesNames = computed(() => {
    if (!form.value.establishmentTypes?.length) return [];
    return props.establishmentTypes
        .filter(et => form.value.establishmentTypes?.includes(et.id))
        .map(et => et.name);
});
</script>

<template>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 md:p-8">
        <!-- Main Search Form -->
        <form @submit.prevent="handleSearch" class="space-y-6">
            <!-- Primary Search Bar -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <BIconSearch class="w-5 h-5 text-gray-400" />
                </div>
                <input
                    v-model="form.query"
                    type="text"
                    class="w-full pl-12 pr-4 py-4 text-lg border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 placeholder-gray-500"
                    placeholder="Rechercher un établissement, une formation, une ville..."
                />
            </div>

            <!-- Quick Filters Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Country Filter -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <BIconGeoAlt class="w-4 h-4 inline mr-1 text-blue-600" />
                        Pays
                    </label>
                    <div class="relative">
                        <select
                            v-model="form.country"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 appearance-none bg-white"
                        >
                            <option value="">Tous les pays</option>
                            <option 
                                v-for="country in countries" 
                                :key="country.id" 
                                :value="country.id"
                            >
                                {{ country.name }}
                            </option>
                        </select>
                        <BIconChevronDown class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                    </div>
                </div>

                <!-- City Filter -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <BIconGeoAlt class="w-4 h-4 inline mr-1 text-blue-600" />
                        Ville
                    </label>
                    <div class="relative">
                        <select
                            v-model="form.city"
                            :disabled="!form.country"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 appearance-none bg-white disabled:bg-gray-50 disabled:text-gray-400"
                        >
                            <option value="">Toutes les villes</option>
                            <option 
                                v-for="city in filteredCities" 
                                :key="city.id" 
                                :value="city.id"
                            >
                                {{ city.name }}
                            </option>
                        </select>
                        <BIconChevronDown class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                    </div>
                </div>

                <!-- Study Fields Filter -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <BIconBook class="w-4 h-4 inline mr-1 text-blue-600" />
                        Domaines d'études
                    </label>
                    <div class="relative">
                        <select
                            v-model="form.studyFields"
                            multiple
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-white"
                            size="1"
                        >
                            <option 
                                v-for="field in studyFields" 
                                :key="field.id" 
                                :value="field.id"
                            >
                                {{ field.name }}
                            </option>
                        </select>
                        <BIconChevronDown class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                    </div>
                </div>

                <!-- Establishment Types Filter -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <BIconBuilding class="w-4 h-4 inline mr-1 text-blue-600" />
                        Type d'établissement
                    </label>
                    <div class="relative">
                        <select
                            v-model="form.establishmentTypes"
                            multiple
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-white"
                            size="1"
                        >
                            <option 
                                v-for="type in establishmentTypes" 
                                :key="type.id" 
                                :value="type.id"
                            >
                                {{ type.name }}
                            </option>
                        </select>
                        <BIconChevronDown class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 justify-between items-center">
                <div class="flex gap-3">
                    <!-- Search Button -->
                    <button
                        type="submit"
                        :disabled="processing"
                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 focus:ring-4 focus:ring-blue-200 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    >
                        <BIconSearch class="w-5 h-5 mr-2" />
                        {{ processing ? 'Recherche...' : 'Rechercher' }}
                    </button>

                    <!-- Advanced Filters Toggle -->
                    <button
                        type="button"
                        @click="showAdvancedFilters = !showAdvancedFilters"
                        class="inline-flex items-center px-6 py-3 bg-white text-gray-700 font-medium rounded-xl border-2 border-gray-200 hover:border-blue-300 hover:bg-blue-50 focus:ring-4 focus:ring-blue-200 transition-all duration-200"
                        :class="{ 'border-blue-300 bg-blue-50 text-blue-700': showAdvancedFilters }"
                    >
                        <BIconFilter class="w-4 h-4 mr-2" />
                        Filtres avancés
                        <BIconChevronDown 
                            class="w-4 h-4 ml-2 transition-transform duration-200"
                            :class="{ 'rotate-180': showAdvancedFilters }"
                        />
                    </button>
                </div>

                <!-- Clear Filters -->
                <button
                    v-if="hasActiveFilters"
                    type="button"
                    @click="clearFilters"
                    class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:text-red-600 transition-colors duration-200"
                >
                    <BIconX class="w-4 h-4 mr-1" />
                    Effacer les filtres
                </button>
            </div>

            <!-- Active Filters Display -->
            <div v-if="hasActiveFilters" class="flex flex-wrap gap-2">
                <span v-if="selectedCountryName" class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                    {{ selectedCountryName }}
                    <button @click="form.country = ''" class="ml-2 hover:text-blue-600">
                        <BIconX class="w-3 h-3" />
                    </button>
                </span>
                <span v-if="selectedCityName" class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                    {{ selectedCityName }}
                    <button @click="form.city = ''" class="ml-2 hover:text-blue-600">
                        <BIconX class="w-3 h-3" />
                    </button>
                </span>
                <span 
                    v-for="fieldName in selectedStudyFieldsNames" 
                    :key="fieldName"
                    class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full"
                >
                    {{ fieldName }}
                    <button @click="form.studyFields = form.studyFields?.filter(id => {
                        const field = studyFields.find(f => f.id === id);
                        return field?.name !== fieldName;
                    })" class="ml-2 hover:text-green-600">
                        <BIconX class="w-3 h-3" />
                    </button>
                </span>
                <span 
                    v-for="typeName in selectedEstablishmentTypesNames" 
                    :key="typeName"
                    class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 text-sm rounded-full"
                >
                    {{ typeName }}
                    <button @click="form.establishmentTypes = form.establishmentTypes?.filter(id => {
                        const type = establishmentTypes.find(t => t.id === id);
                        return type?.name !== typeName;
                    })" class="ml-2 hover:text-purple-600">
                        <BIconX class="w-3 h-3" />
                    </button>
                </span>
            </div>
        </form>

        <!-- Advanced Filters Panel -->
        <div 
            v-if="showAdvancedFilters"
            class="mt-8 pt-8 border-t border-gray-200 space-y-6 animate-fadeIn"
        >
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Filtres avancés</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Study Levels -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        <BIconAward class="w-4 h-4 inline mr-1 text-blue-600" />
                        Niveaux d'études
                    </label>
                    <div class="space-y-2">
                        <label 
                            v-for="level in studyLevels" 
                            :key="level.value"
                            class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors duration-200"
                        >
                            <input
                                v-model="form.levels"
                                type="checkbox"
                                :value="level.value"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            />
                            <span class="text-sm text-gray-700">{{ level.label }}</span>
                        </label>
                    </div>
                </div>

                <!-- Establishment Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Statut de l'établissement</label>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors duration-200">
                            <input
                                v-model="form.isPublic"
                                type="checkbox"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            />
                            <span class="text-sm text-gray-700">Établissement public</span>
                        </label>
                        <label class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors duration-200">
                            <input
                                v-model="form.isPrivate"
                                type="checkbox"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            />
                            <span class="text-sm text-gray-700">Établissement privé</span>
                        </label>
                        <label class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors duration-200">
                            <input
                                v-model="form.isVerified"
                                type="checkbox"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            />
                            <span class="text-sm text-gray-700">Établissement vérifié</span>
                        </label>
                    </div>
                </div>

                <!-- Student Count Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Nombre d'étudiants</label>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Minimum</label>
                            <input
                                v-model.number="form.minStudents"
                                type="number"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                placeholder="Ex: 1000"
                                min="0"
                            />
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Maximum</label>
                            <input
                                v-model.number="form.maxStudents"
                                type="number"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                placeholder="Ex: 50000"
                                min="0"
                            />
                        </div>
                    </div>
                </div>

                <!-- Founded After -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Fondé après</label>
                    <input
                        v-model.number="form.foundedAfter"
                        type="number"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                        placeholder="Ex: 1990"
                        min="1800"
                        :max="new Date().getFullYear()"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}

/* Custom select styling */
select[multiple] {
    background-image: none;
}

select[multiple]:focus {
    outline: none;
}

/* Hide default select arrow for multiple selects */
select[multiple]::-ms-expand {
    display: none;
}

/* Custom checkbox styling */
input[type="checkbox"]:checked {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
    
    .flex.flex-col.sm\\:flex-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .flex.flex-col.sm\\:flex-row .flex {
        justify-content: center;
    }
}
</style>