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
                            <b-card no-body class="bg-transparent">
                                <b-card-header
                                    class="border-bottom bg-transparent px-0 pt-0"
                                >
                                    <h3 class="mb-0">
                                        À propos du logement :
                                        {{ property.propertyType.label }}
                                    </h3>
                                </b-card-header>

                                <b-card-body class="p-0 pt-4">
                                    <h5 class="fw-light mb-4">
                                        Principaux points forts
                                    </h5>

                                    <div class="hstack mb-3 gap-3">
                                        <div
                                            v-if="property.living_room"
                                            class="icon-lg bg-light h5 rounded-2"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Salon"
                                        >
                                            <svg-icon
                                                type="mdi"
                                                :path="mdiSofaSingleOutline"
                                                class="mb-1"
                                            />
                                            {{ property.living_room }}
                                        </div>
                                        <div
                                            v-if="property.room"
                                            class="icon-lg bg-light h5 rounded-2"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Chambre"
                                        >
                                            <svg-icon
                                                type="mdi"
                                                :path="mdiBedOutline"
                                                class="mb-1"
                                            />
                                            {{ property.room }}
                                        </div>
                                        <div
                                            v-if="property.kitchen"
                                            class="icon-lg bg-light h5 rounded-2"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Cuisine"
                                        >
                                            <svg-icon
                                                type="mdi"
                                                :path="mdiCountertopOutline"
                                                class="mb-1"
                                            />
                                            {{ property.kitchen }}
                                        </div>
                                        <div
                                            v-if="property.kitchen"
                                            class="icon-lg bg-light h5 rounded-2"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Salle a manger"
                                        >
                                            <svg-icon
                                                type="mdi"
                                                :path="mdiSilverware"
                                                class="mb-1"
                                            />
                                            {{ property.kitchen }}
                                        </div>
                                        <div
                                            v-if="property.bathroom"
                                            class="icon-lg bg-light h5 rounded-2"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Salle de bain"
                                        >
                                            <svg-icon
                                                type="mdi"
                                                :path="mdiBathtubOutline"
                                                class="mb-1"
                                            />
                                            {{ property.bathroom }}
                                        </div>
                                    </div>

                                    <ExpandableContent
                                        :content="property.description"
                                        :max-chars="440"
                                    />

                                    <div
                                        v-if="
                                            property.outdoor_space ||
                                            property.balcony
                                        "
                                    >
                                        <h5 class="fw-light mb-2">Avantages</h5>
                                        <ul
                                            class="list-group list-group-borderless mb-0"
                                        >
                                            <li
                                                v-if="property.balcony"
                                                class="list-group-item h6 fw-light d-flex mb-0"
                                            >
                                                <BIconPatchCheckFill
                                                    class="text-success me-2"
                                                />
                                                Balcon
                                            </li>
                                            <li
                                                v-if="property.outdoor_space"
                                                class="list-group-item h6 fw-light d-flex mb-0"
                                            >
                                                <BIconPatchCheckFill
                                                    class="text-success me-2"
                                                />
                                                Espace extérieur
                                            </li>
                                        </ul>
                                    </div>
                                </b-card-body>
                            </b-card>

                            <b-card no-body class="bg-transparent">
                                <b-card-header
                                    class="border-bottom bg-transparent px-0 pt-0"
                                >
                                    <b-card-title tag="h3" class="mb-0"
                                        >Commodités</b-card-title
                                    >
                                </b-card-header>

                                <b-card-body class="p-0 pt-4">
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
                                                        class="text-success me-1"
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

                            <b-card no-body class="bg-transparent">
                                <b-card-header
                                    class="border-bottom bg-transparent px-0 pt-0"
                                >
                                    <b-card-title tag="h3" class="mb-0"
                                        >Règlements intérieurs</b-card-title
                                    >
                                </b-card-header>

                                <b-card-body class="p-0 pt-4">
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

<style scoped></style>
