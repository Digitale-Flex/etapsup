<script setup lang="ts">
import AppHead from '@/Components/AppHead.vue';
import ExpandableContent from '@/Components/ExpandableContent.vue';
import CustormerReview from '@/Pages/RealEstate/Partials/CustormerReview.vue';
import MonthlyPriceOverview from '@/Pages/RealEstate/Partials/MonthlyPriceOverview.vue';
import PriceOverview from '@/Pages/RealEstate/Partials/PriceOverview.vue';
import PropertyAddress from '@/Pages/RealEstate/Partials/PropertyAddress.vue';
import PropertyGallery from '@/Pages/RealEstate/Partials/PropertyGallery.vue';
import { Property, RealEstateSetting } from '@/Types/index';
import { faCheckCircle } from '@fortawesome/free-solid-svg-icons';
import SvgIcon from '@jamescoyle/vue-icon';
import {
    mdiBathtubOutline,
    mdiBedOutline,
    mdiCountertopOutline,
    mdiHomeAutomation,
    mdiNaturePeople,
    mdiSilverware,
    mdiSofaSingleOutline,
} from '@mdi/js';
import { BIconPatchCheckFill } from 'bootstrap-icons-vue';

defineProps<{
    property: Property;
    settings: RealEstateSetting;
    rentalMonthlyBilling: boolean;
    pagination: {
        current_page: number;
        per_page: number;
        total: number;
        has_more: boolean;
    };
}>();
</script>

<template>
    <AppHead :title="property.title">
        <meta
            head-key="description"
            name="description"
            :content="property.description"
        />
    </AppHead>
    <main>
        <PropertyAddress :property />

        <PropertyGallery :images="property.images" />

        <section class="pt-0">
            <b-container data-sticky-container>
                <b-row class="g-4 g-xl-5">
                    <b-col xl="7" class="order-1">
                        <div class="vstack gap-5">
                            <b-card no-body class="bg-transparent border-0 shadow-etatsup">
                                <b-card-header
                                    class="header-etatsup px-4 py-3"
                                >
                                    <h3 class="mb-0 text-white">
                                        À propos de l'établissement :
                                        {{ property.propertyType.label }}
                                    </h3>
                                </b-card-header>

                                <b-card-body class="p-4">
                                    <h5 class="fw-bold mb-4 text-etatsup">
                                        Informations clés
                                    </h5>

                                    <!-- Statistiques éducatives EtatSup -->
                                    <div class="hstack mb-4 gap-3" v-if="property.student_count || property.ranking">
                                        <div
                                            v-if="property.student_count"
                                            class="stat-badge"
                                        >
                                            <i class="bi bi-people-fill text-primary me-2"></i>
                                            <strong>{{ property.student_count.toLocaleString() }}</strong> étudiants
                                        </div>
                                        <div
                                            v-if="property.ranking"
                                            class="stat-badge"
                                        >
                                            <i class="bi bi-trophy-fill text-warning me-2"></i>
                                            Classement <strong>#{{ property.ranking }}</strong>
                                        </div>
                                    </div>

                                    <ExpandableContent
                                        :content="property.description"
                                        :max-chars="440"
                                    />

                                    <!-- Contact établissement -->
                                    <div v-if="property.website || property.phone || property.email" class="mt-4">
                                        <h6 class="fw-bold text-etatsup mb-3">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Contact
                                        </h6>
                                        <div class="vstack gap-2">
                                            <a v-if="property.website" :href="property.website" target="_blank" class="contact-link">
                                                <i class="bi bi-globe me-2"></i>
                                                Site web officiel
                                            </a>
                                            <a v-if="property.phone" :href="`tel:${property.phone}`" class="contact-link">
                                                <i class="bi bi-telephone me-2"></i>
                                                {{ property.phone }}
                                            </a>
                                            <a v-if="property.email" :href="`mailto:${property.email}`" class="contact-link">
                                                <i class="bi bi-envelope me-2"></i>
                                                {{ property.email }}
                                            </a>
                                        </div>
                                    </div>
                                </b-card-body>
                            </b-card>

                            <b-card no-body class="bg-transparent border-0 shadow-etatsup">
                                <b-card-header
                                    class="header-etatsup px-4 py-3"
                                >
                                    <b-card-title tag="h3" class="mb-0 text-white"
                                        >Équipements & Services</b-card-title
                                    >
                                </b-card-header>

                                <b-card-body class="p-4">
                                    <b-row class="g-4">
                                        <b-col
                                            sm="6"
                                            v-if="property.equipments"
                                        >
                                            <h6>
                                                <svg-icon
                                                    type="mdi"
                                                    :path="mdiHomeAutomation"
                                                    class="mb-1 me-1"
                                                />
                                                Équipements
                                            </h6>
                                            <ul
                                                class="list-group list-group-borderless mb-0 mt-2"
                                            >
                                                <li
                                                    v-for="(
                                                        equipment, i
                                                    ) in property.equipments"
                                                    :key="i"
                                                    class="list-group-item pb-0"
                                                >
                                                    <font-awesome-icon
                                                        :icon="faCheckCircle"
                                                        class="text-primary me-1"
                                                    />
                                                    {{ equipment.label }}
                                                </li>
                                            </ul>
                                        </b-col>

                                        <b-col sm="6" v-if="property.layouts">
                                            <h6>
                                                <svg-icon
                                                    type="mdi"
                                                    :path="mdiNaturePeople"
                                                    class="mb-1 me-1"
                                                />
                                                Aménagement
                                            </h6>

                                            <ul
                                                class="list-group list-group-borderless mb-0 mt-2"
                                            >
                                                <li
                                                    v-for="(
                                                        layout, i
                                                    ) in property.layouts"
                                                    :key="i"
                                                    class="list-group-item pb-0"
                                                >
                                                    <font-awesome-icon
                                                        :icon="faCheckCircle"
                                                        class="text-success me-1"
                                                    />
                                                    {{ layout.label }}
                                                </li>
                                            </ul>
                                        </b-col>
                                    </b-row>
                                </b-card-body>
                            </b-card>

                            <b-card no-body class="bg-transparent border-0 shadow-etatsup">
                                <b-card-header
                                    class="header-etatsup px-4 py-3"
                                >
                                    <b-card-title tag="h3" class="mb-0 text-white"
                                        >Règlements & Politiques</b-card-title
                                    >
                                </b-card-header>

                                <b-card-body class="p-4">
                                    <b-row class="g-4">
                                        <b-col
                                            sm="12"
                                            v-if="property.regulations"
                                        >
                                            <ul
                                                class="list-group list-group-borderless mb-0 mt-2"
                                            >
                                                <li
                                                    v-for="(
                                                        regulation, i
                                                    ) in property.regulations"
                                                    :key="i"
                                                    class="list-group-item pb-0"
                                                >
                                                    <font-awesome-icon
                                                        :icon="faCheckCircle"
                                                        class="text-success me-1"
                                                    />
                                                    {{ regulation.label }}
                                                </li>
                                            </ul>
                                            <div
                                                class="mt-2"
                                                v-html="property.regulation"
                                            />
                                        </b-col>
                                    </b-row>
                                </b-card-body>
                            </b-card>

                            <CustormerReview
                                :property
                                :can-review="false"
                                :pagination
                            />
                        </div>
                    </b-col>
                    <aside class="col-xl-5 order-xl-2">
                        <MonthlyPriceOverview
                            v-if="rentalMonthlyBilling"
                            :property
                            :settings
                        />

                        <PriceOverview v-else :property :settings />
                    </aside>
                </b-row>
            </b-container>
        </section>
    </main>
</template>

<style scoped>
/* Branding EtatSup - Gradient Purple Perfect Pixel */
.header-etatsup {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    border: none;
    border-radius: 12px 12px 0 0;
}

.shadow-etatsup {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    overflow: hidden;
}

.text-etatsup {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    background-color: #f8fafc;
    border-radius: 8px;
    font-size: 0.9rem;
    border: 2px solid #e5e7eb;
    transition: all 0.2s ease;
}

.stat-badge:hover {
    border-color: #1e3a8a;
    background-color: #f3f4f6;
}

.contact-link {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    color: #64748b;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.2s ease;
    border: 1px solid #e5e7eb;
}

.contact-link:hover {
    background-color: #f8fafc;
    color: #1e3a8a;
    border-color: #1e3a8a;
    transform: translateX(5px);
}
</style>
